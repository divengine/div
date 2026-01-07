Div provide a translator for dialects. This translator can translate from any dialect to current dialect. Div use the current template's variables for self help in the translation. For this reason, you only translate having a instance of div.

Example

index.php

```
<?php
	
include 'div.php';
	
$tpl = new div('index.tpl');
	
$tpl->translateFrom([
	'DIV_TAG_IGNORE_BEGIN' => '{literal}',
	'DIV_TAG_IGNORE_END' => '{/literal}'
]);
	
$tpl->show();
```

index.tpl

```

{= name: "Peter" =}
	
{literal}
	{$name}
{/literal}
	
{$name}

```

index.tpl (translated)

```
	
{= name: "Peter" =}
	
{ignore}
	{$name}
{/ignore}
	
{$name}
```
