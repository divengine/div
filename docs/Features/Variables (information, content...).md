The designer doesn't have reason to know what are a data type to make their work. It should simply consider to the variables of the design as "the information". For this reason, in the templates, Div normalizes all the information coming from PHP, considering an array, objects or combination of arrays and objects as "same things".

The information represents a hierarchy. You can access to all thier "pieces" using the dot "." operator.

Example:

**index.php**

```php
<?php

/* This code... */

echo new div('index.tpl', [
	'single' => 'something',
	'complex' => [
		'single' => 45,
		'subcomplex' => [
			'single' => '60'
		]
	]
]);

/* ... is similar to ... */

$complex = (object) [
	'single' => 45,
	'subcomplex' => [ 'single' => 60 ]

];

echo new div('index.tpl', [
	'single' => 'something',
	'complex' => $complex
]);

```

**index.tpl**

```php
Single value: {$single}
Single value into complex var: {$complex.single}
And more: {$complex.subcomplex.single}
```

**Output**

```php
Single value: something
Single value into complex var: 45
And more: 60
```

[[String's dissection]]
