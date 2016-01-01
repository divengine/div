<?php

/**
 * Div PHP Template Engine Example
 */

// Include the lib
include "div.php";

// Custom function
function t($word)
{
    $dict = array('ejemplo basico' => 'basic example');

    if (isset($dict[strtolower($word)]))
        return $dict[strtolower($word)];

    return $word;
}

div::setAllowedFunction("t");

// Custom parser/subparser
function a($href)
{
    return '<a href = "' . $href . '">' . $href . '</a>';
}

div::setSubParser("a");

// Output
echo new div("example.tpl", array(
    "title" => "Ejemplo basico",
    "year" => "2015",
    "links" => array(
        "Components" => "http://github.com/rrodriguezr/div-components",
        "Tools" => "http://github.com/rrodriguezr/div-tools",
        "Dialects" => "http://github.com/rrodriguezr/div-dialects",
        "3rd Party Integrations" => "http://github.com/rrodriguezr/div-3rd-party")));
