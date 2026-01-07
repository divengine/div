IDE sometimes detects the code of Div in HTML templates like a syntax error, because they don't have a plugin that identify the syntax of Div. Then, to avoid that it is shown as an error, Div provides two tags to encapsulate its code making it a comment.

Syntax in templates

```

<!--| ... some Div code here ... |-->

```

Example

index.tpl

```

This:
	
<!--| [$products] |-->
	Name: {$name}
	Price: {$price}
<!--| [/$products] |-->
	
Is equal to:
	
[$products]
	Name: {$name}
	Price: {$price}
[/$products]

```
