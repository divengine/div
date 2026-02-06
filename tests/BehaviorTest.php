<?php

use PHPUnit\Framework\TestCase;
use divengine\div;

function div_test_upperx($value): string
{
    return strtoupper((string) $value);
}

final class MethodData
{
    private array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function implodeValues(): string
    {
        return implode(',', $this->values);
    }
}

final class NameObj
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function upper(): string
    {
        return strtoupper($this->value);
    }
}

final class BehaviorTest extends TestCase
{
    public function testJsonStringInput(): void
    {
        $tpl = 'Hello {$name}';
        $data = '{"name":"JSON"}';
        $engine = new div($tpl, $data);

        $this->assertSame('Hello JSON', (string) $engine);
    }

    public function testIgnoreSpecificVarsArray(): void
    {
        $tpl = 'Name: {$name} Age: {$age}';
        $engine = new div($tpl, ['name' => 'Ana', 'age' => 30], ['name']);

        $this->assertSame('Name: {$name} Age: 30', (string) $engine);
    }

    public function testIgnoreSpecificVarsStringList(): void
    {
        $tpl = '{$name} {$age}';
        $engine = new div($tpl, ['name' => 'Ana', 'age' => 30], 'name,age');

        $this->assertSame('{$name} {$age}', (string) $engine);
    }

    public function testGlobalVars(): void
    {
        div::setGlobal('greeting', 'Hi');

        try {
            $out1 = (string) new div('{$greeting} {$name}', ['name' => 'A']);
            $out2 = (string) new div('{$greeting} {$name}', ['name' => 'B']);

            $this->assertSame('Hi A', $out1);
            $this->assertSame('Hi B', $out2);
        } finally {
            div::delGlobal('greeting');
        }
    }

    public function testDefaultReplacementStatic(): void
    {
        div::setDefault(true, 'YES');

        try {
            $out = (string) new div('Flag: {$flag}', ['flag' => true]);
            $this->assertSame('Flag: YES', $out);
        } finally {
            div::delDefault(true);
        }
    }

    public function testDefaultReplacementByVar(): void
    {
        div::setDefaultByVar('status', null, 'N/A');

        try {
            $out = (string) new div('Status: {$status}', ['status' => null]);
            $this->assertSame('Status: N/A', $out);
        } finally {
            div::delDefaultByVar('status', null);
        }
    }

    public function testCustomModifier(): void
    {
        div::addCustomModifier('upperx:', 'div_test_upperx');

        $out = (string) new div('{upperx:name}', ['name' => 'peter']);
        $this->assertSame('PETER', $out);
    }

    public function testMacroExecution(): void
    {
        $tpl = '<? $upper = strtoupper($name); ?>{$upper}';
        $out = (string) new div($tpl, ['name' => 'peter']);

        $this->assertSame('PETER', $out);
    }

    public function testMethodCallOnRootObject(): void
    {
        $tpl = '{= list: ->implodeValues() =}{$list}';
        $out = (string) new div($tpl, new MethodData(['A', 'B', 'C']));

        $this->assertSame('A,B,C', $out);
    }

    public function testMethodCallOnNestedObject(): void
    {
        $tpl = '{= up: ->name.upper() =}{$up}';
        $out = (string) new div($tpl, ['name' => new NameObj('peter')]);

        $this->assertSame('PETER', $out);
    }

    public function testSystemVarVersion(): void
    {
        $tpl = 'Version: {$div.version}';
        $out = (string) new div($tpl, []);

        $this->assertSame('Version: ' . div::getVersion(), $out);
    }
}
