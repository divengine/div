## Static methods

**div::addCustomModifier(**string** $prefix, **string** $function)**

Add a [custom variable's modifier](https://divengine.org/documentation/div-php-template-engine/methodss-reference#custom-modifiers). The modifier function should have a single parameter.

**div::asThis(**mixed** $mixed)**

Return mixed value as HTML format, (util for debug and fast presentation)

**div::atLeastOneString(**string** $haystack, **array** $needles)**

Return true if at least one needle is contained in the haystack

**div::delDefault(**mixed** $search)**

Remove a default replacement

**div::delDefaultByVar(**string** $var, **mixed** $search)**

Remove a default replacement for specific variable

**div::delGlobal(**string** $var)**

Remove a global var

**div::disableSystemVar(**string** $var)**

Disable [system var](https://divengine.org/documentation/div-php-template-engine/methodss-reference#system-vars) for performance

**div::enableSystemVar(**string** $var)**

Enable [system var](https://divengine.org/documentation/div-php-template-engine/methodss-reference#system-vars) for utility

**div::error(**string** $errmsg, **string** $level = 'WARNING')**

Show error and die

**div::fileExists(**string** $filename)**

Secure 'file exists' method

**div::getLastKeyOfArray(**array** $arr)**

Return the last key of array or null if not exists

**div::getCountOfParagraphs(**string** $text)**

Count a number of paragraphs in a text

**div::getCountOfSentences(**string** $text)**

Count a number of sentences in a text

**div::getCountOfWords(**string** $text)**

Count a number of words in a text

**div::getDefault(**mixed** $value)**

Return a default replacement of value

**div::getDefaultByVar(**string** $var, **mixed** $value)**

Return a default replacement of value by var

**div::getSystemData()**

Return the [loaded data from the system](https://divengine.org/documentation/div-php-template-engine/methodss-reference#system-vars)

**div::getVarsFromCode(**string** $code)**

Return a list of vars from PHP code

**div::haveVarsThisCode(**string** $code)**

Return true if the PHP code have any var

**div::htmlToText(**string** $html, **integer** $width = 50)**

Convert HTML to plain and formated text

**div::isArrayOfArray(**array** $arr)**

Return true if $arr is array of array

**div::isArrayOfObjects(**array** $arr)**

Return true if $arr is array of objects

**div::isCli()**

Return true if the script was executed in the CLI enviroment

**div::isNumericList(**array** $arr)**

Return true if $arr is array of numbers

**div::isValidExpression(**string** $code)**

Check if code is a valid expression

**div::isDir(**string** $dirname)**

Secure 'is_dir' method

**div::isString(**mixed** $valur)**

Secure 'is_string' method

**div::jsonDecode(**string** $str)**

JSON Decode

**div::jsonEncode(**mixed** $data)**

JSON Encode

**div::log(**string** $msg, **string** $level = ' ')**

Write a message in the log file

**div::logOn(**string** $logfile)**

Activate the debug mode for Div and write the logs into $logfile.

**div::mixedBool(**mixed** $value)**

Return any value as a boolean

**div::setAllowedFunction({$src} $funcname)**

Allow a function for the [formulas](https://divengine.org/documentation/div-php-template-engine/methodss-reference#formulas)

**div::setDefault(**mixed** $search, **mixed** $replace)**

Add or set a default replacement of value

**div::setDefaultByVar(**string** $var, **mixed** $search, **mixed** $replace, **bool** $update = true)**

Add or set a default replacement of value for a specific var

**div::unsetAllowedFunction(**string** $funcname)**

Unset the allowed function

**div::utf162utf8(**string** $utf16)**

Convert string from UTF16 to UTF18

**div::varExists(**string** \$var, **mixed** &\$items = null)**

Return true if var exists in the template's items recursively
