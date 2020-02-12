Feb 11, 2020
--------------------------
- `minor fix`: Array and string offset access syntax with curly braces is deprecated
- `release` version 5.1.6

Sep 21, 2019
--------------------------
- `release` version 5.1.5
- `fix` div::varExists() method

Ago 23, 2019
--------------------------
- `release` version 5.1.4
-  new method div::getVersion()

Ago 22, 2019
--------------------------
- `release` version 5.1.3
- `fix` resolution of templates path for win and *nix OS
- `fix` the relative path of included templates inside loop

Ago 21, 2019
--------------------------
- `release` version 5.1.2
- `fix` orphan conditional parts
- `fix` standalone preprocessed templates

#### Now this example works!

__cmp.tpl__

This is a generic template for create visual components.
Each component have a *face* or *content*, and 
more *child components*. Each child can located in 
the face of their parent. The template self call 
recursively.

```
{strip}
?$location {{{$location} $location?

?$face {$face} $face?

?$components
    [$components] component =>
        {= component.div.standalone: true =}
        {%% cmp: component %%}
    [/$components]
$components?

?$location {$location}}}  $location?
{/strip}
```

__Button.tpl__
```
<button>{$icon}{$caption}</button>
```

__Page.tpl__
```html
<h1>Buttons</h1>
(( top ))
<p>Click on the buttons: <p/>
(( bottom ))

<h1>Fruits</h1>
(( fruits ))
```

__index.php__
```php
<?php

use divengine\div;

echo new div("cmp", [
        "id"         => "welcomePage",
        "face"       => "{% Page2 %}",
        "components" => [
            [
                "face"     => "{% Button %}",
                "location" => "top",
                "caption"  => "Click me",
                "icon"     => '*'
            ],
            [
                "face"     => "{% Button %}",
                "location" => "bottom",
                "caption"  => "Click me again",
                "icon"     => '#'
            ],
            [
                "face"       => "<ul>(( items ))</ul>",
                "location"   => "fruits",
                "components" => array_map(function ($caption) {
                        return [
                            "face"     => "<li>{$caption}</li>", // or "<li>{\$caption}</li>" :D
                            "location" => "items"
                        ];
                }, ["Banana", "Apple", "Orange"])

            ]
        ]
    ]
);
```
 
Jul 22, 2019
--------------------------
- `release` version 5.1.1
- `improvement` support namespaces of div's child
- `release` version 5.1.0 
- `improvement`: Better resolution of default template for 
child classes of div, using Reflection!

**/some/folder/in/the/end/of/the/world/Page.tpl**
```
Hello people
```

**/some/folder/in/the/end/of/the/world/Page.php**
```php
<?php4

use divengine\div;

class Page extends div {

}
```

**/index.php**
```php
<?php

include '/some/folder/in/the/end/of/the/world/Page.php';

echo new Page();
```

**Output**
```
Hello people
```

Jul 6, 2019
--------------------------
- `bugfix` in div::scanMatch

Jul 3, 2019
--------------------------
- Divengine namespace!
- `bugfix`: Fix scope of standalone pre-precessed templates. 
This fix prevent infinite loops and is util for recursive pre-process
in a component based design.

index.tpl
```
{= foo: "bar" =}
{%% component: {
	div: {standalone: true}, // ignore parent scope
	zoo: "monkey"
} %%}
```

component.tpl
```
{$zoo}
{$foo} <!--{ not exists in this scope }-->
```

Jul 2, 2019
----------------------------
- new feature for custom engine:

MyComponent.php
```php
MyComponent extends div {
 ....
}
```

index.tpl:
```
{%% component: {
	div: {
		engine: "MyComponent"
	},
	someProperty: "bla"
} %%}
```

Jun 27, 2019
----------------------------
- important change!: Now NULLs vars exists and are replaced with empty strings

PHP
```php
echo new div('Var is: {$var}', ['var' => null]);
```

OUTPUT before this change:
```
Var is: {$var}
```

OUTPUT after this change:
```
Var is:
```

- `important change!`: Fix scope of pre-processed templates inside loops

Do not pre-process anything within the loops blocks if the loops have not been resolved
The following code did not work as expected, because the pre-process was executed before doing the loop.
So the `$col` variable did not exist and logic of the template will be broken.

```
[$cols] col =>
	{%% element: {
		tag: "td",
		attrs: $col.attrs,
		inner: $col.content
	} %%}
[/$cols]
```

Jun 14, 2019
----------------------------
- `bugfix`: better resolution of tags with empty suffix. In this example "list.filter"
is a substring of "list.filter.category", and then exists resulting unexpected code
if $list.filter is false

TPL
```
?$list.filter
  AAA
	?$list.filter.category
		BBB
	$list.filter.category?
  CCC
$list.filter?
```

OUTPUT
```
?$list.filter
  AAA
```

The fix was for other similar situations in div::getBlockRanges().
The stop chars are the same in favor of text plain and XML family:

```php
$stop_chars = ["<",	">", ' ', "\n", "\r", "\t"];
```

Sep 20, 2018
----------------------------
- Optimize the code: change "is_null" as "=== null", because is_null is 250ns slower (in favor of PHP 5) 

In PHP 7 (phpng), is_null is actually marginally faster than ===, although the performance difference between the two is far smaller.

```
PHP 5.5.9
is_null - float(2.2381200790405)
===     - float(1.0024659633636)
=== faster by ~100ns per call

PHP 7.0.0-dev (built: May 19 2015 10:16:06)
is_null - float(1.4121870994568)
===     - float(1.4577329158783)
is_null faster by ~5ns per call
```

Aug 19, 2018
----------------------------
- `bugfix` on constructor, when div var is an object and not an array
- Add file_exists as allowed function
- Add in_array as allowed function

Oct 8, 2017
----------------------------
- Re-thinking the change in **June 10, 2013** about invalid JSON in assignments.
  Is important the dynamic path of JSON files:

```
{= i18n: i18n/{$lang}.json =}
```

`"i18n/{$lang}.json"` without quotes is invalid JSON

Then, don't use quotes:
```
{= i18n: "i18n/{$lang}.json" =}
```

- Important improvement for loading JSON data from relative path in template
 vars's assignment:

```
         relative  -->
                  |
                  v
    /app/site/view/i18n/en/messages.json
                        ^
                        |
                 replacement result
```

/app/site/view/page.tpl

```    
{= lang: "en" =}
{= i18n: i18n/{$lang}/messages.json =}

{$i18n.message1}
```

/app/site/view/i18n/en/messages.json
```
{
	message1: "Hello"
}
```

Output:
```
Hello
```

Oct 7, 2017
----------------------------
- Change scope of `->loadTemplateProperties()` to public
- Other minor fixes
- Automatic update of template source code after `prepareDialect()` ...
- ... && new param for `->prepareDialect()` for disable automatic update

Sep 30, 2017 [my birthday]
----------------------------
- Fix a bug with `getAuxiliaryEngine` (clone vs assignment)
- Add some new system vars
    - `div.class_name`: the name of current invoked class ('div' or child of 'div')
    - `div.super_class_name`: the name of super parent of current invoked class name (normally is 'div')
- Code review

Sep 25, 2017
----------------------------
- Fix and improve getAuxiliaryEngine

Sep 9, 2017
----------------------------

- Fix dynamic include's paths inside loops
```	
[$blocks]
	{% blocks/block-{$id}.tpl %}
[/$blocks]
```

Jun 2, 2017
----------------------------
- Improve the translator. Now you can translate from and to other dialects.

```php 
$tpl = new div("index.tpl", []);
```

Translate from other dialect to current dialect:

```php
$tpl->translateFrom($dialectFrom);
```

 Translate from current dialect to other dialect:
 
```php
$tpl->translateTo($dialectFrom);
```

Translate from any dialect to any dialect:

```php
$tpl->translate($dialectFrom, $dialectTo, $src, $items);
```
   
Maybe you need prepare the current dialect first:

```php
prop = $tpl->getTemplateProperties();
$tpl->__src = $tpl->prepareDialect(null, $prop);
```

- Improve the Div CLI (https://github.com/divengine/div-cli):

```    
div translate -f =
```

May 29, 2017
----------------------------
- Some bugfixs
- New variable for inline data of preprocessed templates:
  div.standalone, by default is FALSE.
  This means that the "foo" variable will not be passed
  to the template pre-processor. That is, the variables
  in the parent template will be ignored and only the data
  specified in the line will be used.

```
{= foo: value =}
{%% block.tpl: {
	div: {
		standalone: true
	}
} %%}
```

  This better facilitates the recursive inclusion of
  templates, useful in generation of source code and
  other hierarchies like XML, HTML, JSON, etc.

- Do not include anything within the conditional
  blocks if the conditions have not been resolved.
  This check prevent infinite loops.

```php
   ?$block
   {%% block: {...} %%} <-- wait for block question results
   $block?
```

- Priority change for items over filesystem when include o preprocess templates.
  To force load data from external file, please type the path or full path (ex: block.json)

  {= block: {
    var1: "value1"
    var2: "value2"
  } =}

  Take the block value
  {%% tpl: block %%}

  Take json from external file
  {%% tpl: block.json %%}

----------------------------
December 22, 2016
----------------------------
- PHP 7 Compatibility check
- Release 4.9 version 
----------------------------
November 16,  2016
----------------------------
- important bugfix/improvement: access to parent loop

    [$parentloop] parent =>
        [$childloop] child =>
            Parent key: {$parent._key}
            Child key: {$_key} or {$child._key}
        [/$childloop]
    [/$parentloop]
- TODO: test & release
----------------------------
November 14,  2016
----------------------------
- add new default subparser join

    Syntax:

    {join} varname | delimiter {/join}

    index.tpl
    ---------
    {= tags: ['a','b', 'c'] =}
    {join} tags |, {/join}
    {join} tags |,{/join}
    {join} tags {/join}

    Output:
    -------
    a, b, c
    a,b,c
    abc
----------------------------
Octuber 10,  2016
----------------------------
- Several tests
- Release 4.8 version

----------------------------
January 12,  2016
----------------------------
- review example
----------------------------
December 24,  2015
----------------------------
- improved dialect translator div::translateFrom
- some bugfixs
- update documentation

----------------------------
December 23,  2015
----------------------------
- add new feature for dialects: DIV_TAG_VAR_MEMBER_DELIMITER. This dialect's constant 
  define a delimiter for variable's members. For example:

	by default you use:
	{$person.name}

	but now you can do...
	
	-------------
	index.php
	-------------
	<?php

	define('DIV_TAG_VAR_MEMBER_DELIMITER','->');

	include "div.php";

	echo new div("index.tpl", array(
		'person' => array(
			'name' => 'Peter',
			'child' => array(
				'name' => 'eli'
			)
		)
	));
	
	-------------
	index.tpl
	-------------
	{$person->child->name}
	{$person->name}
	
	TODO: improve dialect creator tool
	TODO: check dialect translator method div::translateFrom()
	
----------------------------
December 19,  2015
----------------------------
- several tests
- Release 4.7 version

----------------------------
December 12,  2015
----------------------------
- [begining release 4.7]
- some bugfixs, thanks to gracix and Takefumi Ota
- Improve template's vars and OOP: now you can access to a public method of any object. Example:

	index.php
	---------
	<?php
	class Person {
	
		...
		public function getFullName(){
			return $this->first_name.' '.$this->last_name;
		}
		...
	}
	
	echo new div('index.tpl', array("person" => new Person(...)));
	?>
	
	index.tpl
	---------
	{= fullname: ->person.getFullName() =}
	
	The full name is {$fullname}
- 
----------------------------
December 11,  2015
----------------------------
- Bugfix in div class constructor
- Release 4.6 version

----------------------------
December 1,  2014
----------------------------
- some bugfixs
- Release 4.5 version

----------------------------
Novemeber 24,  2014
----------------------------
- improve div::isValidPHPCode()

----------------------------
October 7,  2014
----------------------------
- improve performance changing $vars with __temp['vars'] var 
  in parseMacros(); because because get_defined_vars return also 'vars'
- prevent infinite loops in div::cop(); 

----------------------------
October 6,  2014
----------------------------
- new setup var: div.clear_locations (= true by default). This means that the locations will 
be clear or not at the end (parse_level = 0). Then, the component are more flexible 
with pre-processed templates: 

comp.tpl
----------------
(( before )) <input type="{$type}" name="{$name}"> (( after ))

index.tpl
----------------
{%% comp: {
	type: "text",
	name: "first_name",
	div: {
		clear_locations: false
	}
} %%}

<!--{ If div.clear_locations is true (by default), the next code don't work }-->

{{before <label>First Name</label> before}}
{{after <input type="submit"> after}}

----------------------------
September 26,  2014
----------------------------
- bugfix parseData() vs parseMatch() logical order

----------------------------
September 23,  2014
----------------------------
- bugfix in parsePreprocessed() when $pdata is null
- bugfix with number formats inside loops  

----------------------------
September 21,  2014
----------------------------
- new feature: advanced options/params for includes

	index.tpl
	----------
	
	{% subtpl: {
		from: "<!--{ begin }-->",
		to: "<!--{ end }-->",
		offset: 2,
		limit: 1
	} %}

	subtpl.tpl
	-------------
	Any text...
	
	<!--{ begin }-->Some text 1<!--{ end }-->
	
	Any text...
	
	<!--{ begin }-->Some text 2<!--{ end }-->
	
	Any text
	
- bugfix: Preparing allowed methods before execute the macros
----------------------------
September 20,  2014
----------------------------
- bugfix: Parsing macros inside preprocessed templates. New argument $min_level for parse() method.
- Add new allowed functions in macros, formulas and expressions: array_keys get_object_vars is_object
- new static method div::div():

	index.php
	---------
	<?php	
		echo div::div('index.tpl', array("item1" => "value1"));
	?>
	
- Allow T_BREAK token in macros for foreach and other loops. Then, the follow macro is an error:

	index.tpl
	---------
	<? break; ?>
	
	Output
	---------
	Fatal error: Cannot break/continue 1 level in div.php: eval()'d code on line 1
	
----------------------------
September 17,  2014
----------------------------
- bugfix/improve - Parsing orphans's parts while checksum not change. Do it because the 
orphans's parts stop the parser and the results are ugly.
			
----------------------------
September 16,  2014
----------------------------
- bigfix: Set the priority to inline data in pre-processed templates above global design vars

----------------------------
September 11,  2014
----------------------------
- bigfix: Save sections of loops and capsules when makeItAgain(); (Div doesn't know the future)

----------------------------
September 9,  2014
----------------------------
- bugfix: Adding items to array in templates

{= somearray[]: "new item" =}

- bugfix: Don't set item var as design var in div::parseData();

----------------------------
September 8,  2014
----------------------------
- bugfix: Parse pre-processed templates with all items/vars (Div doesn't know the future)

----------------------------
August 28,  2014
----------------------------
- New feature for preprocessed templates: specific data
  
  Syntax:
  
  {%% tpl_file: data %%}
  
  data is: json, name of var or filename with json
  
  Example:
  
  Now is more simple for build the components:
  
  index.tpl
  ------------
  {%% form: {
  		action: "login.php",
  		method: "post.php",
  		fields: [
  			{
  				type: "text",
  				name: "user",
  				label: "User"
  			},{
  				type: "password",
  				name: "pass",
  				label: "Password"
  			}
  		],
  		submit: {
  			value: "login",
  			name: "btnLogin"
  		}
  } %%}
  
  form.tpl
  ------------
  <form action="{$action}" method="{$method}">
  	[$fields]
  		{$label}:<br/>
  		<input type="{$type}" name="{$name}"><br/>
  	[/$fields]
  	<input type="submit" name="{$name}" value="{$value}">
  </form>
      
----------------------------
August 17,  2014
----------------------------
- Security fix: prevent obtrusive code in method calls. Now next code dont work:

{= content: ->getPage(file_put_contents('some.txt','some text')) =}

----------------------------
August 5,  2014
----------------------------
- Fix the memory in the loops

----------------------------
August 4,  2014
----------------------------
- Fix macros parsing when a previous template var never match

----------------------------
August 2,  2014
----------------------------
- Allow is_array PHP function in macros
- New method for add literal vars in PHP: div::addLiteral();

----------------------------
June 30,  2014
----------------------------
- Some bugfixs
- Add new important security feature: setup literals items/vars, for prevent injections! 

Example:

index.tpl
---------------
{= div.literals: ["text1", "text2"] =}	<!--{ in PHP div::addLiteral(array("text1","text2")); }-->

{$text1}

{$text2}

{$text3}

index.php
---------------
echo new div('index.tpl', array(
	'text1' => '{/ignore}[:1,5:] {$value} [/]{ignore}', // I am being about deceiving the security
	'text2' => '[:1,100;] text to repeat [/]',
	'text3' => '[:1,3;] some [/]'
));

output
---------------
[:1,5;] {$value} [/]
[:1,100;] text to repeat [/]
some some some

----------------------------
February 05,  2014
----------------------------
- Memory fixed!
  
----------------------------
December 25,  2013
----------------------------
- bugfix: div::fileExists and wrong include paths calculation

----------------------------
December 5,  2013
----------------------------
- Improvement of global design vars in loops and capsules

----------------------------
December 4,  2013
----------------------------
- bugfix: div::getFileContents()

----------------------------
August 30, 2013
----------------------------
- An important bug was fixed: the memory in the loops:

	In div 4.4 dont't work:
	
	index.php
	--------------------------------
	<?php
	
	include "div.php";
	echo new div("test.tpl", array("cities" => array("Havana", "Tokyo")));
	
	index.tpl
	--------------------------------
	{= foo: [
		{ title: "Cities",
		  content: '{% cities.tpl %}'
		}
	] =}

	{% layout.tpl %}

	layout.tpl
	---------------------------------
	?$foo
		[$foo]
			<h1>{$title}</h1>
			{$content}<br/>
		[/$foo]		
	$foo?
	
	cities.tpl
	---------------------------------
	?$cities
		[$cities]
			{$value}
		[/$cities]
	@else@
		No cities
	$cities?
	
	Output (wrong!)
	---------------------------------
	<h1>Cities</h1>
	No cities<br/>

	Output (great in 4.5)
	---------------------------------
	<h1>Cities</h1>
	Havana Tokio<br/>

----------------------------
July 29, 2013
----------------------------
- Decrease of priority in parser's specialchars

----------------------------
July 27, 2013
----------------------------
- bugfixs!
- Release 4.4 version

----------------------------
July 19, 2013
----------------------------
- bugfix the translator
- New feature: Multi template sources (based on include_path PHP setting)
----------------------------
June 15, 2013
----------------------------
- Improvement of the modifier "escape single quotes" (\') 
  to "escape single/double quotes" (\").
- Improvement of default documentation's template.

----------------------------
June 15, 2013
----------------------------
- Improvement of logs's system
- Release 4.3 version

----------------------------
June 13, 2013
----------------------------
- Integration with Google Chrome/Console and Mozilla Firefox/Firebug plugins. 
  Now the engine's messages will be appear in this browsers's features.
  
- Improvement of detection of infinite loops in recursive replacements:
 
  index.tpl
  -------------
  {= bar: {${$e}} =}
  {= e: 'bar'} =}
  
  {$bar}
  
  Output
  -------------
  [[ FATAL ERROR ]] WAS DETECTED AN INFINITE LOOP IN RECURSIVE REPLACEMENT OF $foo.
 
- Improvement of parser and bugs fixes: if foo not existed, widget waits forever.
  Now the next example works:
  
  index.tpl
  ------------------
    {= widget: 45 =}

	{?( "{$foo}" == "a" )?}   <!--{ BUG: if foo not existed ...}-->
		{= bar: 5 =}
	{/?}
	
	{$widget}                 <!--{ ...widget waits forever }-->
	
  Output
  ------------------
  45
  
  Solved!
  
----------------------------
June 12, 2013
----------------------------
- Improvement of relative include/preprocessed templates.
  Now the next example works:
  
  index.tpl
  -------------------------
  {% folder/tpl1 %}
  
  /folder/tpl1.tpl
  -------------------------
  {% folder2/tpl2 %}
  
  /folder1/folder2/tpl2.tpl
  -------------------------
  {% tpl3 %}
  
  /folder1/folder2/tpl3.tpl
  -------------------------
  Hello
  
  Ouput
  -------------------------
  Hello

- Improvement of template's variables assignment.
  Now the next example works:
  
  index.tpl
  -------------------------
  {= position: "absolute" =}

  {?( "{$position}" == "absolute" )?} 
            {= absolute: true =}
  @else@
	    {= absolute: false =}
  {/?}
	
  ?$absolute YES $absolute?
  
  Ouput
  -------------------------
  YES

- Improvement of the variables's scope:
  Now the next example works:
  
  index.tpl
  ----------------
    {= foo: true =}
    {= bar: [1,2,3] =}

    ?$foo
    YES
    $foo?

    [$bar]
            {= foo: (# {$value} > 1 #) =}
            ?$foo
                    YES
            @else@
                    NO
            $foo?
    [/$bar]

    {$foo}
	
  Output
  --------------
  YES
 
  NO	
		
  YES

  YES
    
  true
----------------------------
June 10, 2013
----------------------------
- Improvement of the parser of template's vars: 
  If the value is not valid JSON, it will be considered as 
  a template and will be parsed before decoding.
  
  See the next sequence:
  
  1. Value is not valid JSON: {= digits: [[:0,8:]{$value},[/]9] =}
  2. Value was parsed:        {= digits: [0,1,2,3,4,5,6,7,8,9] =}
  3. Now "digits" is an array.
  4. Replacement:             {$digits} <!--{ OUTPUT "10" }-->
  
  See the difference:
  
  1. Value is valid JSON:     {= digits: "[[:0,8:]{$value},[/]9]" =}
  2. Value was not parsed:    {= digits: "[[:0,8:]{$value},[/]9]" =}
  3. Now "digits" is an string.
  4. Replacement:             {$digits} <!--{ OUTPUT "[0,1,2,3,4,5,6,7,8,9]" }-->
     
- Improvement of the parser of template's variables. Was improved 
  the detection of assignment of variables in any part of the 
  JSONs values. For example:
  
  index.tpl
  ---------
  
  {= cities: ["New York", "Tokyo"] =}

  {= combobox: {
  	 id: "cboCities",
  	 options: $cities  
  } =}

  {$combobox.options.0}
  
  Output
  ---------
  New York

----------------------------
June 08, 2013
----------------------------
- Improvement of template's documentation
- Release new version 1.1 of Div Dialect Creator
- Release the version 4.2

----------------------------
June 02, 2013
----------------------------
- Fix/improve the translator
- Fix/improve the parser 

----------------------------
June 01, 2013
----------------------------
- Improvement of the parser of ignored parts
- Improvement of the parser of includes
- New feature: template's documentation. Now in the comments you can 
document the template. The documentation's parts have @ as prefix. For example:

	<!--{ 
	    @icon [[]]
		@type Div PHP Template Engine Component
		@name Simple Combobox 
		@autor Rafael Rodriguez Ramirez <rafa@pragres.com, rrodriguezramirez@gmail.com>
		@version 1.0
		@update 1/06/2013
		@website http://divengine.com
		
		@property optional <string> id 
		@property optional <string> class 
		@property optional <string> name    
		@property optional <string> label 
		@property optional <number> default 
		@property required <array>  options
		
		@example
		[[_
			{= class: "combo" =}
			{= id: "cboCities" =}
			{= name: "cboCities" =}
			{= options: ["New York", "Tokyo", "Havana"] =}
			{= default: 1 =}
			{= label: "Cities" =}
			{% components/div-cmp-simple-combobox %}
		_]]
		
	}-->

	To obtain the documentation data:
	
	$data = div::getDocs();
	
	To obtain a readable documentation:
	
	echo div::getDocsReadable(/* optional template */);

- Fix the algorithm of getRanges().
- Fix the parser of macros.
- Added a new sub-parser's event: afterReplace.
----------------------------
May 31, 2013
----------------------------
- Improvement of the algorithm of getRanges() to make all the possible one. Now 
Div continues searching ranges after unclosed tags. 

	- For next template:
	
	index.tpl
	----------
	{/
	{/div.now/}
	
	- In previous versions (1.0 - 4.1):
	
	Output:
	----------
	{/
	{/div.now/}
	
	- From Div 4.2:
	
	Output:
	----------
	{/
	2013-05-31
	
----------------------------
May 30, 2013
----------------------------
- Test new version
- Minor bugs was fixed
- Improvement of the detection of date formats
- Release 4.1 version

----------------------------
May 29, 2013
----------------------------
- Fix and improve the algorithm of div::getVarValue() method.
- Fix the detection of conditional parts.

----------------------------
May 27, 2013
----------------------------
- Change to private some div's properties
- Release 4.0 version

----------------------------
May 25, 2013
----------------------------
- Improvement of the conditional parts detection 

----------------------------
May 24, 2013
----------------------------
- Fixed some bugs in locations and conditional parts.
- Created a translator of dialects. Now div have 2 new public methods:

   $tpl = new div('templateWithDialectX.tpl', $data);
   
   $dialectY = 'json code'; // or associative array
   
   // Return the translated template
   $new_code = $tpl->translateFrom($dialectY);
   
   // Translate and change the original template
   $tpl->translateAndChange($dialectY);

- New feature: template properties. Now you can specify some properties 
in the template's code, for example, the dialect of the current template:

	Example:
	
	index.tpl
	------------------------------
	@_DIALECT = smarty.dialect

	{* this is a comment *}
	Name: {$name}
	
	{literal}
	{$name}
	{/literal} 
	
	{% other %}
	
	other.tpl
	------------------------------
	@_DIALECT = twig.dialect
	
	{{ foo.bar }}
	
	smarty.dialect
	------------------------------
	{
	  'DIV_TAG_IGNORE_BEGIN': '{literal}',
	  'DIV_TAG_IGNORE_END': '{/literal}',
	  'DIV_TAG_COMMENT_BEGIN': '{*',
	  'DIV_TAG_COMMENT_END': '*}'
	}
	
	twig.dialect
	-------------------------------
	{
	  'DIV_TAG_REPLACEMENT_SUFFIX': ' }}',
	  'DIV_TAG_MODIFIER_SIMPLE': '{ '
	}
	
	index.php
	-------------------------------
	<?php

	include "div.php";
	echo  new div("index.tpl", array(
		'name' => 'Peter',
		'foo' => array(
			'bar' => 45
		)
	));
	
	Output
	-------------------------------
	Name: Peter

	{$name}

	45
- New feature: predefined subparsers. Div provide pre-defined sub-parsers, for example, 
  {parse}...{/parse}. This example of sub-parser make a pre-proccess of enclosed code.
  This means that a new instance of div will be created, similar to the loops 
  and the capsules. Other predefined subparsers will be developed in future releases.
  
- New feature: sub-parser's events. Now in the templates's code you can specify when
  a sub-parser will be executed: beforeParse, afterInclude or afterParse. Example:
  
  	index.tpl
  	---------------------------
  	{= name: "Peter" =}
	{= products: [
		{
			name: "banana",
			price: 40
		},
		{
			name: "potato",
			price: 25
		}
	] =}
	
	[$products]
		{parse:beforeParse}
		Name: {$name}
		{/parse:beforeParse}
		
		Product name: {$name}
		
		{% other %}
	[/$products]
  
    other.tpl
    ---------------------------
    {parse:beforeParse}
		Other name: {$name}
	{/parse:beforeParse}
	
  	Output
  	--------------------------------------
  	Name: Peter
	Product name: banana
	Other name: banana
	Name: Peter
	Product name: potato
	Other name: potato

----------------------------
May 18, 2013
----------------------------
- Created a tool to build dialects.
- Release the 3.9 version

----------------------------
May 10, 2013
----------------------------
- Enable custom dialect for developers! 

   A dialect is defined by the group of constant whose name 
   begins with DIV_TAG. This dialect is subject to some simple 
   rules that Div forces to complete for preveer inconsistencies and 
   infinite loops.

- New static method isValidCurrentDialect, for detect error in the 
  definition of current dialect, based on this rule:
   - some tags are required, like as, prefixes, suffixes, beginnings and ends.
   - some tags must be unique, like as, modifiers, else, break, empty, ...
   
----------------------------
May 9, 2013
----------------------------
- New static method anyToStr, for convert mixed value to string based on this rule:
	- string is string
	- boolean is "true" or "false"
	- number is "number"
	- object with __toString() is __toString()
	- object without __toString() is array
	- array is count()
- Changed the type of unchangeable methods to "final".

----------------------------
May 3, 2013
----------------------------
- The interpretation of date format was improved.

	If you need type the char ":" in the format, and this 
	char is the separator between var and format, then type 
	a backslash before ":", like as this:
	
	{/2012-01-01 00:30:00 : Y-m-d h\:i\:s/}
	
	In the example the value is "2012-01-01 00:30:00 " and 
	the format is "Y-m-d h:i:s".
	  
----------------------------
April 29, 2013
----------------------------

- The interpretation of aggregate functions was improved. 
  The next example work now:

	index.tpl
	------------
	{= products: [
		{name: "Banana", price: 10},
		{name: "Potato", price: 20}
	] =}
	
	{$products.0.price}
	{#products.0.price:2#}
	
	{$sum:products-price}
	{#sum:products-price:2,#}
	{%sum:products-price} <!--{ digit count }-->

	Output
	-------------
	10
	10.00
	
	30
	30,00
	2
	
----------------------------
April 24, 2013
----------------------------
- Performance: work remembered! Now the engine can remember some 
  actions from previous work and increase their speed. 	 
- New feature: the macros.

  A macro is a restricted PHP code inside the templates to facilitate the complex processing 
  with the advantages of this language. The security is guaranteed. See the next silly example:
  
  index.php
  ----------
  <?php
  
  include 'div.php';
  
  echo new div('index.tpl', array('title' => 'Hello world'));

  index.tpl
  -------------
  <? 
  	echo $title;
  	$title = strtoupper($title); 
  ?>
  
  {$title}
      
  Output
  -----------
  Hello world
  
  HELLO WORLD
  
- New feature: the custom sub-parsers

  A sub-parser is a parser implmemented by the programmer. For example:
  
  index.php
  --------------
  <?php
  
  include 'div.php';
  
  function literal($code){
  	return '{ignore}'.$code.'{/ignore}';
  }
  
  div::setSubparser('literal');
  
  echo new div('index.tpl', array('title' => 'Hello world'));
  
  index.tpl
  ----------------
  
  {literal}
  <script> 
  
  // bla bla bla {$title}
  
  </script>
  {/literal}
  
  {$title}
  
  Ouput
  ----------------
  <script> 
  
  // bla bla bla {$title}
  
  </script>
  
  Hello world

----------------------------
April 17, 2013
----------------------------
- bugfix in the bodies of multi-replacements
- Changed the name of method multiReplace by parseMultiReplace

----------------------------
April 13, 2013
----------------------------
- The template variables's manipulation was improved:

   Example:
   ---------------
   
   {= product: {
   		name: "banana"
   		price: 20   
   } =}
   
   Name: {^product.name}
   Price: ${#product.price:2.#}
   
   {= product.price: (# {$product.price} * 2 #) =}
   
   Double price: ${#product.price:2.#}
   
   [[product
   Current price: {$price}
   product]]
   
   Output:
   ----------------
   
   Name: banana
   Price: $20.00
   
   Double price: $40.00
   
   Current price: 40
   
- New static methods are added:

	div::issetVar($var, $items)
	div::unsetVar($var, $items)
	div::setVarValue($var, $value, $items)
	div::getVarValue($var, $items)
	div::getVars($items)
	
	Example:
	---------------
	<?php
	
	include "div.php";
	
	$data = array();
	
	div::setVarValue("product.name", "Banana", $data);
	div::setVarValue("product.price", 10, $data);
	div::setVarValue("product.amount", 0, $data);
	
	if (div::issetVar("product.name", $data)){
		echo div::getVarValue("product.name", $data);
	}
	
	div::unsetVar("product.amount", $data);
	
	if (!div::issetVar("product.amount", $data)){
		echo "Amount not specified";
	}
	
	print_r(div::getVars($data));
	
	Output:
	---------------------
	Banana
	Amount not specified
	Array
	(
	    [0] => product
	    [1] => product.name
	    [2] => product.price
	)	
	
- The method setItem and getItem was improved with detection of complex variable's names:

	Example:
	----------------------
	
	<?php
	
	include "div.php";
	
	$tpl = new div("index.tpl", array(
		"product" => array(
			"name" => "Banana",
			"price" => null
		)
	)); 
	
	$tpl->setItem("product.price", 10);
	
	index.tpl
	-------------------
	Name: {$product.name}
	Price: ${#product.price:2.#}
	
	Output
	------------------
	Name: Banana
	Price: $10.00	
----------------------------
April 12, 2013
----------------------------
- The order respect of template variables's manipulation was improved:

	Example:
	-------------
	{= a: 5 =}
	
	{$a}
	
	{= a: (# {$a} + 1 #) =}
	
	{$a}
	
	{= a: (# {$a} + 1 #) =}
	
	{$a}

	Output:
	-----------
	
	5
	
	6
	
	7	
----------------------------
April 11, 2013
----------------------------
- bugfix of template variables when it use object's methods
   Now you can call a object's method with some ways:
   
   Similar to PHP:
   
   {= result: ->method(param1, param2, param3) =}
   
   One parameter as JSON data:
   
   {= result: ->method({param1: value1, param2: value2});
   
- bugfix of loops, prevent a recursion with var '_item' as object inside the same object:

	Product Object
	(
	    [price] => 0
	    [quantity] => 0
	    [_item] => Product Object
	 *RECURSION*
	)
	
----------------------------
April 07, 2013
----------------------------
- Fix some issues
- New method div::isSring as a safe is_string():
  - if is a string return true
  - if is a object with __toString method return true

----------------------------
April 04, 2013
----------------------------
- The scalar values as a complex values! What?
  
  Yes! Now all the scalar values can be used as strings. Then, the strings can be 
  used like complex values, that is to say, as group of characters. For example:
  
  index.tpl
  --------------
  {= name: "Peter" =}
  
  <!--{ Show the first character }-->
  {$name.0} 
  
 <!--{ Show the second character }-->
  {$name.1}
  
  {= x: 537 =}
  
  <!--{ Show the first digit }-->
  {$x.0} 
  
 <!--{ Show the second digit }-->
  {$x.1}
    
  <!--{ Spacify the name }-->
  [$name]{$value} [/$name]
  
  <!--{ Multiply the digits of x }-->
  [$x] {$value} * [/$x] = (# [$x] {$value} * [/$x] 1 #)
  
  Output:
  ----------------
  P
  
  e
  
  5
  
  3
  
  P e t e r
  
  5 * 3 * 7 = 105
  
----------------------------
April 03, 2013
----------------------------
- Version 3.7 was released with a serious error that was corrected in the 3.8

- Release the 3.8 version

----------------------------
March 30, 2013
----------------------------
- Improved the interpretation of third parameter of the constructor 
  as a string with the variables's names.
  
  echo new div('index.tpl', array('name' => 'Peter', 'age' => 25, 'sex' => 'M'), 'name,age');

- Release the 3.7 version

----------------------------
March 26, 2013
----------------------------
- Improvement of the speed.
- Improvement of the options arround the __toString method of objects in 3 scopes. See the example below.

  The old policy:
  
  "if an object has implemented the method __toString then be treated as a string" 
  
  It was changed for:
  
  "if an object has implemented the method __toString, you can work with the object as a character string"
  
  
  Example:
    
    index.php
    ---------
    <?php

	include "div.php";
	
	class Product{
		public function __construct($name, $price){
			$this->name = $name;
			$this->price = $price;
		}
	
		public function __toString(){
			return $this->name.' ($'.$this->price.')';
		}
	}

	// The object as string
	echo new div('index.tpl', array("product" => new Product('Banana', 10)));
	
	// Template scope
	echo new div('index1.tpl', array(new Product('Banana', 10)));
	
	// Capsule scope
	echo new div('index2.tpl', array("product" => new Product('Banana', 10)));
	
	// Loop's body scope
	echo new div('index3.tpl', array("products" => array(new Product('Banana', 10))));
	
    ?>
  
  	index.tpl
  	------------
  	{$product}
  	
    Output for index.tpl
    --------------------
    Banana ($10)
    
    index1.tpl
    ----------
	{$value}
	
	is similar to 
	
    {$_to_string}
    
    index2.tpl
    ----------
    [[product
    
    {$value}
	
	is similar to 
	
    {$_to_string}
    
    product]]
    
    index3.tpl
    ----------
    [$products]
	{$value}

	is similar to 

	{$_to_string}
    [/$products]
    	
    Same output for index1, index2 and index3
    -----------------------------------------
    Banana ($10)
    
    is similar to
    
    Banana ($10)
----------------------------
March 24, 2013
----------------------------
- From version 3.6 Div maintains a policy regarding the use of objects: if an 
object has implemented the method __ toString then be treated as a character string.
We are working to improve the policy and avoid unhappy.

	index.php

	<?php

	include "div.php";
	
	class Product{
		public function __construct($name, $price){
			$this->name = $name;
			$this->price = $price;
		}
	
		public function __toString(){
			return $this->name.' ($'.$this->price.')';
		}
	}

	echo new div('index.tpl', array("products" => array(new Product('Banana', 10))));
	?>

	index.tpl
	
	[$products]
		{$value}
	[/$products]

	Output
	Banana ($10)

	We are working to improve the policy and avoid unhappy.
----------------------------
March 22, 2013
----------------------------
- Some functions of PHP are enabled in formulas and conditions. 
  
- Added a new system var named: $div.ascii. This var contain the all chars of ASCII table.

  <?php
  
  include 'div.php';
  
  div::enableSystemVar('div.ascii');
  
  ?>
  
  index.tpl
  -----------
  {$div.ascii.64}
  
  is similar to
  
  (# chr(64) #)
  
  but the replacement is faster than calculation
  
  Output:
  -------
  @
  
  is similar to
  
  @
  
  but the replacement is faster than calculation
    
----------------------------
March 20, 2013
----------------------------
- Added a new feature for programmers: the method changeTemplate()

	<?php
	
	include "div.php";
	
	$tpl = new div('index.tpl', array("title" => "Hello world"));
	
	echo $tpl; // $tpl->show();
	
	$tpl->changeTemplate('index2.tpl');
	
	echo $tpl; // $tpl->show();
	
	?>

- Improvement of the show() method with a new parameter: specific template

  <?php
  
  include "div.php";
  
  $tpl = new div();
  $tpl->title = "Hello world";
  $tpl->show('template.tpl');
  
  ?>
----------------------------
March 18, 2013
----------------------------
- Added new variable's modifiers: 

  {&&var} - rawurlencode
  {'var} - escape unescaped single quotes
  {js:var} - escape quotes and backslashes, newlines, etc.
  {$var:[string format]} - format the value with sprintf PHP function
  
- Added new feature for programmers: custom variable's modifier

  For add a new custom variable's modifier you need call the method:
  
  div::addCustomModifier($prefix, $function)
  
  The parameter $function can be the name of function or the name of static method of a class, for example
 
  div::addCustomModifier('upper', 'MyModifiers::upper');
  
  Example:
  ----------------
  index.php
  
  <?php
  
  function urlpathinfo($value){
  	return str_replace("%2F", "/", rawurlencode($value));
  }
  
  div::addCustomModifier('upi:', 'urlpathinfo');
  
  echo new div('index.tpl',array('url' => 'http://localhost'));
  
  ?>
  
  index.tpl
  -----------
  
  {upi:url}
  
  
  Output
  -----------
  http%3A//localhost

- Added new feature for programmers: the hooks!. The hooks are:

    beforeBuild, afterBuild, beforeParse, afterParse  

    Example:
    
    index.php
    ---------------
    class HomePage extends div{
    
    	public function beforeBuild(){
    		$this->__src = "index";
    		$this->setItem(array(
    			"title" => "Hello World"
    		));
    	}
    }
    
    echo new HomePage();
    
    index.tpl
    ---------------
    <h1>{$title}</h1>
    
    Output
    ----------------
    <h1>Hello World</h1>
    
- Improvement of the setItem method  

----------------------------
March 16, 2013
----------------------------

- Improved the access to object's public methods

	index.php
	-------------------
	<?php
	include 'div.php';
	
	class  Person{
		var $first_name;
		var $last_name;
		
		function __construct($first_name, $last_name){
			$this->first_name = $first_name;
			$this->last_name = $last_name;
		}
		function getCompleteName(){
			return $this->first_name.' '.$this->last_name;
		}
	}
	
	echo new div('index.tpl', array(
		'person' => new Person('John', 'Nash')
	));

	index.tpl
	---------------------
	[[person

		{= cn: ->getCompleteName() =}
		
		First Name: {$first_name}
		Last Name: {$last_name}
		Complete name: {$cn}
	
	person]]
		
	Output
	----------------------
	First Name: John
	Last Name: Nash
	Complete name: John Nash

- Release the 3.6 version

----------------------------
March 13, 2013
----------------------------
- Improved the feature "template vars". Now you can execute the "methods of information".

  Example:
  
  index.php
  ----------------
  <?php
  
  include "div.php";
  
  class MyData {
  
  	  var $somedata = 100;
  	  
  	  function getNames(){
  	  	   return array("Jones", "Pete", "Mark");
  	  }
  }
  
  echo new div('index.tpl', new MyData());
  
  ?>
  
  index.tpl
  ------------------
  
  somedata is: {$somedata}
  
  {= names: ->getNames() =}
  
  The names are: [$names] {$value}  [/$names]
   
  
  Output
  --------------------
  somedata is: 100
  
  The names are: Jones Pete Mark
  
----------------------------
March 13, 2013
----------------------------
- Improved the detection of orphan conditional parts

----------------------------
March 8, 2013
----------------------------
- Update the documentation
- Fixes some bugs of new features
- Release the 3.5 version

----------------------------
February 24, 2013
----------------------------
- New feature: locations!

	Now you can define a diferent locations in your template 
	and put in this locations any content.
	
	
	Example:
	-----------------
	(( top ))
	
	(( any )) Some content here (( any )) 
	
	(( bottom ))
	
	{{top 
		This is the top of the page 
	top}}
	
	{{bottom 
		This is the bottom of the page 
	bottom}} 
	
	{{any
		<br/>
	any}}
	
	Output:
	-----------------
	This is the top of the page 
	
	<br/> Some content here <br/>
	
	This is the bottom of the page
	
- Improvement of the conditional parts: the first and last blank space are removed.

	In Div 1.0 to 3.4:
	---------------------
	
	<b>?$what Hello $what?</b>
	
	Output:
	---------------------
	<b> Hello </b>
	
	From Div 3.5:
	---------------------
	
	<b>?$what Hello $what?</b>
	
	Output:
	---------------------
	<b>Hello</b>
	
----------------------------
February 24, 2013
----------------------------
- New feature: @empty@ tag for list's blocks 

  [$users]
      {$name}
  @empty@
      Show this if list users is empty
  [/$users]

----------------------------
February 19, 2013
----------------------------
- The documentation was updated
- Improved detection of infinite loops on includes and replacements
- Release the 3.4 version

----------------------------
February 17, 2013
----------------------------
- Add new feature: Multiple variable's modifiers

	Syntax:
	----------
	{$varname|modifier1|modifier2|modifier3|...|}

	index.tpl
	----------
	{= word: "ABCDEFG" =}
	
	{$word|0,3|}
	{$word|0,3|_|}
	{$word|0,3|_|^|}
    {$word|0,3|_|^|~2|}
	
	Output
	-------
	ABC
	abc
	Abc
	Ab
	
----------------------------
February 15, 2013
----------------------------
- Fix a critical bug: prevented infinite cycle
- Release the 3.3 version

----------------------------
February 4, 2013
----------------------------
- Fix a bug with {ignore} functionality
- Add new vars for the iterations: $_previous and $_next.
	
	index.tpl
	-------
	{= list: [10,5,7,12,8,8,10,10] =}
	[$list]
		{= _previous:  0 =}
		{= _next:  infinite =}
		{$_previous}..{$value}..{$_next}
	[/$list]	

	Output
	------
	0..1..2
	1..2..3
	2..3..4
	3..4..5
	4..5..6
	5..6..7
	6..7..8
	7..8..9
	8..9..10
	9..10..infinite 

- Algorithm improved: 95% more faster.
- Release the 3.2 version
 
----------------------------
Dec 26, 2012
----------------------------
- Improved date's values detection

----------------------------
Nov 21, 2012
----------------------------
- Update documentation
- Release 3.1 version

----------------------------
Nov 21, 2012
----------------------------
- Detection of recursive inclusion as an error. For example:
	
	index.tpl
	-------------
	<!- This include will not take effect -->
	{% index %}

----------------------------
Nov 19, 2012
----------------------------
- Improved the algorithm of lists/loops/cycles

----------------------------
Nov 16, 2012
----------------------------
- Allowed "intval" PHP function in formulas
 
----------------------------
Nov 4, 2012
----------------------------
- Fix some problems
- Improvement of some mechanisms
- Release the 3.0 version

----------------------------
Sep 7, 2012
----------------------------
- Fix important issue for matchs. Now work the follow example:

	{= list: [
		{
			name: "Banana",
			price: 20,
			shipments: [
				{
					date: "2012-05-09",
					packages: [
						[20, 30, 40]
					]
				}
			]
		},
		{
			name: "Potato",
			price: 40
		}
	] =}
	
	{$list}<br/>
	{$list.0}<br/>
	{$list.0.shipments}<br/>
	{$list.0.shipments.0}<br/>
	{$list.0.shipments.0.adresses}<br/>
	{$list.0.shipments.0.adresses.0}<br/>
	{$list.0.shipments.0.adresses.0.0}<br/>

----------------------------
Sep 2, 2012
----------------------------
- Fix bugs of conditions into loops
----------------------------
Sep 2, 2012
----------------------------
- Fix bugs
- Release the 2.9 version
----------------------------
Aug 30, 2012
----------------------------
- Improvements to the template's vars. Now you can do this:

  article.tpl
  -----------------
  
  <h1>{$title}</h1>
  <p>{$body}</p>
  
  
  page.tpl
  ------------------
  {= content: article =} <!--{ the engine test 
                               if the file PACKAGES."article.".DIV_DEFAULT_TPL_FILE_EXT exists,
                               and then load the data from this }-->
  
  Header
  
  {$content}
  
  Footer
  
----------------------------
Aug 20, 2012
----------------------------
- Delete the DIV_CLASS_NAME constant: now is more simple to change the name of 
  div class. Simply change the name of div class, no more!
   
- Fix problem of template vars's scope. The inheritance mechanism is more simple now:

	-------------------
	parent.tpl
	-------------------
	<!--{ define a block }-->
	{= block1: 
	
	...some code here...
	
	=}
	
	<!--{ show the block }-->
	{$block1}
	
	-------------------
	child.tpl
	-------------------
	<!--{ re-define the block1 (note that the block1 var is protected) }-->
	{= *block1:
	
	...some another code here...
	
	=}
	
	<!--{ extends }-->
	{% parent %}
	
----------------------------
Aug 18, 2012
----------------------------
- The algorithm of text summary was improved.
- New feature: IDE's friendly marks <!--| ... |-->
	
	Example:
	
	<!--|[$products]|-->
	
		<p>Name: {$name}</p>
		<p>Price: {$price}</p>
		
		<!--| {?( {$price} > 10 )?} |-->
			Expensive product
		<!--| {/?} |-->
		
	<!--|[/$products]|-->
	
	Is similar to:
	
	[$products]

		<p>Name: {$name}</p>
		<p>Price: {$price}</p>
		
		{?( {$price} > 10 )?}
			Expensive product
		{/?}
	
	[/$products]

----------------------------
Aug 17, 2012
----------------------------
- Change the type of method of getSystemData from public to static
----------------------------
Aug 16, 2012
----------------------------
- Change the type of method of mixedBool from public to static.
- Release the 2.8 version

----------------------------
Aug 09, 2012
----------------------------
- Added new feature: Relative paths for include and preprocessed templates.

----------------------------
Aug 07, 2012
----------------------------
- Freed of the function json_encode of PHP and corrected some errors of this function.

----------------------------
Aug 05, 2012
----------------------------
- Fixed bugs:
	- If you don't define a variable, the expression is FALSE:

	Example:
	<?php
	
	echo new div("index.tpl", array("var1" => "some")); // var2 is missing
	
	index.tpl
	----------
	
	{?( "{$var1}" == "some" && "{$var2}" == "another" )?)
	Part 1
	@else@
	Part 2
	{/?}
	
	Output:
	----------
	Part 2
	
	- If you don't define a variable, the formula will be ignored:
	
	Example:
	<?php
	
	echo new div("index.tpl", array("var1" => 2)); // var2 is missing
	
	index.tpl
	----------
	
	(# {$var1} + {$var2} #)
	
	Output:
	----------
	
	(# 2 + {$var2} #)

----------------------------
Aug 03, 2012
----------------------------
- Fixed bugs
- Release the 2.7 version

----------------------------
Jul 30, 2012
----------------------------
- Fixed bugs
- Add new feature for json encode.

	Example:
	
	<?php
	
	echo new div("index.tpl", array("numbers" => array(1,2,3,4,5)));

	{json:variable}
	
	Outoput:

	[1,2,3,4,5]
	
----------------------------
Jul 26, 2012
----------------------------
- Fixed bugs
- Release the 2.6 version

----------------------------
Jul 08, 2012
----------------------------
- Added new features for replacements: multiple replacements

	<!--{ First, define the list of replacements as array of array}-->
	
	{= replac: [
		['search this string',      'replace with this string',              false],   <!--{ str_replace }->
		['search regular expresion','replace with this string or expresion', true]     <!--{ preg_replace }-->
	] =]
	
	<!--{ Next, define the block that the replacement will be take effect }-->
	
	{:replac}
	
	... some code here ....
	
	{:/replac}
	
	
	Example:
	----------
	
	{= php-code: [
		['echo ', '<b>echo</b> '],
		['/\'([^\'](?:\\.|[^\\\']*)*)\'/i', '<span class="string">\'$1\'</span>',true]	
	] =}
	
	{:php-code}
	
	<?php
		echo 'hello world';
	?>
	
	{:/php-code}
	
	Output:
	----------
	
	<?php
	    <b>echo</b> <span class="string">'hello world'</span>
	?>
- Fixed bugs

----------------------------
Jul 02, 2012
----------------------------
- Add new features for performance: enable and disable system var

    div::enableSystemVar("div.session");
    div::disableSystemVar("div.server");
    ...

----------------------------
Jun 30, 2012
----------------------------
- Fixed bugs
- Added new funcionality for log: Save the steps of the parser into log file

	// Save the steps of the parser into log file
	div::logOn("mylogfile.log");
	...
- Release the 2.5 version

----------------------------
Jun 13, 2012
----------------------------
- Fixed bugs
- Added new functionality: html to text 
  
  {txt}	... some html code here ..  {/txt}
  {txt} width => ... some html code here {/txt}
  
  The width integer parameter, wrap the text with this width.

----------------------------
Jun 8, 2012
----------------------------
- Fixed bugs
- Added new functionality: text wrap

	If you needed the wrap of a text with a specific width, you can do this:

	{$body:/200} 
	
	If you use the br modifier, the text wrap take effect on the web:
	
	{br:body:/200}	
- Release the 2.4 version

----------------------------
May 27, 2012
----------------------------
- Added new functionality: show the teaser of a text. Similar to get a substring of text:

	{$mytext:100} <!--{ Return the first 100 chars of $mytext }-->
	
	If you add the symbol ~, you can retrieve the teaser of $mytext:
	
	{$mytext:~100} <!--{ Return the $mytext truncated approximately 
	                     with 100 chars and the words without breaking }-->
----------------------------
May 22, 2012
----------------------------
- NEW: Allow to asign a program var to a template var. For example:

	index.php
	-------------
	<?php
	...
	echo new div("index.tpl", array("some" => 5));
	...
	?>
	
	index.tpl
	-------------
	
	{= another: $some =}
	
	{$another}
	
	Output
	-------------
	5
	
	Also you can asign to the specific property of template var:
	
	index.tpl
	--------------
	{= someobj: {
	   property: "$some" <!--{ The value has to be a string }-->
	} =}
	
	{$someobj.property}

- Release the 2.3 version

----------------------------
May 19, 2012
----------------------------
- NEW: Allowed functions. Now the programmer can enable functions 
	of or written in PHP so that the designer can use them in the templates.

	<?php
	...
	function sum($x,$y){
		return $x+$y;
	}
	
	div::setAllowedFunction("suma");
	
	echo new div("index.tpl");
	...
	?>
	
	index.tpl
	-----------------
	(# sum(2,3) #)

- NEW: Add new item to list or set a property of object:

	TEMPLATE
	-----------------------------------------
	... some more code here ...

	{= list: [1,2,3] =}
	{= customer: {
		name: "Peter",
		phone: "222-444555"
	} =}
	
	... some more code here ...
	
	{= list[]: 4 =}                         <!--{ now list = [1,2,3,4] }-->
	{= customer[address]: #221 street 45 =} <!--{ now customer.address is set }-->
	
	... some more code here ...
	
	{$list.4}
	
	{$customer.address}
	
----------------------------
May 14, 2012
----------------------------
- Fixed bugs
- Release 2.2 version

----------------------------
May 13, 2012
----------------------------
- New functionality: assign to design vars the result of method! If the programmer 
	implemented a class that inherits of div, then the designer can use the methods of this
	class. 
	
	Syntax for template:
	
	{= variable: ->methodName(params as JSON) =}
		
	For example:
	
	Page.php
	--------------------------
	<?php
	class Page extends div{
	
		public function getSum($params){
			return $params->x + $params->y;
		}
		
		public function getLetters(){
			return array("A","B","C");
		}
		
	}
	?>
	
	Page.tpl
	---------------------------
	
	{= sum: ->getSum(x: 20, y: 30) =}  <!--{ The value should begin with the operator "->" }--> 
	
	{$sum}
	
	{= lts: ->getLetters() =}
	
	[$lts] {$value} [/$lts]
	
	Output
	---------------------------
	50 A B C
----------------------------
May 12, 2012
----------------------------

- Now the definition of data in templates is similar to set a global var in the programmer side
	and you can re-refine this data every time in the template and now the sequence of the operations 
	is not ignored.  The variables have arrived!
	
	For example:
	
	------------------------------
	TEMPALTE
	------------------------------
	
	<!--{ Using cycles to calculate the price of invoice }-->
	
	{= products: [
		{price: 10, qty: 5},
		{price: 20, qty: 2}
	] =}
	
	{= invoice_price: 0 =}
	{= tax: 20 =}
	
	[$products]	{= invoice_price:  (# {$invoice_price} +{$qty} * {$price} #) =}	[/$products]
	
	Invoice price: {#invoice_price:2.#}<br>
	
	Tax: {#tax:2.#}<br>
	
	<!--{ Sum the tax to invoice_price and change the value of invoice_price variable }-->
	
	{= invoice_price: (# {$invoice_price} + {$tax} #) =}
	
	Total price: {#invoice_price:2.#}

	------------------------------
	OUTPUT
	------------------------------
	Invoice price: 90.00
	Tax: 20.00
	Total price: 110.00
	
	
- Fixed bugs

- Add new functionality: capsules!, with the symbol of Div logo!.... of course!
	
	Now you can create capsules inside the insole to reduce the code and to facilitate 
	the work with objects and arrangements. A capsule consists on a block that fulfills 
	the following syntax:
	
	[[variable
	
	... In this section you can use the properties of variable if it is 
	an object or their keys if it is an array ...
	
	variable]]
	
	For example:
	
	index.php
	-------------------
	
	<?php
	...
	echo new div("index.tpl", array(
		"product" => array(
			"name" => "Banana",
			"price" => 20.4
		)
	));
	...
	?>
	
	index.tpl
	---------------------
	
	[[product
		Name: {$name} <br>
		Price: {$price} <br>
	product]]
	
	Enjoy!
	
- Add new feature for iterations functionality: now you can specify a STEP for iteration. 

	Syntax:
	------------------
	
	Variant 1:
	
	[:from,to,var,step:]
	  
	Variant 2:
	
	[:from,to,step:]
	
	Example 1:
	----------------
	
	Template:
	
	[:1,10,2:] {$value} [/]
	
	Output:
	
	1 3 5 7 9
	
	Example 2:
	-----------------
	
	[:1,10,i,2:] {$i} [/]
	
	Output:
	
	1 3 5 7 9
	
	Example 3:
	-----------------
	[:10,1,i,2:] {$i} [/]
	
	10 8 6 4 2

- Another way to define the iteration var with high priority. Now the follow templates are similars:

	Template 1:
	
	[:1,10,x:] .... [/]
	
	Template 2:
	
	[:1,10:] x => .... [/]
	
	The follow example shows the priority of this new way:
	
	Template:
	
	[:1,10,x:] y => {$y} {$x} [/]
	
	Output:
	
	1 {$x} 2 {$x} 3 {$x} 4 {$x} 5 {$x} 6 {$x} 7 {$x} 8 {$x} 9 {$x} 10 {$x}
	 
----------------------------
May 09, 2012
----------------------------
- Added aggregate functions for the lists: sum, avg, min, max, and the default count function
    
    Now the designer can calculate another statistics from lists, for example:
    
    index.php
    ------------------------------------
    <?php
    
    ...
    echo new div("index.tpl", array(
        "products" => array(
                array("name" => "Banana", "price" => 20.5),
                ....
                ...
                ...                        
        ),
        "values" => array(10,20,30,40,50,60)
    ));

    ...
    ?>
    ------------------------------------
    
    index.tpl
    ------------------------------------
    Minimum price: {$min:products-price}
    Maximum price: {$max:products-price}
    Average of prices: {$avg:products-price}
    Sum of prices: {$sum:products-price}
    Count of products with price: {$count:products-price} or {$products-price}
    
    <!--{ If the list is a numeric list you can ... }-->
    
    Minimum value: {$min:values}
    Maximum value: {$max:values}
    Average of values: {$avg:values}
    Sum of values: {$sum:values}    
    ------------------------------------
    
- Added a new constant constant DIV_CLASS_NAME for define the name of de superclass of div.
    Now the programmer can change the name of the div class to avoid possible
    collisions the class's names of his application.
    
- Added a new functionality: default replacements by variable.
    
    Now the programmer and the designer can define the default replacements for values 
    by variable. For example:
    
    Set the default replacement in PHP:
    
    <?php
    ...
    div::setDefaultByVar("kept", true, "YES");
    div::setDefaultByVar("kept", false, "NO");
    ...
    
    echo new div("index.tpl", array("kept" => true));
    ?>
    
    
    Or set the default replacement in the template:
    
    ...
    {@["kept", true, "YES"]@}
    {@["kept", false, "NO"]@}
    ...

----------------------------
May 08, 2012
----------------------------
- Added a new functionality: pre-processed parts. 
    
    Now you can pre-processed by div any part in template. The pre-processing 
    are similar to include, but the pre-processing parse the code before including it.
    
    Include is: {% part.tpl %}  (include and then parse)
    Pre-processing is {%% part.tpl %%} (parse and then include)
    
    IMPORTANT!: The pre-processing have a priority with regard to the list interations.
    
- Release the 2.1 version

----------------------------
May 06, 2012
----------------------------
    
- Enable two new properties for PHP developers: $__src and $__packages.
    See the follow example:

    <?php
    
    include "div.php";
    
    class Page extends div{
        var $__src = "tpl/Page.tpl"; // Set the template file location;
        var $__packages = "./tpl/"   // Set the folder of templates, see the slash at the end!
    }
    
    ...
    ?>
    
- If you want that the names of the files have a prefix, specify it in constant 
    PACKAGES or in the property $__packages of a class that extends the div. See 
    the following examples:
    
    Example 1
    ----------------
    <?php
    
    define("PACKAGES", "./page_");
    
    include "div.php";
    
    $tpl = new div("index"); // Div load the template from ./page_index.tpl
    
    ... 
    
    ?>
    
    Example 2
    ----------------
    <?php
    
    include "div.php";
    
    class MainPage extends div{
        $__packages = "./page_"; // Div load the template from ./page_MainPage.tpl
        ...
    }
    ...
    ?>
    
- Add new constant DIV_DEFAULT_TPL_FILE_EXT for define a template file extension.
    You can define this constant BEFORE include the div.php script. The default value 
    for this constant is the string "tpl". For example:
    
    <?php
    
    define("DIV_DEFAULT_TPL_FILE_EXT", "phtml"); // Extension without the dot
    
    include "div.php";
    
    ...
    ?>

- Add new constant DIV_DEFAULT_DATA_FILE_EXT for define a data file extension.
    You can define this constant BEFORE include the div.php script. The default value 
    for this constant is the string "json". For example:

    <?php
    
    define("DIV_DEFAULT_DATA_FILE_EXT", "data"); // Extension without the dot
    
    include "div.php";
    
    ...
    ?>
    
- Implement the show() method.
    
    <?php
    
    $tpl = new div("index.tpl");
    $tpl->show();

    ?>
    
- If you don't pass the value of $src for the div class constructor, then 
    Div assumes that $src is the name of the class :)
   
   <?php
   
   class Page extends div {};
   
   echo new Page(); // Div try to load the template code from "Page.".DIV_DEFAULT_TPL_FILE_EXT file.
   
   ?>
   
- Enable the div extends for OOP in the programmer side. The name of the properties 
    should not begin with __ (double underscore). See the follow example:

    Page.tpl
    -------------
    <h1>{$title}</h1}
    <p>{$body}</p>
    
    Page.php
    -------------
    <?php
    
    class Page extends div{
        var $title;
        var $body;
        var $__some; // This property will be ignored because its name begins with __
    } 
    
    ?>
    
    index.php
    -------------
    <?php
    
    include "div.php";
    include "Page.php";
    
    $page = new Page(); 
    $page->title = "Hello world";
    $page->body = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor..";
    $page->show(); // or echo $page;
    
    ?>

----------------------------
April 21, 2012
----------------------------
- Change mixedBool() and parseMatch() methods for trim the string values.

----------------------------
April 14, 2012
----------------------------

- Added a new variable for the cycles: $_order, that is $_index + 1. 
   The index begins with 0. The order bigins with 1. This is util when you
   need build a ordered list without <OL> tag and reused item template. 
   
   For example:
   
   The ordered list:
   -------------------------------
 
   [$list]
           {% reused %}
   [/$list]
   
   The reused template reused.tpl:
   -------------------------------
   
   ?$_order {$_order}. $_order? Name: {$name} Address: {$address}
   
   On the other hand if you use the following template for reuse.tpl, 
   the first order number will be hidden and the template is more complicated:
   
   ?$_index (# {$_index} + #). $_index? Name: {$name} Address: {$address}

- Added new variable's modifiers: html and br

    {html:variable} convert all applicable chracters to HTML entities (see the 
    documentation of htmlentities() PHP function)
    
    {br:variable} convert all \n to <br/>

- Fixed bugs of recursivity and recovered the high priority of variables into the cycles.

- Release the 2.0 Version

----------------------------
April 09, 2012
----------------------------

- Fix some grave bugs of iterations functionality and other improvements.

- Added a new functionalitites:
    - Custom item variable for lists and mark =>. For example:
    
    {= clients: [
        {
            name: 'John',
            products: [{name: 'Banana', price: 1.2},{name: 'Potato', price: 1.3}]
        }
        ]
    =}
    
    [$clients] client =>  <!--{ "client" is the item variable }-->
        [$products]
            {$client.name} - {$name}<br>   <!--{ several properties with the same 
                                                 name in several iteration levels }-->
        [/$products]
    [/$clients]
    
    
    - Custom item variable for iterations: you now can specify the iteration variable. For example:

        [:1,100,i:]
            The current value is {$i}
        [/]
        
    - Nested iterations. The following example...
        
        [:1,10,i:]
            [:1,10,j:]
                {$i} * {$j} = (# {$i} * {$j} #) <br>
            [/]
        [/]
    
        ...is similar to:
        
        <?php
        
        for($i=1; $i<=10; $i++)
            for($j=1; $j<=10; $j++)
                echo "{$i} * {$j} = ".($i * $j). "<br>";
        
        ?>
    
    - New variable for iterations and lists's cycle
    
        - $_list, that it contains the list's name.
            
          For example:
        
            [$products]
                {$_list} <!--{ This output 'products' }-->
            [/$products]
        
             Div associates a name to each iteration that you define. With this new functionality 
             you can know the name of the iteration inside the cycle of the iteration.
             
             Also, if you use the recursion, you can work now with the name of the list thanks to 
             this new variable that doesn't collapse with a variable inside the cycle.
             
             For example:
             
             {= list: "products",
                products: [
                        count: 3,
                        list: ["Banana", "Potato" , "Rice"]
                ]
             =}
             
             [{$list}]
                 {$_list} <--{ This shows the same thing that {$list} that is 'products' ... }--> 
                 {$list}  <--{ This shows the count of items of [$list] }-->
                 [$list]  <--{ and this list is a list into the parent cycle }-->
                     {$value}, <--{ This shows Banana, Potato, Rice }-->
                 [/$list]
             [/{$list}]

        - $_item, that it contains the list's item
        
          For example:
        
            {= products: [
                {
                    name: "Banana",
                    price: 1.2
                },
                {
                    name: "Potato",
                    price: 1.3
                }
            ] =} 
    
            [$products]
                {$_item} <!--{ This show the count of object's properties }-->
                {$_item.price} is similar to {$price}
            [/$products]
            
        - $_key, that it contains the item's key
        
            For example:
            
            [$products]
                [$_item] <!--{ showing the all properties of object }-->
                    {$_key}: {$value} <!--{ showing "property: value" }-->
                [/$_item] 
            [/$products]

- Release the 1.9 version
        
----------------------------
April 07, 2012
----------------------------
- Recovering a lost functionality. In version 1.5 to make the algorithms more efficient 
we made a mistake and break functionality of the clean the orphan parts. Now in version 1.8
is working again just as quickly.

- Fix some grave bugs.

- Added new funtionalities:
    - If a var contain an object, {$var} will be repleace with the count of properties
    - Sub matches: now you can write ($var:0,20} or {$var:20} to replace this mark with
      substr($var, 0, 20);
      
      With this new functionality you can chop a text in half thank 
      to the formulas, for example:
      
      {$text: (# {%text} / 2 #)} <!--{ first half }-->
      {$text: (# {%text} / 2 #), (# {%text} / 2 #)} <!--{ second half }-->
      
      You can also make use of the variable's modifiers:
      
      {^text: 1} <!--{ first character to uppercase }-->
      
- Release the 1.8 version.

Note: The new added features made a little slower the engine. We are working 
in the improvement of the algorithms.

April 05, 2012
----------------------------
- Recovering a lost functionality. In version 1.5 to improve the algorithms made a 
  mistake and break functionality of the Formulas. Now in version 1.7 is working again 
  just as quickly.
  
- Added new functionality for programmers in the constructor of div class with 
  new parameter: IGNORE SOME VARIABLES. Example:

// ignoring the "name" variable
echo new div("index.tpl", array("name" => "Salvi", "age" => 25), array("name"));

- Prevent a bugs with length two first parameters as filenames, in the div constructor
- Release the 1.7 version

April 2, 2012
----------------------------
- In version 1.5 to improve the algorithms made a mistake and lost functionality 
  of the iterations of Lists, which is the priority of an item variable under way, 
  with the same name of a variable outside the loop. Now in version 1.6 is working 
  again just as quickly.
  
- Release the 1.6 version

March 30, 2012
----------------------------
- The algorithm was improved. Div is faster now.
- Fixed bugs.
- A new variable's modifier was added to encode URL. For example:
    
    {&variable}

- Release the 1.5 version

March 28, 2012
----------------------------

- Fixed bugs of blocks of conditions
- Add new feature named ITERATIONS.

    Example:

    [:1,5:] {$value} [/]

    Output:

    1 2 3 4 5
- Release the 1.4 version

March 23, 2012
----------------------------
- Prevent the errors of formulas
- Prevent the errors of conditions
- Fix important bug of @else@ mark of conditions into other conditions and conditionals
- Release the 1.3 version

March 22, 2012
----------------------------
- The @break@ mark
    Add break mark for breaking the loops. The position of 
    break mark in the block are relevant!
    
    Example:
    
    [$products]
        {?( {$_index} == 3 )?} <hr> @break@ {/?}
        {$value}<br>
    [/$products]
    
- Release the 1.2 version

March 15, 2012
----------------------------
- Fixing some several issues of conditional parts!

- Release the 1.1 version