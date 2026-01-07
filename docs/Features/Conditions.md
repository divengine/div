The **conditions** is more complicated than [conditional parts](https://divengine.org/documentation/div-php-template-engine/features/conditions#conditional-parts). With conditions, you can show conditional parts based on a boolean expression and not only from a boolean value. See the [allowed PHP functions](https://divengine.org/documentation/div-php-template-engine/features/conditions#allowed-php-functions). The expression can be an expression of PHP, blended with code of Div.

Syntax: 

```
{?( ... expression ... )?}
	
	... some code here ...
	
@else@
	
	... some another code here ...
	
{/?}
```

Example

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

{?( {$products} > 0 )?}
		There are {$products} products in the warehouse
@else@
		There are not products in the warehouse
{/?}
```

Output

```

There are 2 products in the warehouse

```

You must understand that the expression in parentheses will be interpreted by the template engine as well. That means the end result of that interpretation must be a valid PHP boolean expression. An example to understand this is when comparing to strings.

```
{?( "{$userRole}" === "guest" )?}
        {% loginPage %}
@else@
        {% dashboard %}
{/?}
```

Notice carefully how the $userRole variable substitution is enclosed in quotes.

```
"{$userRole}" === "guest"
```

This means that when it is substituted for its value, the value will be enclosed in quotes, because you have told the template engine so.

```
"guest" === "guest"
```

The following would be an error, because the content of the variable would not be enclosed in quotes and would not result in a valid expression for PHP.

```
{$userRole} === "guest"
```

Output:

```
guest === "guest"
```

