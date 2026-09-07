<?php

namespace Ikechukwukalu\Magicmake\Generation;

use RuntimeException;
use Throwable;

class GenerationPlan
{
    /** @var string */
    private $basePath;

    /** @var array<string, array{path: string, content: string, mode: string, original?: string}> */
    private $operations = [];

    public function __construct($basePath)
    {
        $this->basePath = $this->normalizePath($basePath);
    }

    public function addFile($path, $content)
    {
        return $this->addOperation($path, $content, 'file');
    }

    public function addUniqueAppend($path, $content)
    {
        return $this->addOperation($path, trim($content).PHP_EOL, 'append');
    }

    /**
     * Add a semantics-aware edit whose source bytes must remain unchanged between
     * planning and writing. The operation is never made safe by --force.
     */
    public function addManagedFile($path, $original, $content)
    {
        return $this->addOperation($path, $content, 'managed', (string) $original);
    }

    /**
     * Add a creation whose destination must remain absent through the write.
     * Concurrent appearance is never overridden by --force.
     */
    public function addManagedCreate($path, $content)
    {
        return $this->addOperation($path, $content, 'managed-create');
    }

    /**
     * Add a non-overridable preflight conflict for an unsafe managed file.
     */
    public function addConflict($path)
    {
        return $this->addOperation($path, '', 'blocked');
    }

    /**
     * @return array<int, array{path: string, action: string}>
     */
    public function preview($overwrite = false)
    {
        $preview = [];

        foreach ($this->operations as $operation) {
            $preview[] = [
                'path' => $operation['path'],
                'action' => $this->resolveAction($operation, $overwrite),
            ];
        }

        return $preview;
    }

    /**
     * @return array<int, string>
     */
    public function conflicts($overwrite = false)
    {
        $conflicts = [];

        foreach ($this->preview($overwrite) as $item) {
            if ($item['action'] === 'conflict') {
                $conflicts[] = $item['path'];
            }
        }

        return $conflicts;
    }

    /**
     * Commit all writes, restoring every touched file if any write fails.
     *
     * @param  callable|null  $beforeWrite  Test seam invoked before each write.
     * @return array<int, array{path: string, action: string}>
     */
    public function commit($overwrite = false, ?callable $beforeWrite = null)
    {
        $preview = $this->preview($overwrite);
        $conflicts = $this->conflicts($overwrite);

        if ($conflicts !== []) {
            throw new GenerationConflictException($conflicts);
        }

        $backups = [];
        $createdDirectories = [];
        $writeIndex = 0;

        try {
            foreach ($preview as $index => $item) {
                if ($item['action'] === 'skip') {
                    continue;
                }

                $operation = array_values($this->operations)[$index];
                $path = $operation['path'];
                $this->ensureDirectory(dirname($path), $createdDirectories);

                if ($beforeWrite !== null) {
                    $beforeWrite($path, $writeIndex);
                }

                if ($operation['mode'] === 'managed') {
                    $current = is_file($path) ? file_get_contents($path) : false;
                    if ($current === false || $current !== $operation['original']) {
                        throw new RuntimeException("Managed generation source [{$path}] changed after preflight.");
                    }
                }

                $content = $operation['content'];
                if ($operation['mode'] === 'managed-create') {
                    $this->writeExclusive($path, $content, $backups);
                    $writeIndex++;
                    continue;
                }

                $backups[$path] = is_file($path) ? file_get_contents($path) : null;

                if ($operation['mode'] === 'append' && is_file($path)) {
                    $existing = (string) file_get_contents($path);
                    $content = rtrim($existing).PHP_EOL.PHP_EOL.$content;
                }

                if (file_put_contents($path, $content, LOCK_EX) === false) {
                    throw new RuntimeException("Unable to write [{$path}].");
                }

                $writeIndex++;
            }
        } catch (Throwable $exception) {
            $this->rollback($backups, $createdDirectories);
            throw $exception;
        }

        return $preview;
    }

    private function writeExclusive($path, $content, array &$backups)
    {
        $handle = @fopen($path, 'x');
        if ($handle === false) {
            throw new RuntimeException("Managed generation destination [{$path}] appeared after preflight.");
        }

        $backups[$path] = null;
        try {
            $length = strlen($content);
            $written = 0;
            while ($written < $length) {
                $result = fwrite($handle, substr($content, $written));
                if ($result === false || $result === 0) {
                    throw new RuntimeException("Unable to write [{$path}].");
                }
                $written += $result;
            }
            if (! fflush($handle)) {
                throw new RuntimeException("Unable to write [{$path}].");
            }
        } finally {
            fclose($handle);
        }
    }

    private function addOperation($path, $content, $mode, $original = null)
    {
        $path = $this->normalizePath($path);
        $this->assertSafePath($path);

        if (isset($this->operations[$path])) {
            throw new RuntimeException("The generation plan targets [{$path}] more than once.");
        }

        $this->operations[$path] = compact('path', 'content', 'mode');
        if ($mode === 'managed') {
            $this->operations[$path]['original'] = $original;
        }

        return $this;
    }

    private function resolveAction(array $operation, $overwrite)
    {
        $path = $operation['path'];

        if ($operation['mode'] === 'blocked') {
            return 'conflict';
        }

        if (is_dir($path) || ! $this->destinationIsWritable($path)) {
            return 'conflict';
        }

        if ($operation['mode'] === 'managed-create') {
            return file_exists($path) ? 'conflict' : 'create';
        }

        if (! file_exists($path)) {
            return 'create';
        }

        $existing = (string) file_get_contents($path);

        if ($operation['mode'] === 'managed') {
            if ($existing !== $operation['original']) {
                return 'conflict';
            }

            return $existing === $operation['content'] ? 'skip' : 'update';
        }

        if ($operation['mode'] === 'append') {
            return strpos($existing, trim($operation['content'])) !== false ? 'skip' : 'append';
        }

        if ($existing === $operation['content']) {
            return 'skip';
        }

        return $overwrite ? 'overwrite' : 'conflict';
    }

    private function destinationIsWritable($path)
    {
        if (is_file($path)) {
            return is_writable($path);
        }

        $directory = dirname($path);
        while (! file_exists($directory)) {
            $parent = dirname($directory);
            if ($parent === $directory) {
                return false;
            }
            $directory = $parent;
        }

        return is_dir($directory) && is_writable($directory);
    }

    private function assertSafePath($path)
    {
        if ($path !== $this->basePath && strpos($path, $this->basePath.'/') !== 0) {
            throw new RuntimeException("Generation path [{$path}] is outside the application root.");
        }
    }

    private function ensureDirectory($directory, array &$createdDirectories)
    {
        if (is_dir($directory)) {
            if (! is_writable($directory)) {
                throw new RuntimeException("Generation directory [{$directory}] is not writable.");
            }

            return;
        }

        $parent = dirname($directory);
        $this->ensureDirectory($parent, $createdDirectories);

        if (! mkdir($directory, 0755) && ! is_dir($directory)) {
            throw new RuntimeException("Unable to create generation directory [{$directory}].");
        }

        $createdDirectories[] = $directory;
    }

    private function rollback(array $backups, array $createdDirectories)
    {
        foreach (array_reverse($backups, true) as $path => $content) {
            if ($content === null) {
                if (file_exists($path)) {
                    @unlink($path);
                }
            } else {
                @file_put_contents($path, $content, LOCK_EX);
            }
        }

        foreach (array_reverse($createdDirectories) as $directory) {
            if (is_dir($directory)) {
                @rmdir($directory);
            }
        }
    }

    private function normalizePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $prefix = strpos($path, '/') === 0 ? '/' : '';
        $segments = [];

        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                array_pop($segments);
                continue;
            }

            $segments[] = $segment;
        }

        return $prefix.implode('/', $segments);
    }
}
