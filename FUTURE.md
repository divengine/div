# FUTURE FEATURES

## 2016-11-16
### variable modifier for trim values
###### in the present:
         
```
{strip}{$varname}{/strip}
(# trim("{$varname}") #)
```

###### in the future:
```
ltrim: {-varnme}
rtrim: {varname-}
trim: {-varname-}
trim: {trim:varname}
trim: {$varname:trim}
```
### variable modifier for var_export
###### in the present:
```
// create subparser
function export($src, $items){
    $v = $items[$src];
    if (is_object($v))
    $v = get_object_vars($v);
    return str_replace("\n","",var_export($v, true));
}

div::setSubParser('export');
```

```
{export}varname{/export}
```     
### bug? subparser before conditional parts
    
## 2016-11-21
### new dialect's components (constants)

* DIV_TAG_LOOP_ORDER for '_order' var in loops
* DIV_TAG_LOOP_INDEX for '_index' var in loops
* DIV_TAG_LOOP_KEY for '_key' var in loops
