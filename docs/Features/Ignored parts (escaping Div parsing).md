The way of define a part of the template and ignore their code.

**Syntax in templates**

```
{ignore}
    
... some ignored code here ...
    
{/ignore}
```

**Example**

index.php

```
echo new div('index.tpl', ['name' => "Peter"]);
```

index.tpl

```
{ignore}

Name: {$name}

{/ignore}
```

Output

```
Name: {$name}
```
