Similar to [[Including another templates]], but in this case, first the engine parse the template, and then include it.

Syntax in templates

```

{%% var with path to the template's part %%}
	
OR
	
{%% path/to/the/template/part %%}

```

Example

index.php

```
<?php
	
echo new div('test.tpl', [
	'name' => 'Unnamed',
	'products' => [
		['name' => 'Banana'],
		['name' => 'Potato']
	]
]);

```

index.tpl

```
	
Include:
	
[$products]
	{% part %}
[/$products]
	
Preprocessed:
	
[$products]
	{%% part %%}
[/$products]

```

part.tpl

```

{$name}

```

Output

```

Banana
	
Potato
	
Unnamed
	
Unnamed
```