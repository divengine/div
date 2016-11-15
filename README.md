# Div PHP Template Engine

by Rafa Rodríguez (rafageist86@gmail.com)

Div is a template and language engine for PHP developed by 
the Eng. Rafa Rodriguez since 2011, that allows the separation of labor between 
the programmer and the designer. 
As the designer built templates with some specific tags, the programmer uses 
the template to replace these tags with the information. Div have a compact, flexible 
and descriptive syntax for template's designers.

* "compact" means "a minimum of code for language template" 
* "flexible" means "you can create language dialect"
* "descriptive" means "template language talk by herself"

Basic example:
-------------

index.php
```
<?php

include "div.php"

echo new div('index.tpl', ['message' => 'Hello Unverse']);
```

index.tpl
```
{$message}
```

Of course, the replacement of tags is a basic functionality. More extensions 
of the substitutions exist, for example, replace repeatedly N times, replace 
conditionally, among others.
	
The programmer creates an instance of a class with 2 parameters: the first 
is the designer's code or name of the file that he built, and the second is 
the information represented by an array, objects, or a combination of arrays 
and objects to replace the design's tags. The array's indexes, array's keys 
and object's properties must correspond with the design's tags.
	
The designer work in text files and use various types of tags: simple 
replacements, lists or loops, iterations, conditional parts, separating the 
design into different files, default replacements, and so on.
	
The syntax of Div is very compact. If the programmer wants to do a loop, the 
designer only needs to know a name for that loop, and if he wants to hide a 
part of the GUI, the designer is only responsible for tag the part that will 
be hidden or displayed conditionally.

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

Goals? One class, one file!, considering the template like an object, 
create a minimum of highly descriptive syntax, avoid a cache system, improve 
the algorithms, reuse the knowledge, write mechanisms and extend!.
	
Possibilities for the designer? The designer carries out its work in text 
files and then the designer can use different tags. Div does not provide for 
the design obtrusive code. All that is programmed in the templates has a single 
goal: design.

Possibilities for the programmer? The programmer creates an instance of div 
class, specifying in its constructor, the template	created by the designer 
and the information that will be displayed.
	
For more information visit:
    - http://divengine.com
    - https://divengine.github.io/div
    - https://github.com/divengine/div/wiki

Enjoy!

rafa