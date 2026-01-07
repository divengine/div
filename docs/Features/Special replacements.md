Tags for output special characters that can be used always. The following table show the available tags and their replacements:

|Tag|Replacement|
|---|---|
|{\n}|\n|
|{\r}|\r|
|{\t}|\t|
|{\v}|\v|
|{\f}|\f|
|{\$}|$|

**Example:**

index.tpl

```html
Hello{\n}Peter
{\t}Today is {/div.now:Y-m-d/}
```

Output

```html
Hello
Peter
		Today is 2013-07-24
```
