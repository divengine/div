Replace X with Y.

Syntax in templates

```

{:varname}
		... some code here ...	
{:/varname}

```

The variable should be an array where each item of array is an array with 3 element: **string to search**, **string to replace** and **use or not regular expressions**.

Example

index.php

```
<?php
	
echo new div('index.tpl', [

		/* str_replace */
		'customtags' => [
			['[b]', '<b>'],
			['[/b]', '</b>']
		],

		/* preg_replace */
		'highlight' => [
			['/\*.*\*/', '<span class = "comment">$0</span>', true]
		]
]);
```

index.tpl

```
{= htmlfix: [
	['<b>','<strong>']
	['</b>','</strong>']
] =}
	
{:customtags}
{:htmlfix}
	
[b]Hello World[/b]
	
{:/htmlfix}
{:/customtags}
	
{:highlight}
	
/* this is a PHP comment */
	
{:/highlight}

```

Output

```

<strong>Hello World</strong>
	
<span class = "comment">/* this is a PHP comment */</span>

```