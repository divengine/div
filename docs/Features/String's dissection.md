The scalar values as a complex values. All the scalar values can be used as a strings. Then, the strings can be used like complex values, that is to say, as group of characters. For example:

```
{= name: "Peter" =}
	  
<!-- Show the first character -->
{$name.0} 
	  
<!-- Show the second character -->
{$name.1}
	  
{= x: 537 =}
	  
<!-- Show the first digit -->
{$x.0} 
	  
<!-- Show the second digit -->
{$x.1}
	    
<!-- Spacify the name -->
[$name]{$value} [/$name]
	  
<!-- Multiply the digits of x -->
[$x] {$value} * [/$x] = (# [$x] {$value} * [/$x] 1 #)
```

Output

```
P
	 
e
	  
5
	
3
	
P e t e r
	  
5 * 3 * 7 = 105
```

