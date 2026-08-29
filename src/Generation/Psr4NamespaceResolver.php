<?php

namespace Ikechukwukalu\Magicmake\Generation;

use InvalidArgumentException;

class Psr4NamespaceResolver
{
    /** @var string */
    private $basePath;

    public function __construct($basePath)
    {
        $this->basePath = rtrim(str_replace('\\', '/', $basePath), '/');
    }

    public function resolve($targetPath = null, $namespace = null)
    {
        $targetPath = $this->normalizeTargetPath($targetPath);
        $namespace = $this->normalizeNamespace($namespace);

        if ($targetPath === null && $namespace === null) {
            return new GenerationTarget(null, null);
        }

        $mappings = $this->mappings();

        if ($targetPath !== null) {
            $derivedNamespace = $this->normalizeNamespace($this->namespaceForPath($targetPath, $mappings));
            if ($namespace !== null && $namespace !== $derivedNamespace) {
                throw new InvalidArgumentException("Target path [{$targetPath}] resolves to [{$derivedNamespace}], not [{$namespace}].");
            }

            return new GenerationTarget($targetPath, $namespace ?: $derivedNamespace);
        }

        return new GenerationTarget($this->pathForNamespace($namespace, $mappings), $namespace);
    }

    private function mappings()
    {
        $composerPath = $this->basePath.'/composer.json';
        if (! is_file($composerPath)) {
            throw new InvalidArgumentException('A project composer.json is required for modular namespace resolution.');
        }

        $composer = json_decode((string) file_get_contents($composerPath), true);
        if (! is_array($composer)) {
            throw new InvalidArgumentException('The project composer.json is not valid JSON.');
        }

        $mappings = [];
        foreach (['autoload', 'autoload-dev'] as $section) {
            foreach (($composer[$section]['psr-4'] ?? []) as $prefix => $paths) {
                foreach ((array) $paths as $path) {
                    $mappings[] = [
                        'namespace' => rtrim($prefix, '\\'),
                        'path' => trim(str_replace('\\', '/', $path), '/'),
                    ];
                }
            }
        }

        if ($mappings === []) {
            throw new InvalidArgumentException('No PSR-4 mappings are configured in the project composer.json.');
        }

        usort($mappings, function ($left, $right) {
            return strlen($right['path']) <=> strlen($left['path']);
        });

        return $mappings;
    }

    private function namespaceForPath($targetPath, array $mappings)
    {
        foreach ($mappings as $mapping) {
            if ($targetPath === $mapping['path'] || strpos($targetPath, $mapping['path'].'/') === 0) {
                $relative = trim(substr($targetPath, strlen($mapping['path'])), '/');

                return $mapping['namespace'].($relative === '' ? '' : '\\'.str_replace('/', '\\', $relative));
            }
        }

        throw new InvalidArgumentException("Target path [{$targetPath}] is not covered by a Composer PSR-4 mapping.");
    }

    private function pathForNamespace($namespace, array $mappings)
    {
        usort($mappings, function ($left, $right) {
            return strlen($right['namespace']) <=> strlen($left['namespace']);
        });

        $matches = [];
        foreach ($mappings as $mapping) {
            if ($namespace === $mapping['namespace'] || strpos($namespace, $mapping['namespace'].'\\') === 0) {
                $relative = trim(substr($namespace, strlen($mapping['namespace'])), '\\');
                $matches[] = $mapping['path'].($relative === '' ? '' : '/'.str_replace('\\', '/', $relative));
            }
        }

        $matches = array_values(array_unique($matches));
        if (count($matches) > 1) {
            throw new InvalidArgumentException("Namespace [{$namespace}] maps to multiple PSR-4 paths; provide --path explicitly.");
        }

        if ($matches !== []) {
            return $matches[0];
        }

        throw new InvalidArgumentException("Namespace [{$namespace}] is not covered by a Composer PSR-4 mapping.");
    }

    private function normalizeTargetPath($targetPath)
    {
        if ($targetPath === null || trim((string) $targetPath) === '') {
            return null;
        }

        $rawPath = str_replace('\\', '/', trim((string) $targetPath));
        if (strpos($rawPath, '/') === 0 || preg_match('/^[A-Za-z]:\//', $rawPath)) {
            throw new InvalidArgumentException('Target paths must be project-relative.');
        }

        $targetPath = trim($rawPath, '/');
        if ($targetPath === '' || preg_match('#(^|/)\.\.?(?:/|$)#', $targetPath)) {
            throw new InvalidArgumentException('Target paths must be safe project-relative paths without dot segments.');
        }

        return $targetPath;
    }

    private function normalizeNamespace($namespace)
    {
        if ($namespace === null || trim((string) $namespace, " \\t\n\r\0\x0B\\") === '') {
            return null;
        }

        $namespace = trim((string) $namespace, " \\t\n\r\0\x0B\\");
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*(?:\\\\[A-Za-z_][A-Za-z0-9_]*)*$/', $namespace) !== 1) {
            throw new InvalidArgumentException("Namespace [{$namespace}] is not a valid PHP namespace.");
        }

        return $namespace;
    }
}
