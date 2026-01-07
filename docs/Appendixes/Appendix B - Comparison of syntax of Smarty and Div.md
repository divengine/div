### Loops

Smarty:

```html
{foreach $foo as $bar}
	  <a href="{$bar.zig}">{$bar.zag}</a>
	  <a href="{$bar.zig2}">{$bar.zag2}</a>
	  <a href="{$bar.zig3}">{$bar.zag3}</a>
	{foreachelse}
	  There were no rows found
{/foreach}
```

Div:

```html
[$foo]
  <a href="{$zig}">{$zag}</a>
  <a href="{$zig2}">{$zag2}</a>
  <a href="{$zig3}">{$zag3}</a>
@empty@
  There were no rows found
[/$foo]
```

### Include

Smarty:

```html
{include file="header.tpl"}
```

Div:

```html
(% header %}
```

### Iterations

Smarty:

```html
{for $x = 1 to 20 step 2}
	  {$x}
{/for}
```

Div:

```html
[:1,20,x,2:]
	  {$x} 
[/]
```

