<?php

use PHPUnit\Framework\TestCase;
use divengine\div;

class SimpleTest extends TestCase
{
    public function testSimpleReplacementWithObject()
    {
        $tpl = 'Hello, {$name}!';
        $data = new stdClass();
        $data->name = 'John Doe';
        $expected = 'Hello, John Doe!';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleReplacementWithArray()
    {
        $tpl = 'Hello, {$name}!';
        $data = ['name' => 'John Doe'];
        $expected = 'Hello, John Doe!';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleLoop()
    {
        $tpl = 'Hello, [$names]{$value}!$_is_last  &  $_is_last![/$names]!';
        $data = ['names' => ['John Doe', 'Jane Doe']];
        $expected = 'Hello, John Doe & Jane Doe!';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleLoopWithObject()
    {
        $tpl = 'Hello, [$names]{$value}!$_is_last  &  $_is_last![/$names]!';
        $data = new stdClass();
        $data->names = ['John Doe', 'Jane Doe'];
        $expected = 'Hello, John Doe & Jane Doe!';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleLoopWithObjectAndObject()
    {
        $tpl = 'Hello, [$names]{$name}!$_is_last  &  $_is_last![/$names]!';
        $data = new stdClass();
        $data->names = [
            (object) ['name' => 'John Doe'],
            (object) ['name' => 'Jane Doe']
        ];
        $expected = 'Hello, John Doe & Jane Doe!';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleLoopWithObjectAndArray()
    {
        $tpl = 'Hello, [$names]{$name}!$_is_last  &  $_is_last![/$names]!';
        $data = [
            'names' => [
                ['name' => 'John Doe'],
                ['name' => 'Jane Doe']
            ]
        ];
        $expected = 'Hello, John Doe & Jane Doe!';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleLoopWithArrayAndArray()
    {
        $tpl = 'Hello, [$names]{$name}!$_is_last  &  $_is_last![/$names]!';
        $data = [
            'names' => [
                ['name' => 'John Doe'],
                ['name' => 'Jane Doe']
            ]
        ];
        $expected = 'Hello, John Doe & Jane Doe!';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleLoopWithArrayAndObject()
    {
        $tpl = 'Hello, [$names]{$name}!$_is_last  &  $_is_last![/$names]!';
        $data = new stdClass();
        $data->names = [
            ['name' => 'John Doe'],
            ['name' => 'Jane Doe']
        ];
        $expected = 'Hello, John Doe & Jane Doe!';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleFormula()
    {
        $tpl = '(# 1 + 2 #)';
        $data = [];
        $expected = '3';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleFormulaWithObject()
    {
        $tpl = '(# 1 + 2 #)';
        $data = new stdClass();
        $expected = '3';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }

    public function testSimpleLoopOfFormula()
    {
        $tpl = 'Hello, [$names](# 1 + 2 #)!$_is_last  &  $_is_last![/$names]!';
        $data = ['names' => ['John Doe', 'Jane Doe']];
        $expected = 'Hello, 3 & 3!';
        $compiled = new div($tpl, $data);
        $compiled = "$compiled";
        $this->assertEquals($expected, $compiled);
    }
}
