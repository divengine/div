This feature is dedicated to designers. The designers can declare variables in the template and to use them for different reasons.

The **values** of variables can be:

- A string
- Another var
- An object or an array in JSON
- A call to method of the current PHP class
- The path of JSON file

**Important:**If the value is not valid JSON, it will be considered as a template and will be parsed before decoding. See the next sequence:

```
1. Value is not valid JSON: {= digits: [[:0,8:]{$value},[/]9] =}
2. Value was parsed:        {= digits: [0,1,2,3,4,5,6,7,8,9] =}
3. Now "digists" is an array.
4. Replacement:             {$digits} <!--{ OUTPUT "10" }-->
```

See the difference:

```
1. Value is valid JSON:     {= digits: "[[:0,8:]{$value},[/]9]" =}
2. Value was not parsed:    {= digits: "[[:0,8:]{$value},[/]9]" =}
3. Now "digists" is an string.
4. Replacement:             {$digits} <!--{ OUTPUT "[0,1,2,3,4,5,6,7,8,9]" }-->
```

Syntax:

```
{= varname: ... value ... =}
	
A string
	
{= varname: some string here =}
	
An array in JSON
	
{= varname: [item1, item2, .... ] =}
	
An object in JSON
	
{= varname: {
		prop1: value1,
		prop2: value2,
		...
} =}
	
Get the value of another var
	
{=  var1: value1 =}
{=  var2: $var1 =}
	
Call to method of current PHP class
	
{= sum: ->sum(20,30) =}
	
```

See also [OOP section.](https://divengine.org/documentation/div-php-template-engine/features/templates-variables#oop)

The dollar symbol used to get a variable's value in the JSON, is not the modifier in simple replacements (DIV_TAG_MODIFIER_SIMPLE). This symbol can not be changed with a custom dialect. Is a strict rule in Div.

Example:

index.php

```

echo new div('index.tpl', [
	'price' => 40
]);
```

index.tpl

```

{= price: 20 =} 
	
Price: {$price}
	
{= labels: ['A','B','C','D'] =}
	
Labels: [$labels] {$value}!$_is_last, $_is_last! [/$labels]
	
{= product: {
		name: "Potato",
		price: 45
	} =}
	
Product: {$product.name}
	
Product's price: {$product.price}
	
{= somestring: Blah blah blah =}
	
String: {$somestring}
	
{= somevar: $price =}
	
Some var: {$somevar}
```

Output:

```
Price: 40
	
Labels: A, B, C, D

Product's price: Potato
	
Price: 45
	
String: Blah blah blah
	
Some var: 40
```

## Protected template's vars

To protect the value of a teplate's variable, type an asterisk (*) before the variable's name:

```

{= *protectedvar: "protected value" =}

```

Protect a template's variable means that after this protection, any intent of changing its value will be failed.

## How to load the content of an external template into a variable?

If you have a external template and its content is needed into a variable, the next trick can help you:

```

{= varname: {% external-template %} =}

```

The "external content" are loaded "on demand". This means that the content will be loaded in first replacement of variable.
