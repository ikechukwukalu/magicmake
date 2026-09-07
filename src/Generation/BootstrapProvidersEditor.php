<?php

namespace Ikechukwukalu\Magicmake\Generation;

class BootstrapProvidersEditor
{
    const PROVIDER = 'App\\Providers\\RepositoryServiceProvider';

    public function update($content)
    {
        $stream = PhpTokenStream::from($content);
        if ($stream === null) {
            return ManagedPhpFileResult::conflict($content);
        }
        $tokens = $stream->tokens();
        $returnIndexes = [];
        foreach ($tokens as $index => $token) {
            if ($token['id'] === T_RETURN) {
                $returnIndexes[] = $index;
            }
        }
        if (count($returnIndexes) !== 1) {
            return ManagedPhpFileResult::conflict($content);
        }

        $open = $stream->nextSignificant($returnIndexes[0]);
        if ($open === null || $tokens[$open]['text'] !== '[') {
            return ManagedPhpFileResult::conflict($content);
        }
        $close = $stream->matchingSymbol($open, '[', ']');
        if ($close === null) {
            return ManagedPhpFileResult::conflict($content);
        }
        $semicolon = $stream->nextSignificant($close);
        if ($semicolon === null || $tokens[$semicolon]['text'] !== ';') {
            return ManagedPhpFileResult::conflict($content);
        }

        $aliases = $this->aliases($stream, $returnIndexes[0]);
        $matches = 0;
        foreach ($this->directElements($stream, $open, $close) as $element) {
            $resolved = $this->directClassLiteral($stream, $element[0], $element[1], $aliases);
            if ($resolved === self::PROVIDER) {
                $matches++;
            } elseif ($resolved === 'RepositoryServiceProvider') {
                return ManagedPhpFileResult::conflict($content);
            }
        }
        if ($matches === 1) {
            return ManagedPhpFileResult::safe($content);
        }
        if ($matches > 1) {
            return ManagedPhpFileResult::conflict($content);
        }

        $closeOffset = $tokens[$close]['start'];
        $bodyStart = $tokens[$open]['end'];
        $body = substr($content, $bodyStart, $closeOffset - $bodyStart);
        $lastSignificant = $stream->previousSignificant($close);
        if ($lastSignificant !== null && $lastSignificant !== $open && $tokens[$lastSignificant]['text'] !== ',') {
            $relativeOffset = $tokens[$lastSignificant]['end'] - $bodyStart;
            $body = substr($body, 0, $relativeOffset).','.substr($body, $relativeOffset);
        }

        $prefix = $body === '' ? "\n" : (substr($body, -1) === "\n" ? '' : "\n");
        $updatedBody = $body.$prefix.'    '.self::PROVIDER."::class,\n";
        $updated = substr($content, 0, $bodyStart).$updatedBody.substr($content, $closeOffset);
        if (PhpTokenStream::from($updated) === null) {
            return ManagedPhpFileResult::conflict($content);
        }

        return ManagedPhpFileResult::safe($updated);
    }

    private function directElements(PhpTokenStream $stream, $open, $close)
    {
        $tokens = $stream->tokens();
        $elements = [];
        $start = $open + 1;
        $round = 0;
        $square = 0;
        $curly = 0;

        for ($i = $open + 1; $i < $close; $i++) {
            if ($tokens[$i]['text'] === '(') {
                $round++;
            } elseif ($tokens[$i]['text'] === ')') {
                $round--;
            } elseif ($tokens[$i]['text'] === '[') {
                $square++;
            } elseif ($tokens[$i]['text'] === ']') {
                $square--;
            } elseif ($tokens[$i]['text'] === '{') {
                $curly++;
            } elseif ($tokens[$i]['text'] === '}') {
                $curly--;
            } elseif ($tokens[$i]['text'] === ',' && $round === 0 && $square === 0 && $curly === 0) {
                if ($this->hasSignificantTokens($tokens, $start, $i - 1)) {
                    $elements[] = [$start, $i - 1];
                }
                $start = $i + 1;
            }
        }

        if ($this->hasSignificantTokens($tokens, $start, $close - 1)) {
            $elements[] = [$start, $close - 1];
        }

        return $elements;
    }

    private function directClassLiteral(PhpTokenStream $stream, $start, $end, array $aliases)
    {
        $tokens = $stream->tokens();
        $significant = [];
        for ($i = $start; $i <= $end; $i++) {
            if (! in_array($tokens[$i]['id'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                $significant[] = $i;
            }
        }
        if (count($significant) !== 3
            || $tokens[$significant[1]]['text'] !== '::'
            || strtolower($tokens[$significant[2]]['text']) !== 'class') {
            return null;
        }

        $name = trim($tokens[$significant[0]]['text'], '\\');
        if (strpos($name, '\\') === false && isset($aliases[$name])) {
            return $aliases[$name];
        }

        return $name;
    }

    private function hasSignificantTokens(array $tokens, $start, $end)
    {
        for ($i = $start; $i <= $end; $i++) {
            if (! in_array($tokens[$i]['id'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                return true;
            }
        }

        return false;
    }

    private function aliases(PhpTokenStream $stream, $beforeIndex)
    {
        $tokens = $stream->tokens();
        $aliases = [];
        for ($index = 0; $index < $beforeIndex; $index++) {
            if ($tokens[$index]['id'] !== T_USE) {
                continue;
            }
            $end = $index;
            while (isset($tokens[$end]) && $tokens[$end]['text'] !== ';') {
                $end++;
            }
            if (! isset($tokens[$end])) {
                return [];
            }
            $statement = trim($stream->textBetween($index + 1, $end - 1));
            $parts = preg_split('/\s+as\s+/i', $statement);
            $fqcn = trim($parts[0], " \\t\n\r\0\x0B\\");
            $segments = explode('\\', $fqcn);
            $alias = isset($parts[1]) ? trim($parts[1]) : end($segments);
            $aliases[$alias] = $fqcn;
            $index = $end;
        }

        return $aliases;
    }
}
