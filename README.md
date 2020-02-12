# Div PHP Template Engine 5.1.6

Div is a [template] engine and [code generator tool] written in [PHP](http://php.net/) and
developed since 2011, that allows the separation of labor between developers and designers,
and improve the software development based on [Generative Programming], [Model Driven Architecture]
and [Meta programming].
 
As the designer or developer built templates with some specific tags, other 
developers or designers uses the template to replace these tags with data or more 
template code. Div have a compact, flexible and descriptive syntax for templates:

* "compact" means "a minimum of code for template language" 
* "flexible" means "you can create dialects of template language"
* "descriptive" means "template language talk by herself"

This project is the main piece of the organization [Div Software Solutions](https://divengine.com),
following the philosophy of "build more with less" and "divide the problem and not to the people". 
To obtain this goal, Div propose the [code generation based on templates], following this rules:

   1. The **model** will have all information about _"what do you want to do"_
   2. The **templates** will have all information about _"what results you expect"_
   3. The **engine** if the black-box that achieve the _"how to make it"_. 

Then, exists these basics operations:
  - Operation #1. **Compile** a template with models and save the result
  - Operation #2. **Transform** a model to another, reusing Operation #1.
  - Operation #3. **Compose** different results, using the engine and other tools.

When you have white these rules, then you will be able to use your imagination and you 
will be able to achieve the following results, and any combination of them:

   1. Avoid repetitive tasks in the programming
   2. Save your models and reuse them in another project
   3. Scaling your project based in your models.
   4. Migrate your project to different technology
   5. Take advantage of your models and develop parallel versions
   6. Expand your application in other platforms and devices
   7. Improve the performance of your application
   9. Make a documentation of your work
   10. Involve the "non technician" people in the development of the project

Install
-----------------------
```
composer require divengine/div
```

Upgrade
-----------------------

```
composer upgrade
```

Basic example:
-------------

index.php
```php
<?php

use divengine;

include "vendor/autoload.php";
// include "path/to/div.php";

echo new div('index.tpl', ['message' => 'Hello Universe']);
```

index.tpl
```
{$message}
```

Your own dialect:
----------------
index.php
```php
<?php

define ("DIV_TAG_REPLACEMENT_PREFIX", "<");
define ("DIV_TAG_REPLACEMENT_SUFFIX", "/>");
define ("DIV_TAG_MODIFIER_SIMPLE", "show:");

include "div.php";

echo new div('index.tpl', ['message' => 'Hello Universe']);
```

index.tpl
```
<show:message/>
```

Of course, the replacement of tags is a basic functionality. More extensions 
of the substitutions exist, for example, replace repeatedly N times, replace 
conditionally, among others.

**Loops**:
```
[$products]
	Name: {$name}
	Price: ${#price:2#}
[/$products]
```

**Conditions**
```
?$login
	Show login form
@else@
	Show content
$login?
```

**Include**
```html
<html>
	<head>
		{% head %}
	</head>
	<body>
		{% body %}
	</body>
</html>
```

**Locations and inheritance**

layout.tpl:
```html
<html>
	<head>
		(( head-top ))
		{% head %}
		(( head-bottom ))
	</head>
	<body>
		(( body-top ))
		{% body %}
		(( body-bottom ))
	</body>
</html>
```

home.tpl:

```html
{% layout.tpl %}

{{head-top {% google-analitycs.tpl %} head-top}}

{{head-bottom {% home-css-files.tpl %} head-bottom}}

{{body-bottom {% home-js-files.tpl %} body-bottom}}
```

The programmer creates an instance of a class with 2 parameters: the first 
is the designer's code or name of the file that he built, and the second is 
the information represented by an array, objects, or a combination of arrays 
and objects to replace the design's tags. The array's indexes, array's keys 
and object's properties must correspond with the design's tags.
	
The designer work in text files and use various types of tags: simple 
replacements, lists or loops, iterations, conditional parts, separating the 
design into different files, default replacements, and so on.

## The parser

Div's parser work with 3 ideas:
#### Parse only that can be parse, else, don't touch.

If in the template exists a tag, for example, a simple replacement {$name}, and the programmer don't especify data for the variable $name, the engine will ignore this template's code, and don't show an error.

#### Parsing until exists a template's code that can be parsed

The parser don't stop until in the code some piece need be parsed.

#### Parsing with some syntax's rules

Some piece of codes will be parsed before others piece of codes.

## The syntaxis
The syntax of Div is **compact** and **adaptable**. If the programmer wants 
to do a loop, the designer only needs to know a name for that loop, and if 
he wants to hide a part of the GUI, the designer is only responsible for 
tag the part that will be hidden or displayed conditionally. If the designer 
is a expert in other template engine, he can use a dialect to facilitate 
their work. 

## Reasons
Our reasons? Div is developed with the philosophy of the knowledge reused. 
Of course, Div is released in time of recognized template engines that are 
widely used. For this reason, Div develop a minimum new knowledge so that 
the developers can quickly become familiar with this engine and they can 
understand when, how and why to use it.

The features are added if it is really necessary. That is, if there is a need 
to add another functionality, we first analyzed whether there is a mechanism 
to resolve this functionality, and then we publish an article that explains 
how to implement this mechanism.
	
The argument to develop Div was obtained from various tests with PHP and we 
concluded that it is faster replace the portions of string than includes of 
PHP scripts.

The fact remains, that the replacement of substrings is a fast process but 
it require more memory. However, this consumption is so small that it is 
worthwhile the sacrifice.
	
Div development is to avoid creating a cache system because we believe that 
it is unnecessary according	to their characteristics as an engine. A learning 
system can be sufficient: it can prevent the repeated processing of the same 
templates.
	
Finally, it is known that the most popular engines are composed of more than 
one file, classes and libraries. Div sought since its inception, the 
implementation of everything in one class, in a single file. This allows easy 
adaptation to existing development platforms.

## Goals?
One class, one file!, considering the template like an object, 
create a minimum of highly descriptive syntax, avoid a cache system, improve 
the algorithms, reuse the knowledge, write mechanisms and extend!.
	
Possibilities for developers? The designer carries out its work in text 
files and then the designer can use different tags. Div does not provide for 
the design obtrusive code. All that is programmed in the templates has a single 
goal: design.

Possibilities for the programmer? The programmer creates an instance of div 
class, specifying in its constructor, the template	created by the designer 
and the information that will be displayed.
	
## References
For more information visit:

- [Website of Div Software Solutions](https://divengine.github.io)
- [Wiki of Div PHP Template Engine](https://github.com/divengine/div/wiki)

Enjoy!

-- 

@rafageist

Eng. Rafa Rodriguez
rafageist@hotmail.com
https://rafageist.github.io