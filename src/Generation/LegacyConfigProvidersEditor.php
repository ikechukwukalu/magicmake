<?php

namespace Ikechukwukalu\Magicmake\Generation;

class LegacyConfigProvidersEditor
{
    const PROVIDER = 'App\\Providers\\RepositoryServiceProvider';
    const SERVICE_PROVIDER = 'Illuminate\\Support\\ServiceProvider';

    public function update($content)
    {
        $stream = PhpTokenStream::from($content);
        if ($stream === null) {
            return ManagedPhpFileResult::conflict($content);
        }
        $tokens = $stream->tokens();
        $returns = [];
        foreach ($tokens as $index => $token) {
            if ($token['id'] === T_RETURN) {
                $returns[] = $index;
            }
        }
        if (count($returns) !== 1) {
            return ManagedPhpFileResult::conflict($content);
        }

        $configOpen = $stream->nextSignificant($returns[0]);
        if ($configOpen === null || $tokens[$configOpen]['text'] !== '[') {
            return ManagedPhpFileResult::conflict($content);
        }
        $configClose = $stream->matchingSymbol($configOpen, '[', ']');
        if ($configClose === null) {
            return ManagedPhpFileResult::conflict($content);
        }

        $providerValues = [];
        foreach ($this->directElements($stream, $configOpen, $configClose) as $element) {
            $arrow = $this->topLevelArrow($stream, $element[0], $element[1]);
            if ($arrow === null || $this->stringKey($stream, $element[0], $arrow - 1) !== 'providers') {
                continue;
            }
            $providerValues[] = [$arrow + 1, $element[1]];
        }
        if (count($providerValues) !== 1) {
            return ManagedPhpFileResult::conflict($content);
        }

        $aliases = $this->aliases($stream, $returns[0]);
        $mergeArray = $this->canonicalMergeArray($stream, $providerValues[0][0], $providerValues[0][1], $aliases);
        if ($mergeArray === null) {
            return ManagedPhpFileResult::conflict($content);
        }

        [$open, $close] = $mergeArray;
        $matches = 0;
        foreach ($this->directElements($stream, $open, $close) as $element) {
            $resolved = $this->directClassLiteral($stream, $element[0], $element[1], $aliases);
            if ($resolved !== null && $this->sameClass($resolved, self::PROVIDER)) {
                $matches++;
                continue;
            }
            if ($resolved === null
                || $this->hasTargetBasename($resolved)
                || $this->containsTargetClassLiteral($stream, $element[0], $element[1], $aliases)) {
                return ManagedPhpFileResult::conflict($content);
            }
        }
        if ($matches === 1) {
            return ManagedPhpFileResult::safe($content);
        }
        if ($matches > 1) {
            return ManagedPhpFileResult::conflict($content);
        }

        $updated = $this->insertBeforeClose($content, $stream, $open, $close);

        return PhpTokenStream::from($updated) === null
            ? ManagedPhpFileResult::conflict($content)
            : ManagedPhpFileResult::safe($updated);
    }

    private function canonicalMergeArray(PhpTokenStream $stream, $start, $end, array $aliases)
    {
        $indexes = $this->significant($stream, $start, $end);
        if (count($indexes) < 13) {
            return null;
        }
        $tokens = $stream->tokens();
        $texts = array_map(function ($index) use ($tokens) {
            return $tokens[$index]['text'];
        }, $indexes);
        if (! $this->sameClass($this->resolveName($texts[0], $aliases), self::SERVICE_PROVIDER)
            || $texts[1] !== '::'
            || strtolower($texts[2]) !== 'defaultproviders'
            || $texts[3] !== '('
            || $texts[4] !== ')'
            || $texts[5] !== '->'
            || strtolower($texts[6]) !== 'merge'
            || $texts[7] !== '('
            || $texts[8] !== '[') {
            return null;
        }

        $open = $indexes[8];
        $close = $stream->matchingSymbol($open, '[', ']');
        if ($close === null) {
            return null;
        }
        $closePosition = array_search($close, $indexes, true);
        if ($closePosition === false) {
            return null;
        }
        $suffix = array_slice($texts, $closePosition + 1);
        if ($suffix !== [')', '->', 'toArray', '(', ')']) {
            return null;
        }

        return [$open, $close];
    }

    private function insertBeforeClose($content, PhpTokenStream $stream, $open, $close)
    {
        $tokens = $stream->tokens();
        $eol = strpos($content, "\r\n") !== false ? "\r\n" : "\n";
        $bodyStart = $tokens[$open]['end'];
        $closeOffset = $tokens[$close]['start'];
        $body = substr($content, $bodyStart, $closeOffset - $bodyStart);
        $indent = $this->providerIndent($content, $stream, $open, $close);
        $last = $stream->previousSignificant($close);
        if ($last !== null && $last !== $open && $tokens[$last]['text'] !== ',') {
            $relative = $tokens[$last]['end'] - $bodyStart;
            $body = substr($body, 0, $relative).','.substr($body, $relative);
        }

        preg_match('/([ \t]*)$/', $body, $closingMatch);
        $closingIndent = $closingMatch[1];
        $body = substr($body, 0, strlen($body) - strlen($closingIndent));
        if ($body === '' || substr($body, -strlen($eol)) !== $eol) {
            $body .= $eol;
        }
        $updatedBody = $body.$indent.self::PROVIDER.'::class,'.$eol.$closingIndent;

        return substr($content, 0, $bodyStart).$updatedBody.substr($content, $closeOffset);
    }

    private function providerIndent($content, PhpTokenStream $stream, $open, $close)
    {
        $tokens = $stream->tokens();
        foreach ($this->directElements($stream, $open, $close) as $element) {
            $indexes = $this->significant($stream, $element[0], $element[1]);
            if ($indexes === []) {
                continue;
            }
            $offset = $tokens[$indexes[0]]['start'];
            $lineStart = strrpos(substr($content, 0, $offset), "\n");
            $indent = substr($content, $lineStart === false ? 0 : $lineStart + 1, $offset - ($lineStart === false ? 0 : $lineStart + 1));
            if ($indent !== '' && preg_match('/^[ \t]+$/', $indent)) {
                return $indent;
            }
        }

        $closeOffset = $tokens[$close]['start'];
        $lineStart = strrpos(substr($content, 0, $closeOffset), "\n");
        $closingIndent = substr($content, $lineStart === false ? 0 : $lineStart + 1, $closeOffset - ($lineStart === false ? 0 : $lineStart + 1));

        return preg_match('/^[ \t]*$/', $closingIndent) ? $closingIndent.'    ' : '        ';
    }

    private function containsTargetClassLiteral(PhpTokenStream $stream, $start, $end, array $aliases)
    {
        $indexes = $this->significant($stream, $start, $end);
        $tokens = $stream->tokens();
        for ($i = 0, $count = count($indexes) - 2; $i < $count; $i++) {
            if ($tokens[$indexes[$i + 1]]['text'] !== '::'
                || strtolower($tokens[$indexes[$i + 2]]['text']) !== 'class') {
                continue;
            }
            $resolved = $this->resolveName($tokens[$indexes[$i]]['text'], $aliases);
            if ($this->sameClass($resolved, self::PROVIDER) || $this->hasTargetBasename($resolved)) {
                return true;
            }
        }

        return false;
    }

    private function directClassLiteral(PhpTokenStream $stream, $start, $end, array $aliases)
    {
        $indexes = $this->significant($stream, $start, $end);
        if (count($indexes) !== 3) {
            return null;
        }
        $tokens = $stream->tokens();
        if ($tokens[$indexes[1]]['text'] !== '::' || strtolower($tokens[$indexes[2]]['text']) !== 'class') {
            return null;
        }

        return $this->resolveName($tokens[$indexes[0]]['text'], $aliases);
    }

    private function stringKey(PhpTokenStream $stream, $start, $end)
    {
        $indexes = $this->significant($stream, $start, $end);
        if (count($indexes) !== 1) {
            return null;
        }
        $token = $stream->tokens()[$indexes[0]];
        if ($token['id'] !== T_CONSTANT_ENCAPSED_STRING) {
            return null;
        }

        return stripcslashes(substr($token['text'], 1, -1));
    }

    private function topLevelArrow(PhpTokenStream $stream, $start, $end)
    {
        $tokens = $stream->tokens();
        $round = $square = $curly = 0;
        for ($i = $start; $i <= $end; $i++) {
            $text = $tokens[$i]['text'];
            if ($text === '(') $round++;
            elseif ($text === ')') $round--;
            elseif ($text === '[') $square++;
            elseif ($text === ']') $square--;
            elseif ($text === '{') $curly++;
            elseif ($text === '}') $curly--;
            elseif ($text === '=>' && $round === 0 && $square === 0 && $curly === 0) return $i;
        }

        return null;
    }

    private function directElements(PhpTokenStream $stream, $open, $close)
    {
        $tokens = $stream->tokens();
        $elements = [];
        $start = $open + 1;
        $round = $square = $curly = 0;
        for ($i = $open + 1; $i < $close; $i++) {
            $text = $tokens[$i]['text'];
            if ($text === '(') $round++;
            elseif ($text === ')') $round--;
            elseif ($text === '[') $square++;
            elseif ($text === ']') $square--;
            elseif ($text === '{') $curly++;
            elseif ($text === '}') $curly--;
            elseif ($text === ',' && $round === 0 && $square === 0 && $curly === 0) {
                if ($this->significant($stream, $start, $i - 1) !== []) $elements[] = [$start, $i - 1];
                $start = $i + 1;
            }
        }
        if ($this->significant($stream, $start, $close - 1) !== []) $elements[] = [$start, $close - 1];

        return $elements;
    }

    private function significant(PhpTokenStream $stream, $start, $end)
    {
        $tokens = $stream->tokens();
        $indexes = [];
        for ($i = $start; $i <= $end; $i++) {
            if (isset($tokens[$i]) && ! in_array($tokens[$i]['id'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                $indexes[] = $i;
            }
        }

        return $indexes;
    }

    private function resolveName($name, array $aliases)
    {
        $name = trim((string) $name, '\\');
        $segments = explode('\\', $name);
        $alias = strtolower($segments[0]);
        if (isset($aliases[$alias])) {
            array_shift($segments);
            return $aliases[$alias].($segments === [] ? '' : '\\'.implode('\\', $segments));
        }

        return $name;
    }

    private function sameClass($left, $right)
    {
        return strcasecmp($left, $right) === 0;
    }

    private function hasTargetBasename($name)
    {
        $segments = explode('\\', trim((string) $name, '\\'));

        return strcasecmp((string) end($segments), 'RepositoryServiceProvider') === 0;
    }

    private function aliases(PhpTokenStream $stream, $before)
    {
        $tokens = $stream->tokens();
        $aliases = [];
        for ($index = 0; $index < $before; $index++) {
            if ($tokens[$index]['id'] !== T_USE) continue;
            $end = $index;
            while (isset($tokens[$end]) && $tokens[$end]['text'] !== ';') $end++;
            if (! isset($tokens[$end])) return [];
            $statement = trim($stream->textBetween($index + 1, $end - 1));
            if (strpos($statement, '{') !== false || strpos($statement, ',') !== false) continue;
            $parts = preg_split('/\s+as\s+/i', $statement);
            $fqcn = trim($parts[0], " \t\n\r\0\x0B\\");
            $segments = explode('\\', $fqcn);
            $alias = isset($parts[1]) ? trim($parts[1]) : end($segments);
            $aliases[strtolower($alias)] = $fqcn;
            $index = $end;
        }

        return $aliases;
    }
}
