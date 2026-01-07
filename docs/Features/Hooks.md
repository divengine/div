The hooks are methods that can be implemented by the programmer in a class that inherit from div. This methods will be executed by div in some events. For example, before or after parse of template.

At the moment, the hooks are **beforeBuild**, **afterBuild**, **beforeParse** and **afterParse**. In the **beforeBuild** hook, you can modify the **$src** and **$items** optional parameters of the div constructor.

Example:

index.php

```
	
class Page extends div{
	
	public function beforeBuild(&$src = null, &$items = null){
		$this->title = 'Hello World';
		$items['body'] = 'This is the hook!'; 
	}
	
}

echo new Page('index.tpl');

```

index.tpl

```

<h1>{$title}</h1>
<p>{$body}</p>

```

Output

```

<h1>Hello World</h1>
<p>This is the hook!</p>

```
