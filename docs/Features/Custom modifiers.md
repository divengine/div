The programmers can create new modifiers of variables. For this the programmers should use the static method "addCustomModifier." The modifier can be a function or a static method of class. The modifier function should have a single parameter.

**Example:**

index.php

```
<?php
	
include 'div.php';
	
class MyModifiers {
		
	/* The modifier function should have a single parameter. */
	static function upper($value){
		return strtoupper($value);
	}
		
}
	
function lower($value){
	return strtolower($value);
}
	
div::addCustomModifier('upper:', 'MyModifiers::upper');
div::addCustomModifier('lower:', 'lower');
	
echo new div('index.tpl', array('text' => 'Hello World'));
```

index.tpl

```
{upper:text}
	
{lower:text}
```

Output:

```
HELLO WORLD

hello world
```
