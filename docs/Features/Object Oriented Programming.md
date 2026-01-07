In this section you can learn how the programmer can create classes that inherits from the **div** class.

The constructor should respect the parent's constructor. Change the default constructor is not recommended. IMPORTANT: The recommended way for do something before build is the implementation of [**beforeBuild()** hook](https://divengine.org/docs/div-php-template-engine/features/object-oriented-programming#hooks).

```
<?php
	
include 'div.php';

use divengine\div;
	
class MyPage extends div{
		
	/* IMPORTANT: Change the default constructor is not recommended */
	public function __construct($src = null, $items = [], $ignored = []){
			
		/*.. some code here */
			
		parent::__construct($src, $items, $ignored);
	}
		
	/* IMPORTANT: The recommended way for do something before              */ 
	/* construct the object is the implementation of beforeBuild() hook.   */	
	
	public function beforeBuild(&$src = null, &$items = null, &$ignore =[]){
			
		// something to do before build the object
			
	}
		
	/* ... custom methods here... */
		
}

```

Example:

index.php

```
<?php
	
class MyPage extends div{
		
	public function getProducts(){
		return [
			[
				'name' => 'Banana',
				'price' => 20	
			],
			[
				'name' => 'Potato',
				'price' => 30
			]
		];
	}
		
	public function sum($x, $y){
		return $x + $y;
	}
		
}
```

index.tpl

```

{= products: ->getProducts() =}
{= result: ->sum(20,30) =}
	
[$products] 
		{$name} 
[/$products]
	
{$result}

```

Output

```

Banana
Potato
	
50

```

The arrow symbol used to get a method's result in the template example, can not be changed with a custom dialect. Is a strict rule in Div.

[[The __toString magic method]]
[[Content like an object (intelligent data)]]

