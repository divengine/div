Each sub-parser is processed in some different moments. At the moment, the events are: beforeParse, afterInclude and afterParse. Now in the templates's code you can specify when a sub-parser will be executed. The moment, or the event, can be specified in the template as following example:

index.tpl

```
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

```

other.tpl

```
{parse:beforeParse}
	Other name: {$name}
{/parse:beforeParse}
```

Output

```
Name: Peter
Product name: banana
Other name: banana
Name: Peter
Product name: potato
Other name: potato
```
