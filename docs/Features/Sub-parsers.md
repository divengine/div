The sub-parsers are parsers that run before the main parser of div, like as [[Ignored parts (escaping Div parsing)]] . The custom sub-parsers are built by the programmer to perform _pre-processing_ part of the template. The PHP implementation can be a function or static method. If you have implemented [**a class that inherits from div**](https://divengine.org/documentation/div-php-template-engine/features/custom-sub-parsers#oop), and uses it to process your templates, then the sub-parser can be implemented in any method of that class.

For security reasons, the sub-parsers implemented as functions and static methods, must be registered prior to processing the template with **div::setSubParser()** method. Each sub-parser receive the template code to process and optionally the information provided to the engine. The string returned by the sub-parser will replace the content between pre-parse's tags.

Syntax in templates

```
{sub-parser-name}
	
	... this code will be sent to the sub-parser function/method ...
	
{/sub-parser-name}

	
```

Example

index.php

```
<?php 
	
include "div.php";
	
/* Sub-parser as a function */
function literal($code){
	return "{ignore}$code{/ignore}";
}
	
/* Sub-parser as a function more complex */
function body($code, &$items){
	$items['body'] = '<p>'.$code.'</p>';
	return "";
}
	
/* Sub-parser as a method */
class MyPage extends div{
		
	/* You can set the sub-parsers in the constructor */
	public function beforeBuild(){
		
		// Sub-parser with their name different to the name of function  
		self::setSubParser('combobox', 'buildCombobox');
	}
		
	/* A sub-parser ... */
	public function buildCombobox($properties){
			
		$prop = self::jsonDecode('{'.$properties.'}');
			
		$html = "<select name = \"{$prop->name}\">\n";
		foreach($prop->options as $option) {
			$html .= "<option value=\"{$option->v}\">{$option->c}</option>\n";
		}
		$html .= "</select>\n";
			
		return $html;
	}
		
	/* Other sub-parser */
	public function upperthis($text, &$items){
		$text = trim($text);
		if (self::issetVar($text, $items)) {
			$items[$text] = strtoupper($items[$text]);
		}
	}
}
	
/* Set sub-parsers before */
	
/* Same as MyPage::setSubParser("literal", "literal"); */
MyPage::setSubParser('literal');
	           
/* Alias for 'literal' */ 
MyPage::setSubParser('noparse','literal'); 
	
/* Name of sub-parser equal to name of function */
MyPage::setSubParser('body');              
	
/* Similar way ... */
div::setSubParser('upperthis');
	
echo new MyPage('index.tpl');

```

index.tpl

```
{body}
	Hello world, this is my first sub-parser
{/body}
	
{combobox}
    name: 'cboCities',
    options: [
        {v: 'NY', c: 'New York'}, 
        {v: 'PA', c: 'Paris'},
        {v: 'TK', c: 'Tokio'}
    ]
{/combobox}
	
{upperthis}body{/upperthis}
	
{$body}
	
{literal}
  {$body}
{/literal}

```

Output

```
<select name = "cboCities">
  <option value="NY">New York</option>
  <option value="PA">Paris</option>
  <option value="TK">Tokio</option>
</select>
	
<P>
	HELLO WORLD, THIS IS MY FIRST SUB-PARSER
</P>
	
{$body}

```

[[Pre-defined sub-parsers]]
[[Sub-parser's events]]

