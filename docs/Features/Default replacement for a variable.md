How to define a default replacement for a specific variable? It is really simple:

Syntax in PHP

```
<?php
	
div::selDefaultByVar($varname, $value_to_search, $replace_with, $update);

```

Syntax in templates

```

{@ ['varname', value_to_search, replace_with ] @}

```
