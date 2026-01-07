This feature is for perform a N iterations of a loop. The iterations are loops that increment a variable in each cycle. The value of this variable can be accessed by **$value**. You can also specify the name of the variable and the steps of the increments.

Syntax:

```
[:from,to,var,step:]
	... some code here ...
[/]
```

Example:

index.tpl

```

[:1,10:] {$value} [/]
[:1,10,x:] {$x} [/]
[:1,10,x,2:] {$x} [/]
```

Output:

```
 1  2  3  4  5  6  7  8  9  10 
 1  2  3  4  5  6  7  8  9  10 
 1  3  5  7  9 
```

