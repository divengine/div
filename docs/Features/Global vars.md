The global variables conserve their value and they are independent of the instances of the div class.

Syntax in PHP

```
<?php
	
div::setGlobal('var-name', $mixed_value);

```

Example

index.php

```
<?php
	
div::setGlobal('today', date('Y-m-d'));
	
echo new div('index.tpl', [
	'name' => 'Peter'
]);
	
echo new div('index.tpl', [
		'name' => 'Jack'
]);

```

index.tpl

```

Hello {$name}
Today is: {$today}

```

Output

```

Hello Peter
Today is 2012-08-17
	
Hello Jack
Today is 2012-08-17

```

