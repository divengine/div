Variable's modifiers allow you to change the value of a variable or obtain information about the value in the templates, so they change the way they are displayed, such as a text in capital letters, etc..

**Syntax in templates:**

The following variable's modifiers can be used with the current version of Div:

|Modifier|Description|
|---|---|
|{$variable}|Nonthing to change|
|{^variable}|Capitalize the first character of the string.|
|{^^variable}|Capitalize the first character of each word of the string.|
|{^^^variable}|Convert the entire string to uppercase.|
|{_variable}|Convert the entire string to lowercase.|
|{%variable}|Count of characters.|
|{%%variable}|Count of words.|
|{%%%variable}|Count of sentences.|
|{%%%%variable}|Count of paragraphs.|
|{&variable}|URL encode (see also [urlencode](http://www.php.net/manual/en/function.urlenconde.php)).|
|{&&variable}|Raw URL encode (see also [rawurlencode](http://www.php.net/manual/en/function.rawurlenconde.php)).|
|{html:variable}|Convert all aplicable characters to HTML entities (see also [htmlentities](http://www.php.net/manual/en/function.htmlentities.php)).|
|{br:variable}|Convert new lines to HTML line breaks (see also [nl2br](http://www.php.net/manual/en/function.nl2br.php)).|
|{json:variable}|Encode the value as JSON.|
|{[other-modifier]variable:~truncate-length}|Truncate the content for create a teaser content. If the content have the break tag (**<!--break-->**) the parser truncate the content in this tag.|
|{[other-modifier]variable:/wordwrap-length}|Word wrap.|
|{[other-modifier]variable:from,length}|Sub-string|
|{'variable}|Escape unescaped single quotes|
|{js:variable}|Output JavaScript code (or similar) - Escape quotes and backslashes, newlines, etc.|
|{$variable:[string format]}|Format the string with [sprintf](http://www.php.net/manual/en/function.srpintf.php) PHP function|

**Example:**

index.tpl

```html
{= title: mozilla firefox =}
{= body: A wonderful web browser=} 

Nothing to change: 
{$title}

Capitalize the first character of the string: 
{^title}

Capitalize the first character of each word of the string: 
{^^title}

Convert the entire string to uppercase:
{^^^title}

Convert the entire string to lowercase: 
{_title}

Count of characters: 
{%title}

Sub-string:

{$body:0,11}

Truncate: 

{$body:~25}...

Word wrap: 

{$body:/30}

Combining the modifiers:

{^^^body:0,11}

{^^^body:/40}

String format:

{= value: 10 =}

{$value:%1$04d}
```

Output:

```html
Nothing to change: 
mozilla firefox

Capitalize the first character of the string: 
Mozilla firefox
	
Capitalize the first character of each word of the string: 
Mozilla Firefox
	
Convert to uppercase:
MOZILLA FIREFOX
	
Convert to lowercase: 
mozilla firefox
	
Count of characters: 
15
	
Sub-string:
web browser
	
Truncate: 
A wonderful...

Word wrap: 
A wonderful web browser


Combining the modifiers:
A WONDERFUL WEB BROWSER

A WONDERFUL

	
String format:
	
0010
```

[[Custom modifiers]]
[[Multiple variable's modifiers]]

