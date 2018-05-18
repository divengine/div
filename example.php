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
	"links" => array("Core" => "http://github.com/rafageist/div", "Extras" => "http://github.com/rafageist/div-extras")));
