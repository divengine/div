The aggregate functions similar to SQL. With this feature you can get some results from list's operations, like as "sum", "average", etc. Think of "aggregate functions" as "list's modifiers", similarly to "variable's modifiers".

Syntax in templates

```html

{$function:variable-property}
```

|Aggregate function|Syntax for list of lists/objects|Syntax for array of atomic values|Description|
|---|---|---|---|
|min|{$min:list-property}|{$min:arrayname}|Minimum "property"|
|max|{$max:list-property}|{$max:arrayname}|Maximum "property"|
|sum|{$sum:list-property}|{$sum:arrayname}|Sum of "properties"|
|avg|{$avg:list-property}|{$avg:arrayname}|Average of "properties"|
|count|{$list-property}|{$arrayname}|Count of true "properties"|

Example

index.php

```php
<?php
	
echo new div('index.tpl', [
		'blocks' => [
			['title' => 'Who is online', 'weight' => 0, "show" => true],
			['title' => 'Last comments', 'weight' => 1, "show" => false],
			['title' => 'Forum topics', 'weight' => 2, "show" => true]
		],
		'widths' => [800, 700, 600, 500]
]);

```

index.tpl

```html
<!--{ array of array/object }-->
	
Minimum weight: {$min:blocks-weight}
Maximum weight: {$max:blocks-weight}
Weight average: {$avg:blocks-weight}
Weight sum: {$sum:blocks-weight}
Showed blocks: {$blocks-weight} or {$count:blocks-weight}
	
<!--{ array of atomic values }-->
	
Minimum weight: {$min:widths}
Maximum weight: {$max:widths}
Weight average: {$avg:widths}
Weight sum: {$sum:widths}
Showed blocks: {$widths}

	
```

Output

```html
Minimum weight: 0
Maximum weight: 2
Weight average: 1
Weight sum: 3
Showed blocks: 2 or 2
	
Minimum weight: 500
Maximum weight: 800
Weight average: 650
Weight sum: 2600
Showed blocks: 4

```
