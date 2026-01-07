Clean the resulting code of the parser, eliminating double spaces, unnecessary new lines, etc.

Syntax in templates

```

{strip}
	... some ugly code here ...
{/strip}

```

Example

index.tpl

```
{strip}
Hello Jack, ...
	
          ...the previous lines are of more.
{/strip}
```

Output

```
Hello Jack, ...
...the previous lines are of more.
```
