A macro is a restricted PHP code dedicated to execute the design's complex tasks. For this reason, Div assures that the PHP code will be not intrusive or insecure.

Syntax

```
<?

// ... some PHP code here ...
	
?>
```

Example

index.php

```
<?php
	
include 'div.php';
	
class page extends div{
		
	public function upper($str){
		return strtoupper($str);
	}
}
	
echo new page('index.tpl', [
	'products' => ['Banana', 'Potato']
]);
```

index.tpl

```

{= text: "hello" =}
	
{$text}
	
<? 
		// upper() is a method of current class
		$text = upper($text); 
?>
	
{$text}
	
Products:
	
<? 
	
	$i = 0; 
	
	foreach($products as $product) {
		$i++;
		echo "$i - $product\n";
	}
	
?>
	
They are {$i} products 
	
```

Output

```
hello
	
HELLO
	
Products:
	
1 - Banana
2 - Potato
	
They are 2 products
```

## Restrictions

- It is not allowed to use **$this** or **self**
- It is only allowed to use [some functions of PHP](https://divengine.org/documentation/div-php-template-engine/features/macros#allowed-php-functions) by default. You can enable the use of another function through the method [setAllowedFunction](https://divengine.org/documentation/div-php-template-engine/features/macros#div-methods).
- It is not allowed to create functions neither classes.
- It is not allowed to include other script.

## Features

- Create new template's variables
- Change the value of any template's variable
- ECHO any content (take care of not provoking an infinite loop)
- Use the allowed methods of div as a functions:
    - asThis
    - atLeastOneString
    - getCountOfParagraphs
    - getCountOfSentences
    - getCountOfWords
    - getLastKeyOfArray
    - getRanges
    - htmlToText
    - isArrayOfArray
    - isArrayOfObjects
    - isCli
    - isNumericList
    - isString
    - jsonDecode
    - jsonEncode
    - mixedBool
- Use the methods of current class without restrictions if it is a [class that extends div](https://divengine.org/documentation/div-php-template-engine/features/macros#oop)