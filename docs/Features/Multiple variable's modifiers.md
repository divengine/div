**Syntax:**

```
{$varname|modifier1|modifier2|modifier3|...|}
```

**Example:**

**index.tpl**

```
{= word: "ABCDEFG" =}
		
{$word|0,3|}
{$word|0,3|_|}
{$word|0,3|_|^|}
{$word|0,3|_|^|~2|}
```

**Output:**

```
ABC
abc
Abc
Ab
```
