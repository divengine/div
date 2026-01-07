Replace some values for another values.

Syntax in PHP

```
<?php
	
div::setDefault($value_to_search, $replace_with);

	
```

Syntax in templates

```

{@ [value_to_search, replace_with] @}

```

Example

index.php

```
<?php
	
div::setDefault(true, "YES");
	
echo new div("index.tpl", [
		"haveproducts" => true,
		"havemoney" => false
]);

```

index.tpl

```
{@ [false, "NO"] @}
	 
Have products: {$haveproducts}
Have money: {$havemoney}

	
```

Output

```

Have products: YES
Have money: NO

```

[[Default replacement for a variable]]