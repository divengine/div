If you want that the engine ignore some variables in template, specify the variables as a list of names in the third parameter of constructor:

```php
/* Third parameter as array */
	
echo new div('index.tpl', ['name' => 'Peter'], ['name']);
	
/* Third parameter as string  */
	
echo new div('index.tpl',['name' => 'Peter', 'age' => 25, 'sex' => 'M'], 'name,age');
```
