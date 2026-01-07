## $div reserved variable

Some system vars are available in the templates. This vars are provided by the engine. The following table shows the system vars:

|System var|Description|
|---|---|
|div.now|The result of **time()** PHP function|
|div.post|$_POST|
|div.get|$_GET|
|div.server|$_SERVER|
|div.session|$_SESSION|
|div.version|div::$__version|
|div.script_name|The cuurent script file name - $_SERVER['SCRIPT_NAME']|
|div.ascii|The ASCII chars. For example, the **{$div.ascii.64}** to replace with character 64 (@ symbol). You don't made a mistake, this replacement is different to use the HTML entities just as "**&#64;**".|

Now then, all the variables of the system are not enabled by default. The system vars enabled by default are **div.now**, **div.version**, **div.get** and **div.post**.

If you need enable some system vars use the method **div::enableSystemVar($varname)**. If you need disable a system var use the method **div::disableSystemVar($varname)**.

Example

index.php

```php
<?php
	
session_start();
	
include 'div.php';
	
div::enableSystemVar('div.session');
	
if (isset($_GET['user'])){
	if ($_GET['user'] == 'peter'){
		$_SESSION['user'] = $_GET['user'];
	}
}
	
echo new div('index.tpl');

```

index.tpl

```html

?$div.session.user
	?$div.get.user
		- Welcome {$div.get.user}
	@else@
	 	- Access denied for user {$div.session.user}
	 	- Show the login form
	$div.get.user?
@else@
	Show the login form
$div.session.user?

```

Output

Testing the script with **index.php?user=peter** in the URL.

```html
Welcome peter
```

