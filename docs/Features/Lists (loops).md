With a list you can repeat some part of template code and work with each item of the list.

**Syntax:**

```

[$listvar]
	   ... some code here ... 
@empty@
   ... some code when the list is empty ...
[/$listvar]
```

**Example:**

```

echo new div('index.tpl', array(
	'employees' => [
		'Rafa',
		'Peter',
		'John'	
	],
	'products' => []
]);
```

**index.tpl**

```
Employees:
	
[$employees]
	{$value}
[/$employees]
	
Products:
	
[$products]
	{$name}
@empty@
    Empty list of products!
[/$employees]
```

**Output:**

```
Employees:
	
Rafa
Peter
John
	
Products:
	
Empty list of products!
```

[[Dynamic vars inside a loop]]
