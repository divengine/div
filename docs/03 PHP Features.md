# 3. PHP Features

This section lists the public API for the div class.

## 3.1 Static methods

**div::addCustomModifier(**string** $prefix, **string** $function)**

Register a custom variable modifier. The modifier function must accept a single parameter.

**div::asThis(**mixed** $mixed)**

Return a value formatted as HTML for debugging.

**div::atLeastOneString(**string** $haystack, **array** $needles)**

Return true if at least one needle is found in the haystack.

**div::delDefault(**mixed** $search)**

Remove a default replacement.

**div::delDefaultByVar(**string** $var, **mixed** $search)**

Remove a default replacement for a specific variable.

**div::delGlobal(**string** $var)**

Remove a global variable.

**div::disableSystemVar(**string** $var)**

Disable a system variable for performance.

**div::enableSystemVar(**string** $var)**

Enable a system variable.

**div::error(**string** $errmsg, **string** $level = 'WARNING')**

Emit an error and stop execution.

**div::fileExists(**string** $filename)**

Secure file existence check.

**div::getLastKeyOfArray(**array** $arr)**

Return the last key of an array.

**div::getCountOfParagraphs(**string** $text)**

Count paragraphs in a string.

**div::getCountOfSentences(**string** $text)**

Count sentences in a string.

**div::getCountOfWords(**string** $text)**

Count words in a string.

**div::getDefault(**mixed** $value)**

Return a default replacement for a value.

**div::getDefaultByVar(**string** $var, **mixed** $value)**

Return a default replacement for a value scoped to a variable.

**div::getSystemData()**

Return loaded system data.

**div::getVersion()**

Return the current engine version string.

**div::getVarsFromCode(**string** $code)**

Return the list of variables referenced in PHP code.

**div::haveVarsThisCode(**string** $code)**

Return true if PHP code references any variables.

**div::htmlToText(**string** $html, **integer** $width = 50)**

Convert HTML to plain text.

**div::isArrayOfArray(**array** $arr)**

Return true if the array contains arrays.

**div::isArrayOfObjects(**array** $arr)**

Return true if the array contains objects.

**div::isCli()**

Return true if the script runs in CLI.

**div::isNumericList(**array** $arr)**

Return true if the array is numeric.

**div::isValidExpression(**string** $code)**

Validate a PHP expression.

**div::isDir(**string** $dirname)**

Secure `is_dir`.

**div::isString(**mixed** $value)**

Secure `is_string`.

**div::jsonDecode(**string** $str)**

Decode JSON.

**div::jsonEncode(**mixed** $data)**

Encode JSON.

**div::log(**string** $msg, **string** $level = ' ')**

Write a log message.

**div::logOn(**string** $logfile)**

Enable debug logging to a file.

**div::mixedBool(**mixed** $value)**

Convert a value to boolean using Div rules.

**div::setAllowedFunction(**string** $funcname)**

Allow a PHP function in formulas or macros.

**div::setDefault(**mixed** $search, **mixed** $replace)**

Add or update a default replacement.

**div::setDefaultByVar(**string** $var, **mixed** $search, **mixed** $replace, **bool** $update = true)**

Add or update a default replacement for a specific variable.

**div::unsetAllowedFunction(**string** $funcname)**

Remove a previously allowed function.

**div::utf162utf8(**string** $utf16)**

Convert UTF-16 to UTF-8.

**div::varExists(**string** $var, **mixed** &$items = null)**

Return true if a variable exists in items (recursive).

## 3.2 Instance methods

**div->addLiteral(**string** $var)**

Mark one or more template variables as literal (skip further parsing). Accepts a space- or comma-separated list.

**div->getLiterals()**

Return the current literal vars map for this instance.
