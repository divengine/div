An capsule is a part of template for reduce their code, make the template more readable, among other advantages.

Syntax in templates

```

[[varname
	... In this section you can use the properties of variable if it is
	an object or their keys if it is an array ...
varname]]

```

Example

index.php

```
<?php
	
echo new div('index.tpl', array(
	'product' => [
		'name' => 'Banana',
		'price' => 20.5,
		'tax' => 1.5
	]
]);

```

index.tpl

```
Product:
	
[[product
		Name: {$name}
		Price: {$price}
		Tax: {$tax}
product]]
	
Similar:
	
		Name: {$product.name}
		Price: {$product.price}
		Tax: {$product.tax}
```

Output

```
Product:
	
		Name: Banana
		Price: 20.5
		Tax: 1.5
	
Similar:
	
		Name: Banana
		Price: 20.5
		Tax: 1.5

```
