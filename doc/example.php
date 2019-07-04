<?php

use Divengine\div;

/**
 * Div PHP Template Engine Example
 */
include '../src/div.php';

// Custom function
function t($word)
{
    $dict = ['ejemplo basico' => 'basic example'];

    if (isset($dict[strtolower($word)])) {
        return $dict[strtolower($word)];
    }

    return $word;
}

div::setAllowedFunction('t');

// Custom parser/sub-parser
function a($href)
{
    return '<a href = "'.$href.'">'.$href.'</a>';
}

div::setSubParser('a');

// Output
echo new div('example.tpl', [
    'title' => 'Ejemplo basico',
    'year'  => '2011',
    'links' => [
        'Portal' => 'https://divengine.com',
        'Core'   => 'https://github.com/divengine/div',
        'Repo'   => 'https://github.com/divengine/repo',
    ],
]);
