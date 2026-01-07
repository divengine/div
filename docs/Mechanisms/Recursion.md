Div interprets the template until it is not anything to interpret. For that reason, the recursion is implicit and is an intrinsic characteristic of the eninge.

If you have a code like '**{$\{$list}}**' and \$list = 'products' then the engine convert this code to '**{$products}**' and if you have another variable $products with your list of products, the engine replace it, in this example, with the count of products.

Low performance? No! The implementation of Div is not a recursive algorithm. The recursion is only a mechanism for the designer. Don't worry.

The recursion is very useful in the creation of [components](https://divengine.org/documentation/div-php-template-engine/mechanisms/recursion#components). 

Example

index.php

```
<?php
	
include 'div.php';
	
$product = new stdClass();
	
$product->name = 'Banana';
$product->price = 20.5;
	
echo new div('index.tpl', [
		'product' => $product,
		'object' => 'product'
]);
```

index.tpl

Origin

```
[${$object}]
	
{$_key} = {$value}
	
[/${$object}]
```

Step 1

```
[$product]
	
{$_key} = {$value}
	
[/$product]
```

Step 2

```
[$product]
	
name = {$value}
	
[/$product]
```

Step 3

```
[$product]
	
name = Banana
price = {$value}
	
[/$product]
```

Step 4

```
name = Banana
price = 20.5
```
