A simple replacement is the replacement of parts of the template with any content. The variable can be contain a mixed value:

- If the value is a string, the replacement is the string.
- If the value is a number, the replacement is the "number".
- If the value is an array, the replacement is the length of the array.
- If the value is an object without __toString method implemented, the replacement is the count of properties of the object.

**Syntax in templates**

```
{$varname}
```

**Example:**

**index.php**

```
<?php
	
include 'div.php';
	
echo new div('index.tpl', [
	'first_name' => 'Peter',
	'last_name' => 'Pan'
]);
```

**index.tpl**

```
First name: {$first_name}
Last name: {$last_name}
```

**Output**

```
First name: Peter
Last name: Pan
```