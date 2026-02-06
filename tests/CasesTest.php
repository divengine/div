<?php

use PHPUnit\Framework\TestCase;
use divengine\div;

final class CasesTest extends TestCase
{
    public static function caseProvider(): array
    {
        $base = __DIR__ . DIRECTORY_SEPARATOR . 'cases';
        if (!is_dir($base)) {
            return [];
        }

        $cases = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getFilename() !== 'case.tpl') {
                continue;
            }

            $dir = $file->getPath();
            $json = $dir . DIRECTORY_SEPARATOR . 'case.json';
            $out = $dir . DIRECTORY_SEPARATOR . 'case.out';

            if (!is_file($json) || !is_file($out)) {
                continue;
            }

            $name = str_replace($base . DIRECTORY_SEPARATOR, '', $dir);
            $cases[$name] = [$name, $file->getPathname(), $json, $out];
        }

        ksort($cases);

        return array_values($cases);
    }

    /**
     * @dataProvider caseProvider
     */
    public function testCaseOutputs(string $name, string $tplPath, string $jsonPath, string $outPath): void
    {
        $engine = new div($tplPath, $jsonPath);
        $actual = (string) $engine;
        $expected = file_get_contents($outPath);

        $this->assertSame(
            self::normalize($expected),
            self::normalize($actual),
            "Case failed: {$name}"
        );
    }

    protected function tearDown(): void
    {
        // Prevent template defaults from leaking across cases.
        div::delDefault(true);
        div::delDefault(false);
    }

    private static function normalize(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        return rtrim($text, "\n");
    }
}
