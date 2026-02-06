<?php

use PHPUnit\Framework\TestCase;
use divengine\div;

final class StaticMethodsTest extends TestCase
{
    public function testGetLastKeyOfArray(): void
    {
        $this->assertSame('b', div::getLastKeyOfArray(['a' => 1, 'b' => 2]));
        $this->assertNull(div::getLastKeyOfArray([]));
    }

    public function testCountHelpers(): void
    {
        $this->assertSame(3, div::getCountOfWords('one two three'));
        $this->assertSame(2, div::getCountOfSentences('Hello. World.'));
        $this->assertSame(3, div::getCountOfParagraphs("a\nb\nc"));
    }

    public function testArrayHelpers(): void
    {
        $this->assertTrue(div::isArrayOfArray([[1], [2]]));
        $this->assertFalse(div::isArrayOfArray([1, 2]));

        $this->assertTrue(div::isArrayOfObjects([(object) ['a' => 1], (object) ['b' => 2]]));
        $this->assertFalse(div::isArrayOfObjects([(object) ['a' => 1], ['b' => 2]]));

        $this->assertTrue(div::isNumericList([1, '2', 3.5]));
        $this->assertFalse(div::isNumericList([1, 'x']));
    }

    public function testMixedBool(): void
    {
        $this->assertFalse(div::mixedBool(false));
        $this->assertFalse(div::mixedBool(null));
        $this->assertFalse(div::mixedBool(0));
        $this->assertFalse(div::mixedBool('0'));
        $this->assertFalse(div::mixedBool('false'));
        $this->assertFalse(div::mixedBool(-1));

        $this->assertTrue(div::mixedBool(true));
        $this->assertTrue(div::mixedBool(1));
        $this->assertTrue(div::mixedBool('true'));
        $this->assertTrue(div::mixedBool('yes'));
    }

    public function testAtLeastOneString(): void
    {
        $this->assertTrue(div::atLeastOneString('abc', ['x', 'b']));
        $this->assertFalse(div::atLeastOneString('abc', ['x', 'y']));
    }

    public function testJsonEncodeDecodeRoundTrip(): void
    {
        $json = div::jsonEncode(['a' => 1, 'b' => 'x']);
        $decoded = div::jsonDecode($json);

        $this->assertEquals((object) ['a' => 1, 'b' => 'x'], $decoded);

        $listJson = div::jsonEncode([1, 2, 3]);
        $listDecoded = div::jsonDecode($listJson);

        $this->assertSame([1.0, 2.0, 3.0], $listDecoded);
    }

    public function testGetVarsFromCodeAndHaveVarsThisCode(): void
    {
        $code = '$a = $b + $c;';

        $this->assertSame(['a', 'b', 'c'], div::getVarsFromCode($code));
        $this->assertTrue(div::haveVarsThisCode($code, ['a']));
        $this->assertFalse(div::haveVarsThisCode($code, ['a', 'b', 'c']));
    }

    public function testIsValidExpression(): void
    {
        $this->assertTrue(div::isValidExpression('1 + 2'));
        $this->assertFalse(div::isValidExpression('echo 1'));
    }

    public function testIsCli(): void
    {
        $this->assertTrue(div::isCli());
    }
}
