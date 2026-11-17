<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use divengine\div;

/**
 * div::isValidCurrentDialect(): the begin and the end of a block must be different.
 *
 * A dialect is made of constants, defined once per process: every case runs in a
 * process of its own, with its tags defined before div.php is loaded.
 */
final class DialectValidationTest extends TestCase
{
    private static function dialect(array $tags): void
    {
        foreach ($tags as $name => $value) {
            define($name, $value);
        }
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testTheDefaultDialectIsValid(): void
    {
        $this->assertTrue(div::isValidCurrentDialect());
    }

    public static function blocksWithTheSameBeginAndEnd(): array
    {
        return [
            'ignore' => [['DIV_TAG_IGNORE_BEGIN' => '{i}', 'DIV_TAG_IGNORE_END' => '{i}'],
                'DIV_TAG_IGNORE_END must not be equal to DIV_TAG_IGNORE_BEGIN'],
            'comment' => [['DIV_TAG_COMMENT_BEGIN' => '##', 'DIV_TAG_COMMENT_END' => '##'],
                'DIV_TAG_COMMENT_END must not be equal to DIV_TAG_COMMENT_BEGIN'],
            'txt' => [['DIV_TAG_TXT_BEGIN' => '{t}', 'DIV_TAG_TXT_END' => '{t}'],
                'DIV_TAG_TXT_END must not be equal to DIV_TAG_TXT_BEGIN'],
            'strip' => [['DIV_TAG_STRIP_BEGIN' => '{s}', 'DIV_TAG_STRIP_END' => '{s}'],
                'DIV_TAG_STRIP_END must not be equal to DIV_TAG_STRIP_BEGIN'],
            'formula' => [['DIV_TAG_FORMULA_BEGIN' => '##', 'DIV_TAG_FORMULA_END' => '##'],
                'DIV_TAG_FORMULA_END must not be equal to DIV_TAG_FORMULA_BEGIN'],
            'iteration' => [['DIV_TAG_ITERATION_BEGIN_PREFIX' => '[:', 'DIV_TAG_ITERATION_END' => '[:'],
                'DIV_TAG_ITERATION_END must not be equal to DIV_TAG_ITERATION_BEGIN_PREFIX'],
            'conditions' => [['DIV_TAG_CONDITIONS_BEGIN_PREFIX' => '{?', 'DIV_TAG_CONDITIONS_END' => '{?'],
                'DIV_TAG_CONDITIONS_END must not be equal to DIV_TAG_CONDITIONS_BEGIN_PREFIX'],
            'template variables' => [['DIV_TAG_TPLVAR_BEGIN' => '=#', 'DIV_TAG_TPLVAR_END' => '=#'],
                'DIV_TAG_TPLVAR_END must not be equal to DIV_TAG_TPLVAR_BEGIN'],
            'default replacement' => [['DIV_TAG_DEFAULT_REPLACEMENT_BEGIN' => '@@', 'DIV_TAG_DEFAULT_REPLACEMENT_END' => '@@'],
                'DIV_TAG_DEFAULT_REPLACEMENT_END must not be equal to DIV_TAG_DEFAULT_REPLACEMENT_BEGIN'],
            'include' => [['DIV_TAG_INCLUDE_BEGIN' => '%%', 'DIV_TAG_INCLUDE_END' => '%%'],
                'DIV_TAG_INCLUDE_END must not be equal to DIV_TAG_INCLUDE_BEGIN'],
            'preprocessed' => [['DIV_TAG_PREPROCESSED_BEGIN' => '%%%', 'DIV_TAG_PREPROCESSED_END' => '%%%'],
                'DIV_TAG_PREPROCESSED_END must not be equal to DIV_TAG_PREPROCESSED_BEGIN'],
            'friendly' => [['DIV_TAG_FRIENDLY_BEGIN' => '<!--|', 'DIV_TAG_FRIENDLY_END' => '<!--|'],
                'DIV_TAG_FRIENDLY_END must not be equal to DIV_TAG_FRIENDLY_BEGIN'],
            'location' => [['DIV_TAG_LOCATION_BEGIN' => '((', 'DIV_TAG_LOCATION_END' => '(('],
                'DIV_TAG_LOCATION_END must not be equal to DIV_TAG_LOCATION_BEGIN'],
            'macro' => [['DIV_TAG_MACRO_BEGIN' => '<?', 'DIV_TAG_MACRO_END' => '<?'],
                'DIV_TAG_MACRO_END must not be equal to DIV_TAG_MACRO_BEGIN'],
            'loop' => [self::keyed('DIV_TAG_LOOP', '<% for $', ' %>'), self::keyedMessage('DIV_TAG_LOOP')],
            'subparser' => [self::keyed('DIV_TAG_SUBPARSER', '{', '}'), self::keyedMessage('DIV_TAG_SUBPARSER')],
            'conditional true' => [self::keyed('DIV_TAG_CONDITIONAL_TRUE', '?$', '?'), self::keyedMessage('DIV_TAG_CONDITIONAL_TRUE')],
            'conditional false' => [self::keyed('DIV_TAG_CONDITIONAL_FALSE', '!$', '!'), self::keyedMessage('DIV_TAG_CONDITIONAL_FALSE')],
            'capsule' => [self::keyed('DIV_TAG_CAPSULE', '[[', ']]'), self::keyedMessage('DIV_TAG_CAPSULE')],
            'multi replacement' => [self::keyed('DIV_TAG_MULTI_REPLACEMENT', '{:', '}'), self::keyedMessage('DIV_TAG_MULTI_REPLACEMENT')],
            'location content' => [self::keyed('DIV_TAG_LOCATION_CONTENT', '{{', '}}'), self::keyedMessage('DIV_TAG_LOCATION_CONTENT')],
        ];
    }

    private static function keyed(string $block, string $prefix, string $suffix): array
    {
        return [
            $block . '_BEGIN_PREFIX' => $prefix, $block . '_BEGIN_SUFFIX' => $suffix,
            $block . '_END_PREFIX' => $prefix, $block . '_END_SUFFIX' => $suffix,
        ];
    }

    private static function keyedMessage(string $block): string
    {
        return $block . '_END_PREFIX and ' . $block . '_END_SUFFIX must not be equal to '
            . $block . '_BEGIN_PREFIX and ' . $block . '_BEGIN_SUFFIX';
    }

    #[DataProvider('blocksWithTheSameBeginAndEnd')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testABlockWithTheSameBeginAndEndIsInvalid(array $tags, string $message): void
    {
        self::dialect($tags);
        $this->assertSame($message, div::isValidCurrentDialect());
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testTheSameLoopPrefixWithAnotherSuffixIsValid(): void
    {
        self::dialect([
            'DIV_TAG_LOOP_BEGIN_PREFIX' => '<% for $', 'DIV_TAG_LOOP_BEGIN_SUFFIX' => ' %>',
            'DIV_TAG_LOOP_END_PREFIX' => '<% for $', 'DIV_TAG_LOOP_END_SUFFIX' => ' end %>',
        ]);
        $this->assertTrue(div::isValidCurrentDialect());
        $tpl = new div('A<% for $xs %>{$value}@empty@none<% for $xs end %>B', ['xs' => [1, 2]]);
        $this->assertSame('A12B', (string) $tpl);
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testOverlappingButDifferentLoopTagsAreValid(): void
    {
        self::dialect([
            'DIV_TAG_LOOP_BEGIN_PREFIX' => '<% $', 'DIV_TAG_LOOP_BEGIN_SUFFIX' => ' %>',
            'DIV_TAG_LOOP_END_PREFIX' => '<% $/', 'DIV_TAG_LOOP_END_SUFFIX' => ' %>',
        ]);
        $this->assertTrue(div::isValidCurrentDialect());
        $this->assertSame('A12B', (string) new div('A<% $xs %>{$value}<% $/xs %>B', ['xs' => [1, 2]]));
    }
}
