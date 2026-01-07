The inheritance allows you to construct a base template that contains all the common elements and defines "zones" that other templates can change. At the moment Div doesn't provide the template inheritance explicitly. But we wonder if it is really necessary.

For those that don't know about this topic, the template inheritance means that a template can inherit the design of another template and then redesign the necessary parts.

The templates inheritance can be solved with inclusions in engines that don't provide the inheritance, but it can be a not very elegant solution. Does Div have some mechanism that can be considered a solution for the templates inheritance?

Div considers that the templates inheritance can be solved with some of their features, like as inclusions, default values, template vars, locations and recursion. This section explain three variants for implement the inheritance.

## Variant 1: Switch

This way, when you write

**{% block %}**  
  
Or  
  
**{% {$block} %}**

the variable $block can have a default value and then this code can be include different templates. You can call this mechanism as "switch".

### Variant 2: Using protected template's variables

Another way to implement inheritance is using the template variables. In the parent template defines a block as it defines a template variable, then the variable positions in place of the template you want. In the child template redefines the "blocks" ([protected template's variables](https://divengine.org/documentation/div-php-template-engine/mechanisms/templates-inheritance#protected-template-vars)) and then includes the parent template.

Example

parent.tpl

```
	... any code ...
{= block1:
	
... code of block 1 ...
	
=}
	
... another code...
	
{$block1}
```

child.tpl

```
{= *block1:
	
	... another code for block 1 ...
	
=}
	
{% parent %}
```

### Variant 3: Using locations

This is the most elegant solution because you not need to define a variable. In the parent template you define the locations of the common content (for example "top", "header", "footer", "left", "right", etc), and then in the child template you can locate contents in the parent's locations.

Example

parent.tpl

```
... any code ...
	
(( block1 ))
	
... another code...
	
{= parent_block1:
	
... code block 1 written by the parent ...
	
=}
```

child.tpl

```
{% parent %}
	
{{block1
	
	{$parent_block1}
	
... The child's content ...
	
block1}}
```

Output

```
... any code ...
	
... code block 1 written by the parent ...
... The child's content ...
	
... another code...
```
