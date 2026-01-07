All implementation of Div is the **div** class and different forms exist of using it. **If you have another class named "div", you can rename the div class.**

**First, include the div.php file:**

```php
<?php

include "div.php";

// or using composer

include "path/to/vendor/autoload.php";

// div class is inside the "divengine" namespace

use divengine\div;
```

**Variant 1: All in one instruction**

```php
echo new div('Hello {$name}', [
	'name' => 'Peter'
]);
```

**Variant 2: First instance, then show**

```php

$t = new div('Hello {$name}', ['name' => 'Peter']);
	
echo $t; /* or $t->show(); */
```

**Variant 3: The template in external file**

```php

/* The file index.tpl contain the template code */
 
echo new div('index.tpl', ['name' => 'Peter']);
```

**Variant 4: The data as JSON code**

```php

echo new div('Hello {$name}', '{name: "Peter"}');

```

**Variant 5: The data in JSON file**

```php
/* The file index.json contain the data as JSON code */

echo new div('index.tpl', 'index.json');
```

[[Ignore specific variables (the third parameter of constructor)]]