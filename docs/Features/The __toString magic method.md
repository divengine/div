If an object has implemented the method __toString, you can work with the object as a character string in three possible scopes: template's scope, loop body's scope and capsule's scope. You can use two possible variables to put the content returned by the implemented __toString() method:

- The variable **$_to_string** in template's scope.
- The variables **$_to_string** and **$value** in capsule and loop's body scopes.
    

Suppose that you have the following class:

Person.php

```
	
class Person{
		
	public function __construct($first_name, $last_name){
		$this->first_name = $first_name;
		$this->last_name;
	}
		
	public function __toString(){
		return $this->first_name." ".$this->last_name;
	}
	
}

```

Then, the following examples show the use of the variables:

Example 1: Template's scope

index.php

```

	
include 'div.php';
	
include 'Person.php';
	
echo new div('index.tpl', new Person("Albert", "Einstein"));

```

index.tpl

```

{$_to_string}

```

Outpput

```

Albert Einstein

```

Example 2: Loop body's scope

index.php

```
<?php
	
include 'div.php';
	
include 'Person.php';
	
echo new div('index.tpl', [
		"persons" => [
			new Person("Albert", "Einstein"),
			new Person("John", "Nash")
		]
));

```

index.tpl

```

If Person not have a $value property:
	
[$persons]
	{$value}
[/$persons]
	
You can always use the variable $_to_string:
	
[$persons]
	{^^^_to_string}
[/$persons]

```

Output

```

If Person not have a $value property:
	
	Albert Einstein
	John Nash
	
You can always use the variable $_to_string:
	
	ALBERT EINSTEIN
	JOHN NASH

```

Example 3: Capsule's scope

index.php

```
<?php
	
include 'div.php';
	
include 'Person.php';
	
echo new div('index.tpl', [
	"person" => new Person("Albert", "Einstein")
]);

```

index.tpl

```

[[person
	
 {$_to_string}
	
 {^^^value}
	
person]]

```

Output

```

Albert Einstein
	
ALBERT EINSTEIN

```