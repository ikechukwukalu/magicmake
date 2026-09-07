<?php

namespace Ikechukwukalu\Magicmake\Generation;

use ParseError;

class PhpTokenStream
{
    /** @var array<int, array{id: int|null, text: string, start: int, end: int}> */
    private $tokens = [];

    public static function from($content)
    {
        try {
            $rawTokens = token_get_all((string) $content, TOKEN_PARSE);
        } catch (ParseError $exception) {
            return null;
        }

        $stream = new self();
        $offset = 0;
        foreach ($rawTokens as $token) {
            $text = is_array($token) ? $token[1] : $token;
            $stream->tokens[] = [
                'id' => is_array($token) ? $token[0] : null,
                'text' => $text,
                'start' => $offset,
                'end' => $offset + strlen($text),
            ];
            $offset += strlen($text);
        }

        return $stream;
    }

    public function tokens()
    {
        return $this->tokens;
    }

    public function nextSignificant($index)
    {
        for ($i = $index + 1, $count = count($this->tokens); $i < $count; $i++) {
            if (! $this->isTrivia($this->tokens[$i])) {
                return $i;
            }
        }

        return null;
    }

    public function previousSignificant($index)
    {
        for ($i = $index - 1; $i >= 0; $i--) {
            if (! $this->isTrivia($this->tokens[$i])) {
                return $i;
            }
        }

        return null;
    }

    public function matchingSymbol($index, $open, $close)
    {
        $depth = 0;
        for ($i = $index, $count = count($this->tokens); $i < $count; $i++) {
            if ($this->tokens[$i]['text'] === $open) {
                $depth++;
            } elseif ($this->tokens[$i]['text'] === $close) {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            }
        }

        return null;
    }

    public function textBetween($startIndex, $endIndex)
    {
        $text = '';
        for ($i = $startIndex; $i <= $endIndex; $i++) {
            $text .= $this->tokens[$i]['text'];
        }

        return $text;
    }

    private function isTrivia(array $token)
    {
        return in_array($token['id'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true);
    }
}
