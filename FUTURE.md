FUTURE FEATURES
==

* 2016-11-16
    - variable modifier for trim values
        - at the moment:
        ```
            {strip}{$varname}{/strip}
            (# trim("{$varname}") #)
        ```
        - for the future
        ```
            {-varnme}
            {varname-}
            {-varname-}
            {trim:varname}
            {$varname:trim}
        ```
    - variable modifier for var_export
        - at the moment
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
    - bug? subparser before conditional parts
    
* 2016-11-21
    - new dialect's components (constants)
        - DIV_TAG_LOOP_ORDER for '_order' var in loops
        - DIV_TAG_LOOP_INDEX for '_index' var in loops
        - DIV_TAG_LOOP_KEY for '_key' var in loops
        - ...