With the [template's property](https://divengine.org/documentation/div-php-template-engine/features/custom-dialects/multiple-dialects#template-property) @__DIALECT you can specify the dialect for current template source. This dialect should be written in a separeted file with JSON code. For example:

Example

index.tpl

```
@_DIALECT = smarty.dialect
{* this is a comment *}
Name: {$name}
{literal}
{$name}
{/literal}
{% other %}
```

other.tpl

```
@_DIALECT = twig.dialect
{{ foo.bar }}
```

smarty.dialect

```
{
  'DIV_TAG_IGNORE_BEGIN': '{literal}',
  'DIV_TAG_IGNORE_END': '{/literal}',
  'DIV_TAG_COMMENT_BEGIN': '{*',
  'DIV_TAG_COMMENT_END': '*}'
}
```

twig.dialect

```
{
  'DIV_TAG_REPLACEMENT_SUFFIX': ' }}',
  'DIV_TAG_MODIFIER_SIMPLE': '{ '
}
```

index.php

```
<?php
	
include "div.php";
	
echo  new div("index.tpl", [
	'name' => 'Peter',
	'foo' => [
		'bar' => 45
	]
]);
```

Output

```
Name: Peter
	
{$name}
	
45
```
