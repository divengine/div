The data formats are [[Variable's modifiers]] that need more information than a symbol.

**Date format {Format a timestamp}**

Syntax in templates

```html
{/variable:php-format-date/}
```

Example

index.php

```php
echo new div('index.tpl', ['today' => time()]);
```

index.tpl

```html
Timestamp: {$today}
Today is: {/today:Y-m-d/}
Now is: {/today:h:i:s/}
```

Output

```html
Timestamp: 1341956900
Today is: 2012-07-10
Now is: 05:48:20
```

**Number format**

Syntax in templates

```html
{#variable:decimals separator miles-separator#}
```

Example

index.php

```php
echo new div('index.tpl', ['number' => 2900200.4567]);
```

index.tpl

```html
The number: {$number}
The integer part: {#number:0#}
Two decimals: {#number:2#}
Two decimals and separators: {#number:2,.#}
Two decimals and other separators: {#number:2.'#}
More decimals and other separators: {#number:9|-#}
```

Output

```html
The number: 2900200.4567
The integer part: 2900200
Two decimals: 2900200.46
Two decimals and separators: 2.900.200,46
Two decimals and other separators: 2'900'200.46
More decimals and other separators: 2-900-200|456700000
```