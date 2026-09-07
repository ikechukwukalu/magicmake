<?php

namespace Ikechukwukalu\Magicmake\Generation;

class RepositoryProviderEditor
{
    public function update($content, $contract, $repository)
    {
        $stream = PhpTokenStream::from($content);
        if ($stream === null) {
            return ManagedPhpFileResult::conflict($content);
        }

        $tokens = $stream->tokens();
        $namespace = $this->namespaceName($stream);
        $aliases = $this->aliases($stream);
        $classIndex = $this->classIndex($stream, 'RepositoryServiceProvider');
        if ($namespace !== 'App\\Providers' || $classIndex === null) {
            return ManagedPhpFileResult::conflict($content);
        }

        $classOpen = $this->nextSymbol($stream, $classIndex, '{');
        if ($classOpen === null || ! $this->extendsServiceProvider($stream, $classIndex, $classOpen, $namespace, $aliases)) {
            return ManagedPhpFileResult::conflict($content);
        }
        $classClose = $stream->matchingSymbol($classOpen, '{', '}');
        $method = $this->methodBody($stream, $classOpen, $classClose, 'register');
        if ($method === null) {
            return ManagedPhpFileResult::conflict($content);
        }

        $matchingBinding = false;
        foreach ($this->bindings($stream, $method[0], $method[1], $namespace, $aliases, $contract) as $binding) {
            if ($binding[0] !== ltrim($contract, '\\')) {
                continue;
            }
            $matchingBinding = true;
            if ($binding[1] !== ltrim($repository, '\\')) {
                return ManagedPhpFileResult::conflict($content);
            }
        }

        if ($matchingBinding) {
            return ManagedPhpFileResult::safe($content);
        }

        $close = $tokens[$method[1]]['start'];
        $bodyStart = $tokens[$method[0]]['end'];
        $body = substr($content, $bodyStart, $close - $bodyStart);
        $binding = '$this->app->bind(\\'.ltrim($contract, '\\').'::class, \\'.ltrim($repository, '\\').'::class);';
        $updatedBody = rtrim($body)."\n        {$binding}\n    ";

        $updated = substr($content, 0, $bodyStart).$updatedBody.substr($content, $close);

        return PhpTokenStream::from($updated) === null
            ? ManagedPhpFileResult::conflict($content)
            : ManagedPhpFileResult::safe($updated);
    }

    public function create($contract, $repository)
    {
        $contract = ltrim($contract, '\\');
        $repository = ltrim($repository, '\\');

        return "<?php\n\nnamespace App\\Providers;\n\nuse Illuminate\\Support\\ServiceProvider;\n\nclass RepositoryServiceProvider extends ServiceProvider\n{\n    public function register(): void\n    {\n        \$this->app->bind(\\{$contract}::class, \\{$repository}::class);\n    }\n}\n";
    }

    private function namespaceName(PhpTokenStream $stream)
    {
        $tokens = $stream->tokens();
        foreach ($tokens as $index => $token) {
            if ($token['id'] !== T_NAMESPACE) {
                continue;
            }
            $name = '';
            for ($i = $index + 1, $count = count($tokens); $i < $count && $tokens[$i]['text'] !== ';'; $i++) {
                if (! in_array($tokens[$i]['id'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                    $name .= $tokens[$i]['text'];
                }
            }
            return trim($name, '\\');
        }
        return null;
    }

    private function aliases(PhpTokenStream $stream)
    {
        $tokens = $stream->tokens();
        $aliases = [];
        $depth = 0;
        foreach ($tokens as $index => $token) {
            if ($token['text'] === '{') {
                $depth++;
            } elseif ($token['text'] === '}') {
                $depth--;
            } elseif ($depth === 0 && $token['id'] === T_USE) {
                $end = $index;
                while (isset($tokens[$end]) && $tokens[$end]['text'] !== ';') {
                    $end++;
                }
                if (! isset($tokens[$end])) {
                    continue;
                }
                $statement = trim($stream->textBetween($index + 1, $end - 1));
                foreach ($this->parseUseStatement($statement) as $alias => $fqcn) {
                    $aliases[$alias] = $fqcn;
                }
            }
        }
        return $aliases;
    }

    private function parseUseStatement($statement)
    {
        $statement = trim($statement);
        if (stripos($statement, 'function ') === 0 || stripos($statement, 'const ') === 0) {
            return [];
        }

        $imports = [];
        $open = strpos($statement, '{');
        $close = strrpos($statement, '}');
        if ($open !== false || $close !== false) {
            if ($open === false || $close === false || $close < $open) {
                return [];
            }
            $prefix = rtrim(trim(substr($statement, 0, $open)), '\\').'\\';
            $entries = explode(',', substr($statement, $open + 1, $close - $open - 1));
        } else {
            $prefix = '';
            $entries = explode(',', $statement);
        }

        foreach ($entries as $entry) {
            $parts = preg_split('/\s+as\s+/i', trim($entry));
            $fqcn = trim($prefix.$parts[0], " \\t\n\r\0\x0B\\");
            if ($fqcn === '') {
                continue;
            }
            $segments = explode('\\', $fqcn);
            $alias = isset($parts[1]) ? trim($parts[1]) : end($segments);
            $imports[$alias] = $fqcn;
        }

        return $imports;
    }

    private function classIndex(PhpTokenStream $stream, $name)
    {
        $tokens = $stream->tokens();
        foreach ($tokens as $index => $token) {
            if ($token['id'] !== T_CLASS) {
                continue;
            }
            $next = $stream->nextSignificant($index);
            if ($next !== null && $tokens[$next]['text'] === $name) {
                return $index;
            }
        }
        return null;
    }

    private function methodBody(PhpTokenStream $stream, $classOpen, $classClose, $name)
    {
        $tokens = $stream->tokens();
        for ($i = $classOpen + 1; $i < $classClose; $i++) {
            if ($tokens[$i]['id'] !== T_FUNCTION) {
                continue;
            }
            $methodName = $stream->nextSignificant($i);
            if ($methodName === null || $tokens[$methodName]['text'] !== $name) {
                continue;
            }
            $open = $this->nextSymbol($stream, $methodName, '{');
            if ($open === null || $open >= $classClose) {
                return null;
            }
            $close = $stream->matchingSymbol($open, '{', '}');
            return $close === null ? null : [$open, $close];
        }
        return null;
    }

    private function bindings(PhpTokenStream $stream, $open, $close, $namespace, array $aliases, $targetContract)
    {
        $tokens = $stream->tokens();
        $bindings = [];
        $bindingMethods = ['bind', 'bindif', 'singleton', 'singletonif', 'scoped', 'scopedif', 'instance'];
        for ($i = $open + 1; $i < $close; $i++) {
            if ($tokens[$i]['id'] !== T_STRING
                || ! in_array(strtolower($tokens[$i]['text']), $bindingMethods, true)
                || ! $this->isApplicationContainerCall($stream, $i)) {
                continue;
            }
            $paren = $stream->nextSignificant($i);
            if ($paren === null || $tokens[$paren]['text'] !== '(') {
                continue;
            }
            $parenClose = $stream->matchingSymbol($paren, '(', ')');
            if ($parenClose === null || $parenClose > $close) {
                return [];
            }
            $method = strtolower($tokens[$i]['text']);
            $arguments = $this->registrationArguments(
                $stream,
                $paren + 1,
                $parenClose - 1,
                $method,
                $namespace,
                $aliases,
                ltrim($targetContract, '\\')
            );
            if ($arguments !== null) {
                $bindings[] = $arguments;
            }
            $i = $parenClose;
        }
        return $bindings;
    }

    private function registrationArguments(PhpTokenStream $stream, $start, $end, $method, $namespace, array $aliases, $targetContract)
    {
        $parameterNames = $method === 'instance'
            ? ['abstract', 'instance']
            : (in_array($method, ['bind', 'bindif'], true)
                ? ['abstract', 'concrete', 'shared']
                : ['abstract', 'concrete']);
        $values = [];
        $argumentExpressions = [];
        $invalid = false;
        $position = 0;
        $seenNamed = false;

        foreach ($this->topLevelSegments($stream->tokens(), $start, $end) as $segment) {
            $wholeExpression = $stream->textBetween($segment[0], $segment[1]);
            $colon = $this->namedArgumentSeparator($stream->tokens(), $segment[0], $segment[1]);
            if ($colon === null) {
                $argumentExpressions[] = $wholeExpression;
                if ($seenNamed || ! isset($parameterNames[$position]) || isset($values[$parameterNames[$position]])) {
                    $invalid = true;
                    continue;
                }
                $values[$parameterNames[$position]] = $wholeExpression;
                $position++;
                continue;
            }

            $seenNamed = true;
            $name = trim($stream->textBetween($segment[0], $colon - 1));
            $valueExpression = $stream->textBetween($colon + 1, $segment[1]);
            $argumentExpressions[] = $valueExpression;
            if (! in_array($name, $parameterNames, true) || isset($values[$name])) {
                $invalid = true;
                continue;
            }
            $values[$name] = $valueExpression;
        }

        $abstractExpression = isset($values['abstract']) ? $values['abstract'] : '';
        $abstract = $this->classLiteral($abstractExpression, $namespace, $aliases);
        if ($abstract !== $targetContract) {
            foreach ($argumentExpressions as $argumentExpression) {
                if ($this->containsClassLiteral($argumentExpression, $targetContract, $namespace, $aliases)) {
                    return [$targetContract, null];
                }
            }
            return null;
        }
        if ($invalid) {
            return [$targetContract, null];
        }

        $mappingName = $method === 'instance' ? 'instance' : 'concrete';
        $mappingExpression = isset($values[$mappingName]) ? $values[$mappingName] : '';
        $mapping = $method === 'instance'
            ? $this->constructedClass($mappingExpression, $namespace, $aliases)
            : $this->classLiteral($mappingExpression, $namespace, $aliases);

        return [$targetContract, $mapping];
    }

    private function topLevelSegments(array $tokens, $start, $end)
    {
        if ($start > $end) {
            return [];
        }

        $segments = [];
        $segmentStart = $start;
        while ($segmentStart <= $end) {
            $comma = $this->topLevelComma($tokens, $segmentStart, $end);
            $segmentEnd = $comma === null ? $end : $comma - 1;
            if ($this->hasSignificantTokens($tokens, $segmentStart, $segmentEnd)) {
                $segments[] = [$segmentStart, $segmentEnd];
            }
            if ($comma === null) {
                break;
            }
            $segmentStart = $comma + 1;
        }

        return $segments;
    }

    private function topLevelSymbol(array $tokens, $start, $end, $symbol)
    {
        $depth = 0;
        for ($i = $start; $i <= $end; $i++) {
            if (in_array($tokens[$i]['text'], ['(', '[', '{'], true)) {
                $depth++;
            } elseif (in_array($tokens[$i]['text'], [')', ']', '}'], true)) {
                $depth--;
            } elseif ($tokens[$i]['text'] === $symbol && $depth === 0) {
                return $i;
            }
        }

        return null;
    }

    private function namedArgumentSeparator(array $tokens, $start, $end)
    {
        $colon = $this->topLevelSymbol($tokens, $start, $end, ':');
        if ($colon === null) {
            return null;
        }

        $significant = [];
        for ($i = $start; $i < $colon; $i++) {
            if (! in_array($tokens[$i]['id'], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                $significant[] = $i;
            }
        }

        return count($significant) === 1 && $tokens[$significant[0]]['id'] === T_STRING
            ? $colon
            : null;
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

    private function containsClassLiteral($expression, $target, $namespace, array $aliases)
    {
        $stream = PhpTokenStream::from('<?php '.$expression.';');
        if ($stream === null) {
            return false;
        }
        $tokens = $stream->tokens();
        foreach ($tokens as $index => $token) {
            if (! in_array($token['id'], [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)) {
                continue;
            }
            $doubleColon = $stream->nextSignificant($index);
            $class = $doubleColon === null ? null : $stream->nextSignificant($doubleColon);
            if ($doubleColon === null || $tokens[$doubleColon]['text'] !== '::'
                || $class === null || strtolower($tokens[$class]['text']) !== 'class') {
                continue;
            }
            if ($this->classLiteral($stream->textBetween($index, $class), $namespace, $aliases) === $target) {
                return true;
            }
        }

        return false;
    }

    private function isApplicationContainerCall(PhpTokenStream $stream, $methodIndex)
    {
        $tokens = $stream->tokens();
        $operatorBeforeMethod = $stream->previousSignificant($methodIndex);
        if ($operatorBeforeMethod === null || $tokens[$operatorBeforeMethod]['id'] !== T_OBJECT_OPERATOR) {
            return false;
        }

        $receiverEnd = $stream->previousSignificant($operatorBeforeMethod);
        if ($receiverEnd === null) {
            return false;
        }

        $operatorBeforeApp = $stream->previousSignificant($receiverEnd);
        $thisVariable = $operatorBeforeApp === null ? null : $stream->previousSignificant($operatorBeforeApp);
        if ($tokens[$receiverEnd]['id'] === T_STRING
            && strtolower($tokens[$receiverEnd]['text']) === 'app'
            && $operatorBeforeApp !== null
            && $tokens[$operatorBeforeApp]['id'] === T_OBJECT_OPERATOR
            && $thisVariable !== null
            && $tokens[$thisVariable]['id'] === T_VARIABLE
            && $tokens[$thisVariable]['text'] === '$this') {
            return true;
        }

        if ($tokens[$receiverEnd]['text'] !== ')') {
            return false;
        }
        $open = $stream->previousSignificant($receiverEnd);
        $helper = $open === null ? null : $stream->previousSignificant($open);

        return $open !== null
            && $tokens[$open]['text'] === '('
            && $stream->matchingSymbol($open, '(', ')') === $receiverEnd
            && $helper !== null
            && in_array($tokens[$helper]['id'], [T_STRING, T_NAME_FULLY_QUALIFIED], true)
            && strtolower(ltrim($tokens[$helper]['text'], '\\')) === 'app';
    }

    private function classLiteral($expression, $namespace, array $aliases)
    {
        $expression = trim($expression);
        if (substr(strtolower($expression), -7) !== '::class') {
            return null;
        }
        $name = trim(substr($expression, 0, -7));
        if ($name === '') {
            return null;
        }
        if ($name[0] === '\\') {
            return ltrim($name, '\\');
        }
        $first = explode('\\', $name)[0];
        if (isset($aliases[$first])) {
            return $aliases[$first].substr($name, strlen($first));
        }
        return strpos($name, '\\') === false ? $namespace.'\\'.$name : $namespace.'\\'.$name;
    }

    private function constructedClass($expression, $namespace, array $aliases)
    {
        $stream = PhpTokenStream::from('<?php '.$expression.';');
        if ($stream === null) {
            return null;
        }

        $tokens = $stream->tokens();
        $openTagIndex = null;
        foreach ($tokens as $index => $token) {
            if ($token['id'] === T_OPEN_TAG) {
                $openTagIndex = $index;
                break;
            }
        }
        $newIndex = $openTagIndex === null ? null : $stream->nextSignificant($openTagIndex);
        if ($newIndex === null || $tokens[$newIndex]['id'] !== T_NEW) {
            return null;
        }

        $classIndex = $stream->nextSignificant($newIndex);
        if ($classIndex === null || ! in_array($tokens[$classIndex]['id'], [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true)) {
            return null;
        }

        $expressionEnd = $stream->nextSignificant($classIndex);
        if ($expressionEnd === null) {
            return null;
        }
        if ($tokens[$expressionEnd]['text'] === '(') {
            $close = $stream->matchingSymbol($expressionEnd, '(', ')');
            $expressionEnd = $close === null ? null : $stream->nextSignificant($close);
        }
        if ($expressionEnd === null || $tokens[$expressionEnd]['text'] !== ';') {
            return null;
        }

        $name = $tokens[$classIndex]['text'];
        if ($name[0] === '\\') {
            return ltrim($name, '\\');
        }
        $first = explode('\\', $name)[0];
        if (isset($aliases[$first])) {
            return $aliases[$first].substr($name, strlen($first));
        }

        return $namespace.'\\'.$name;
    }

    private function topLevelComma(array $tokens, $start, $end)
    {
        $depth = 0;
        for ($i = $start; $i <= $end; $i++) {
            if (in_array($tokens[$i]['text'], ['(', '[', '{'], true)) {
                $depth++;
            } elseif (in_array($tokens[$i]['text'], [')', ']', '}'], true)) {
                $depth--;
            } elseif ($tokens[$i]['text'] === ',' && $depth === 0) {
                return $i;
            }
        }
        return null;
    }

    private function extendsServiceProvider(PhpTokenStream $stream, $classIndex, $classOpen, $namespace, array $aliases)
    {
        $tokens = $stream->tokens();
        for ($i = $classIndex + 1; $i < $classOpen; $i++) {
            if ($tokens[$i]['id'] !== T_EXTENDS) {
                continue;
            }
            $nameIndex = $stream->nextSignificant($i);
            if ($nameIndex === null || $nameIndex >= $classOpen) {
                return false;
            }
            $name = $tokens[$nameIndex]['text'];
            if ($name[0] === '\\') {
                $resolved = ltrim($name, '\\');
            } elseif (isset($aliases[$name])) {
                $resolved = $aliases[$name];
            } else {
                $resolved = $namespace.'\\'.$name;
            }

            return $resolved === 'Illuminate\\Support\\ServiceProvider';
        }

        return false;
    }

    private function nextSymbol(PhpTokenStream $stream, $index, $symbol)
    {
        $tokens = $stream->tokens();
        for ($i = $index + 1, $count = count($tokens); $i < $count; $i++) {
            if ($tokens[$i]['text'] === $symbol) {
                return $i;
            }
        }
        return null;
    }
}
