The information or content that it is passed to the constructor of the div class, can be an object and/or it can contain objects and you can access to the methods of these objects. The access to those methods to obtain information depends on the context or scope in which is you working.

Example: "template scope"

index.php

```
<?php
	
include 'div.php';
	
class MyData{
		
	var $values;
	
	function __construct($values){
		$this->values = $values;
	}
		
	public function implode(){
		return implode(",",$this->values);
	}
		
}
	
echo new div('index.tpl', new MyData(["A","B","C","D"]));
```

index.tpl

```

{= data: ->implode() =}

{$data} 

```

Output

```

A,B,C,D

```

Example: "capsule scope"

index.php

```
<?php
	
include 'div.php';
	
// create a class ...
	
class MyString{
		
	var $value;
		
	function __construct($value){
		$this->value = $value;
	}
		
	public function upper(){
		return strtoupper($this->value);
	}
		
}
	
// using the class
	
echo new div('index.tpl', array('name' => new MyString('peter')));

```

index.tpl

```

[[name
	  {$value}
	  
	  {= up: ->upper() =}
	  
	  {$up}
name]]
```

Output

```

peter
PETER

```

#### Example: accesing to any var/object method (new from 4.7)

**index.tpl**

```

{= up: ->name.upper() =}
{$up}

```

Output

```

PETER
```

#### Example: "loop's body scope"

index.php

```
class Person{
		
	var $first_name;
	var $last_name;
	
	function __construct($first_name, $last_name){
		$this->first_name = $first_name;
		$this->last_name = $last_name;
	}
	
	public function getName(){
		return $this->first_name.' '.$this->last_name;
	}
	
}

echo new div('index.tpl', [
	'people' => [
		new Person('John', 'Nash'),
		new Person('Albert', 'Einstein'),
		new Person('Jacque', 'Fresco')
	]
]);
```

index.tpl

```

[$people]
	{= complete_name: ->getName() =}
	
	First name: {$first_name}
	Last name: {$last_name}
	Complete name: {$complete_name}
	
[/$people]

```

Output

```

First name: John
Last name: Nash
Complete name: John Nash
	
First name: Albert
Last name: Einstein
Complete name: Albert Einstein
	
First name: Jacque
Last name: Fresco
Complete name: Jacque Fresco

```

[[Hooks]]
