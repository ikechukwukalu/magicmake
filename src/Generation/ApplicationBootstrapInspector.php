<?php

namespace Ikechukwukalu\Magicmake\Generation;

class ApplicationBootstrapInspector
{
    const MODERN = 'modern';
    const LEGACY = 'legacy';

    const APPLICATION = 'Illuminate\\Foundation\\Application';

    public function mode($content)
    {
        $stream = PhpTokenStream::from($content);
        if ($stream === null) {
            return null;
        }

        $tokens = $stream->tokens();
        $aliases = $this->aliases($stream);
        $legacyVariables = [];
        $topLevelReturns = [];
        $guardedReturns = [];
        $guarded = $this->guardedTokens($stream);

        foreach ($tokens as $index => $token) {
            if ($token['id'] === T_RETURN) {
                if ($guarded[$index]) {
                    $value = $stream->nextSignificant($index);
                    if ($value !== null && $tokens[$value]['id'] === T_VARIABLE) {
                        $guardedReturns[] = $tokens[$value]['text'];
                    } elseif ($this->isCanonicalModernReturn($stream, $index, $aliases)) {
                        return null;
                    }
                } else {
                    $topLevelReturns[] = $index;
                }
            }
            if ($token['id'] !== T_NEW) {
                if ($guarded[$index] && $this->isConfigureCall($stream, $index, $aliases)) {
                    return null;
                }
                continue;
            }

            $nameIndex = $stream->nextSignificant($index);
            if ($nameIndex === null || ! $this->sameClass($this->resolveName($tokens[$nameIndex]['text'], $aliases), self::APPLICATION)) {
                continue;
            }
            if ($guarded[$index]) {
                return null;
            }
            $equals = $stream->previousSignificant($index);
            $variable = $equals === null ? null : $stream->previousSignificant($equals);
            if ($equals !== null && $variable !== null
                && $tokens[$equals]['text'] === '='
                && $tokens[$variable]['id'] === T_VARIABLE) {
                $legacyVariables[] = $tokens[$variable]['text'];
            }
        }

        $legacyVariables = array_values(array_unique($legacyVariables));
        if (array_intersect($legacyVariables, $guardedReturns) !== []) {
            return null;
        }
        if (count($legacyVariables) === 1 && count($topLevelReturns) === 1) {
            $value = $stream->nextSignificant($topLevelReturns[0]);
            $end = $value === null ? null : $stream->nextSignificant($value);
            if ($value !== null && $end !== null
                && $tokens[$value]['id'] === T_VARIABLE
                && $tokens[$value]['text'] === $legacyVariables[0]
                && $tokens[$end]['text'] === ';') {
                return self::LEGACY;
            }
        }

        if ($legacyVariables !== [] || count($topLevelReturns) !== 1) {
            return null;
        }

        return $this->isCanonicalModernReturn($stream, $topLevelReturns[0], $aliases)
            && $this->configureCallCount($stream, $aliases) === 1
            ? self::MODERN
            : null;
    }

    private function guardedTokens(PhpTokenStream $stream)
    {
        $tokens = $stream->tokens();
        $guarded = [];
        $curly = 0;
        $alternative = 0;
        $alternativeEnds = [T_ENDIF, T_ENDFOR, T_ENDFOREACH, T_ENDWHILE, T_ENDSWITCH];

        foreach ($tokens as $index => $token) {
            if (in_array($token['id'], $alternativeEnds, true)) {
                $alternative = max(0, $alternative - 1);
            }
            if ($token['text'] === '}') {
                $curly = max(0, $curly - 1);
            }

            $guarded[$index] = $curly > 0
                || $alternative > 0
                || $this->immediatelyGuarded($stream, $index);

            if ($token['text'] === '{') {
                $curly++;
            } elseif ($token['text'] === ':' && $this->startsAlternativeControl($stream, $index)) {
                $alternative++;
            }
        }

        return $guarded;
    }

    private function startsAlternativeControl(PhpTokenStream $stream, $colon)
    {
        $tokens = $stream->tokens();
        $close = $stream->previousSignificant($colon);
        if ($close === null || $tokens[$close]['text'] !== ')') {
            return false;
        }
        $open = $this->previousMatchingSymbol($tokens, $close, '(', ')');
        $control = $open === null ? null : $stream->previousSignificant($open);

        return $control !== null && in_array(
            $tokens[$control]['id'],
            [T_IF, T_FOR, T_FOREACH, T_WHILE, T_SWITCH],
            true
        );
    }

    private function immediatelyGuarded(PhpTokenStream $stream, $index)
    {
        $tokens = $stream->tokens();
        $start = $index;
        if ($tokens[$index]['id'] === T_NEW) {
            $equals = $stream->previousSignificant($index);
            $variable = $equals === null ? null : $stream->previousSignificant($equals);
            if ($equals !== null && $variable !== null
                && $tokens[$equals]['text'] === '='
                && $tokens[$variable]['id'] === T_VARIABLE) {
                $start = $variable;
            }
        }

        $previous = $stream->previousSignificant($start);
        if ($previous === null) {
            return false;
        }
        if ($tokens[$previous]['text'] === ':' || in_array($tokens[$previous]['id'], [T_ELSE, T_DO], true)) {
            return true;
        }
        if ($tokens[$previous]['text'] !== ')') {
            return false;
        }

        $open = $this->previousMatchingSymbol($tokens, $previous, '(', ')');
        $control = $open === null ? null : $stream->previousSignificant($open);

        return $control !== null && in_array(
            $tokens[$control]['id'],
            [T_IF, T_ELSEIF, T_FOR, T_FOREACH, T_WHILE, T_SWITCH],
            true
        );
    }

    private function previousMatchingSymbol(array $tokens, $index, $open, $close)
    {
        $depth = 0;
        for ($i = $index; $i >= 0; $i--) {
            if ($tokens[$i]['text'] === $close) {
                $depth++;
            } elseif ($tokens[$i]['text'] === $open) {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            }
        }

        return null;
    }

    private function isConfigureCall(PhpTokenStream $stream, $index, array $aliases)
    {
        $tokens = $stream->tokens();
        if (! $this->sameClass($this->resolveName($tokens[$index]['text'], $aliases), self::APPLICATION)) {
            return false;
        }
        $operator = $stream->nextSignificant($index);
        $method = $operator === null ? null : $stream->nextSignificant($operator);

        return $operator !== null && $method !== null
            && $tokens[$operator]['text'] === '::'
            && strtolower($tokens[$method]['text']) === 'configure';
    }

    private function isCanonicalModernReturn(PhpTokenStream $stream, $return, array $aliases)
    {
        $tokens = $stream->tokens();
        $indexes = [];
        $round = $square = $curly = 0;
        for ($index = $return + 1, $count = count($tokens); $index < $count; $index++) {
            $text = $tokens[$index]['text'];
            if ($text === ';' && $round === 0 && $square === 0 && $curly === 0) {
                break;
            }
            if (! in_array($tokens[$index]['id'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                $indexes[] = $index;
            }
            if ($text === '(') $round++;
            elseif ($text === ')') $round--;
            elseif ($text === '[') $square++;
            elseif ($text === ']') $square--;
            elseif ($text === '{') $curly++;
            elseif ($text === '}') $curly--;
        }

        if (count($indexes) < 8
            || ! $this->sameClass($this->resolveName($tokens[$indexes[0]]['text'], $aliases), self::APPLICATION)
            || $tokens[$indexes[1]]['text'] !== '::'
            || strtolower($tokens[$indexes[2]]['text']) !== 'configure'
            || $tokens[$indexes[3]]['text'] !== '(') {
            return false;
        }

        $position = $this->positionAfterMatchingParenthesis($stream, $indexes, 3);
        $lastMethod = null;
        while ($position !== null && $position < count($indexes)) {
            if (! isset($indexes[$position + 2])
                || $tokens[$indexes[$position]]['text'] !== '->'
                || $tokens[$indexes[$position + 2]]['text'] !== '(') {
                return false;
            }
            $lastMethod = strtolower($tokens[$indexes[$position + 1]]['text']);
            $position = $this->positionAfterMatchingParenthesis($stream, $indexes, $position + 2);
        }

        return $position === count($indexes) && $lastMethod === 'create';
    }

    private function positionAfterMatchingParenthesis(PhpTokenStream $stream, array $indexes, $openPosition)
    {
        $close = $stream->matchingSymbol($indexes[$openPosition], '(', ')');
        if ($close === null) {
            return null;
        }
        $position = array_search($close, $indexes, true);

        return $position === false ? null : $position + 1;
    }

    private function configureCallCount(PhpTokenStream $stream, array $aliases)
    {
        $tokens = $stream->tokens();
        $calls = 0;
        foreach ($tokens as $index => $token) {
            if (! $this->sameClass($this->resolveName($token['text'], $aliases), self::APPLICATION)) {
                continue;
            }
            $operator = $stream->nextSignificant($index);
            $method = $operator === null ? null : $stream->nextSignificant($operator);
            if ($operator !== null && $method !== null
                && $tokens[$operator]['text'] === '::'
                && strtolower($tokens[$method]['text']) === 'configure') {
                $calls++;
            }
        }

        return $calls;
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

    private function aliases(PhpTokenStream $stream)
    {
        $tokens = $stream->tokens();
        $aliases = [];
        foreach ($tokens as $index => $token) {
            if ($token['id'] !== T_USE) {
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
            if (strpos($statement, '{') !== false || strpos($statement, ',') !== false) {
                continue;
            }
            $parts = preg_split('/\s+as\s+/i', $statement);
            $fqcn = trim($parts[0], " \t\n\r\0\x0B\\");
            $segments = explode('\\', $fqcn);
            $alias = isset($parts[1]) ? trim($parts[1]) : end($segments);
            $aliases[strtolower($alias)] = $fqcn;
        }

        return $aliases;
    }
}
