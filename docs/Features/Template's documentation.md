Div provides a functionality that create a documentation of your templates. The template's documentation is written in the comments with a specific syntax, similar to some programming languages. In each comment's line, the parser recognizes the documentation's properties and their values . The documentation's properties begin with @. See the example.

Now, a specific or restricted list of properties not exists. All the properties that you want to write in the comments will be saved by the parser. After parsing, getDocs returns the saved documentation's properties. With this information and a template, you can build the documentation that you want.

Now, don't invent the wheel. Use getDocsReadable method and obtain a readable documentation based on the saved properties and a template provided by Div. You can specify a custom template.

Important, the template given by Div if it is prepared to recognize certain properties that are listed now:

|Variable/property|Description|
|---|---|
|title|Documentation's title. This variable can be passed to the method with $items parameter. For example:<br><br>echo div::getDocsReadable(null, array('title' => 'My docs'));|
|name|Template's name|
|description|Description of the template in one line|
|version|Version of the template|
|author|Author|
|update|Date of last update|
|vars|List of the template's variables. The fourth part can contain several spaces, but the three first not, because Div considers them as words. Remember that this is specifically for the documentation template that Div provides. You can build your own documentation template with defined variables by your work team:<br><br>1. optional/required<br>2. data type<br>3. variable's name<br>4. variable's description<br><br>For example:<br><br><!--{  <br>...<br>@vars required string title Blog's title <br>      optional string body Blog's body<br>...			<br>}-->|
|include|The list of included templates. The parser will add all includes automatically. Also you can specify it, for example, when the include tag use a variable and not the template's specific path.|
|example|The example of how to use this template|

Syntax in templates

```html
<--{ 
	
	... unsaved content here ....
	
	@fist_saved_property value
	@other_property value
	@other_property value
	@other_property value
	@multiline_property: line1
		                     line2
		                     line3
	@other_multiline_property: 
	line1
	line2
	line3	
	...
		
}-->
```

Example

index.tpl

```html
<--{ 
	
	The next comments are the documentation of this template
	
	@name My template
	@description My first template with documentation
	@author Me 
	@vars: required string title
	       optional string body 
		
		@example:
		{= title: "My first blog" =}
		{= body: "This is my first blog" =}
		{% blog.tpl %}
		
}-->
```

index.php

```php
<?php
	
include 'div.php';
	
div::docsOn();
	
$tpl = new div('index.tpl');
$tpl->parse();
	
echo $tpl->getDocsReadable();
```
