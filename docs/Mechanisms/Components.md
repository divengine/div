Complex GUIs are created with components. A component can reduces the effort dedicated in the production. Create a component with Div and for Div, is a very simple mechanism to implement. See the next example:

#### 1. Create the component:

**combobox.tpl**

```
<select id="{$id}" name="{$name}">
		[${$options}]
		<option value="{$val}">{$text}</option>
		[/${$options}]
</select>
```

This component use: [simple replacements](https://divengine.org/documentation/div-php-template-engine/mechanisms/components#simple-replacements), [lists](https://divengine.org/documentation/div-php-template-engine/mechanisms/components#lists) and [recursion](https://divengine.org/documentation/div-php-template-engine/mechanisms/components#recursion).

#### 2. Use your component:

**index.tpl**

```
[[_empty
		{= id: "products" =}
		{= name: "products" =}
		{= options: "products" =}
		{% combobox %}
_empty]]
```

"Use a component" means "[include](https://divengine.org/documentation/div-php-template-engine/mechanisms/components#include) the component", for example, into a [capsule](https://divengine.org/documentation/div-php-template-engine/mechanisms/components#capsules), that can be the _empty variable.

#### 3. Write your PHP code:

**index.php**

```
<?php
	
echo new div('index.tpl', [
		'products' => [
			['val' => 1, 'text' => 'Banana'],
			['val' => 2, 'text' => 'Potato'],
			['val' => 3, 'text' => 'Apple']
		]
]);
```

#### And when you run your script:

```
<select id="products" name="products">
	<option value="1">Banana</option>
	<option value="2">Potato</option>
	<option value="3">Apple</option>
</select>
```

