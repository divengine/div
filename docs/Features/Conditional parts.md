One of the most commonly used functions in the GUI is to show or hide part of the interface. This is achieved with Div in various ways, and one of them is the use of the conditional parts.

Some conditional on the template is defined by a block that begins and ends with the character question tag (?) or exclamation (!) otherwise, accompanied by a variable that must be a boolean value. For example, you can do this in the template:

```
?$showproducts
	... some template here ...
$showproducts?
```

If the value of "showproducts" is true, it shows the code between both tags. If the value is false or if you not pass the variable "showproducts", that part of code will be hidden.

In general, the boolean value is defined by the method **div::mixedBool**, which takes into account the following criteria:

1. False if the value is false
2. False if the value is null
3. False if the value is not greater than zero
4. False if is "0"
5. False if is an empty string
6. False if is an object without properties
7. The same value in any other case

Syntax:

```

For test the var as TRUE:
	
?$var
	... some code here ...
@else@
	... some another code here
$var?
	
For test the var as FALSE:
	
!$var
	... some code here ...
@else@
   ... some another code here
$var!

```

Example:

index.php

```

echo new div('index.tpl', [
		'products' => [
			['name' => 'Banana', 'price' => 20.5],
			['name' => 'Potato', 'price' => 10.8]
		]
]);
```

index.tpl

```

Products:
	
?$products
	[$products]
		{$name} - {$price}
	[/$products]
@else@
	No products
$products?
	
Similar result:

!$products
	No products
@else@
	[$products]
		{$name} - {$price}
	[/$products]
$products!

```

Output

```
Products:
	
Banana - 20.5
Potato - 10.8
	
Similar result:
	
Banana - 20.5
Potato - 10.8
```

