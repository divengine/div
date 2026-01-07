A complex design, require split of the template. Then you can include the design's parts in a container template.

Syntax in templates

```

{% var with path to the template's part %}
	
OR
	
{% path/to/the/template/part %}

```

Example

part.tpl

```
Hello world!
```

index.tpl

```

This is the container template:
	
{% part %}

```

Output

```

This is the container template:
	
Hello world!

```

Important:

The engine does not accept that a template is included itself.
