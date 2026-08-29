<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$docsRoot = realpath($root.'/docs');
$builtSiteArgument = $argv[1] ?? null;
$required = [
    'README.md',
    'CHANGELOG.md',
    'UPGRADE.md',
    'RELEASE_CHECKLIST.md',
    'docs/_config.yml',
    'docs/_layouts/default.html',
    'docs/index.md',
    'docs/compatibility.md',
    'docs/usage.md',
    'docs/upgrade.md',
    'docs/release-readiness.md',
];

$errors = [];

if ($docsRoot === false) {
    fwrite(STDERR, "Missing documentation source directory: docs\n");
    exit(1);
}

$isWithin = static function (string $path, string $directory): bool {
    $resolvedPath = realpath($path);
    $resolvedDirectory = realpath($directory);

    return $resolvedPath !== false
        && $resolvedDirectory !== false
        && ($resolvedPath === $resolvedDirectory
            || str_starts_with($resolvedPath, $resolvedDirectory.DIRECTORY_SEPARATOR));
};

foreach ($required as $relativePath) {
    $path = $root.'/'.$relativePath;
    if (! is_file($path) || filesize($path) === 0) {
        $errors[] = "Missing or empty documentation file: {$relativePath}";
    }
}

foreach (glob($root.'/docs/*.md') ?: [] as $document) {
    $contents = file_get_contents($document);
    if ($contents === false) {
        $errors[] = 'Unable to read '.substr($document, strlen($root) + 1);
        continue;
    }

    preg_match_all('/\[[^\]]+\]\((?!https?:|mailto:|#)([^)#]+)(?:#[^)]+)?\)/', $contents, $matches);
    foreach ($matches[1] as $target) {
        if (str_contains($target, '{{') || str_contains($target, '{%')) {
            continue;
        }

        $resolved = dirname($document).'/'.rawurldecode($target);
        if (! file_exists($resolved) || ! $isWithin($resolved, $docsRoot)) {
            $errors[] = sprintf(
                'Broken or source-escaping local link in %s: %s',
                substr($document, strlen($root) + 1),
                $target
            );
        }
    }

    preg_match_all('/\{\{\s*[\'\"]([^\'\"]+)[\'\"]\s*\|\s*relative_url\s*\}\}/', $contents, $liquidMatches);
    foreach ($liquidMatches[1] as $target) {
        $sourceTarget = ltrim($target, '/');
        $sourceTarget = preg_replace('/\.html$/', '.md', $sourceTarget) ?? $sourceTarget;
        $resolved = $docsRoot.'/'.$sourceTarget;
        if (! is_file($resolved) || ! $isWithin($resolved, $docsRoot)) {
            $errors[] = sprintf(
                'Broken Pages source link in %s: %s',
                substr($document, strlen($root) + 1),
                $target
            );
        }
    }
}

if ($builtSiteArgument !== null) {
    $builtSite = realpath($root.'/'.$builtSiteArgument);
    if ($builtSite === false || ! is_dir($builtSite)) {
        $errors[] = "Missing built Pages site: {$builtSiteArgument}";
    } else {
        foreach (['index.html', 'compatibility.html', 'usage.html', 'upgrade.html', 'release-readiness.html'] as $page) {
            if (! is_file($builtSite.'/'.$page)) {
                $errors[] = "Missing built Pages output: {$page}";
            }
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($builtSite));
        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'html') {
                continue;
            }

            $contents = file_get_contents($file->getPathname());
            if ($contents === false) {
                $errors[] = 'Unable to read built page '.$file->getPathname();
                continue;
            }

            preg_match_all('/\bhref=([\'\"])(.*?)\1/i', $contents, $hrefMatches);
            foreach ($hrefMatches[2] as $href) {
                if ($href === '' || $href[0] === '#' || str_starts_with($href, '//')
                    || preg_match('/^[a-z][a-z0-9+.-]*:/i', $href) === 1) {
                    continue;
                }

                $path = rawurldecode((string) parse_url($href, PHP_URL_PATH));
                if ($path === '') {
                    continue;
                }

                if ($path === '/magicmake') {
                    $path = '/';
                } elseif (str_starts_with($path, '/magicmake/')) {
                    $path = substr($path, strlen('/magicmake'));
                } elseif (str_starts_with($path, '/')) {
                    $errors[] = "Built link escapes configured base URL in {$file->getFilename()}: {$href}";
                    continue;
                }

                $candidate = str_starts_with($path, '/')
                    ? $builtSite.'/'.ltrim($path, '/')
                    : $file->getPath().'/'.$path;
                if (str_ends_with($candidate, '/')) {
                    $candidate .= 'index.html';
                }

                if (! file_exists($candidate) || ! $isWithin($candidate, $builtSite)) {
                    $errors[] = "Broken built-site link in {$file->getFilename()}: {$href}";
                }
            }
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors).PHP_EOL);
    exit(1);
}

printf(
    "Documentation readiness verified (%d required files%s).\n",
    count($required),
    $builtSiteArgument === null ? '' : ', built Pages links checked'
);
