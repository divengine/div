The locations are tags that identify some positions into template. With this feature you can define a location in any part of the template, and then collocate any content in this location. The tag that represent the location can be repeated in other positions. Also, you can locate several template pieces in the same location.

Now you can separate two concepts: the content from their location in the template. This advantage can be resolved with [[Simple replacements]] of  [[Template's variables]] but this is not sufficient and it is not equal.

Syntax

```
Define the location:
	
(( location_name ))
	
Define the content:
	
{{location_name
	
... some content here ...
	
location_name}}
```

Example

layout.tpl

```
<html>
	<body>
		<div id="header">(( header ))</div>
		<div id="content">(( content ))</div>
		<div id="footer">(( footer ))</div>
	</body> 
</html>
```

index.tpl

```
{% layout %}
	
{{header
	This is the header
header}}
	
{{footer
	This is the footer
footer}}
	
{{content
	This is the content
content}}
	
{{header
	<br/>.... more in the header .....
header}}

```

Output

```
<html>
	<body>
		<div id="header">This is the header<br/>.... more in the header .....</div>
		<div id="content">This is the content</div>
		<div id="footer">This is the footer</div>
	</body> 
</html>
```
