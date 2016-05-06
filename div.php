<?php

/**
 * @package [[]] Div PHP Template Engine
 * 
 * The div class is the complete implementation of Div.
 *
 * Div (division) is a template engine for PHP 5.x or higher and it is a social project
 * without spirit of lucre
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program as the file LICENSE.txt; if not, please see
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt.
 *
 * @author Rafa Rodriguez <rafacuba2015@gmail.com,rrodriguezramirez@nauta.cu>
 * @version 4.8
 * @link http://divengine.com
 * @link http://github.com/rafageist/div
 * @link http://github.com/rafageist/div-extras
 * @example example.php
 * @example example.tpl
 */

// -- Constants --

// The path of templates's root directory
// @formatter:off
if (! defined ( 'PACKAGES' )) define ( 'PACKAGES', './' );

// @formatter:on
	// The default extension for template files
if (! defined ( 'DIV_DEFAULT_TPL_FILE_EXT' ))
	define ( 'DIV_DEFAULT_TPL_FILE_EXT', 'tpl' );
	
	// The default extension for data files
if (! defined ( 'DIV_DEFAULT_DATA_FILE_EXT' ))
	define ( 'DIV_DEFAULT_DATA_FILE_EXT', 'json' );
	
	// The max number of cycles of the parser (prevent infinite loop and more)
if (! defined ( 'DIV_MAX_PARSE_CYCLES' ))
	define ( 'DIV_MAX_PARSE_CYCLES', 100 );
	
	// The max size of file name or dir name in your operating system
if (! defined ( 'DIV_MAX_FILENAME_SIZE' ))
	define ( 'DIV_MAX_FILENAME_SIZE', 250 );
	
	// PHP allowed functions for macros and formulas
define ( 'DIV_PHP_ALLOWED_FUNCTIONS', 'isset,empty,is_null,is_numeric,is_bool,is_integer,is_double,is_array,sizeof,is_finite,is_float,is_infinite,' . 'is_int,is_long,is_nan,is_real,is_scalar,is_string,mt_rand,mt_srand,mt_getrandmax,rand,urlencode,urldecode,' . 'uniqid,date,time,intval,htmlspecialchars,htmlspecialchars_decode,strtr,strpos,str_replace,str_ireplace,substr,' . 'sprintf,abs,acos,acosh,asin,atan2,atanh,base_convert,bindec,ceil,cos,cosh,decbin,dechex,decoct,deg2rad,exp,expm1,' . 'floor,fmod,getrandmax,hexdec,hypot,lcg_value,log10,log1p,log,max,min,octdec,pi,pow,rad2deg,rand,round,sin,sinh,' . 'sqrt,srand,tan,tanh,cal_days_in_month,cal_from_jd,cal_info,cal_to_jd,easter_date,easter_days,frenchtojd,gregoriantojd,' . 'jddayofweek,jdmonthname,jdtofrench,jdtogregorian,jdtojewish,jdtojulian,jdtounix,jewishtojd,jewishtojd,unixtojd,checkdate,' . 'date_default_timezone_get,strtotime,date_sunset,gmdate,gmmktime,gmstrftime,idate,microtime,mktime,strftime,strptime,' . 'strtotime,timezone_name_from_abbr,timezone_version_get,bcadd,bccomp,bcdiv,bcmod,bcmul,bcpow,bcpowmod,bcscale,bcsqrt,' . 'bcsub,addcslashes,addslashes,bin2hex,chop,chr,chunk_split,convert_cyr_string,convert_uudecode,convert_uuencode,count,' . 'count_chars,crc32,crypt,hebrev,hebrevc,hex2bin,html_entity_decode,htmlentities,htmlspecialchars_decode,htmlspecialchars,' . 'lcfirst,levenshtein,ltrim,md5,metaphone,money_format,nl_langinfo,nl2br,number_format,ord,quoted_printable_decode,' . 'quoted_printable_encode,quotemeta,rtrim,sha1,similar_text,soundex,sprintf,str_pad,str_repeat,str_rot13,str_shuffle,' . 'strcasecmp,strchr,strcmp,strcoll,strcspn,strip_tags,stripcslashes,stripos,stripslashes,stristr,strlen,strnatcasecmp,' . 'strnatcmp,strncasecmp,strncmp,strpbrk,strrchr,strrev,strripos,strrpos,strspn,strtolower,strtoupper,strtr,substr_compare,' . 'substr_count,substr_replace,trim,ucfirst,ucwords,wordwrap,floatval,strval,implode,explode,array_keys,get_object_vars,is_object' );

// Valid PHP tokens in expressions
define ( 'DIV_PHP_VALID_TOKENS_FOR_EXPRESSIONS', 'T_ARRAY,T_ARRAY_CAST,T_BOOLEAN_AND,T_BOOLEAN_OR,T_BOOL_CAST,T_CHARACTER,T_CONSTANT_ENCAPSED_STRING,' . 'T_DNUMBER,T_DOUBLE_CAST,T_EMPTY,T_INT_CAST,T_ISSET,T_IS_EQUAL,T_IS_GREATER_OR_EQUAL,T_SR,T_IS_IDENTICAL,T_IS_NOT_EQUAL,T_IS_NOT_IDENTICAL,' . 'T_IS_SMALLER_OR_EQUAL,T_LNUMBER,T_LOGICAL_AND,T_LOGICAL_OR,T_LOGICAL_XOR,T_SL,T_SL_EQUAL,T_SR_EQUAL,T_STRING_CAST,T_STRING_VARNAME,' . 'T_VARIABLE,T_WHITESPACE,T_CURLY_OPEN,T_INC,T_COMMENT,T_DOUBLE_ARROW,T_ENCAPSED_AND_WHITESPACE' );

// Valid PHP tokens in macros
define ( 'DIV_PHP_VALID_TOKENS_FOR_MACROS', 'T_AS,T_DO,T_DOUBLE_COLON,T_ECHO,T_ELSE,T_ELSEIF,T_FOR,T_FOREACH,T_IF,T_MOD_EQUAL,T_MUL_EQUAL,' . 'T_OBJECT_OPERATOR,T_NUM_STRING,T_OR_EQUAL,T_PAAMAYIM_NEKUDOTAYIM,T_PLUS_EQUAL,T_PRINT,T_START_HEREDOC,T_SWITCH,' . 'T_WHILE,T_ENDIF,T_ENDFOR,T_ENDFOREACH,T_ENDSWITCH,T_ENDWHILE,T_END_HEREDOC,T_PAAMAYIM_NEKUDOTAYIM,T_BREAK' );

// Allowed Div methods in macros and formulas
define ( 'DIV_PHP_ALLOWED_METHODS', 'getRanges,asThis,atLeastOneString,getLastKeyOfArray,' . 'getCountOfParagraphs,getCountOfSentences,getCountOfWords,htmlToText,' . 'isArrayOfArray,isArrayOfObjects,isCli,isNumericList,jsonDecode,jsonEncode,isString,mixedBool,div' );

// Other internal constatns
define ( 'DIV_ERROR_WARNING', 'WARNING' );
define ( 'DIV_ERROR_FATAL', 'FATAL' );
define ( 'DIV_METHOD_NOT_EXISTS', 'DIV_METHOD_NOT_EXISTS' );
define ( 'DIV_UNICODE_ERROR', - 1 );
define ( 'DIV_MOMENT_BEFORE_PARSE', 'DIV_MOMENT_BEFORE_PARSE' );
define ( 'DIV_MOMENT_AFTER_PARSE', 'DIV_MOMENT_AFTER_PARSE' );
define ( 'DIV_MOMENT_AFTER_INCLUDE', 'DIV_MOMENT_AFTER_INCLUDE' );
define ( 'DIV_MOMENT_AFTER_REPLACE', 'DIV_MOMENT_AFTER_REPLACE' );

// ------------------------------------- D E F A U L T -- D I A L E C T --------------------------------------//
if (! defined ( 'DIV_TAG_VAR_MEMBER_DELIMITER' ))
	define ( 'DIV_TAG_VAR_MEMBER_DELIMITER', '.' );
	
	// Variables
if (! defined ( 'DIV_TAG_REPLACEMENT_PREFIX' ))
	define ( 'DIV_TAG_REPLACEMENT_PREFIX', '{' );
if (! defined ( 'DIV_TAG_REPLACEMENT_SUFFIX' ))
	define ( 'DIV_TAG_REPLACEMENT_SUFFIX', '}' );
if (! defined ( 'DIV_TAG_MULTI_MODIFIERS_PREFIX' ))
	define ( 'DIV_TAG_MULTI_MODIFIERS_PREFIX', '{$' );
if (! defined ( 'DIV_TAG_MULTI_MODIFIERS_OPERATOR' ))
	define ( 'DIV_TAG_MULTI_MODIFIERS_OPERATOR', '|' );
if (! defined ( 'DIV_TAG_MULTI_MODIFIERS_SEPARATOR' ))
	define ( 'DIV_TAG_MULTI_MODIFIERS_SEPARATOR', '|' );
if (! defined ( 'DIV_TAG_MULTI_MODIFIERS_SUFFIX' ))
	define ( 'DIV_TAG_MULTI_MODIFIERS_SUFFIX', '|}' );
if (! defined ( 'DIV_TAG_SUBMATCH_SEPARATOR' ))
	define ( 'DIV_TAG_SUBMATCH_SEPARATOR', ':' );
	
	// Variable's modifiers
if (! defined ( 'DIV_TAG_MODIFIER_SIMPLE' ))
	define ( 'DIV_TAG_MODIFIER_SIMPLE', '$' );
if (! defined ( 'DIV_TAG_MODIFIER_CAPITALIZE_FIRST' ))
	define ( 'DIV_TAG_MODIFIER_CAPITALIZE_FIRST', '^' );
if (! defined ( 'DIV_TAG_MODIFIER_CAPITALIZE_WORDS' ))
	define ( 'DIV_TAG_MODIFIER_CAPITALIZE_WORDS', '^^' );
if (! defined ( 'DIV_TAG_MODIFIER_UPPERCASE' ))
	define ( 'DIV_TAG_MODIFIER_UPPERCASE', '^^^' );
if (! defined ( 'DIV_TAG_MODIFIER_LOWERCASE' ))
	define ( 'DIV_TAG_MODIFIER_LOWERCASE', '_' );
if (! defined ( 'DIV_TAG_MODIFIER_LENGTH' ))
	define ( 'DIV_TAG_MODIFIER_LENGTH', '%' );
if (! defined ( 'DIV_TAG_MODIFIER_COUNT_WORDS' ))
	define ( 'DIV_TAG_MODIFIER_COUNT_WORDS', '%%' );
if (! defined ( 'DIV_TAG_MODIFIER_COUNT_SENTENCES' ))
	define ( 'DIV_TAG_MODIFIER_COUNT_SENTENCES', '%%%' );
if (! defined ( 'DIV_TAG_MODIFIER_COUNT_PARAGRAPHS' ))
	define ( 'DIV_TAG_MODIFIER_COUNT_PARAGRAPHS', '%%%%' );
if (! defined ( 'DIV_TAG_MODIFIER_ENCODE_URL' ))
	define ( 'DIV_TAG_MODIFIER_ENCODE_URL', '&' );
if (! defined ( 'DIV_TAG_MODIFIER_ENCODE_RAW_URL' ))
	define ( 'DIV_TAG_MODIFIER_ENCODE_RAW_URL', '&&' );
if (! defined ( 'DIV_TAG_MODIFIER_ENCODE_JSON' ))
	define ( 'DIV_TAG_MODIFIER_ENCODE_JSON', 'json:' );
if (! defined ( 'DIV_TAG_MODIFIER_HTML_ENTITIES' ))
	define ( 'DIV_TAG_MODIFIER_HTML_ENTITIES', 'html:' );
if (! defined ( 'DIV_TAG_MODIFIER_NL2BR' ))
	define ( 'DIV_TAG_MODIFIER_NL2BR', 'br:' );
if (! defined ( 'DIV_TAG_MODIFIER_TRUNCATE' ))
	define ( 'DIV_TAG_MODIFIER_TRUNCATE', '~' );
if (! defined ( 'DIV_TAG_MODIFIER_WORDWRAP' ))
	define ( 'DIV_TAG_MODIFIER_WORDWRAP', '/' );
if (! defined ( 'DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR' ))
	define ( 'DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR', ',' );
if (! defined ( 'DIV_TAG_MODIFIER_SINGLE_QUOTES' ))
	define ( 'DIV_TAG_MODIFIER_SINGLE_QUOTES', "'" );
if (! defined ( 'DIV_TAG_MODIFIER_JS' ))
	define ( 'DIV_TAG_MODIFIER_JS', "js:" );
if (! defined ( 'DIV_TAG_MODIFIER_FORMAT' ))
	define ( 'DIV_TAG_MODIFIER_FORMAT', '' );
	
	// Data format
if (! defined ( 'DIV_TAG_DATE_FORMAT_PREFIX' ))
	define ( 'DIV_TAG_DATE_FORMAT_PREFIX', '{/' );
if (! defined ( 'DIV_TAG_DATE_FORMAT_SUFFIX' ))
	define ( 'DIV_TAG_DATE_FORMAT_SUFFIX', '/}' );
if (! defined ( 'DIV_TAG_DATE_FORMAT_SEPARATOR' ))
	define ( 'DIV_TAG_DATE_FORMAT_SEPARATOR', ':' );
if (! defined ( 'DIV_TAG_NUMBER_FORMAT_PREFIX' ))
	define ( 'DIV_TAG_NUMBER_FORMAT_PREFIX', '{#' );
if (! defined ( 'DIV_TAG_NUMBER_FORMAT_SUFFIX' ))
	define ( 'DIV_TAG_NUMBER_FORMAT_SUFFIX', '#}' );
if (! defined ( 'DIV_TAG_NUMBER_FORMAT_SEPARATOR' ))
	define ( 'DIV_TAG_NUMBER_FORMAT_SEPARATOR', ':' );
	
	// Formulas
if (! defined ( 'DIV_TAG_FORMULA_BEGIN' ))
	define ( 'DIV_TAG_FORMULA_BEGIN', '(#' );
if (! defined ( 'DIV_TAG_FORMULA_END' ))
	define ( 'DIV_TAG_FORMULA_END', '#)' );
if (! defined ( 'DIV_TAG_FORMULA_FORMAT_SEPARATOR' ))
	define ( 'DIV_TAG_FORMULA_FORMAT_SEPARATOR', ':' );
	
	// Sub-parsers
if (! defined ( 'DIV_TAG_SUBPARSER_BEGIN_PREFIX' ))
	define ( 'DIV_TAG_SUBPARSER_BEGIN_PREFIX', '{' );
if (! defined ( 'DIV_TAG_SUBPARSER_BEGIN_SUFFIX' ))
	define ( 'DIV_TAG_SUBPARSER_BEGIN_SUFFIX', '}' );
if (! defined ( 'DIV_TAG_SUBPARSER_END_PREFIX' ))
	define ( 'DIV_TAG_SUBPARSER_END_PREFIX', '{/' );
if (! defined ( 'DIV_TAG_SUBPARSER_END_SUFFIX' ))
	define ( 'DIV_TAG_SUBPARSER_END_SUFFIX', '}' );
	
	// Ignored parts
if (! defined ( 'DIV_TAG_IGNORE_BEGIN' ))
	define ( 'DIV_TAG_IGNORE_BEGIN', '{ignore}' );
if (! defined ( 'DIV_TAG_IGNORE_END' ))
	define ( 'DIV_TAG_IGNORE_END', '{/ignore}' );
	
	// Comments
if (! defined ( 'DIV_TAG_COMMENT_BEGIN' ))
	define ( 'DIV_TAG_COMMENT_BEGIN', '<!--{' );
if (! defined ( 'DIV_TAG_COMMENT_END' ))
	define ( 'DIV_TAG_COMMENT_END', '}-->' );
	
	// HTML to Plain text
if (! defined ( 'DIV_TAG_TXT_BEGIN' ))
	define ( 'DIV_TAG_TXT_BEGIN', '{txt}' );
if (! defined ( 'DIV_TAG_TXT_END' ))
	define ( 'DIV_TAG_TXT_END', '{/txt}' );
if (! defined ( 'DIV_TAG_TXT_WIDTH_SEPARATOR' ))
	define ( 'DIV_TAG_TXT_WIDTH_SEPARATOR', '=>' );
	
	// Strip
if (! defined ( 'DIV_TAG_STRIP_BEGIN' ))
	define ( 'DIV_TAG_STRIP_BEGIN', '{strip}' );
if (! defined ( 'DIV_TAG_STRIP_END' ))
	define ( 'DIV_TAG_STRIP_END', '{/strip}' );
	
	// Loops
if (! defined ( 'DIV_TAG_LOOP_BEGIN_PREFIX' ))
	define ( 'DIV_TAG_LOOP_BEGIN_PREFIX', '[$' );
if (! defined ( 'DIV_TAG_LOOP_BEGIN_SUFFIX' ))
	define ( 'DIV_TAG_LOOP_BEGIN_SUFFIX', ']' );
if (! defined ( 'DIV_TAG_LOOP_END_PREFIX' ))
	define ( 'DIV_TAG_LOOP_END_PREFIX', '[/$' );
if (! defined ( 'DIV_TAG_LOOP_END_SUFFIX' ))
	define ( 'DIV_TAG_LOOP_END_SUFFIX', ']' );
if (! defined ( 'DIV_TAG_EMPTY' ))
	define ( 'DIV_TAG_EMPTY', '@empty@' );
if (! defined ( 'DIV_TAG_BREAK' ))
	define ( 'DIV_TAG_BREAK', '@break@' );
if (! defined ( 'DIV_TAG_LOOP_VAR_SEPARATOR' ))
	define ( 'DIV_TAG_LOOP_VAR_SEPARATOR', '=>' );
	
	// Iterations
if (! defined ( 'DIV_TAG_ITERATION_BEGIN_PREFIX' ))
	define ( 'DIV_TAG_ITERATION_BEGIN_PREFIX', '[:' );
if (! defined ( 'DIV_TAG_ITERATION_BEGIN_SUFFIX' ))
	define ( 'DIV_TAG_ITERATION_BEGIN_SUFFIX', ':]' );
if (! defined ( 'DIV_TAG_ITERATION_END' ))
	define ( 'DIV_TAG_ITERATION_END', '[/]' );
if (! defined ( 'DIV_TAG_ITERATION_PARAM_SEPARATOR' ))
	define ( 'DIV_TAG_ITERATION_PARAM_SEPARATOR', ',' );
	
	// Conditional parts
if (! defined ( 'DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX' ))
	define ( 'DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX', '?$' );
if (! defined ( 'DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX' ))
	define ( 'DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX', '' );
if (! defined ( 'DIV_TAG_CONDITIONAL_TRUE_END_PREFIX' ))
	define ( 'DIV_TAG_CONDITIONAL_TRUE_END_PREFIX', '$' );
if (! defined ( 'DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX' ))
	define ( 'DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX', '?' );
if (! defined ( 'DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX' ))
	define ( 'DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX', '!$' );
if (! defined ( 'DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX' ))
	define ( 'DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX', '' );
if (! defined ( 'DIV_TAG_CONDITIONAL_FALSE_END_PREFIX' ))
	define ( 'DIV_TAG_CONDITIONAL_FALSE_END_PREFIX', '$' );
if (! defined ( 'DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX' ))
	define ( 'DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX', '!' );
if (! defined ( 'DIV_TAG_ELSE' ))
	define ( 'DIV_TAG_ELSE', '@else@' );
	
	// Conditions
if (! defined ( 'DIV_TAG_CONDITIONS_BEGIN_PREFIX' ))
	define ( 'DIV_TAG_CONDITIONS_BEGIN_PREFIX', '{?(' );
if (! defined ( 'DIV_TAG_CONDITIONS_BEGIN_SUFFIX' ))
	define ( 'DIV_TAG_CONDITIONS_BEGIN_SUFFIX', ')?}' );
if (! defined ( 'DIV_TAG_CONDITIONS_END' ))
	define ( 'DIV_TAG_CONDITIONS_END', '{/?}' );
	
	// Template vars
if (! defined ( 'DIV_TAG_TPLVAR_BEGIN' ))
	define ( 'DIV_TAG_TPLVAR_BEGIN', '{=' );
if (! defined ( 'DIV_TAG_TPLVAR_END' ))
	define ( 'DIV_TAG_TPLVAR_END', '=}' );
if (! defined ( 'DIV_TAG_TPLVAR_ASSIGN_OPERATOR' ))
	define ( 'DIV_TAG_TPLVAR_ASSIGN_OPERATOR', ':' );
if (! defined ( 'DIV_TAG_TPLVAR_PROTECTOR' ))
	define ( 'DIV_TAG_TPLVAR_PROTECTOR', '*' );
	
	// Default replacement
if (! defined ( 'DIV_TAG_DEFAULT_REPLACEMENT_BEGIN' ))
	define ( 'DIV_TAG_DEFAULT_REPLACEMENT_BEGIN', '{@' );
if (! defined ( 'DIV_TAG_DEFAULT_REPLACEMENT_END' ))
	define ( 'DIV_TAG_DEFAULT_REPLACEMENT_END', '@}' );
	
	// Includes
if (! defined ( 'DIV_TAG_INCLUDE_BEGIN' ))
	define ( 'DIV_TAG_INCLUDE_BEGIN', '{% ' );
if (! defined ( 'DIV_TAG_INCLUDE_END' ))
	define ( 'DIV_TAG_INCLUDE_END', ' %}' );
	
	// Pre-processed
if (! defined ( 'DIV_TAG_PREPROCESSED_BEGIN' ))
	define ( 'DIV_TAG_PREPROCESSED_BEGIN', '{%% ' );
if (! defined ( 'DIV_TAG_PREPROCESSED_END' ))
	define ( 'DIV_TAG_PREPROCESSED_END', ' %%}' );
if (! defined ( 'DIV_TAG_PREPROCESSED_SEPARATOR' ))
	define ( 'DIV_TAG_PREPROCESSED_SEPARATOR', ':' );
	
	// Capsules
if (! defined ( 'DIV_TAG_CAPSULE_BEGIN_PREFIX' ))
	define ( 'DIV_TAG_CAPSULE_BEGIN_PREFIX', '[[' );
if (! defined ( 'DIV_TAG_CAPSULE_BEGIN_SUFFIX' ))
	define ( 'DIV_TAG_CAPSULE_BEGIN_SUFFIX', '' );
if (! defined ( 'DIV_TAG_CAPSULE_END_PREFIX' ))
	define ( 'DIV_TAG_CAPSULE_END_PREFIX', '' );
if (! defined ( 'DIV_TAG_CAPSULE_END_SUFFIX' ))
	define ( 'DIV_TAG_CAPSULE_END_SUFFIX', ']]' );
	
	// Multi replacements
if (! defined ( 'DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX' ))
	define ( 'DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX', '{:' );
if (! defined ( 'DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX' ))
	define ( 'DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX', '}' );
if (! defined ( 'DIV_TAG_MULTI_REPLACEMENT_END_PREFIX' ))
	define ( 'DIV_TAG_MULTI_REPLACEMENT_END_PREFIX', '{:/' );
if (! defined ( 'DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX' ))
	define ( 'DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX', '}' );
	
	// Friendly tags
if (! defined ( 'DIV_TAG_FRIENDLY_BEGIN' ))
	define ( 'DIV_TAG_FRIENDLY_BEGIN', '<!--|' );
if (! defined ( 'DIV_TAG_FRIENDLY_END' ))
	define ( 'DIV_TAG_FRIENDLY_END', '|-->' );
	
	// Aggregate functions
if (! defined ( 'DIV_TAG_AGGREGATE_FUNCTION_COUNT' ))
	define ( 'DIV_TAG_AGGREGATE_FUNCTION_COUNT', 'count' );
if (! defined ( 'DIV_TAG_AGGREGATE_FUNCTION_MAX' ))
	define ( 'DIV_TAG_AGGREGATE_FUNCTION_MAX', 'max' );
if (! defined ( 'DIV_TAG_AGGREGATE_FUNCTION_MIN' ))
	define ( 'DIV_TAG_AGGREGATE_FUNCTION_MIN', 'min' );
if (! defined ( 'DIV_TAG_AGGREGATE_FUNCTION_SUM' ))
	define ( 'DIV_TAG_AGGREGATE_FUNCTION_SUM', 'sum' );
if (! defined ( 'DIV_TAG_AGGREGATE_FUNCTION_AVG' ))
	define ( 'DIV_TAG_AGGREGATE_FUNCTION_AVG', 'avg' );
if (! defined ( 'DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR' ))
	define ( 'DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR', ':' );
if (! defined ( 'DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR' ))
	define ( 'DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR', '-' );
	
	// Locations
if (! defined ( 'DIV_TAG_LOCATION_BEGIN' ))
	define ( 'DIV_TAG_LOCATION_BEGIN', '(( ' );
if (! defined ( 'DIV_TAG_LOCATION_END' ))
	define ( 'DIV_TAG_LOCATION_END', ' ))' );
if (! defined ( 'DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX' ))
	define ( 'DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX', '{{' );
if (! defined ( 'DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX' ))
	define ( 'DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX', '' );
if (! defined ( 'DIV_TAG_LOCATION_CONTENT_END_PREFIX' ))
	define ( 'DIV_TAG_LOCATION_CONTENT_END_PREFIX', '' );
if (! defined ( 'DIV_TAG_LOCATION_CONTENT_END_SUFFIX' ))
	define ( 'DIV_TAG_LOCATION_CONTENT_END_SUFFIX', '}}' );
	
	// Macros
if (! defined ( 'DIV_TAG_MACRO_BEGIN' ))
	define ( 'DIV_TAG_MACRO_BEGIN', '<?' );
if (! defined ( 'DIV_TAG_MACRO_END' ))
	define ( 'DIV_TAG_MACRO_END', '?>' );
	
	// Special replacements
if (! defined ( 'DIV_TAG_SPECIAL_REPLACE_NEW_LINE' ))
	define ( 'DIV_TAG_SPECIAL_REPLACE_NEW_LINE', '{\n}' );
if (! defined ( 'DIV_TAG_SPECIAL_REPLACE_CAR_RETURN' ))
	define ( 'DIV_TAG_SPECIAL_REPLACE_CAR_RETURN', '{\r}' );
if (! defined ( 'DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB' ))
	define ( 'DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB', '{\t}' );
if (! defined ( 'DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB' ))
	define ( 'DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB', '{\v}' );
if (! defined ( 'DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE' ))
	define ( 'DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE', '{\f}' );
if (! defined ( 'DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL' ))
	define ( 'DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL', '{\$}' );
if (! defined ( 'DIV_TAG_TEASER_BREAK' ))
	define ( 'DIV_TAG_TEASER_BREAK', '<!--break-->' );

define ( 'DIV_DEFAULT_DIALECT', '{
		\'DIV_TAG_VAR_MEMBER_DELIMITER\' : \'{\',		\'DIV_TAG_VAR_MEMBER_DELIMITER\' : \'.\',
		\'DIV_TAG_REPLACEMENT_PREFIX\' : \'{\',		    \'DIV_TAG_REPLACEMENT_SUFFIX\' : \'}\',
		\'DIV_TAG_MULTI_MODIFIERS_PREFIX\' : \'{$\',	\'DIV_TAG_MULTI_MODIFIERS_SEPARATOR\' : \'|\',
		\'DIV_TAG_MULTI_MODIFIERS_OPERATOR\' : \'|\',	\'DIV_TAG_MULTI_MODIFIERS_SUFFIX\' : \'|}\',
		\'DIV_TAG_SUBMATCH_SEPARATOR\' : \':\',		    \'DIV_TAG_MODIFIER_SIMPLE\' : \'$\',
		\'DIV_TAG_MODIFIER_CAPITALIZE_FIRST\' : \'^\',	\'DIV_TAG_MODIFIER_CAPITALIZE_WORDS\' : \'^^\',
		\'DIV_TAG_MODIFIER_UPPERCASE\' : \'^^^\',		\'DIV_TAG_MODIFIER_LOWERCASE\' : \'_\',
		\'DIV_TAG_MODIFIER_LENGTH\' : \'%\',		    \'DIV_TAG_MODIFIER_COUNT_WORDS\' : \'%%\',
		\'DIV_TAG_MODIFIER_COUNT_SENTENCES\' : \'%%%\',	\'DIV_TAG_MODIFIER_COUNT_PARAGRAPHS\' : \'%%%%\',
		\'DIV_TAG_MODIFIER_ENCODE_URL\' : \'&\',		\'DIV_TAG_MODIFIER_ENCODE_RAW_URL\' : \'&&\',
		\'DIV_TAG_MODIFIER_ENCODE_JSON\' : \'json:\',	\'DIV_TAG_MODIFIER_HTML_ENTITIES\' : \'html:\',
		\'DIV_TAG_MODIFIER_NL2BR\' : \'br:\',		    \'DIV_TAG_MODIFIER_TRUNCATE\' : \'~\',
		\'DIV_TAG_MODIFIER_WORDWRAP\' : \'/\',		    \'DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR\' : \',\',
		\'DIV_TAG_MODIFIER_SINGLE_QUOTES\' : "\'",		\'DIV_TAG_MODIFIER_JS\' :  \'js:\',
		\'DIV_TAG_MODIFIER_FORMAT\' : \'\',		        \'DIV_TAG_DATE_FORMAT_PREFIX\' : \'{/\',
		\'DIV_TAG_DATE_FORMAT_SUFFIX\' : \'/}\',		\'DIV_TAG_DATE_FORMAT_SEPARATOR\' : \':\',
		\'DIV_TAG_NUMBER_FORMAT_PREFIX\' : \'{#\',		\'DIV_TAG_NUMBER_FORMAT_SUFFIX\' : \'#}\',
		\'DIV_TAG_NUMBER_FORMAT_SEPARATOR\' : \':\',	\'DIV_TAG_FORMULA_BEGIN\' : \'(#\',
		\'DIV_TAG_FORMULA_END\' : \'#)\',		        \'DIV_TAG_FORMULA_FORMAT_SEPARATOR\' : \':\',
		\'DIV_TAG_SUBPARSER_BEGIN_PREFIX\' : \'{\',		\'DIV_TAG_SUBPARSER_BEGIN_SUFFIX\' : \'}\',
		\'DIV_TAG_SUBPARSER_END_PREFIX\' : \'{/\',		\'DIV_TAG_SUBPARSER_END_SUFFIX\' : \'}\',
		\'DIV_TAG_IGNORE_BEGIN\' : \'{ignore}\',		\'DIV_TAG_IGNORE_END\' : \'{/ignore}\',
		\'DIV_TAG_COMMENT_BEGIN\' : \'<!--{\',		    \'DIV_TAG_COMMENT_END\' : \'}-->\',
		\'DIV_TAG_TXT_BEGIN\' : \'{txt}\',		        \'DIV_TAG_TXT_END\' : \'{/txt}\',			
		\'DIV_TAG_TXT_WIDTH_SEPARATOR\' : \'=>\',		\'DIV_TAG_STRIP_BEGIN\' : \'{strip}\',
		\'DIV_TAG_STRIP_END\' : \'{/strip}\',		    \'DIV_TAG_LOOP_BEGIN_PREFIX\' : \'[$\',
		\'DIV_TAG_LOOP_BEGIN_SUFFIX\' : \']\',		    \'DIV_TAG_LOOP_END_PREFIX\' : \'[/$\',
		\'DIV_TAG_LOOP_END_SUFFIX\' : \']\',		    \'DIV_TAG_EMPTY\' : \'@empty@\',
		\'DIV_TAG_BREAK\' : \'@break@\',		        \'DIV_TAG_LOOP_VAR_SEPARATOR\' : \'=>\',
		\'DIV_TAG_ITERATION_BEGIN_PREFIX\' : \'[:\',	\'DIV_TAG_ITERATION_BEGIN_SUFFIX\' : \':]\',
		\'DIV_TAG_ITERATION_END\' : \'[/]\',		                \'DIV_TAG_ITERATION_PARAM_SEPARATOR\' : \',\',
		\'DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX\' :  \'?$\',	    \'DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX\' : \'\',
		\'DIV_TAG_CONDITIONAL_TRUE_END_PREFIX\' :    \'$\',		    \'DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX\' : \'?\',
		\'DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX\' : \'!$\',	    \'DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX\' : \'\',
		\'DIV_TAG_CONDITIONAL_FALSE_END_PREFIX\' :   \'$\',	        \'DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX\' : \'!\',
		\'DIV_TAG_ELSE\' : \'@else@\',		                        \'DIV_TAG_CONDITIONS_BEGIN_PREFIX\' : \'{?(\',
		\'DIV_TAG_CONDITIONS_BEGIN_SUFFIX\' : \')?}\',		        \'DIV_TAG_CONDITIONS_END\' : \'{/?}\',
		\'DIV_TAG_TPLVAR_BEGIN\' : \'{=\',		                    \'DIV_TAG_TPLVAR_END\' : \'=}\',
		\'DIV_TAG_TPLVAR_ASSIGN_OPERATOR\' : \':\',		            \'DIV_TAG_TPLVAR_PROTECTOR\' : \'*\',
		\'DIV_TAG_DEFAULT_REPLACEMENT_BEGIN\' : \'{@\',		        \'DIV_TAG_DEFAULT_REPLACEMENT_END\' : \'@}\',
		\'DIV_TAG_INCLUDE_BEGIN\' : \'{% \',		                \'DIV_TAG_INCLUDE_END\' : \' %}\',
		\'DIV_TAG_PREPROCESSED_BEGIN\' : \'{%% \',		            \'DIV_TAG_PREPROCESSED_END\' : \' %%}\',
		\'DIV_TAG_PREPROCESSED_SEPARATOR\' : \':\',		            
		\'DIV_TAG_CAPSULE_BEGIN_PREFIX\' : \'[[\',		            \'DIV_TAG_CAPSULE_BEGIN_SUFFIX\' : \'\',
		\'DIV_TAG_CAPSULE_END_PREFIX\' : \'\',		                \'DIV_TAG_CAPSULE_END_SUFFIX\' : \']]\',
		\'DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX\' : \'{:\', 	    \'DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX\' : \'}\',
		\'DIV_TAG_MULTI_REPLACEMENT_END_PREFIX\' : \'{:/\',		    \'DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX\' : \'}\',
		\'DIV_TAG_FRIENDLY_BEGIN\' : \'<!--|\',		                \'DIV_TAG_FRIENDLY_END\' : \'|-->\',
		\'DIV_TAG_AGGREGATE_FUNCTION_COUNT\' : \'count\',		    \'DIV_TAG_AGGREGATE_FUNCTION_MAX\' : \'max\',
		\'DIV_TAG_AGGREGATE_FUNCTION_MIN\' : \'min\',		        \'DIV_TAG_AGGREGATE_FUNCTION_SUM\' : \'sum\',
		\'DIV_TAG_AGGREGATE_FUNCTION_AVG\' : \'avg\',		        \'DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR\' : \':\',
		\'DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR\' : \'-\',	\'DIV_TAG_LOCATION_BEGIN\' : \'(( \',
		\'DIV_TAG_LOCATION_END\' : \' ))\',		                    \'DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX\' : \'{{\',
		\'DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX\' : \'\',		    \'DIV_TAG_LOCATION_CONTENT_END_PREFIX\' : \'\',
		\'DIV_TAG_LOCATION_CONTENT_END_SUFFIX\' : \'}}\',		    \'DIV_TAG_MACRO_BEGIN\' : \'<?\',
		\'DIV_TAG_MACRO_END\' : \'?>\',		                        \'DIV_TAG_SPECIAL_REPLACE_NEW_LINE\' : \'{\\n}\',
		\'DIV_TAG_SPECIAL_REPLACE_CAR_RETURN\' : \'{\\r}\',		    \'DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB\' : \'{\\t}\',
		\'DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB\' : \'{\\v}\',		\'DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE\' : \'{\\f}\',
		\'DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL\' : \'{\\$}\',		\'DIV_TAG_TEASER_BREAK\' : \'<!--break-->\'
		}' );

// --------------------------------------------------------------------------------------------------------------------------------------//

define ( 'DIV_TEMPLATE_FOR_DOCS', '
@_DIALECT = ' . uniqid () . '
<html>
	<head>
		<title>{$title}</title>
		<style type="text/css">
			body          {background: #656565; font-family: Verdana;}
			div.section   {background: white;	margin-top: 20px; padding: 10px; width: 780px;}
			.section h2   {color: white; font-size: 24px;font-weight: bold; margin-left: -30px;padding-bottom: 5px; padding-left: 30px;padding-top: 5px; background: black; border-left: 10px solid gray;}	
			h1            {color: white;}
			table.data td {padding: 5px;border-bottom: 1px solid gray; border-right: 1px solid gray;}
			table.data th {padding: 5px;color: white; background: black;}
			.code         {padding: 0px; margin: 0px; background: #eeeeee; color: black; font-family: "Courier New"; text-align: left; font-size: 13px;}					
			.code .line   {text-align:right; background: gray; color: white; border-right: 2px solid black; padding-right: 5px;}
			table.index, table.index a, table.index a:visited {color:white;}					
			div.header    {color: white;}
			.template-description{background: #eeeeee; padding: 10px;}
		</style>
	</head>
	<body>
		<label><a href="#header" style="position: fixed; bottom: 5px; right: 5px;color: white;">Index</a></label>
		<table width="750" align="center"><tr><td valign="top">
		<div id = "header" class = "header">
		<h1>{$title}</h1>
		<p>Generated by Div at {/div.now:Y-m-d H:i:s/}</p>
		<h2>Index</h2>
		<table class="index data" width="100%">
			<tr><th></th><th>Name</th><th>Description</th><th>Version</th></tr>
			[$docs]
				<tr><td>{$_order}</td>
				<td><a href="#{$_key}">?$name {$name} $name?</a></td>
				<td>?$description {$description} $description?</td>
				<td>?$version {$version} $version?</td></tr>
			[/$docs]
		</table>
		</div>
		
		{= repl1: [["<",""],[">",""]] =}
		
		[$docs]
			<div class="section">
				<h2 id = "{$_key}">?$icon {$icon} $icon? {$name}</h2>
				<table width="100%">
				<tr><td align="right">Path:</td><td>{$_key} </td></tr>
				?$type    <tr><td align="right" width="150">Type:</td><td><b>{$type}</b></td></tr>$type?
				?$author  <tr><td align="right" width="150">Author:</td><td><b>{html:author}</b></td></tr> $author?
				?$version <tr><td align="right" width="150">Version:</td><td><b>{$version}</b></td></tr> $version?
				?$update  <tr><td align="right" width="150">Last update:</td><td>{$update} </td></tr>$update?
				</table>
				<br/>
				?$description <p class="template-description">{$description}</p>$description?
				?$vars
					<h3>Template\'s Variables ({$vars})</h3>
					<table class="data">
					<tr><th></th><th></th><th>Type</th><th>Name</th><th>Description</th></tr>
					[$vars]
					
						{?( trim("{\'value}") !== "" )?}
						<?
							$value = trim(str_replace(array("\t","\n","\r")," ", $value));	
							while(strpos($value, "  ")) $value = str_replace("  "," ", $value);
							$pars = explode(" ", $value, 4);
						?>
						<tr>
							<td>{$_order}</td>
							[$pars]
							<td>{:repl1}{$value}{:/repl1}</td>
							[/$pars]
						</tr>
						{/?}
					[/$vars]
					</table>
				$vars?
				?$include
					<h3>Include:</h3>
					[$include]
						{$_order}. <a href="#{$value}">{$value}</a><br/>
					[/$include]
				$include?
				?$example
					<h3>Example:</h3>
					<table width = "100%" class="code" cellspacing="0" cellpadding="0">
					[$example]
							<tr>
							<td class="line" width="30">{$_order}</td>
							<td><pre class="code">{html_wysiwyg:afterReplace}{$value}{/html_wysiwyg:afterReplace}</pre></td>
							</tr>
					[/$example]
					</table> 
				$example?
			</div>
		[/$docs]
		</td></tr></table>
	</body>
</html>' );
class div {
	
	// Public
	
	// template source
	public $__src = null;
	
	// original template source
	public $__src_original = null;
	
	// template variables
	public $__items = array ();
	
	// original template variables
	public $__items_orig = array ();
	
	// to remember the template variables
	public $__memory = array ();
	
	// path to current template file
	public $__path = '';
	
	// template variables to ignore
	public $__ignore = array ();
	
	// internal and random ignore tag (security)
	public $__ignore_secret_tag = null;
	
	// template's parts to restore after parse
	public $__restore = array ();
	
	// path of current templates's root folder
	public $__packages = PACKAGES;
	
	// properties of the template
	public $__properties = array ();
	
	// ----- Private ------
	// template id
	private $__id = null;
	
	// temporal vars
	private $__temp = array ();
	
	// template cheksum
	private $__crc = null;
	
	// ----- Globals -----
	
	// custom variable's modifiers
	private static $__custom_modifiers = array ();
	
	// global template's variables
	private static $__globals = array ();
	
	// global template's variables defined in the design
	private static $__globals_design = array ();
	
	// global and protected template variables defined in the design
	private static $__globals_design_protected = array ();
	
	// default value for another value
	private static $__defaults = array ();
	
	// default value for another value by variable
	private static $__defaults_by_var = array ();
	
	// system data
	private static $__system_data = null;
	private static $__system_data_allowed = array ();
	
	// do not load code from files
	private static $__discard_file_system = false;
	
	// list of allowed custom functions
	private static $__allowed_functions = array ();
	
	// list of allowed class's methods
	private static $__allowed_methods = null;
	
	// list of subparsers
	private static $__sub_parsers = array ();
	
	// template's documentation
	private static $__docs = array ();
	
	// on/off documentation
	private static $__docs_on = false;
	
	// includes's historial
	private static $__includes_historial = array ();
	
	// ----- Internals -----
	
	// current version of Div
	private static $__version = '4.8';
	
	// name of the super class
	private static $__super_class = null;
	
	// name of parent class's methods
	private static $__parent_method_names = array ();
	
	// name of current methods
	private static $__method_names = null;
	
	// duration of parser
	private static $__parse_duration = null;
	
	// current level of parser
	private static $__parse_level = 0;
	
	// auxiliary engine
	private static $__engine = null;
	
	// variable's modifiers
	private static $__modifiers = array (
			DIV_TAG_MODIFIER_SIMPLE,
			DIV_TAG_MODIFIER_CAPITALIZE_FIRST,
			DIV_TAG_MODIFIER_CAPITALIZE_WORDS,
			DIV_TAG_MODIFIER_UPPERCASE,
			DIV_TAG_MODIFIER_LOWERCASE,
			DIV_TAG_MODIFIER_LENGTH,
			DIV_TAG_MODIFIER_COUNT_WORDS,
			DIV_TAG_MODIFIER_COUNT_SENTENCES,
			DIV_TAG_MODIFIER_COUNT_PARAGRAPHS,
			DIV_TAG_MODIFIER_ENCODE_URL,
			DIV_TAG_MODIFIER_ENCODE_RAW_URL,
			DIV_TAG_MODIFIER_HTML_ENTITIES,
			DIV_TAG_MODIFIER_NL2BR,
			DIV_TAG_MODIFIER_ENCODE_JSON,
			DIV_TAG_MODIFIER_SINGLE_QUOTES,
			DIV_TAG_MODIFIER_JS 
	);
	
	// is current dialect checked?
	private static $__dialect_checked = false;
	
	// allowed PHP functions
	private static $__allowed_php_functions = null;
	
	// is log mode?
	private static $__log_mode = false;
	
	// the log filename
	private static $__log_file = null;
	
	// is PHP cli?
	private static $__is_cli = null;
	
	// ignored parts
	private static $__ignored_parts = array ();
	
	// last template id
	private static $__last_id = 0;
	
	// remember previous work
	private static $__remember = array ();
	
	// do not remember this work
	private static $__dont_remember_it = array ();
	
	// historical errors
	private static $__errors = array ();
	
	// include path
	private static $__include_paths = null;
	
	// packages by class
	private static $__packages_by_class = array ();
	
	// internal messages
	private static $__internal_messages = array ();
	
	/**
	 * Constructor
	 *
	 * @param string $src        	
	 * @param mixed $items        	
	 * @param array $ignore        	
	 * @return div
	 */
	public function __construct($src = null, $items = null, $ignore = array()) {
		// Enabling system vars
		if (self::$__parse_level < 2) {
			self::enableSystemVar ( 'div' . DIV_TAG_VAR_MEMBER_DELIMITER . 'version' );
			self::enableSystemVar ( 'div' . DIV_TAG_VAR_MEMBER_DELIMITER . 'post' );
			self::enableSystemVar ( 'div' . DIV_TAG_VAR_MEMBER_DELIMITER . 'get' );
			self::enableSystemVar ( 'div' . DIV_TAG_VAR_MEMBER_DELIMITER . 'now' );
		}
		
		// Generate internal and random ignore tag (security reasons)
		$this->__ignore_secret_tag = uniqid ();
		
		// Validate the current dialect
		if (self::$__dialect_checked == false) {
			$r = self::isValidCurrentDialect ();
			if ($r !== true)
				self::error ( 'Current dialect is invalid: ' . $r, DIV_ERROR_FATAL );
			self::$__dialect_checked = true;
		}
		
		$classname = get_class ( $this );
		
		self::$__packages_by_class [$classname] = $this->__packages;
		
		if (is_null ( self::$__super_class ))
			self::$__super_class = $this->getSuperParent ();
		if (is_null ( self::$__parent_method_names ))
			self::$__parent_method_names = get_class_methods ( self::$__super_class );
		
		$this->__id = ++ self::$__last_id;
		
		if (self::$__log_mode)
			$this->logger ( 'Building instance #' . $this->__id . ' of ' . $classname . '...' );
			
			// Calling the beforeBuild hook
		$this->beforeBuild ( $src, $items );

		if (is_null ( $items ) && ! is_null ( $this->__items ))
			$items = $this->__items;
		
		$this->__items_orig = $items;
		
		$decode = true;
		
		$discardfs = self::$__discard_file_system;
		
		if (is_null ( $src )) {
			if ($classname != self::$__super_class && is_null ( $this->__src ))
				$src = $classname;
			if (! is_null ( $this->__src ))
				$src = $this->__src;
		}
		
		if (is_null ( $items )) {
			$items = $src;
			$items = str_replace ( '.' . DIV_DEFAULT_TPL_FILE_EXT, '', $items );
			$decode = false;
		}
		
		if (! $discardfs) {
			if (self::isString ( $items ))
				
				if (strlen ( $items . '.' . DIV_DEFAULT_DATA_FILE_EXT ) < 255) {
					$exists = false;
					
					
					if (self::fileExists ( $items )) {
						$items = self::getFileContents ( $items );
						$exists = true;
					} elseif (self::fileExists ( $items . '.' . DIV_DEFAULT_DATA_FILE_EXT )) {
						$items = self::getFileContents ( $items . '.' . DIV_DEFAULT_DATA_FILE_EXT );
						$exists = true;
					}
					
					if ($exists === true || $decode === true)
						$items = self::jsonDecode ( $items );
					
					/*
					 * if ($exists === true)
					 * break;
					 */
				} else {
					$items = self::jsonDecode ( $items );
				}
		}
		
		if (is_object ( $items )) {
			if (method_exists ( $items, '__toString' )) {
				$itemstr = "$items";
				if (! isset ( $items->value ))
					$items->value = $itemstr;
				$items->_to_string = $itemstr;
			}
			$items = get_object_vars ( $items );
		}
		
		if (! $discardfs)
			$src = $this->loadTemplate ( $src );
		
		if (! is_array ( $items ))
			$items = array ();
		
		$this->__src = $src;
		$this->__src_original = $src;
		$this->__items = $items;
		
		if (self::isString ( $ignore ))
			$ignore = explode ( ',', $ignore );
		if (isset ( $ignore [0] ))
			foreach ( $ignore as $key => $val )
				$this->__ignore [$val] = true;
			
			// Calling the afterBuild hook
		$this->afterBuild ();
		
		// Enabling methods
		if (is_null ( self::$__allowed_methods )) {
			$keys = explode ( ",", DIV_PHP_ALLOWED_METHODS );
			;
			self::$__allowed_methods = array_combine ( $keys, $keys );
			
			if (self::$__super_class != $classname) {
				$keys = array_diff ( get_class_methods ( $classname ), get_class_methods ( self::$__super_class ) );
				if (isset ( $keys [0] ))
					self::$__allowed_methods = array_merge ( self::$__allowed_methods, array_combine ( $keys, $keys ) );
			}
		}
		
		// Pre-defined subparsers
		
		self::setSubParser ( 'parse', 'subParse_parse' );
		self::setSubParser ( 'html_wysiwyg', 'subParse_html_wysiwyg' );
	}
	
	/**
	 * Return a list of include_path setting + the PACKAGES
	 *
	 * @return array
	 */
	final static function getIncludePaths($packages = PACKAGES) {
		if (is_null ( self::$__include_paths )) {
			$os = self::getOperatingSystem ();
			self::$__include_paths = explode ( ($os == "win32" ? ";" : ":"), ini_get ( "include_path" ) );
			self::$__include_paths [] = $packages;
		}
		return self::$__include_paths;
	}
	
	/**
	 * Return the current operating system
	 *
	 * @return string (win32/linux/unix)
	 */
	final static function getOperatingSystem() {
		if (isset ( $_SERVER ['SERVER_SOFTWARE'] )) {
			if (isset ( $_SERVER ['WINDIR'] ) || strpos ( $_SERVER ['SERVER_SOFTWARE'], 'Win32' ) !== false)
				return "win32";
			if (! isset ( $_SERVER ['WINDIR'] ) && strpos ( $_SERVER ['SERVER_SOFTWARE'], 'Linux' ) !== false)
				return "linux";
		}
		
		if (file_exists ( "C:\Windows" ))
			return "win32";
		
		return "unix";
	}
	
	/**
	 * Return the super parent class name
	 *
	 * @param string $classname        	
	 * @return string
	 */
	final public function getSuperParent($classname = null) {
		if (is_null ( $classname ))
			$classname = get_class ( $this );
		$parent = get_parent_class ( $classname );
		if ($parent === false)
			return $classname;
		return $this->getSuperParent ( $parent );
		;
	}
	
	/**
	 * Return the current template's id
	 *
	 * @return integer
	 */
	final public function getId() {
		return $this->__id;
	}
	
	/**
	 * Create an auxiliar instance (as singleton)
	 *
	 * @param string $classname        	
	 */
	final static function createAuxiliarEngine(&$from = null) {
		if (is_null ( $from ))
			$classname = self::$__super_class;
		else
			$classname = get_class ( $from );
		if (! is_null ( self::$__engine ))
			if (get_class ( self::$__engine ) != $classname)
				self::$__engine = null;
		if (is_null ( self::$__engine )) {
			if (self::$__log_mode)
				self::log ( "createAuxiliarEngine: A new $classname instance will be created ..." );
			$tmp = self::$__discard_file_system;
			self::$__discard_file_system = true;
			self::$__engine = new $classname ( "", array () );
			if (! is_null ( $from )) {
				self::$__engine->__items = $from->__items;
				self::$__engine->__items_orig = $from->__items_orig;
			}
			self::$__discard_file_system = $tmp;
		}
	}
	
	/**
	 * Create a clone of auxiliary
	 *
	 * @return div
	 */
	final static function getAuxiliaryEngineClone(&$items = null, &$items_orig = null) {
		if (is_null ( self::$__engine ))
			self::createAuxiliarEngine ();
		
		$obj = clone self::$__engine;
		
		if (self::$__log_mode)
			self::log ( "getAuxiliaryEngineClone: New auxiliary #" . $obj->getId () );
		if (! is_null ( $items ))
			$obj->__items = $items;
		if (! is_null ( $items_orig ))
			$obj->__items_orig = $items_orig;
		
		return $obj;
	}
	
	/**
	 * Save parser's operationsd
	 *
	 * @param array $params        	
	 */
	final public function saveOperation($params = array()) {
		if (! isset ( self::$__remember [$this->__crc] ))
			self::$__remember [$this->__crc] = array ();
		$id = crc32 ( serialize ( $params ) );
		if (! isset ( self::$__remember [$this->__crc] [$id] ))
			self::$__remember [$this->__crc] [$id] = $params;
	}
	
	/**
	 * Return the saved operations in $__remember
	 *
	 * @return array:
	 */
	final static function getMemories() {
		return self::$__remember;
	}
	
	/**
	 * Set operations saved previously
	 *
	 * @param array $memories        	
	 */
	final static function setMemories($memories) {
		foreach ( $memories as $k => $v ) {
			self::$__remember [$k] = $v;
		}
	}
	
	/**
	 * Add a custom variable's modifier
	 *
	 * @param string $prefix        	
	 * @param string $function        	
	 */
	final static function addCustomModifier($prefix, $function) {
		self::$__custom_modifiers [$prefix] = array (
				$prefix,
				$function 
		);
		self::$__modifiers [] = $prefix;
	}
	
	/**
	 * Enable system var for utility
	 *
	 * @param string $var        	
	 */
	final static function enableSystemVar($var) {
		self::$__system_data_allowed [$var] = true;
	}
	
	/**
	 * Disable system var for performance
	 *
	 * @param string $var        	
	 */
	final static function disableSystemVar($var) {
		if (isset ( self::$__system_data_allowed [$var] ))
			unset ( self::$__system_data_allowed [$var] );
	}
	
	/**
	 * Return the loaded data from the system
	 *
	 * @return array
	 */
	final static function getSystemData() {
		$d = DIV_TAG_VAR_MEMBER_DELIMITER;
		if (self::$__system_data == null) {
			
			self::$__system_data = array ();
			
			if (isset ( self::$__system_data_allowed ["div{$d}ascii"] )) {
				$ascii = array ();
				for($i = 0; $i <= 255; $i ++)
					$ascii [$i] = chr ( $i );
				self::$__system_data ["div{$d}ascii"] = $ascii;
			}
			
			if (isset ( self::$__system_data_allowed ["div{$d}now"] ))
				self::$__system_data ["div{$d}now"] = time ();
			if (isset ( self::$__system_data_allowed ["div{$d}post"] ))
				self::$__system_data ["div{$d}post"] = $_POST;
			if (isset ( self::$__system_data_allowed ["div{$d}get"] ))
				self::$__system_data ["div{$d}get"] = $_GET;
			if (isset ( self::$__system_data_allowed ["div{$d}server"] ))
				self::$__system_data ["div{$d}server"] = $_SERVER;
			if (isset ( self::$__system_data_allowed ["div{$d}session"] ))
				self::$__system_data ["div{$d}session"] = isset ( $_SESSION ) ? $_SESSION : array ();
			if (isset ( self::$__system_data_allowed ["div{$d}version"] ))
				self::$__system_data ["div{$d}version"] = self::$__version;
			if (isset ( self::$__system_data_allowed ["div{$d}script_name"] )) {
				$script_name = explode ( '/', $_SERVER ['SCRIPT_NAME'] );
				$script_name = $script_name [count ( $script_name ) - 1];
				self::$__system_data ["div{$d}script_name"] = $script_name;
			}
		}
		return self::$__system_data;
	}
	
	/**
	 * Set allowed function
	 *
	 * @param string $funcname        	
	 */
	final static function setAllowedFunction($funcname) {
		self::$__allowed_functions [$funcname] = true;
	}
	
	/**
	 * Unset allowed function
	 *
	 * @param string $funcname        	
	 */
	final static function unsetAllowedFunction($funcname) {
		self::$__allowed_functions [$funcname] = false;
	}
	
	/**
	 * Add or set a global var
	 *
	 * @param string $var        	
	 * @param mixed $value        	
	 */
	final static function setGlobal($var, $value) {
		self::$__globals [$var] = $value;
	}
	
	/**
	 * Remove a global var
	 *
	 * @param string $var        	
	 */
	final static function delGlobal($var) {
		unset ( self::$__globals [$var] );
	}
	
	/**
	 * Add or set a default replacement of value
	 *
	 * @param mixed $search        	
	 * @param mixed $replace        	
	 */
	final static function setDefault($search, $replace) {
		self::$__defaults [serialize ( $search )] = $replace;
	}
	
	/**
	 * Add or set a default replacement of value for a specific var
	 *
	 * @param string $var        	
	 * @param mixed $search        	
	 * @param mixed $replace        	
	 * @param boolean $update        	
	 */
	final static function setDefaultByVar($var, $search, $replace, $update = true) {
		$id = serialize ( $search );
		if (! isset ( self::$__defaults_by_var [$var] ))
			self::$__defaults_by_var [$var] = array ();
		if (! isset ( self::$__defaults_by_var [$var] [$id] ) && $update === true)
			self::$__defaults_by_var [$var] [$id] = $replace;
	}
	
	/**
	 * Set a sub-parser
	 *
	 * @param string $name        	
	 * @param string $function        	
	 */
	final static function setSubParser($name, $function = null) {
		if (is_array ( $name )) {
			if (is_null ( $function )) {
				foreach ( $name as $key => $value ) {
					if (is_numeric ( $key ))
						self::$__sub_parsers [$value] = $value;
					else
						self::$__sub_parsers [$key] = $value;
				}
			} elseif (is_array ( $function )) {
				foreach ( $name as $key => $value )
					self::$__sub_parsers [$value] = $function [$key];
			} else {
				foreach ( $name as $key => $value )
					self::$__sub_parsers [$value] = $function;
			}
		} else {
			if (is_null ( $function ))
				$function = $name;
			self::$__sub_parsers [$name] = $function;
		}
		self::repairSubparsers ();
	}
	
	/**
	 * Repair the subparsers and their events
	 */
	final static function repairSubparsers() {
		$events = array (
				'beforeParse',
				'afterInclude',
				'afterParse',
				'afterReplace' 
		);
		$news = array ();
		
		foreach ( self::$__sub_parsers as $parser => $function ) {
			$arr = explode ( ":", $parser );
			
			if (isset ( $arr [1] )) {
				$last = array_pop ( $arr );
				if (array_search ( $last, $events ) !== false)
					continue;
			}
			
			foreach ( $events as $event )
				if (! isset ( self::$__sub_parsers ["$parser:$event"] )) {
					$news ["$parser:$event"] = $function;
				}
		}
		
		self::$__sub_parsers = array_merge ( self::$__sub_parsers, $news );
	}
	
	/**
	 * Load template from filesystem
	 *
	 * @param string $path        	
	 * @return string
	 */
	final public function loadTemplate($path) {
		if (self::$__log_mode === true)
			$this->logger ( "Loading the template: $path" );
		
		$src = $path;
		
		if (strlen ( $path ) < 255) {
			$paths = array (
					$path,
					$path . '.' . DIV_DEFAULT_TPL_FILE_EXT,
					$path,
					$path . '.' . DIV_DEFAULT_TPL_FILE_EXT 
			);
			
			foreach ( $paths as $pathx ) {
				if (strlen ( $pathx ) < 255)
					if (self::fileExists ( $pathx )) {
						$src = self::getFileContents ( $pathx );
						$this->__path = $pathx;
						break;
					}
			}
		}
		
		return $src;
	}
	
	/**
	 * Change the template and the original template
	 *
	 * @param string $src        	
	 */
	final public function changeTemplate($src = null) {
		$decode = true;
		$classname = get_class ( $this );
		$discardfs = self::$__discard_file_system;
		
		if (is_null ( $src )) {
			if ($classname != self::$__super_class && is_null ( $this->__src ))
				$src = $classname;
			if (! is_null ( $this->__src ))
				$src = $this->__src;
		}
		
		if (! $discardfs)
			$src = $this->loadTemplate ( $src );
		
		$this->__src = $src;
		$this->__src_original = $src;
	}
	
	/**
	 * Return the code of current template
	 *
	 * @return string
	 */
	final public function getTemplate() {
		return $this->__src;
	}
	
	/**
	 * Return the original code of template
	 *
	 * @return string
	 */
	final public function getOriginalTemplate() {
		return $this->__src_original;
	}
	
	/**
	 * Remove a default replacement
	 *
	 * @param mixed $search        	
	 */
	final static function delDefault($search) {
		$id = serialize ( $search );
		if (isset ( self::$__defaults [$id] ))
			unset ( self::$__defaults [$id] );
	}
	
	/**
	 * Remove a default replacement by var
	 *
	 * @param string $var        	
	 * @param mixed $search        	
	 */
	final static function delDefaultByVar($var, $search) {
		if (isset ( self::$__defaults_by_var [$var] )) {
			$id = serialize ( $search );
			if (isset ( self::$__defaults_by_var [$var] [$id] ))
				unset ( self::$__defaults_by_var [$var] [$id] );
		}
	}
	
	/**
	 * Add or Set item of information
	 *
	 * @param string $var        	
	 * @param mixed $value        	
	 * @return mixed
	 */
	final public function setItem($var, $value = null) {
		if (is_array ( $var )) {
			$r = array ();
			foreach ( $var as $idx => $val ) {
				if (self::issetVar ( $idx, $this->__items ))
					$r [$idx] = self::getVarValue ( $idx, $this->__items );
				else
					$r [$idx] = null;
				
				self::setVarValue ( $idx, $val, $this->__items );
			}
			
			return $r;
		}
		
		if (self::issetVar ( $var, $this->__items ))
			$item = self::getVarValue ( $var, $this->__items );
		else
			$item = null;
		
		self::setVarValue ( $var, $value, $this->__items );
		
		return $item;
	}
	
	/**
	 * Delete an item of information
	 *
	 * @param string $var        	
	 * @return boolean
	 */
	final public function delItem($var) {
		return self::unsetVar ( $var, $this->__items );
	}
	
	/**
	 * Return an item
	 *
	 * @param array $array        	
	 * @param mixed $index        	
	 * @param mixed $default        	
	 * @return mixed
	 */
	final public function getItem($var, $default = null) {
		if (! self::issetVar ( $var, $this->__items ))
			return $default;
		return self::getVarValue ( $var, $this->__items );
	}
	
	/**
	 * Return all items (in memory and context)
	 *
	 * @param array $items        	
	 * @return array
	 */
	final public function getAllItems($items = array()) {
		$i = $this->__items;
		$m = $this->__memory;
		$g = self::$__globals_design;
		$i = self::cop ( $i, $m );
		$i = self::cop ( $i, $g );
		$i = self::cop ( $i, $items );
		return $i;
	}
	
	/**
	 * Return a list of block's ranges
	 *
	 * @param string $tagini        	
	 * @param string $tagend        	
	 * @param boolean $onlyfirst        	
	 * @param integer $pos        	
	 * @return array
	 */
	final public function getRanges($tagini, $tagend, $src = null, $onlyfirst = false, $pos = 0) {
		$ranges = array ();
		if (is_null ( $src ))
			if (isset ( $this ))
				$src = $this->__src;
		if (! is_null ( $src ))
			if (isset ( $src [0] ) && ! empty ( $src )) {
				$ltagini = strlen ( $tagini );
				$ltagend = strlen ( $tagend );
				do {
					$ini = false;
					
					if (isset ( $src [$pos] ))
						$ini = strpos ( $src, $tagini, $pos );
					
					if ($ini !== false) {
						if (isset ( $src [$ini + $ltagini] )) {
							$fin = strpos ( $src, $tagend, $ini + $ltagini );
							if ($fin !== false) {
								$l = strlen ( $src );
								$last_pos = - 1;
								while ( true ) {
									$ini = strpos ( $src, $tagini, $pos );
									if ($ini === false || ($ini !== false && $pos == $last_pos))
										break;
									$end = false;
									$plus = 1;
									$posi = $ini + $ltagini;
									$last_posi = $posi - 1;
									
									while ( true ) {
										$open = strpos ( $src, $tagini, $posi );
										$close = strpos ( $src, $tagend, $posi );
										
										if ($open === false && $close === false)
											break; // not open and not close
										if ($open === false && $close !== false && $posi === $last_posi)
											break; // close and not open
										if ($open !== false && $close === false && $posi === $last_posi)
											break; // open and not close
										
										if ($open !== false || $close !== false) { // open or close
											if (($close < $open || $open === false) && $close !== false) { // close if is closed and before open or not open
												$last_posi = $posi;
												$posi = $close + $ltagend;
												$plus --;
												// IMPORTANT! Don't separate elseif
											} elseif (($open < $close || $close === false) && $open !== false) { // open if is opened and before close or not close
												$last_posi = $posi;
												$posi = $open + $ltagini;
												$plus ++;
											}
										}
										
										if ($plus === 0) { // all opens are closed
											$end = $close;
											break;
										}
										
										if ($open >= $l)
											break;
									}
									
									$last_pos = $pos;
									
									if ($end != false) {
										$ranges [] = array (
												$ini,
												$end 
										);
										if ($onlyfirst == true)
											break;
										$pos = $ini + $ltagini;
										continue;
									}
								}
							}
						}
					}
					
					if (! isset ( $ranges [0] ) && $ini !== false) {
						if (self::$__log_mode)
							if (isset ( $this )) {
								foreach ( $this->__items as $key => $value )
									if (strpos ( $tagini, $key ) !== false) {
										$this->logger ( 'Unclosed tag ' . $tagini . ' at ' . $ini . ' character', DIV_ERROR_WARNING );
										break;
									}
							}
						
						$pos = $ini + 1;
						
						continue;
					}
					
					break;
				} while ( true );
			}
		return $ranges;
	}
	
	/**
	 * Return a list of ranges of blocks
	 *
	 * @param string $src        	
	 * @param string $begin_prefix        	
	 * @param string $begin_suffix        	
	 * @param string $end_prefix        	
	 * @param string $end_suffix        	
	 * @param integer $after        	
	 * @param integer $before        	
	 * @return array
	 */
	final public function getBlockRanges($src = null, $begin_prefix = '{', $begin_suffix = '}', $end_prefix = '{/', $end_suffix = '}', $after = 0, $before = null, $onlyfirst = false, $var_member_delimiter = DIV_TAG_VAR_MEMBER_DELIMITER) {
		if (is_null ( $src ))
			$src = $this->__src;
		if (! is_null ( $before ))
			$src = substr ( $src, 0, $before );
		
		$l = strlen ( $src );
		$l1 = strlen ( $begin_prefix );
		$tagsya = array ();
		$ranges = array ();
		$from = $after;
		$ldelimiter = strlen ( $var_member_delimiter );
		do {
			$prefix_pos = strpos ( $src, $begin_prefix, $from );
			if ($prefix_pos !== false) {
				if (isset ( $src [$prefix_pos + 1] )) {
					
					if ($begin_suffix != '' && ! is_null ( $begin_suffix )) {
						$suffix_pos = strpos ( $src, $begin_suffix, $prefix_pos + 1 );
					} else {
						
						$stopchars = array (
								"<",
								">",
								' ',
								"\n",
								"\r",
								"\t" 
						);
						$stoppos = array ();
						
						foreach ( $stopchars as $k => $v ) {
							$continue = false;
							$pp = false;
							do {
								
								$pp = strpos ( $src, $v, $pp !== false ? $pp + 1 : $prefix_pos );
								
								if ($pp === false) {
									$continue = true;
									break;
								}
							} while ( substr ( $src, $pp - $ldelimiter + 1, $ldelimiter ) === $var_member_delimiter );
							
							if ($continue)
								continue;
							
							$stoppos [] = $pp;
						}
						
						$suffix_pos = false;
						if (count ( $stoppos ) > 0)
							$suffix_pos = min ( $stoppos );
					}
					
					$key = '';
					if ($suffix_pos < $l && $suffix_pos !== false)
						$key = substr ( $src, $prefix_pos + $l1, $suffix_pos - $prefix_pos - $l1 );
					
					if ($key !== '' && ! isset ( $tagsya [$key] )) {
						$tag_begin = $begin_prefix . $key . $begin_suffix;
						$tag_end = $end_prefix . $key . $end_suffix;
						
						$rs = $this->getRanges ( $tag_begin, $tag_end, $src, $onlyfirst, $from );
						$l2 = strlen ( $tag_begin );
						foreach ( $rs as $k => $v ) {
							$rs [$k] [2] = $key;
							$rs [$k] [3] = substr ( $src, $v [0] + $l2, $v [1] - $v [0] - $l2 );
							$rs [$k] [4] = strlen ( $key );
						}
						$ranges = array_merge ( $ranges, $rs );
						
						// Only the first...
						if ($onlyfirst)
							if (isset ( $ranges [0] ))
								break;
						
						$tagsya [$key] = true;
					}
				}
				$from = $prefix_pos + 1;
			}
		} while ( $prefix_pos !== false );
		
		return $ranges;
	}
	
	/**
	 * Return a default replacement of value
	 *
	 * @param mixed $value        	
	 * @return mixed
	 */
	final static function getDefault($value) {
		$id = serialize ( $value );
		if (isset ( self::$__defaults [$id] ))
			return self::$__defaults [$id];
		return $value;
	}
	
	/**
	 * Return a default replacement of value by var
	 *
	 * @param string $var        	
	 * @param mixed $value        	
	 * @return mixed
	 */
	final static function getDefaultByVar($var, $value) {
		if (isset ( self::$__defaults_by_var [$var] )) {
			$id = serialize ( $value );
			if (isset ( self::$__defaults_by_var [$var] [$id] ))
				return self::$__defaults_by_var [$var] [$id];
		}
		
		return $value;
	}
	
	// ------------------------------------------- SEARCHERS ----------------------------------------- //
	
	/**
	 * Search a position in a list of ranges
	 *
	 * @param array $ranges        	
	 * @param integer $pos        	
	 * @return boolean
	 */
	final public function searchInRanges($ranges, $pos, $strict = false) {
		foreach ( $ranges as $range ) {
			if ($strict) {
				if ($pos > $range [0] && $pos < $range [1])
					return true;
			} else {
				if ($pos >= $range [0] && $pos <= $range [1])
					return true;
			}
		}
		return false;
	}
	
	/**
	 * Search $pos in the ranges of lists/loops in current source
	 *
	 * @param array $items        	
	 * @return boolean
	 */
	final public function searchInListRanges($pos = 0) {
		$rangs = $this->getBlockRanges ( null, DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_BEGIN_SUFFIX, DIV_TAG_LOOP_END_PREFIX, DIV_TAG_LOOP_END_SUFFIX );
		foreach ( $rangs as $rang )
			if ($pos > $rang [0] && $pos < $rang [1])
				return true;
		
		return false;
	}
	
	/**
	 * Search $pos before the frist range of lists/loops in current source
	 *
	 * @param array $items        	
	 * @param integer $pos        	
	 * @return boolean
	 */
	final public function searchPreviousLoops($pos = 0) {
		$rangs = $this->getBlockRanges ( null, DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_BEGIN_SUFFIX, DIV_TAG_LOOP_END_PREFIX, DIV_TAG_LOOP_END_SUFFIX );
		foreach ( $rangs as $rang )
			if ($pos > $rang [0])
				return $rang [0];
		
		return false;
	}
	
	/**
	 * Return true if pos is after first range
	 *
	 * @param string $tag_begin        	
	 * @param string $tag_end        	
	 * @param integer $pos        	
	 * @return boolean
	 */
	final public function searchPosAfterRange($tag_begin, $tag_end, $pos) {
		$rangs = $this->getRanges ( $tag_begin, $tag_end, null, true );
		if (isset ( $rangs [0] ))
			if ($rangs [0] [0] < $pos)
				return true;
		return false;
	}
	
	/**
	 * Return true if pos is in the ranges of capsules of current source
	 *
	 * @param array $items        	
	 * @return array
	 */
	final public function searchInCapsuleRanges(&$items = null, $pos = 0) {
		if (is_null ( $items ))
			$items = &$this->__items;
		foreach ( $items as $key => $value ) {
			$rangs = $this->getRanges ( DIV_TAG_CAPSULE_BEGIN_PREFIX . $key, DIV_TAG_CAPSULE_END_PREFIX . $key );
			foreach ( $rangs as $rang )
				if ($pos >= $rang [0] && $pos <= $rang [1])
					return true;
		}
		return false;
	}
	
	// -------------------------------------------------------------------------------------------------- //
	
	/**
	 * Return any value as a boolean
	 *
	 * @param mixed $value        	
	 * @return boolean
	 */
	final static function mixedBool($value) {
		if (is_bool ( $value ))
			return $value;
		if (is_object ( $value ))
			return count ( get_object_vars ( $value ) ) > 0;
		if (is_array ( $value ))
			return count ( $value ) > 0;
		if (self::isString ( $value )) {
			if (strtolower ( $value ) == 'false' || $value == '0')
				return false;
			if (strtolower ( $value ) == 'true' || $value == '1')
				return true;
			return strlen ( trim ( $value ) ) > 0;
		}
		if (is_numeric ( $value ))
			return $value > 0;
		if (is_null ( $value ))
			return false;
		return $value;
	}
	
	/**
	 * Return the correct @else@ tag of conditional block
	 *
	 * @param string $subsrc        	
	 * @return mixed
	 */
	final public function getElseTag($subsrc) {
		$else = false;
		$range_conditions = $this->getRanges ( DIV_TAG_CONDITIONS_BEGIN_PREFIX, DIV_TAG_CONDITIONS_END, $subsrc );
		$range_conditionals = $this->getConditionalRanges ( true, $subsrc );
		$rangesx = array_merge ( $range_conditions, $range_conditionals );
		
		$pelse = 0;
		$ls = strlen ( $subsrc );
		do {
			$continue = false;
			if ($pelse < $ls)
				$else = strpos ( $subsrc, DIV_TAG_ELSE, $pelse );
			else
				$else = false;
				
				// checking that the tag doesn't belong to another IF inside this IF
			if ($else !== false) {
				foreach ( $rangesx as $r )
					if ($else >= $r [0] && $else <= $r [1]) {
						$pelse = $r [1] + 1;
						$else = false;
						$continue = true;
						break;
					}
			}
		} while ( $continue == true );
		
		return $else;
	}
	
	/**
	 * Return the correct DIV_TAG_EMPTY tag of list block
	 *
	 * @param string $subsrc        	
	 * @return mixed
	 */
	final public function getEmptyTag($subsrc) {
		$else = false;
		$rangesx = $this->getRanges ( DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_END_PREFIX, $subsrc );
		
		$pempty = 0;
		$ls = strlen ( $subsrc );
		do {
			$continue = false;
			if ($pempty < $ls)
				$empty = strpos ( $subsrc, DIV_TAG_EMPTY, $pempty );
			else
				$empty = false;
				
				// checking that the tag doesn't belong to another list block inside this list block
			if ($empty !== false) {
				foreach ( $rangesx as $r )
					if ($empty >= $r [0] && $empty <= $r [1]) {
						$pempty = $r [1] + 1;
						$empty = false;
						$continue = true;
						break;
					}
			}
		} while ( $continue == true );
		
		return $empty;
	}
	
	/**
	 * Parse conditional blocks
	 *
	 * @param string $src        	
	 * @param string $key        	
	 * @param mixed $value        	
	 * @return string
	 */
	final public function parseConditionalBlock($key, $value) {
		if (self::$__log_mode)
			$this->logger ( 'Parsing conditional block: ' . $key );
		
		if (isset ( $this->__ignore [$key] ))
			return false;
		$src = &$this->__src;
		
		if (is_array ( $value ) || is_object ( $value )) {
			$vars = $value;
			if (is_object ( $vars ))
				$vars = get_object_vars ( $vars );
			foreach ( $vars as $k => $val ) {
				if (is_numeric ( $k ))
					break;
				$this->parseConditionalBlock ( $key . DIV_TAG_VAR_MEMBER_DELIMITER . $k, $val );
			}
		}
		
		$value = self::mixedBool ( $value );
		$passes = array (
				false,
				true 
		);
		$pos = 0;
		
		foreach ( $passes as $flag ) {
			
			if ($flag === false) {
				$tag_begin = DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX . $key . DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX;
				$tag_end = DIV_TAG_CONDITIONAL_TRUE_END_PREFIX . $key . DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX;
			} else {
				$tag_begin = DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX . $key . DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX;
				$tag_end = DIV_TAG_CONDITIONAL_FALSE_END_PREFIX . $key . DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX;
			}
			
			$l = strlen ( $tag_begin );
			$l2 = strlen ( $tag_end );
			$lelse = strlen ( DIV_TAG_ELSE );
			
			while ( true ) {
				
				if (strpos ( $src, $tag_begin ) === false)
					break;
				$ranges = $this->getRanges ( $tag_begin, $tag_end, $src, true, $pos );
				
				if (count ( $ranges ) > 0) {
					
					$ini = $ranges [0] [0];
					$fin = $ranges [0] [1];
					
					// Controlling injected vars
					// _is_last _is_first _is_odd _is_even
					
					if (array_search ( $key, array (
							"_is_last",
							"_is_first",
							"_is_odd",
							"_id_even" 
					) ) !== false) {
						if ($this->isBlockInsideBlock ( DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_END_PREFIX, $ini, $fin )) {
							$pos = $fin + 1;
							if (self::$__log_mode)
								$this->logger ( "Ignore the injected var inside another list block: $key.." );
							continue;
						}
					}
					
					if ($this->searchPosAfterRange ( DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END, $ini )) {
						$pos = $fin + 1;
						continue;
					}
					
					$subsrc = substr ( $src, $ini + $l, $fin - ($ini + $l) );
					
					$else = $this->getElseTag ( $subsrc );
					
					if ($value === $flag) {
						if ($else !== false) {
							$con = substr ( $src, $ini + $l + $else + $lelse, $fin - ($ini + $l + $else + $lelse) );
							if ($con [0] == ' ')
								$con = substr ( $con, 1 );
							if (substr ( $con, - 1 ) == ' ')
								$con = substr_replace ( $con, "", - 1 );
							$src = substr ( $src, 0, $ini ) . $con . substr ( $src, $fin + $l2 );
						} else
							$src = substr ( $src, 0, $ini ) . substr ( $src, $fin + $l2 );
					} else {
						if ($else !== false) {
							$con = substr ( $src, $ini + $l, $else );
							if ($con [0] == ' ')
								$con = substr ( $con, 1 );
							if (substr ( $con, - 1 ) == ' ')
								$con = substr_replace ( $con, "", - 1 );
							$src = substr ( $src, 0, $ini ) . $con . substr ( $src, $fin + $l2 );
						} else {
							$con = substr ( $src, $ini + $l, $fin - ($ini + $l) );
							if ($con [0] == ' ')
								$con = substr ( $con, 1 );
							if (substr ( $con, - 1 ) == ' ')
								$con = substr_replace ( $con, "", - 1 );
							$src = substr ( $src, 0, $ini ) . $con . substr ( $src, $fin + $l2 );
						}
					}
					
					$pos = $ini + 1;
				} else {
					break;
				}
			}
		}
	}
	
	/**
	 * Format for number
	 *
	 * @param string $key        	
	 * @param string $src        	
	 * @return string
	 */
	final public function numberFormat($key, $value) {
		if (isset ( $this->__ignore [$key] ))
			return false;
		
		$tag_begin = DIV_TAG_NUMBER_FORMAT_PREFIX . $key . DIV_TAG_NUMBER_FORMAT_SEPARATOR;
		$tag_end = DIV_TAG_NUMBER_FORMAT_SUFFIX;
		$l1 = strlen ( $tag_begin );
		$l2 = strlen ( $tag_end );
		if (strpos ( $this->__src, $tag_begin ) === false)
			return false;
		if (strpos ( $this->__src, $tag_end ) === false)
			return false;
		
		$p1 = strpos ( $this->__src, DIV_TAG_TPLVAR_BEGIN );
		$pos = 0;
		while ( true ) {
			$ranges = $this->getRanges ( $tag_begin, $tag_end, null, true, $pos );
			
			if (count ( $ranges ) < 1)
				break;
			$ini = $ranges [0] [0];
			$fin = $ranges [0] [1];
			
			if (self::$__log_mode)
				$this->logger ( "Formatting number $key = $value" );
			
			if ($ini > $p1 && $p1 !== false)
				return true;
			
			if ($this->searchPosAfterRange ( DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END, $ini )) {
				$pos = $ini + 1;
				continue;
			}
			
			if ($this->searchInListRanges ( $ini )) {
				$pos = $ini + 1;
				continue;
			}
			
			$format = substr ( $this->__src, $ini + $l1, $fin - ($ini + $l1) );
			if (trim ( $format ) == "")
				$format = "0.";
			
			if (! is_numeric ( $value )) {
				$this->__src = substr ( $this->__src, 0, $ini ) . DIV_TAG_NUMBER_FORMAT_PREFIX . $value . DIV_TAG_NUMBER_FORMAT_SEPARATOR . $format . DIV_TAG_NUMBER_FORMAT_SUFFIX . substr ( $this->__src, $fin + $l2 );
				return true;
			}
			
			$separator = ".";
			$miles_sep = "";
			
			if (! is_numeric ( substr ( $format, strlen ( $format ) - 1 ) )) {
				$separator = substr ( $format, strlen ( $format ) - 1 );
				$format = substr ( $format, 0, strlen ( $format ) - 1 );
			}
			
			if (! is_numeric ( substr ( $format, strlen ( $format ) - 1 ) )) {
				$miles_sep = $separator;
				$separator = substr ( $format, strlen ( $format ) - 1 );
				$format = substr ( $format, 0, strlen ( $format ) - 1 );
			}
			
			$decimals = intval ( $format );
			$this->__src = substr ( $this->__src, 0, $ini ) . number_format ( $value, $decimals, $separator, $miles_sep ) . substr ( $this->__src, $fin + $l2 );
		}
	}
	
	/**
	 * Parse submatch
	 *
	 * @param mixed $items        	
	 * @param string $key        	
	 * @param mixed $value        	
	 * @param string $modifiers        	
	 * @return boolean
	 */
	final public function parseSubmatch(&$items, $key, $value, $modifiers = array()) {
		if (isset ( $this->__ignore [$key] ))
			return false;
		
		$literal = $this->isLiteral ( $key );
		
		$vpx = '';
		$vsx = '';
		
		if ($literal === true) {
			$vpx = '{' . $this->__ignore_secret_tag . '}';
			$vsx = '{/' . $this->__ignore_secret_tag . '}';
		}
		
		if (strpos ( $this->__src, $key . DIV_TAG_SUBMATCH_SEPARATOR ) !== false)
			foreach ( $modifiers as $modifier )
				while ( true ) {
					$tag_begin = DIV_TAG_REPLACEMENT_PREFIX . $modifier . $key . DIV_TAG_SUBMATCH_SEPARATOR;
					$ranges = $this->getRanges ( $tag_begin, DIV_TAG_REPLACEMENT_SUFFIX, null, true );
					$l = strlen ( $tag_begin );
					if (count ( $ranges ) < 1)
						break;
					
					if (self::$__log_mode)
						$this->logger ( "Parsing submatch  $tag_begin" );
						
						// User wrote
					$r = substr ( $this->__src, $ranges [0] [0] + $l, $ranges [0] [1] - ($ranges [0] [0] + $l) );
					
					// Interpreted by Div
					$arr = explode ( DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR, $r );
					if (count ( $arr ) < 2)
						$arr = array (
								0,
								$arr [0] 
						);
					$arr [0] = trim ( $arr [0] );
					$arr [1] = trim ( $arr [1] );
					
					$nkey = str_replace ( ".", "", uniqid ( "submatch", true ) );
					
					self::$__dont_remember_it [$nkey] = true;
					
					if (! is_numeric ( $arr [0] ) || ! is_numeric ( $arr [1] )) {
						if (substr ( "{$arr[1]}", 0, 1 ) == DIV_TAG_MODIFIER_TRUNCATE) {
							$items [$nkey] = $vpx . self::teaser ( "{$value}", intval ( substr ( $arr [1], 1 ) ) ) . $vsx;
							$this->saveOperation ( array (
									"o" => "replace_submatch_teaser",
									"key" => $key,
									"modifier" => $modifier,
									"param" => $r 
							) );
						} elseif (substr ( "{$arr[1]}", 0, 1 ) == DIV_TAG_MODIFIER_WORDWRAP) {
							$items [$nkey] = $vpx . wordwrap ( "{$value}", intval ( substr ( $arr [1], 1 ) ), "\n", 1 ) . $vsx;
							$this->saveOperation ( array (
									"o" => "replace_submatch_wordwrap",
									"key" => $key,
									"modifier" => $modifier,
									"param" => $r 
							) );
						} elseif (substr ( "{$arr[1]}", 0, 1 ) == DIV_TAG_MODIFIER_FORMAT || DIV_TAG_MODIFIER_FORMAT == '') {
							$items [$nkey] = $vpx . sprintf ( $arr [1], $value ) . $vsx;
							$this->saveOperation ( array (
									"o" => "replace_submatch_sprintf",
									"key" => $key,
									"modifier" => $modifier,
									"param" => $r 
							) );
						}
					} else {
						$items [$nkey] = $vpx . substr ( "$value", $arr [0], $arr [1] ) . $vsx;
						$this->saveOperation ( array (
								"o" => "replace_submatch_substr",
								"key" => $key,
								"modifier" => $modifier,
								"param" => $r,
								"from" => $arr [0],
								"for" => $arr [1] 
						) );
					}
					
					$right = "";
					
					if ($ranges [0] [1] + 1 < strlen ( $this->__src ))
						$right = substr ( $this->__src, $ranges [0] [1] + strlen ( DIV_TAG_REPLACEMENT_SUFFIX ) );
					$this->__src = substr ( $this->__src, 0, $ranges [0] [0] ) . DIV_TAG_REPLACEMENT_PREFIX . "{$modifier}$nkey" . DIV_TAG_REPLACEMENT_SUFFIX . $right;
				}
		
		if (strpos ( $this->__src, "$key" . DIV_TAG_VAR_MEMBER_DELIMITER ) !== false) {
			if (is_object ( $value )) {
				$vars = get_object_vars ( $value );
				foreach ( $vars as $kk => $v )
					$this->parseSubmatch ( $items, $key . DIV_TAG_VAR_MEMBER_DELIMITER . $kk, $v, $modifiers );
			}
			if (is_array ( $value ))
				foreach ( $value as $kk => $v )
					$this->parseSubmatch ( $items, $key . DIV_TAG_VAR_MEMBER_DELIMITER . $kk, $v, $modifiers );
		}
		
		return true;
	}
	
	/**
	 * Parsing sub matches
	 *
	 * @param mixed $items        	
	 */
	final public function parseSubmatches(&$items = null) {
		if (self::$__log_mode)
			$this->logger ( "Parsing submatches..." );
		if (is_null ( $items ))
			$items = &$this->__items;
		$modifiers = array ();
		foreach ( self::$__modifiers as $m )
			if (strpos ( $this->__src, DIV_TAG_REPLACEMENT_PREFIX . $m ) !== false)
				$modifiers [] = $m;
		foreach ( $items as $key => $value )
			$this->parseSubmatch ( $items, $key, $value, $modifiers );
	}
	
	/**
	 * Parse IDE's friendly marks
	 */
	final public function parseFriendly() {
		$l1 = strlen ( DIV_TAG_FRIENDLY_BEGIN );
		$l2 = strlen ( DIV_TAG_FRIENDLY_END );
		while ( true ) {
			$r = $this->getRanges ( DIV_TAG_FRIENDLY_BEGIN, DIV_TAG_FRIENDLY_END, null, true );
			if (count ( $r ) < 1)
				break;
			$ini = $r [0] [0];
			$fin = $r [0] [1];
			$this->__src = substr ( $this->__src, 0, $ini ) . substr ( $this->__src, $ini + $l1, $fin - ($ini + $l1) ) . substr ( $this->__src, $fin + $l2 );
		}
	}
	
	/**
	 * Set a variable as literal
	 *
	 * @param string $var        	
	 */
	final public function addLiteral($var) {
		if (is_string ( $var ))
			$var = explode ( " ", str_replace ( ",", " ", $var ) );
		
		$literals = self::getVarValue ( 'div' . DIV_TAG_VAR_MEMBER_DELIMITER . 'literals', self::$__globals_design );
		
		if (is_null ( $literals ) || $literals === false)
			$literals = array ();
		
		if (is_string ( $literals ))
			$literals = explode ( " ", str_replace ( ",", " ", $literals ) );
		
		$literals = array_merge ( $literals, $var );
		
		self::setVarValue ( 'div' . DIV_TAG_VAR_MEMBER_DELIMITER . 'literals', $literals, self::$__globals_design, true );
	}
	
	/**
	 * Get literal vars from dynamic configuration
	 */
	final public function getLiterals() {
		$val = self::getVarValue ( 'div' . DIV_TAG_VAR_MEMBER_DELIMITER . 'literals', $this->__memory );
		
		if (is_null ( $val ) || $val === false)
			return array ();
		
		if (is_string ( $val ))
			$val = explode ( " ", str_replace ( ",", " ", $val ) );
		
		$arr = array ();
		foreach ( $val as $v ) {
			$v = trim ( $v );
			if ($v != '') {
				$arr [$v] = $v;
			}
		}
		
		return $arr;
	}
	
	/**
	 * Return if a var is literal or not
	 *
	 * @param string $key        	
	 */
	final public function isLiteral($key) {
		if (trim ( $key ) == '')
			return false;
		
		$literals = $this->getLiterals ();
		
		return isset ( $literals [$key] );
	}
	
	/**
	 * Parse matches
	 *
	 * @param string $src        	
	 * @param string $key        	
	 * @param mixed $value        	
	 *
	 * @see [1] ignoring conditional tag length, don't worry
	 */
	final public function parseMatch($key, $value, &$engine, $ignore_logical_order = false) {
		if (isset ( $this->__ignore [$key] ))
			return false;
		
		$value = self::getDefault ( $value );
		$value = self::getDefaultByVar ( $key, $value );
		
		if (is_bool ( $value ))
			$value = $value ? "true" : "false";
		
		$is_string = self::isString ( $value );
		
		if ($is_string)
			$value = "$value";
		
		$literal = $this->isLiteral ( $key );
		
		$vpx = '';
		$vsx = '';
		
		if ($literal === true) {
			$vpx = '{' . $this->__ignore_secret_tag . '}';
			$vsx = '{/' . $this->__ignore_secret_tag . '}';
		}
		
		$prefix = DIV_TAG_REPLACEMENT_PREFIX;
		$suffix = DIV_TAG_REPLACEMENT_SUFFIX;
		
		if ($is_string || is_numeric ( $value )) {
			if (! $ignore_logical_order) {
				$p1 = strpos ( $this->__src, DIV_TAG_TPLVAR_BEGIN );
				$p2 = strpos ( $this->__src, DIV_TAG_MACRO_BEGIN );
			} else {
				$p1 = false;
				$p2 = false;
			}
			
			if ($p1 === false && $p2 === false)
				$substr = $this->__src;
			elseif ($p1 !== false && $p2 !== false) {
				$min = min ( $p1, $p2 );
				$substr = substr ( $this->__src, 0, $min );
				$p1 = $min;
			} elseif ($p1 !== false && $p2 === false)
				$substr = substr ( $this->__src, 0, $p1 );
			else {
				$substr = substr ( $this->__src, 0, $p2 );
				$p1 = $p2;
			}
			
			if (strpos ( $value, $prefix . DIV_TAG_MODIFIER_SIMPLE . $key . $suffix ) !== false) {
				$value = str_replace ( $prefix . DIV_TAG_MODIFIER_SIMPLE . $key . $suffix, "", $value );
				self::error ( "Was detected an infinite loop in recursive replacement of ${$key}.", DIV_ERROR_WARNING );
			}
			
			$px = false;
			foreach ( self::$__modifiers as $lm ) {
				$py = strpos ( $substr, $prefix . $lm . $key . $suffix );
				if ($py !== false) {
					$px = $py;
					break;
				}
			}
			
			if ($px !== false) {
				
				$rcount = 0;
				if (self::$__log_mode)
					$this->logger ( "Parsing match: $key in '" . substr ( $substr, 0, 50 ) . "'" );
				
				$value = trim ( "$value" );
				
				if (trim ( $value ) != "" && $literal === false) {
					$crc = crc32 ( $value );
					$engine->__src = $value;
					$engine->__items = $this->__items;
					$engine->parseInclude ( $this->__items );
					$engine->parsePreprocessed ( $this->__items );
					$value = $engine->__src;
					if (self::issetVar ( $key, $this->__items ) && crc32 ( $value ) != $crc) {
						if (gettype ( self::getVarValue ( $key, $this->__items ) ) == "string")
							self::setVarValue ( $key, $value, $this->__items );
					}
				}
				
				$mod = DIV_TAG_MODIFIER_SIMPLE;
				$substr = str_replace ( $prefix . $mod . $key . $suffix, $vpx . $value . $vsx, $substr, $rcount ); // simple replacement
				
				if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
					$this->saveOperation ( array (
							'o' => 'simple_replacement',
							'key' => $key,
							'modifier' => $mod,
							'before' => $p1 
					) );
				
				$mod = DIV_TAG_MODIFIER_CAPITALIZE_FIRST;
				if (strpos ( $substr, $prefix . $mod ) !== false) {
					$substr = str_replace ( DIV_TAG_REPLACEMENT_PREFIX . $mod . $key . $suffix, $vpx . ucfirst ( $value ) . $vsx, $substr, $rcount );
					if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
						$this->saveOperation ( array (
								"o" => 'simple_replacement',
								'key' => $key,
								'modifier' => $mod,
								'before' => $p1 
						) );
				}
				
				$mod = DIV_TAG_MODIFIER_CAPITALIZE_WORDS;
				if (strpos ( $substr, $prefix . $mod ) !== false) {
					$substr = str_replace ( $prefix . $mod . $key . $suffix, $vpx . ucwords ( $value ) . $vsx, $substr, $rcount );
					if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
						$this->saveOperation ( array (
								'o' => 'simple_replacement',
								'key' => $key,
								'modifier' => $mod,
								'before' => $p1 
						) );
				}
				
				$mod = DIV_TAG_MODIFIER_UPPERCASE;
				if (strpos ( $substr, $prefix . $mod ) !== false) {
					$substr = str_replace ( $prefix . $mod . $key . $suffix, $vpx . strtoupper ( $value ) . $vsx, $substr, $rcount );
					if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
						$this->saveOperation ( array (
								'o' => 'simple_replacement',
								'key' => $key,
								'modifier' => DIV_TAG_MODIFIER_UPPERCASE,
								'before' => $p1 
						) );
				}
				
				$mod = DIV_TAG_MODIFIER_LENGTH;
				if (strpos ( $substr, $prefix . $mod ) !== false) {
					$substr = str_replace ( $prefix . $mod . $key . $suffix, strlen ( $value ), $substr, $rcount );
					if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
						$this->saveOperation ( array (
								'o' => 'simple_replacement',
								'key' => $key,
								'modifier' => $mod,
								'before' => $p1 
						) );
				}
				
				$mod = DIV_TAG_MODIFIER_COUNT_WORDS;
				if (strpos ( $substr, $prefix . $mod ) !== false) {
					$substr = str_replace ( $prefix . $mod . $key . $suffix, self::getCountOfWords ( $value ), $substr, $rcount );
					if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
						$this->saveOperation ( array (
								'o' => 'simple_replacement',
								'key' => $key,
								'modifier' => $mod,
								'before' => $p1 
						) );
				}
				
				$mod = DIV_TAG_MODIFIER_COUNT_SENTENCES;
				if (strpos ( $substr, $prefix . $mod ) !== false) {
					$substr = str_replace ( $prefix . $mod . $key . $suffix, self::getCountOfSentences ( $value ), $substr, $rcount );
					if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
						$this->saveOperation ( array (
								'o' => 'simple_replacement',
								'key' => $key,
								'modifier' => $mod,
								'before' => $p1 
						) );
				}
				
				$mod = DIV_TAG_MODIFIER_COUNT_PARAGRAPHS;
				if (strpos ( $substr, $prefix . $mod ) !== false) {
					$substr = str_replace ( $prefix . $mod . $key . $suffix, self::getCountOfParagraphs ( $value ), $substr, $rcount );
					if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
						$this->saveOperation ( array (
								'o' => 'simple_replacement',
								'key' => $key,
								'modifier' => $mod,
								'before' => $p1 
						) );
				}
				
				$mod = DIV_TAG_MODIFIER_ENCODE_URL;
				if (strpos ( $substr, $prefix . $mod ) !== false) {
					$substr = str_replace ( $prefix . $mod . $key . $suffix, $vpx . urlencode ( $value ) . $vsx, $substr, $rcount );
					if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
						$this->saveOperation ( array (
								'o' => 'simple_replacement',
								'key' => $key,
								'modifier' => $mod,
								'before' => $p1 
						) );
				}
				
				$mod = DIV_TAG_MODIFIER_ENCODE_RAW_URL;
				if (strpos ( $substr, $prefix . $mod ) !== false) {
					$substr = str_replace ( $prefix . $mod . $key . $suffix, $vpx . rawurlencode ( $value ) . $vsx, $substr, $rcount );
					if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
						$this->saveOperation ( array (
								'o' => 'simple_replacement',
								'key' => $key,
								'modifier' => $mod,
								'before' => $p1 
						) );
				}
				
				$substr = str_replace ( array (
						$prefix . DIV_TAG_MODIFIER_LOWERCASE . $key . $suffix, // lower case
						$prefix . DIV_TAG_MODIFIER_HTML_ENTITIES . $key . $suffix, // html entities
						$prefix . DIV_TAG_MODIFIER_NL2BR . $key . $suffix, // convert newlines to <br/>
						$prefix . DIV_TAG_MODIFIER_SINGLE_QUOTES . $key . $suffix, // escape unescaped single quotes,
						$prefix . DIV_TAG_MODIFIER_JS . $key . $suffix 
				), 
						// escape quotes and backslashes, newlines, etc.
						array (
								$vpx . strtolower ( $value ) . $vsx,
								$vpx . htmlentities ( $value ) . $vsx,
								$vpx . nl2br ( $value ) . $vsx,
								$vpx . preg_replace ( array (
										"%(?<!\\\\\\\\)'%",
										"%(?<!\\\\\\\\)\"%" 
								), array (
										"\\'",
										"\\\"" 
								), $value ) . $vsx,
								$vpx . strtr ( $value, array (
										"\\" => "\\\\",
										"'" => "\\'",
										"\"" => "\\\"",
										"\r" => "\\r",
										"\n" => "\\n",
										"</" => "<\/" 
								) ) . $vsx 
						), $substr );
				
				foreach ( self::$__custom_modifiers as $modifier ) {
					$proced = false;
					if (function_exists ( $modifier [1] ))
						$proced = true;
					else {
						if (strpos ( $modifier [1], "::" )) {
							$temp = explode ( "::", $modifier [1] );
							if (class_exists ( $temp [0] )) {
								if (method_exists ( $temp [0], $temp [1] ))
									$proced = true;
							}
						}
					}
					
					if ($proced)
						if (strpos ( $substr, $modifier [0] ) !== false) {
							eval ( '$vvalue = ' . $modifier [1] . '($value);' );
							$substr = str_replace ( $prefix . $modifier [0] . $key . $suffix, $vpx . $vvalue . $vsx, $substr );
						}
				}
				
				if ($p1 !== false)
					$this->__src = $substr . substr ( $this->__src, $p1 );
				else
					$this->__src = $substr;
			}
		}
		
		$mod = DIV_TAG_MODIFIER_ENCODE_JSON;
		if (strpos ( $this->__src, $prefix . $mod . $key . $suffix ) !== false) {
			$this->__src = str_replace ( $prefix . $mod . $key . $suffix, $vpx . self::jsonEncode ( $value ) . $vsx, $this->__src, $rcount );
			if ($rcount > 0 && ! isset ( self::$__dont_remember_it [$key] ))
				$this->saveOperation ( array (
						'o' => 'json_encode',
						'key' => $key,
						'before' => false 
				) );
		}
	}
	
	/**
	 * Parse iterations
	 *
	 * @param mixed $items        	
	 * @param string $src        	
	 */
	final public function parseIterations(&$items, &$src = null) {
		$l1 = strlen ( DIV_TAG_ITERATION_BEGIN_PREFIX );
		$l2 = strlen ( DIV_TAG_ITERATION_END );
		
		if (self::$__log_mode)
			$this->logger ( "Parsing iterations..." );
		
		if (is_null ( $src ))
			$src = &$this->__src;
		
		$continue = true;
		$p = 0;
		$last_ranges = array (
				array (
						- 99 
				) 
		);
		
		while ( true ) {
			$ranges = $this->getRanges ( DIV_TAG_ITERATION_BEGIN_PREFIX, DIV_TAG_ITERATION_END, $src, true );
			
			if (count ( $ranges ) < 1)
				break;
			if ($ranges [0] [0] === $last_ranges [0] [0])
				break;
			
			$last_ranges = $ranges;
			
			$p = $ranges [0] [0];
			$p2 = $ranges [0] [1];
			$p1 = strpos ( $src, DIV_TAG_ITERATION_BEGIN_SUFFIX, $p + 1 );
			
			$s = substr ( $src, $p + $l1, $p1 - ($p + $l1) );
			
			$range = explode ( DIV_TAG_ITERATION_PARAM_SEPARATOR, $s );
			$c = count ( $range );
			if ($c < 2) {
				$range [1] = $range [0];
				$range [0] = 1;
			}
			$itervar = "value";
			$step = 1;
			
			if ($c == 3) {
				if (is_numeric ( $range [2] ))
					$step = trim ( $range [2] ) * 1;
				else
					$itervar = trim ( $range [2] );
			}
			
			if ($c == 4) {
				$itervar = $range [2];
				$step = trim ( $range [3] ) * 1;
			}
			
			if (is_numeric ( $range [0] ) && is_numeric ( $range [1] )) {
				
				$range [0] = trim ( $range [0] ) * 1;
				$range [1] = trim ( $range [1] ) * 1;
				
				$key = uniqid ();
				$l = strlen ( $key );
				
				$subsrc = substr ( $src, $p1 + $l1, $p2 - ($p1 + $l1) );
				
				$sitervar = " $itervar " . DIV_TAG_LOOP_VAR_SEPARATOR;
				
				if (strpos ( $subsrc, DIV_TAG_LOOP_VAR_SEPARATOR ))
					$sitervar = '';
				
				$src = substr ( $src, 0, $p ) . DIV_TAG_LOOP_BEGIN_PREFIX . $key . DIV_TAG_LOOP_BEGIN_SUFFIX . $sitervar . $subsrc . DIV_TAG_LOOP_END_PREFIX . $key . DIV_TAG_LOOP_END_SUFFIX . substr ( $src, $p2 + $l2 );
				
				$items [$key] = array ();
				if ($range [1] >= $range [0])
					for($i = $range [0]; $i >= $range [0] && $i <= $range [1]; $i = $i + $step)
						$items [$key] [] = $i;
				else
					for($i = $range [0]; $i >= $range [1] && $i <= $range [0]; $i = $i - $step)
						$items [$key] [] = $i;
			}
		}
	}
	
	/**
	 * Return true if a block is inside another block
	 *
	 * @param string $tagini        	
	 * @param string $tagend        	
	 * @param integer $pos1        	
	 * @param integer $pos2        	
	 * @return boolean
	 */
	final public function isBlockInsideBlock($tagini, $tagend, $pos1, $pos2, $rangs = null) {
		if (is_null ( $rangs ))
			$rangs = $this->getRanges ( $tagini, $tagend );
		foreach ( $rangs as $rang )
			if ($rang [0] < $pos1 && $rang [1] > $pos2)
				return true;
		return false;
	}
	
	/**
	 * Parse list block
	 *
	 * @param mixed $value        	
	 * @param string $key        	
	 * @param mixed $items        	
	 */
	final public function parseListBlock($value, $key, $items) {
		if (isset ( $this->__ignore [$key] ))
			return false;
		
		$tag_begin = DIV_TAG_LOOP_BEGIN_PREFIX . $key . DIV_TAG_LOOP_BEGIN_SUFFIX;
		$tag_end = DIV_TAG_LOOP_END_PREFIX . $key . DIV_TAG_LOOP_END_SUFFIX;
		if (strpos ( $this->__src, $tag_begin ) === false)
			return false;
		
		$l1 = strlen ( $tag_begin );
		$l2 = strlen ( $tag_end );
		$ranges = array ();
		$classname = get_class ( $this );
		$pos = 0;
		$total = 0;
		$subtotal = 0;
		
		while ( true ) {
			
			$lists = $this->getRanges ( $tag_begin, $tag_end, null, true, $pos );
			if (count ( $lists ) < 1)
				break;
			if (self::$__log_mode)
				$this->logger ( "Parsing the list: $key.." );
			
			$list = $lists [0];
			
			$p1 = $list [0];
			$p2 = $list [1];
			
			// Checking logical order ...
			$r = $this->checkLogicalOrder ( $p1, "", false, false, false, true );
			if ($r !== false) {
				$pos = $p2 + 1;
				continue;
			}
			
			// Check if list is inside another list block ...
			if ($this->searchInListRanges ( $p1 )) {
				$pos = $p2 + 1;
				continue;
			}
			
			$ranges [] = $list;
			
			if ($p2 > $p1) {
				$minihtml = substr ( $this->__src, $p1 + $l1, $p2 - $p1 - $l1 );
				
				$itemkey = "value";
				
				// The itemkey/itervar can't have space or newline chararters
				
				if (strpos ( $minihtml, DIV_TAG_LOOP_VAR_SEPARATOR ) !== false) {
					$arr = explode ( DIV_TAG_LOOP_VAR_SEPARATOR, $minihtml, 2 );
					if (strpos ( $arr [0], "\n" ) === false) {
						$arr [0] = trim ( $arr [0] );
						if (strpos ( $arr [0], ' ' ) === false) {
							if ($itemkey != "")
								$itemkey = $arr [0];
							$minihtml = $arr [1];
						}
					}
				}
				
				$ii = 0;
				$randoms = array ();
				$count = count ( $value );
				
				$ii = 0;
				
				$keys = array_keys ( $value );
				$ckeys = count ( $keys );
				
				$go_index = strpos ( $minihtml, '_index' ) !== false;
				$go_key = strpos ( $minihtml, '_key' ) !== false;
				$go_index_random = strpos ( $minihtml, '_index_random' ) !== false;
				$go_is_odd = strpos ( $minihtml, '_is_odd' ) !== false;
				$go_is_even = strpos ( $minihtml, '_is_even' ) !== false;
				$go_is_first = strpos ( $minihtml, '_is_first' ) !== false;
				$go_is_last = strpos ( $minihtml, '_is_last' ) !== false;
				$go_list = strpos ( $minihtml, '_list' ) !== false;
				$go_item = strpos ( $minihtml, '_item' ) !== false;
				$go_order = strpos ( $minihtml, '_order' ) !== false;
				$go_previous = strpos ( $minihtml, '_previous' ) !== false;
				$go_next = strpos ( $minihtml, '_next' ) !== false;
				
				$xitems = array ();
				$xitems_orig = array ();
				
				// Preparing xitems data
				$previous = null;
				$next = null;
				
				$empty = $this->getEmptyTag ( $minihtml );
				if ($empty !== false) {
					$body_parts = array (
							substr ( $minihtml, 0, $empty ),
							substr ( $minihtml, $empty + strlen ( DIV_TAG_EMPTY ) ) 
					);
				} else
					$body_parts = array (
							$minihtml,
							"" 
					);
				
				$h = "";
				if (empty ( $value ))
					$h = $body_parts [1];
				else {
					$minihtml = $body_parts [0];
					
					foreach ( $value as $kk => $item ) {
						
						if (isset ( $keys [$ii + 1] )) {
							$next = $value [$keys [$ii + 1]];
						} else
							$next = null;
						
						$ii ++;
						$anothers = array ();
						
						$item_orig = $item;
						
						if ($go_index)
							$anothers ['_index'] = $ii - 1;
						
						if ($go_key)
							$anothers ['_key'] = $kk;
						
						if ($go_index_random) {
							do {
								$random = rand ( 1, $count );
							} while ( isset ( $randoms [$random] ) );
							
							$randoms [$random] = true;
							$anothers ['_index_random'] = $random - 1;
						}
						if ($go_is_odd)
							$anothers ['_is_odd'] = ($ii % 2 != 0);
						if ($go_is_even)
							$anothers ['_is_even'] = ($ii % 2 == 0);
						if ($go_is_first)
							$anothers ['_is_first'] = ($ii === 1);
						if ($go_is_last)
							$anothers ['_is_last'] = ($ii == $ckeys);
						if ($go_list)
							$anothers ['_list'] = $key;
						
						if ($go_item) {
							if (is_object ( $item ))
								$anothers ['_item'] = clone $item;
							else
								$anothers ['_item'] = $item;
						}
						
						if ($go_order)
							$anothers ['_order'] = $ii;
						
						if ($go_previous) {
							if (is_object ( $previous ))
								$anothers ['_previous'] = clone $previous;
							else
								$anothers ['_previous'] = $previous;
						}
						
						if ($go_next) {
							if (is_object ( $next ))
								$anothers ['_next'] = clone $next;
							else
								$anothers ['_next'] = $next;
						}
						
						$previous = $item;
						
						if (is_object ( $item ))
							if (self::isString ( $item )) {
								$itemstr = "$item";
								if (! isset ( $item->value ))
									$item->value = $itemstr;
								if (! isset ( $item->_to_string ))
									$item->_to_string = $itemstr;
							}
						
						if (is_object ( $item ))
							$item = get_object_vars ( $item );
						
						$isscalar = false;
						if (! is_array ( $item ) || is_scalar ( $value )) {
							$item = array (
									$itemkey => $item 
							);
							$isscalar = true;
						} else if ($itemkey != "value") {
							$item [$itemkey] = $item;
						}
						
						$item = array_merge ( $item, $anothers );
						
						$xitems [] = $item;
						$xitems_orig [] = self::cop ( $item_orig, $anothers );
					}
					
					// Parsing ...
					$h = "";
					$engine = self::getAuxiliaryEngineClone ( $xitems );
					$engine->__src_original = $minihtml;
					$engine->__memory = $this->__memory;
					
					$globals_design = self::$__globals_design;
					
					foreach ( $xitems as $xkey => $item ) {
						$tempglobal = array (); // priority to item's properties
						                        // Save similar global design vars
						
						$tempglobal = self::$__globals_design;
						foreach ( $item as $kkk => $vvv )
							if (isset ( self::$__globals_design [$kkk] ))
								unset ( self::$__globals_design [$kkk] );
						
						if (self::$__log_mode)
							$this->logger ( "Parsing item $xkey of the list '$key'..." );
						
						$engine->__items [$xkey] = array_merge ( $items, $item );
						
						$engine->__items_orig = $xitems_orig [$xkey];
						
						// Save some vars
						$memory = $engine->__memory;
						
						foreach ( $item as $kkk => $vvv )
							if (isset ( $engine->__memory [$kkk] ))
								unset ( $engine->__memory [$kkk] );
							
							// Parse minihtml
						$engine->parse ( true, $xkey );
						
						// Rresore some vars
						$engine->__memory = $memory;
						$engine->__items [$xkey] = $item;
						self::$__globals_design = array_merge ( $tempglobal, self::$__globals_design );
						
						$break = strpos ( $engine->__src, DIV_TAG_BREAK );
						
						foreach ( $item as $kkk => $vvv ) {
							if (isset ( $this->__memory [$kkk] )) {
								unset ( $this->__memory [$kkk] );
							}
						}
						
						if ($break !== false) {
							$engine->__src = substr ( $engine->__src, 0, $break );
							$h .= $engine->__src;
							break;
						}
						
						$h .= $engine->__src;
					}
					
					// Restore global design vars
					self::$__globals_design = $globals_design;
				}
				
				// Replace
				$this->__src = substr ( $this->__src, 0, $p1 ) . $h . substr ( $this->__src, $p2 + $l2 );
			}
		}
	}
	
	/**
	 * Parse list
	 *
	 * @param mixed $items        	
	 * @param string $superkey        	
	 */
	final public function parseList($items = null, $superkey = "") {
		if (self::$__log_mode)
			$this->logger ( "Parsing loops, SUPERKEY = '$superkey'..." );
		
		if (isset ( $this->__ignore [$superkey] ))
			return false;
		
		if (is_null ( $items ))
			$items = $this->__items;
		
		if (! is_array ( $items ))
			if (is_object ( $items )) {
				$items = get_object_vars ( $items );
			}
		
		if (! is_array ( $items ))
			return false;
		
		if ($superkey != "")
			$superkey .= DIV_TAG_VAR_MEMBER_DELIMITER;
		
		if (strpos ( $this->__src, DIV_TAG_LOOP_BEGIN_PREFIX . $superkey ) !== false)
			foreach ( $items as $key => $value ) {
				$key = $superkey . $key;
				if (strpos ( $this->__src, DIV_TAG_LOOP_BEGIN_PREFIX . $key . DIV_TAG_VAR_MEMBER_DELIMITER ) !== false) {
					if (! is_array ( $value ))
						if (is_object ( $value )) {
							$value = get_object_vars ( $value );
						} else
							continue;
					$this->parseList ( $value, $key );
				}
			}
		
		$pos = array ();
		foreach ( $items as $key => $value ) {
			$p = strpos ( $this->__src, DIV_TAG_LOOP_BEGIN_PREFIX . $superkey . $key . DIV_TAG_LOOP_BEGIN_SUFFIX );
			if ($p !== false)
				$pos [$key] = $p;
		}
		
		asort ( $pos );
		
		foreach ( $pos as $key => $v ) {
			
			$value = $items [$key];
			
			if (is_scalar ( $value ))
				$value = "$value";
			if (self::isString ( $value ))
				$value = str_split ( $value );
			
			if (! is_array ( $value ))
				if (is_object ( $value )) {
					$value = get_object_vars ( $value );
				} else
					continue;
			
			$this->parseListBlock ( $value, $superkey . $key, $items );
		}
	}
	
	/**
	 * Ignore parts of template
	 *
	 * @return array
	 */
	final public function parseIgnore() {
		if (self::$__log_mode)
			$this->logger ( "Parsing ignore's blocks..." );
			
			// Generate internal and random ignore tag (security reasons)
		if (is_null ( $this->__ignore_secret_tag ))
			$this->__ignore_secret_tag = uniqid ();
		
		for($i = 0; $i < 2; $i ++) {
			$tag_begin = DIV_TAG_IGNORE_BEGIN;
			$tag_end = DIV_TAG_IGNORE_END;
			
			if ($i == 1) {
				$tag_begin = '{' . $this->__ignore_secret_tag . '}';
				$tag_end = '{/' . $this->__ignore_secret_tag . '}';
			}
			
			$l1 = strlen ( $tag_begin );
			$l2 = strlen ( $tag_end );
			
			$pos = 0;
			while ( true ) {
				$ranges = $this->getRanges ( $tag_begin, $tag_end, null, true, $pos );
				if (count ( $ranges ) < 1)
					break;
				
				if (self::searchInRanges ( $this->getRanges ( DIV_TAG_COMMENT_BEGIN, DIV_TAG_COMMENT_END ), $ranges [0] [0] ) !== false) {
					$pos = $ranges [0] [1] + 1;
					continue;
				}
				
				$id = uniqid ();
				self::$__ignored_parts [$id] = substr ( $this->__src, $ranges [0] [0] + $l1, $ranges [0] [1] - $ranges [0] [0] - $l1 );
				$this->__src = substr ( $this->__src, 0, $ranges [0] [0] ) . '{' . $id . '}' . substr ( $this->__src, $ranges [0] [1] + $l2 );
				$pos = $ranges [0] [0] + 1;
			}
		}
		
		return self::$__ignored_parts;
	}
	
	/**
	 * Return the resolved path for include and preprocessed
	 *
	 * @param string $path        	
	 * @return string
	 */
	final public function getTplPath($path) {
		$return = null;
		
		if (self::fileExists ( $path . "." . DIV_DEFAULT_TPL_FILE_EXT ))
			$path .= "." . DIV_DEFAULT_TPL_FILE_EXT;
		
		$path = str_replace ( "." . DIV_DEFAULT_TPL_FILE_EXT . "." . DIV_DEFAULT_TPL_FILE_EXT, "." . DIV_DEFAULT_TPL_FILE_EXT, $path );
		
		// Relative path
		if (! self::fileExists ( $path )) {
			if ($this->__path != "") {
				if (self::fileExists ( $this->__path )) {
					$folder = self::getFolderOf ( $this->__path );
					if (self::fileExists ( $folder . "/" . $path . "." . DIV_DEFAULT_TPL_FILE_EXT ))
						$path .= "." . DIV_DEFAULT_TPL_FILE_EXT;
					if (self::fileExists ( $folder . "/" . $path )) {
						$return = $folder . "/" . $path;
					}
				}
			}
		}
		
		if (is_null ( $return )) {
			// Resolving with the historial ...
			$max = 0;
			$return = $path;
			foreach ( self::$__includes_historial as $ih ) {
				$folder = self::getFolderOf ( $ih );
				$fullpath = $folder . "/" . $path;
				
				if (self::fileExists ( $fullpath . "." . DIV_DEFAULT_TPL_FILE_EXT ))
					$fullpath .= "." . DIV_DEFAULT_TPL_FILE_EXT;
				else if (self::fileExists ( $fullpath . "." . DIV_DEFAULT_TPL_FILE_EXT ))
					$fullpath .= "." . DIV_DEFAULT_TPL_FILE_EXT;
				
				$similar = similar_text ( $ih, $fullpath );
				if ((self::fileExists ( $fullpath ) || self::fileExists ( $fullpath )) && $similar >= $max)
					$return = $fullpath;
			}
		}
		
		if (! self::fileExists ( $return ))
			return null;
		
		return $return;
	}
	
	/**
	 * Detect recursive inclusion
	 *
	 * @param array $exclusion        	
	 * @param string $path        	
	 * @param integer $ini        	
	 * @return boolean
	 */
	final static function detectRecursiveInclusion($exclusion, $path, $ini) {
		if (trim ( $path ) == '')
			return false;
		
		foreach ( $exclusion as $exc ) {
			$p = $exc ['path'];
			$i = $exc ['ini'];
			$f = $exc ['end'];
			if ($p == $path && $ini >= $i && $ini <= $f)
				return true;
		}
		return false;
	}
	
	/**
	 * Secure is_string
	 *
	 * @param mixed $value        	
	 * @return boolean
	 */
	final static function isString($value) {
		if (is_string ( $value ))
			return true;
		if (is_object ( $value ))
			if (method_exists ( $value, "__toString" ))
				return true;
		return false;
	}
	
	/**
	 * Include others templates
	 *
	 * @param mixed $items        	
	 */
	final public function parseInclude(&$items) {
		if (self::$__log_mode)
			$this->logger ( "Parsing includes..." );
		
		$prefix = DIV_TAG_INCLUDE_BEGIN;
		$suffix = DIV_TAG_INCLUDE_END;
		
		if (is_object ( $items ))
			$items = get_object_vars ( $items );
		if (is_array ( $items ))
			foreach ( $items as $key => $value ) {
				if (isset ( $this->__ignore [$key] ))
					continue;
				
				if (strpos ( $this->__src, $prefix . $key . $suffix ) !== false && self::isString ( $value )) {
					if (self::fileExists ( $value . "." . DIV_DEFAULT_TPL_FILE_EXT ))
						$value .= "." . DIV_DEFAULT_TPL_FILE_EXT;
					$this->__src = str_replace ( $prefix . $key . $suffix, $prefix . $value . $suffix, $this->__src );
				}
			}
		
		$restore = array ();
		$pos = 0;
		
		$exclusion = array ();
		
		$l1 = strlen ( $prefix );
		$l2 = strlen ( $suffix );
		
		while ( true ) {
			$ranges = $this->getRanges ( $prefix, $suffix, null, true, $pos );
			if (count ( $ranges ) < 1)
				break;
			
			$ini = $ranges [0] [0];
			$fin = $ranges [0] [1];
			
			$rangex = $this->getRanges ( DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END );
			$procede = true;
			foreach ( $rangex as $rx ) {
				if ($ini >= $rx [0] && $ini <= $rx [1]) {
					$pos = $fin + 1;
					$procede = false;
					break;
				}
			}
			
			if (! $procede)
				continue;
			
			if (self::searchInRanges ( $this->getRanges ( DIV_TAG_COMMENT_BEGIN, DIV_TAG_COMMENT_END ), $ranges [0] [0] ) !== false) {
				$pos = $fin + 1;
				continue;
			}
			
			$path = trim ( substr ( $this->__src, $ini + $l1, $fin - $ini - $l1 ) );
			
			// New feature in 4.5: advanced params for includes
			$pdata = null;
			if (! self::fileExists ( $path )) {
				$sep = strpos ( $path, DIV_TAG_PREPROCESSED_SEPARATOR );
				
				if ($sep !== false) {
					$pdata = trim ( substr ( $path, $sep + 1 ) );
					$path = substr ( $path, 0, $sep );
					
					$allitems = $this->getAllItems ( $items );
					
					if (self::fileExists ( $pdata . "." . DIV_DEFAULT_DATA_FILE_EXT )) {
						$pdata = file_get_contents ( $pdata . "." . DIV_DEFAULT_DATA_FILE_EXT );
						$pdata = self::jsonDecode ( $pdata, $allitems );
					} elseif (self::fileExists ( $pdata )) {
						$pdata = file_get_contents ( $pdata );
						$pdata = self::jsonDecode ( $pdata, $allitems );
					} elseif (self::varExists ( $pdata, $allitems ))
						$pdata = self::getVarValue ( $pdata, $allitems );
					else
						$pdata = self::jsonDecode ( $pdata, $allitems );
					
					if (is_object ( $pdata ))
						$pdata = get_object_vars ( $pdata );
				}
			}
			
			$path = $this->getTplPath ( $path );
			
			if (self::$__log_mode)
				$this->logger ( "Trying to include $path" );
			
			if ($path != $this->__path && ! self::detectRecursiveInclusion ( $exclusion, $path, $ini )) {
				if (self::fileExists ( $path )) {
					$c = "";
					if (! is_dir ( $path )) {
						
						self::$__includes_historial [] = $path;
						
						$c = self::getFileContents ( $path );
						
						// advanced operations before include
						if (! is_null ( $pdata )) {
							
							// extract part of template
							if (isset ( $pdata ['from'] )) {
								if (isset ( $pdata ['to'] )) {
									$from = $pdata ['from'];
									$to = $pdata ['to'];
									
									if (! is_numeric ( $from ) && ! is_numeric ( $to ))
										if (! empty ( $from ) && ! empty ( $to )) {
											// string from/to (ranges)
											
											$extract = $this->getRanges ( $from, $to, $c );
											
											$newc = '';
											$lfrom = strlen ( $from );
											
											$i = 0;
											foreach ( $extract as $extr ) {
												$i ++;
												
												if (isset ( $pdata ['offset'] ))
													if ($i < $pdata ['offset'] * 1)
														continue;
												
												$newc .= substr ( $c, $extr [0] + $lfrom, $extr [1] - $extr [0] - $lfrom );
												
												if (isset ( $pdata ['limit'] ))
													if ($i == $pdata ['limit'] * 1)
														break;
											}
											
											$c = $newc;
										} else {
											// numeric/string from/to
											if (! is_numeric ( $from ))
												if (! empty ( $from ))
													$from = strpos ( $c, $from );
												else
													$from = false;
											if (! is_numeric ( $to ))
												if (! empty ( $to ))
													$to = strpos ( $c, $to );
												else
													$to = false;
											
											if ($from !== false && $to !== false && $from <= $to) {
												$c = substr ( $c, $from, $from + ($to - $from) + 1 );
											}
										}
								} else {
									// only from
									$from = $pdata ['from'];
									
									if (! is_numeric ( $from ))
										$from = strpos ( $c, $from );
									
									if ($from !== false) {
										$c = substr ( $c, $from );
									}
								}
							} else {
								// only to
								if (isset ( $pdata ['to'] )) {
									if (! is_numeric ( $to ))
										$to = strpos ( $c, $to );
									
									if ($to !== false) {
										$c = substr ( $c, 0, $to + 1 );
									}
								}
							}
						}
						
						$tpl_prop = $this->getTemplateProperties ( $c );
						$c = $this->prepareDialect ( $c, $tpl_prop );
						
						if (self::$__docs_on) {
							if (self::fileExists ( $this->__path ) || self::fileExists ( PACKAGES . $this->__path )) {
								$section = trim ( $this->__path );
								$contained = trim ( $path );
								
								if (substr ( $section, 0, 2 ) == './')
									$section = substr ( $this->__path, 2 );
								if (substr ( $contained, 0, 2 ) == './')
									$contained = substr ( $path, 2 );
								
								self::$__docs [$contained] = array ();
								if (! isset ( self::$__docs [$section] ))
									self::$__docs [$section] = array ();
								if (! isset ( self::$__docs [$section] ['include'] ))
									self::$__docs [$section] ['include'] = array ();
								self::$__docs [$section] ['include'] [$contained] = $contained;
								
								$engine = self::getAuxiliaryEngineClone ( $items );
								$engine->__src = $c;
								$engine->parseComments ( $path );
								$c = $engine->__src;
								unset ( $engine );
							}
						}
					} else {
						self::error ( "Template '$path' not found or is not a template", DIV_ERROR_WARNING );
					}
					
					$lenc = strlen ( $c );
					
					foreach ( $exclusion as $idx => $exc ) {
						if ($exc ['ini'] > $ini)
							$exclusion [$idx] ['ini'] += $lenc;
						if ($exc ['end'] > $ini)
							$exclusion [$idx] ['end'] += $lenc;
					}
					
					$exclusion ["inclusion-" . $this->__path] = array (
							"path" => $path,
							"ini" => $ini,
							"end" => $ini + $lenc 
					);
					
					$this->__src = substr ( $this->__src, 0, $ini ) . $c . substr ( $this->__src, $fin + $l2 );
				} else {
					$id = uniqid ();
					$restore [$id] = substr ( $this->__src, $ini, $fin + $l2 - $ini );
					$this->__src = substr ( $this->__src, 0, $ini ) . $id . substr ( $this->__src, $fin + $l2 );
				}
			} else {
				if (trim ( $path ) != '') {
					self::error ( "Recursive inclusion of template '$path' in '" . substr ( $this->__src, $ini - 20, 20 ) . "'is not allowed", DIV_ERROR_WARNING );
				}
				$this->__src = substr ( $this->__src, 0, $ini ) . substr ( $this->__src, $fin + $l2 );
			}
		}
		
		foreach ( $restore as $id => $restor )
			$this->__src = str_replace ( $id, $restor, $this->__src );
	}
	
	/**
	 * Parsing preprocessed templates
	 *
	 * @param mixed $items        	
	 */
	final public function parsePreprocessed($items) {
		
		// Div doesn't know the future!
		$items = array_merge ( $this->__memory, $items );
		
		// Tags
		$prefix = DIV_TAG_PREPROCESSED_BEGIN;
		$suffix = DIV_TAG_PREPROCESSED_END;
		
		$l1 = strlen ( $prefix );
		$l2 = strlen ( $suffix );
		
		if (self::$__log_mode)
			$this->logger ( "Parsing preprocessed..." );
		
		$classname = get_class ( $this );
		
		if (is_object ( $items ))
			$items = get_object_vars ( $items );
		
		if (is_array ( $items ))
			foreach ( $items as $key => $value ) {
				if (isset ( $this->__ignore [$key] ))
					continue;
				if (strpos ( $this->__src, $prefix . $key . $suffix ) !== false) {
					if (self::fileExists ( $value . "." . DIV_DEFAULT_TPL_FILE_EXT ))
						$value .= "." . DIV_DEFAULT_TPL_FILE_EXT;
					$this->__src = str_replace ( $prefix . $key . $suffix, $prefix . $value . $suffix, $this->__src );
				}
			}
		
		$restore = array ();
		
		$pos = 0;
		$originalitems = $items;
		while ( true ) {
			
			$items = $originalitems;
			
			$ranges = $this->getRanges ( $prefix, $suffix, null, true, $pos );
			if (count ( $ranges ) < 1)
				break;
			
			$ini = $ranges [0] [0];
			$fin = $ranges [0] [1];
			
			$r = $this->checkLogicalOrder ( $ini, "", false, false, false, true );
			if ($r !== false) {
				$pos = $ini + 1;
				continue;
			}
			
			$path = trim ( substr ( $this->__src, $ini + $l1, $fin - $ini - $l1 ) );
			
			// New feature in 4.5: specific data for preprocessed template
			if (! self::fileExists ( $path )) {
				$sep = strpos ( $path, DIV_TAG_PREPROCESSED_SEPARATOR );
				
				if ($sep !== false) {
					
					$pdata = trim ( substr ( $path, $sep + 1 ) );
					
					$path = substr ( $path, 0, $sep );
					
					$allitems = $this->getAllItems ( $items );
					
					if (self::fileExists ( $pdata . "." . DIV_DEFAULT_DATA_FILE_EXT )) {
						$pdata = file_get_contents ( $pdata . "." . DIV_DEFAULT_DATA_FILE_EXT );
						$pdata = self::jsonDecode ( $pdata, $allitems );
					} elseif (self::fileExists ( $pdata )) {
						$pdata = file_get_contents ( $pdata );
						$pdata = self::jsonDecode ( $pdata, $allitems );
					} elseif (self::varExists ( $pdata, $allitems ))
						$pdata = self::getVarValue ( $pdata, $allitems );
					else
						$pdata = self::jsonDecode ( $pdata, $allitems );
					
					if (is_object ( $pdata ))
						$pdata = get_object_vars ( $pdata );
					
					if (is_array ( $pdata ))
						$items = array_merge ( $items, $pdata );
				}
			}
			
			$rangex = $this->getRanges ( DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END );
			$procede = true;
			foreach ( $rangex as $rx ) {
				if ($ini >= $rx [0] && $ini <= $rx [1]) {
					$pos = $ini + 1;
					$procede = false;
					break;
				}
			}
			
			if (! $procede)
				continue;
			
			$path = $this->getTplPath ( $path );
			
			if (self::fileExists ( $path )) {
				$c = self::getFileContents ( $path );
				$engine = self::getAuxiliaryEngineClone ( $items );
				$engine->__src = $c;
				
				if (self::$__docs_on) {
					if (self::fileExists ( $this->__path )) {
						$section = trim ( $this->__path );
						$contained = trim ( $path );
						
						if (substr ( $section, 0, 2 ) == './')
							$section = substr ( $this->__path, 2 );
						if (substr ( $contained, 0, 2 ) == './')
							$contained = substr ( $path, 2 );
						
						self::$__docs [$contained] = array ();
						if (! isset ( self::$__docs [$section] ))
							self::$__docs [$section] = array ();
						if (! isset ( self::$__docs [$section] ['preprocess'] ))
							self::$__docs [$section] ['include'] = array ();
						self::$__docs [$section] ['preprocess'] [] = $contained;
					}
				}
				
				$engine->__path = $path;
				self::$__includes_historial [] = $path;
				
				$originals = self::$__globals_design;
				self::$__globals_design = array_merge ( self::$__globals_design, $items );
				
				$engine->__items = $items;
				$engine->parse ( false, null, self::$__parse_level + 1 );
				self::$__globals_design = $originals;
				
				$pre = $engine->__src;
				
				$this->__src = substr ( $this->__src, 0, $ini ) . $pre . substr ( $this->__src, $fin + $l2 );
			} else {
				$id = uniqid ();
				$restore [$id] = substr ( $this->__src, $ini, $fin + $l2 - $ini );
				$this->__src = substr ( $this->__src, 0, $ini ) . $id . substr ( $this->__src, $fin + $l2 );
			}
		}
		
		foreach ( $restore as $id => $restor )
			$this->__src = str_replace ( $id, $restor, $this->__src );
	}
	
	/**
	 * Parse comments
	 *
	 * @param string $section        	
	 */
	final public function parseComments($section = null) {
		if (is_null ( $section ))
			$section = trim ( $this->__path );
		if ($section == '')
			$section = uniqid ();
		if (substr ( $section, 0, 2 ) == './')
			$section = substr ( $section, 2 );
		
		if (self::$__log_mode)
			$this->logger ( "Parsing comments..." );
		
		$lbegin = strlen ( DIV_TAG_COMMENT_BEGIN );
		
		$pos = 0;
		while ( true ) {
			$ranges = $this->getRanges ( DIV_TAG_COMMENT_BEGIN, DIV_TAG_COMMENT_END, null, true, $pos );
			
			if (count ( $ranges ) < 1)
				break;
			
			if (self::searchInRanges ( $this->getRanges ( DIV_TAG_INCLUDE_BEGIN, DIV_TAG_INCLUDE_END ), $ranges [0] [0] ) !== false) {
				$pos = $ranges [0] [1] + 1;
				continue;
			}
			
			// Parse template's docs
			if (self::$__docs_on) {
				$subsrc = substr ( $this->__src, $ranges [0] [0] + $lbegin, $ranges [0] [1] - $ranges [0] [0] - $lbegin );
				$arr = explode ( "\n", $subsrc );
				
				$last_prop = '';
				$last_tab = 0;
				foreach ( $arr as $line ) {
					$orig = $line;
					$line = str_replace ( "\r\n", "\n", $line );
					$line = trim ( $line );
					$line = str_replace ( "\t", " ", $line );
					
					if ($last_prop !== '')
						if (isset ( $orig [0] ) && $line == '')
							$line = " ";
					
					if (isset ( $line [0] )) {
						if ($last_prop !== '')
							if ($line [0] !== '@')
								$line = '@' . $last_prop . ': ' . substr ( $orig, $last_tab );
						if ($line [0] == '@') {
							
							$multiline = false;
							
							$p = strpos ( $line, " " );
							if ($p !== false) {
								$prop = substr ( $line, 1, $p - 1 );
								$value = substr ( $line, $p );
							} else {
								$prop = substr ( $line, 1 );
								$value = "";
							}
							$l = strlen ( $prop );
							
							if ($prop [$l - 1] == ":") {
								$multiline = true;
								$prop = substr ( $prop, 0, $l - 1 );
							}
							
							if (! isset ( self::$__docs [$section] ))
								self::$__docs [$section] = array ();
							if (isset ( self::$__docs [$section] [$prop] )) {
								if (! is_array ( self::$__docs [$section] [$prop] )) {
									if (trim ( self::$__docs [$section] [$prop] ) !== '')
										self::$__docs [$section] [$prop] = array (
												self::$__docs [$section] [$prop] 
										);
								}
								if (isset ( self::$__docs [$section] [$prop] [0] ) || (! isset ( self::$__docs [$section] [$prop] [0] ) && trim ( $value ) !== ''))
									self::$__docs [$section] [$prop] [] = $value;
							} else
								self::$__docs [$section] [$prop] = $value;
							
							if ($multiline)
								$last_prop = $prop;
							else
								$last_prop = '';
							
							$ppp = strpos ( $orig, '@' . $prop );
							if ($ppp !== false)
								$last_tab = $ppp;
						}
					}
				}
			}
			
			// Extract
			$this->__src = substr ( $this->__src, 0, $ranges [0] [0] ) . substr ( $this->__src, $ranges [0] [1] + strlen ( DIV_TAG_COMMENT_END ) );
		}
	}
	
	/**
	 * Isset template var in items?
	 *
	 * @param string $var        	
	 * @param mixed $items        	
	 * @return boolean
	 */
	final static function issetVar($var, $items) {
		if (isset ( $items [$var] ))
			return true;
		
		$var = trim ( $var );
		$parts = explode ( DIV_TAG_VAR_MEMBER_DELIMITER, $var );
		$current = $items;
		
		foreach ( $parts as $part ) {
			
			if (trim ( $part ) == "")
				return false;
			
			if (is_array ( $current )) {
				if (isset ( $current [$part] )) {
					$current = $current [$part];
				} else
					return false;
			} elseif (is_object ( $current )) {
				if (isset ( $current->$part )) {
					$current = $current->$part;
				} else
					return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Return a list of all template vars
	 *
	 * @param mixed $items        	
	 * @param string $superkey        	
	 * @return array
	 */
	final public function getVars($items = null, $superkey = '') {
		if (is_null ( $items ))
			$items = $this->__memory;
		
		$vars = array ();
		
		$itemsx = array ();
		
		if (is_object ( $items ))
			$itemsx = get_object_vars ( $items );
		elseif (is_array ( $items ))
			$itemsx = $items;
		else
			return array ();
		
		foreach ( $itemsx as $key => $value ) {
			$xkey = $superkey . $key;
			
			if ($xkey !== '') {
				if ($xkey == 'vars' . DIV_TAG_VAR_MEMBER_DELIMITER . 'this' || $xkey == 'this') {
					if (is_object ( $value )) {
						if ($this === $value)
							continue;
						
						$sp = $this->getSuperParent ( get_class ( $value ) );
						
						if ($sp == 'div')
							continue;
					}
				}
				
				$vars [] = $xkey;
				
				if (! is_scalar ( $value ) && ! is_null ( $value ))
					$vars = array_merge ( $vars, $this->getVars ( $itemsx [$key], $xkey . DIV_TAG_VAR_MEMBER_DELIMITER ) );
			}
		}
		
		return $vars;
	}
	
	/**
	 * Unset template var
	 *
	 * @param string $var        	
	 * @param mixed $items        	
	 * @return boolean
	 */
	final static function unsetVar($var, &$items) {
		$unset = false;
		if (isset ( $items [$var] )) {
			$unset = true;
			unset ( $items [$var] );
		}
		
		$var = trim ( $var );
		
		$parts = explode ( DIV_TAG_VAR_MEMBER_DELIMITER, $var );
		if (! isset ( $parts [0] ))
			return $unset;
		
		$current = $items;
		$code = '$items';
		
		foreach ( $parts as $part ) {
			
			if (trim ( $part ) == "")
				return $unset;
			
			if (is_array ( $current )) {
				if (isset ( $current [$part] )) {
					$current = $current [$part];
					$code .= '["' . $part . '"]';
				} else
					return $unset;
			} elseif (is_object ( $current )) {
				if (isset ( $current->$part )) {
					$current = $current->$part;
					$code .= '->' . $part;
				} else
					return $unset;
			}
		}
		
		eval ( "unset($code);" );
		
		return true;
	}
	
	/**
	 * Return value of template var
	 *
	 * @param string $var        	
	 * @param mixed $items        	
	 * @return value
	 */
	final static function getVarValue($var, $items) {
		$itemsx = array ();
		
		if (is_object ( $items ))
			$itemsx = get_object_vars ( $items );
		elseif (is_array ( $items ))
			$itemsx = $items;
		
		if (isset ( $itemsx [$var] ))
			return $itemsx [$var];
		
		$var = trim ( $var );
		$parts = explode ( DIV_TAG_VAR_MEMBER_DELIMITER, $var );
		
		$current = $itemsx;
		foreach ( $parts as $part ) {
			
			if (trim ( $part ) == "")
				return null;
			
			if (is_array ( $current )) {
				if (isset ( $current [$part] )) {
					$current = $current [$part];
				} else {
					$current = null;
					break;
				}
			} elseif (is_object ( $current )) {
				if (isset ( $current->$part )) {
					$current = $current->$part;
				} else {
					$current = null;
					break;
				}
			}
		}
		
		if (is_null ( $current )) {
			$s = '';
			foreach ( $parts as $part ) {
				if (isset ( $itemsx [$s . $part] )) {
					$current = self::getVarValue ( substr ( $var, strlen ( $s . $part ) + 1 ), $itemsx [$s . $part] );
					if (! is_null ( $current ))
						break;
				}
				$s .= $part . DIV_TAG_VAR_MEMBER_DELIMITER;
			}
		}
		
		return $current;
	}
	
	/**
	 * Set value of template var
	 *
	 * @param string $var        	
	 * @param mixed $value        	
	 * @param mixed $items        	
	 * @return mixed
	 */
	final static function setVarValue($var, $value, &$items, $force = true) {
		if (isset ( $items [$var] ))
			$items [$var] = $value;
		
		$var = trim ( $var );
		$parts = explode ( DIV_TAG_VAR_MEMBER_DELIMITER, $var );
		$current = $items;
		
		$c = count ( $parts );
		$i = 0;
		
		$code = '$items';
		foreach ( $parts as $part ) {
			$i ++;
			
			if (trim ( $part ) == "" && is_object ( $current ))
				return null;
			
			if (is_array ( $current )) {
				
				if (isset ( $current [$part] )) {
					if ($i < $c) {
						if ($part == "")
							$code .= '[]';
						else
							$code .= '[\'' . addslashes ( $part ) . '\']';
						$current = $current [$part];
					} else {
						if ($part == "")
							eval ( $code . '[] = $value;' );
						else
							eval ( $code . '[$part] = $value;' );
					}
				} else {
					if ($i < $c) {
						if ($part == "") {
							eval ( $code . '[] = array();' );
							$current = array ();
							$code .= '[]';
						} else {
							eval ( $code . '[$part] = array();' );
							$current = array ();
							$code .= '[\'' . addslashes ( $part ) . '\']';
						}
					} else {
						if ($part == "")
							eval ( $code . '[] = $value;' );
						else
							eval ( $code . '[$part] = $value;' );
					}
				}
			} elseif (is_object ( $current )) {
				if (self::isValidVarName ( $part )) {
					$part = str_replace ( '$', '', $part );
					if (isset ( $current->$part )) {
						if ($i < $c) {
							$code .= '->' . $part;
							$current = $current->$part;
						} else {
							eval ( $code . '->$part = $value;' );
						}
					} else {
						if ($i < $c) {
							eval ( $code . '->$part = new stdClass();' );
							$code .= '->' . $part;
							$current = new stdClass ();
						} else {
							eval ( $code . '->$part = $value;' );
						}
					}
				} else
					break;
			}
		}
		
		return $items;
	}
	
	/**
	 * Parse data
	 *
	 * @param array $items        	
	 */
	final public function parseData(&$items) {
		if (self::$__log_mode)
			$this->logger ( "Parsing data in templates..." );
		
		$varsya = array ();
		
		$pos = 0;
		
		$tag_begin = DIV_TAG_TPLVAR_BEGIN;
		$tag_end = DIV_TAG_TPLVAR_END;
		
		$l1 = strlen ( $tag_begin );
		$l2 = strlen ( $tag_end );
		
		while ( true ) {
			$ranges = $this->getRanges ( $tag_begin, $tag_end, null, true, $pos );
			if (count ( $ranges ) < 1)
				break;
			
			$ini = $ranges [0] [0];
			$fin = $ranges [0] [1];
			
			if ($this->searchInListRanges ( $ini )) {
				$pos = $ini + 1;
				continue;
			}
			if ($this->searchPosAfterRange ( DIV_TAG_FORMULA_BEGIN, DIV_TAG_FORMULA_END, $ini )) {
				$pos = $ini + 1;
				continue;
			}
			
			// Div 4.5: checking also orphan parts
			// TODO: see "last chance" algorithm in ->parse(); and improve this solution (2 solutions was found)
			$cr = $this->getConditionalRanges ( true, null, false );
			if ($this->searchInRanges ( $cr, $ini )) {
				$pos = $ini + 1;
				continue;
			}
			
			if ($this->searchInCapsuleRanges ( $items, $ini )) {
				$pos = $ini + 1;
				continue;
			}
			
			if ($this->searchInRanges ( $this->getRanges ( DIV_TAG_CONDITIONS_BEGIN_PREFIX, DIV_TAG_CONDITIONS_END ), $ini )) {
				$pos = $ini + 1;
				continue;
			}
			
			$body = substr ( $this->__src, $ini + $l1, $fin - $ini - $l1 );
			$arr = explode ( ":", $body, 2 );
			$var = $arr [0];
			
			if (isset ( $arr [1] ))
				$exp = $arr [1];
			else
				$exp = "";
			
			$var = trim ( $var );
			
			$setup_design = true;
			
			// Check for append of array items
			$ni = strpos ( $var, '[]' );
			if ($ni !== false) {
				
				$tempvalue = self::getVarValue ( trim ( substr ( $var, 0, $ni ) ), $items );
				
				if (is_array ( $tempvalue )) {
					if (! isset ( self::$__globals_design [$var] ))
						$setup_design = false;
					$var = str_replace ( '[]', '[' . count ( $tempvalue ) . ']', $var );
				}
			}
			
			// Normalize varname syntax (to dots/DIV_TAG_VAR_MEMBER_DELIMITER)
			$var = str_replace ( array (
					"[",
					"]" 
			), array (
					DIV_TAG_VAR_MEMBER_DELIMITER,
					"" 
			), $var );
			
			$var = str_replace ( "->", DIV_TAG_VAR_MEMBER_DELIMITER, $var );
			
			if ($setup_design) {
				// Search if var is design var or not
				$ni = - 1;
				do {
					$ni = strpos ( $var, DIV_TAG_VAR_MEMBER_DELIMITER, $ni + 1 );
					
					if ($ni !== false) {
						$nv = trim ( substr ( $var, 0, $ni ) );
						if (! isset ( self::$__globals_design [$nv] ) && self::issetVar ( $nv, $items )) {
							$setup_design = false;
							break;
						}
					}
				} while ( $ni !== false );
			}
			
			// Protect the variable
			if (substr ( $var, 0, 1 ) == DIV_TAG_TPLVAR_PROTECTOR) {
				$var = substr ( $var, 1 );
				self::$__globals_design_protected [$var] = true;
			}
			
			if ($this->searchPosAfterRange ( DIV_TAG_MACRO_BEGIN, DIV_TAG_MACRO_END, $ini )) {
				$pos = $ini + 1;
				continue;
			}
			
			// Previous use
			if (self::issetVar ( $var, $items )) {
				$r = $this->checkLogicalOrder ( $ini, $var, true, true, true, false );
				if ($r !== false) {
					$pos = $r;
					continue;
				}
			}
			
			$setup = false;
			
			// Check protection
			if ((! isset ( self::$__globals_design [$var] ) || (isset ( self::$__globals_design [$var] ) && ! isset ( self::$__globals_design_protected [$var] )))) {
				
				$exp = trim ( $exp );
				
				if (substr ( $exp, 0, 2 ) == "->") { // parsing a method
					$exp = substr ( $exp, 2 );
					$value = $this->getMethodResult ( $exp, $items );
					$setup = $value != DIV_METHOD_NOT_EXISTS && (! self::issetVar ( $var, $items ) || self::issetVar ( $var, self::$__globals_design ));
				} elseif (substr ( $exp, 0, 1 ) == "$") {
					$varx = substr ( $exp, 1 );
					if (self::issetVar ( $varx, $items )) {
						$value = self::getVarValue ( $varx, $items );
						$setup = ! self::issetVar ( $var, $items ) || self::issetVar ( $var, self::$__globals_design );
					}
				} else { // parsing a JSON code
					$allitems = $this->getAllItems ( $items );
					$json = self::jsonDecode ( $exp, $allitems );
					
					if (is_null ( self::compact ( $json ) )) {
						$temp = uniqid ();
						$temp1 = uniqid ();
						$exp = str_replace ( DIV_TAG_INCLUDE_BEGIN, $temp, $exp );
						$exp = str_replace ( DIV_TAG_PREPROCESSED_BEGIN, $temp1, $exp );
						$engine = self::getAuxiliaryEngineClone ( $allitems );
						$engine->__src = $exp;
						$engine->parse ( false );
						$exp = $engine->__src;
						$exp = str_replace ( $temp, DIV_TAG_INCLUDE_BEGIN, $exp );
						$exp = str_replace ( $temp1, DIV_TAG_PREPROCESSED_BEGIN, $exp );
					}
					
					if (! self::issetVar ( $var, $items ) || self::issetVar ( $var, self::$__globals_design )) {
						
						if (self::fileExists ( $exp ) && ! self::isDir ( $exp )) {
							$fgc = self::getFileContents ( $exp );
							if ($fgc != "")
								$exp = $fgc;
						} elseif (self::fileExists ( $exp . "." . DIV_DEFAULT_DATA_FILE_EXT ) && ! self::isDir ( $exp . "." . DIV_DEFAULT_DATA_FILE_EXT )) {
							$fgc = self::getFileContents ( $exp . "." . DIV_DEFAULT_DATA_FILE_EXT );
							if ($fgc != "")
								$exp = $fgc;
						} elseif (self::fileExists ( $exp . "." . DIV_DEFAULT_TPL_FILE_EXT ) && ! self::isDir ( $exp . "." . DIV_DEFAULT_TPL_FILE_EXT )) {
							$fgc = self::getFileContents ( $exp . "." . DIV_DEFAULT_TPL_FILE_EXT );
							if ($fgc != "")
								$exp = $fgc;
						}
						
						if (isset ( $exp [0] ))
							$_exp = $exp [0];
						else
							$_exp = '';
						
						if (($_exp != '{' && $_exp != "[" && ! is_numeric ( $_exp ) && $_exp != '"' && $_exp != "'") || (substr ( $exp, 0, strlen ( DIV_TAG_INCLUDE_BEGIN ) ) == DIV_TAG_INCLUDE_BEGIN && substr ( $exp, 0 - strlen ( DIV_TAG_INCLUDE_END ) ) == DIV_TAG_INCLUDE_END) || (substr ( $exp, 0, strlen ( DIV_TAG_PREPROCESSED_BEGIN ) ) == DIV_TAG_PREPROCESSED_BEGIN && substr ( $exp, 0 - strlen ( DIV_TAG_PREPROCESSED_END ) ) == DIV_TAG_PREPROCESSED_END)) {
							$exp = '"' . str_replace ( '"', '\"', $exp ) . '"';
						}
						
						$value = self::jsonDecode ( $exp, $allitems );
						
						$vars = $value;
						if (is_object ( $vars ))
							$vars = get_object_vars ( $vars );
						
						if (is_array ( $vars )) {
							foreach ( $vars as $kkk => $vvv ) {
								if (self::isString ( $vvv )) {
									$vvv = trim ( $vvv );
									if (isset ( $vvv [0] ))
										if ($vvv [0] == "$") {
											$varx = substr ( $vvv, 1 );
											if (self::issetVar ( $varx, $items )) {
												if (is_array ( $value ))
													$value [$kkk] = self::getVarValue ( $varx, $items );
												if (is_object ( $value ))
													$value->$kkk = self::getVarValue ( $varx, $items );
											}
										}
								}
							}
						}
						$setup = true;
					}
				}
			}
			if ($setup == true) {
				self::setVarValue ( $var, $value, $items );
				if ($setup_design)
					self::setVarValue ( $var, $value, self::$__globals_design );
			}
			
			$this->__src = substr ( $this->__src, 0, $ini ) . substr ( $this->__src, $fin + $l2 );
		}
	}
	
	/**
	 * Parse defaults replacements
	 *
	 * @param array $items        	
	 */
	final public function parseDefaults(&$items) {
		if (self::$__log_mode)
			$this->logger ( "Parsing default replacements..." );
		
		$prefix = DIV_TAG_DEFAULT_REPLACEMENT_BEGIN;
		$suffix = DIV_TAG_DEFAULT_REPLACEMENT_END;
		
		$l1 = strlen ( $prefix );
		$l2 = strlen ( $suffix );
		
		while ( true ) {
			$ranges = $this->getRanges ( $prefix, $suffix, null, true );
			if (count ( $ranges ) < 1)
				break;
			
			$ini = $ranges [0] [0];
			$fin = $ranges [0] [1];
			
			$body = substr ( $this->__src, $ini + $l1, $fin - $ini - $l1 );
			
			$arr = self::jsonDecode ( $body, $this->getAllItems ( $items ) );
			
			if (! isset ( $arr [0] ) || ! isset ( $arr [1] ))
				self::error ( "Was detected an invalid JSON in default values: " . substr ( $body, 0, 80 ) . "...", DIV_ERROR_FATAL );
			
			if (isset ( $arr [2] )) {
				// Default by var
				$var = $arr [0];
				$search = $arr [1];
				$replace = $arr [2];
			} else {
				// Global default
				$var = null;
				$search = $arr [0];
				$replace = $arr [1];
			}
			
			if (self::fileExists ( $replace ) && ! self::isDir ( $search ))
				$replace = self::jsonDecode ( self::getFileContents ( $replace ), $this->getAllItems ( $items ) );
			if (self::fileExists ( $replace . "." . DIV_DEFAULT_DATA_FILE_EXT ) && ! self::isDir ( $search . "." . DIV_DEFAULT_DATA_FILE_EXT ))
				$replace = self::jsonDecode ( self::getFileContents ( $replace . "." . DIV_DEFAULT_DATA_FILE_EXT ), self::cop ( $this->__memory, $items ) );
			if (self::fileExists ( $search ) && ! self::isDir ( $search ))
				$search = self::jsonDecode ( self::getFileContents ( $search ), $this->getAllItems ( $items ) );
			if (self::fileExists ( $search . "." . DIV_DEFAULT_DATA_FILE_EXT ) && ! self::isDir ( $search . "." . DIV_DEFAULT_DATA_FILE_EXT ))
				$search = self::jsonDecode ( self::getFileContents ( $search . "." . DIV_DEFAULT_DATA_FILE_EXT ), $this->getAllItems ( $items ) );
			
			if (is_null ( $var )) {
				self::setDefault ( $search, $replace );
			} else {
				self::setDefaultByVar ( $var, $search, $replace, false );
			}
			
			$this->__src = substr ( $this->__src, 0, $ini ) . substr ( $this->__src, $fin + 2 );
		}
	}
	
	/**
	 * Parse number formats
	 *
	 * @param array $items        	
	 */
	final public function parseNumberFormat(&$items = array()) {
		if (self::$__log_mode)
			$this->logger ( "Parsing number's formats..." );
		
		$prefix = DIV_TAG_NUMBER_FORMAT_PREFIX;
		$suffix = DIV_TAG_NUMBER_FORMAT_SUFFIX;
		$ranges = $this->getRanges ( $prefix, $suffix );
		
		$l1 = strlen ( $prefix );
		$l2 = strlen ( $suffix );
		
		foreach ( $ranges as $range ) {
			$s = substr ( $this->__src, $ranges [0] [0] + $l1, $ranges [0] [1] - $ranges [0] [0] - $l1 );
			$arr = explode ( DIV_TAG_NUMBER_FORMAT_SEPARATOR, $s );
			if (! isset ( $items [$arr [0]] ) && is_numeric ( $arr [0] ))
				$items [$arr [0]] = ( float ) $arr [0];
		}
		
		if (is_array ( $items ))
			foreach ( $items as $key => $value ) {
				if (is_numeric ( $value ))
					$this->numberFormat ( $key, "$value" );
			}
	}
	
	/**
	 * Scan matches and call parseMatch
	 *
	 * @param string $key        	
	 * @param mixed $value        	
	 */
	final public function scanMatch($key, $value, $engine = null, &$items = null, $ignore_logical_order = false) {
		if (is_null ( $items ))
			$items = &$this->__items;
		if (is_null ( $engine ))
			$engine = self::getAuxiliaryEngineClone ( $items );
			
			// Scan child properties
		if (strpos ( $this->__src, $key . DIV_TAG_VAR_MEMBER_DELIMITER ) !== false) {
			$vars = false;
			
			$vvv = $value;
			if (is_scalar ( $vvv ))
				$vvv = "$value";
			
			if (self::isString ( $vvv ))
				$vars = str_split ( $vvv );
			elseif (is_object ( $vvv ))
				$vars = get_object_vars ( $vvv );
			else
				$vars = $vvv;
			
			if (is_array ( $vars ))
				foreach ( $vars as $kk => $v )
					$this->scanMatch ( $key . DIV_TAG_VAR_MEMBER_DELIMITER . $kk, $v, $engine, $items, $ignore_logical_order );
		}
		
		// Match this key
		$this->parseMatch ( $key, $value, $engine, $ignore_logical_order );
		
		// Match aggregate functions
		if (is_object ( $value ))
			if (! method_exists ( $value, '__toString' ))
				$value = get_object_vars ( $value );
		
		if (is_array ( $value )) {
			
			$cant_values = count ( $value );
			
			$this->parseMatch ( $key, $cant_values, $engine, $ignore_logical_order );
			
			$sep = DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR;
			$fmax = DIV_TAG_AGGREGATE_FUNCTION_MAX;
			$fmin = DIV_TAG_AGGREGATE_FUNCTION_MIN;
			
			if ($cant_values > 0) {
				if (strpos ( $this->__src, $key ) !== false) {
					if (self::isNumericList ( $value ) === true) {
						$sum = array_sum ( $value );
						$keys = array_keys ( $value );
						
						if ($cant_values > 1) {
							$this->parseMatch ( $fmax . $sep . $key, max ( $value ), $engine, $ignore_logical_order );
							$this->parseMatch ( $fmin . $sep . $key, min ( $value ), $engine, $ignore_logical_order );
						} else {
							
							$this->parseMatch ( $fmax . $sep . $key, $value [$keys [0]], $engine, $ignore_logical_order );
							$this->parseMatch ( $fmin . $sep . $key, $value [$keys [0]], $engine, $ignore_logical_order );
						}
						
						$this->parseMatch ( DIV_TAG_AGGREGATE_FUNCTION_SUM . $sep . $key, $sum, $engine, $ignore_logical_order );
						$this->parseMatch ( DIV_TAG_AGGREGATE_FUNCTION_AVG . $sep . $key, $sum / $cant_values, $engine, $ignore_logical_order );
					}
				}
				
				if (strpos ( $this->__src, $key . DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR ) !== false) {
					
					$functions = array (
							"",
							DIV_TAG_AGGREGATE_FUNCTION_COUNT,
							DIV_TAG_AGGREGATE_FUNCTION_MAX,
							DIV_TAG_AGGREGATE_FUNCTION_MIN,
							DIV_TAG_AGGREGATE_FUNCTION_SUM,
							DIV_TAG_AGGREGATE_FUNCTION_AVG 
					);
					
					foreach ( $functions as $function ) {
						if ($function == "")
							$ff = "";
						else
							$ff = $function . DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR;
						
						$tag_begin = $ff . $key . DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR;
						$tag_end = DIV_TAG_REPLACEMENT_SUFFIX;
						
						$l = strlen ( $tag_begin );
						$result = array ();
						
						$p = 0;
						while ( true ) {
							$ranges = $this->getRanges ( $tag_begin, $tag_end, $this->__src, true, $p );
							if (count ( $ranges ) < 1)
								break;
							
							$range = $ranges [0];
							
							if ($this->searchInRanges ( $this->getRanges ( DIV_TAG_MACRO_BEGIN, DIV_TAG_MACRO_END ), $range [0] )) {
								$p = $range [0] + 1;
								continue;
							}
							
							$var = substr ( $this->__src, $range [0] + $l, $range [1] - ($range [0] + $l) );
							
							if (strpos ( $var, DIV_TAG_SUBMATCH_SEPARATOR ) !== false) {
								$var = explode ( DIV_TAG_SUBMATCH_SEPARATOR, $var );
								$var = $var [0];
							} elseif (strpos ( $var, DIV_TAG_NUMBER_FORMAT_SEPARATOR ) !== false) {
								$var = explode ( DIV_TAG_NUMBER_FORMAT_SEPARATOR, $var );
								$var = $var [0];
							}
							
							if (! isset ( $result [$var] )) {
								$c = 0;
								$result [$var] = array ();
								$max = null;
								$min = null;
								$sum = 0;
								$avg = 0;
								
								foreach ( $value as $v ) {
									if (is_object ( $v ))
										$v = get_object_vars ( $v );
									if (isset ( $v [$var] )) {
										if (is_bool ( $v [$var] ) || self::isString ( $v [$var] ))
											$cant = 1;
										if (is_numeric ( $v [$var] ))
											$cant = $v [$var];
										
										switch ($function) {
											case DIV_TAG_AGGREGATE_FUNCTION_MIN :
												if ($min * 1 > $v [$var] * 1 || is_null ( $min ))
													$min = $v [$var] * 1;
												break;
											case DIV_TAG_AGGREGATE_FUNCTION_MAX :
												if ($max * 1 < $v [$var] * 1 || is_null ( $max ))
													$max = $v [$var] * 1;
												break;
											case DIV_TAG_AGGREGATE_FUNCTION_SUM :
												$sum += $cant;
												break;
											case DIV_TAG_AGGREGATE_FUNCTION_AVG :
												$avg += $cant;
											default :
												if (self::mixedBool ( $v [$var] ) === true)
													$c ++;
										}
									}
								}
								
								$result [$var] [DIV_TAG_AGGREGATE_FUNCTION_MIN] = $min;
								$result [$var] [DIV_TAG_AGGREGATE_FUNCTION_MAX] = $max;
								$result [$var] [DIV_TAG_AGGREGATE_FUNCTION_COUNT] = $c;
								$result [$var] [DIV_TAG_AGGREGATE_FUNCTION_SUM] = $sum;
								
								if ($cant_values > 0)
									$result [$var] [DIV_TAG_AGGREGATE_FUNCTION_AVG] = $avg / $cant_values;
								else
									$result [$var] [DIV_TAG_AGGREGATE_FUNCTION_AVG] = 0;
							}
							
							$res = $result [$var] [$function == "" ? DIV_TAG_AGGREGATE_FUNCTION_COUNT : $function];
							$var = $ff . $key . DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR . $var;
							$items [$var] = $res;
							
							$this->parseMatch ( $var, $res, $engine, $ignore_logical_order );
							$p = $range [0] + 1;
						}
					}
				}
			}
		}
	}
	
	/**
	 * Parsing multple variable's modifiers
	 */
	final public function parseMultipleModifiers() {
		$prefix = DIV_TAG_MULTI_MODIFIERS_PREFIX;
		$suffix = DIV_TAG_MULTI_MODIFIERS_SUFFIX;
		$l1 = strlen ( $prefix );
		$l2 = strlen ( $suffix );
		$p = 0;
		while ( true ) {
			$ranges = $this->getRanges ( $prefix, $suffix, null, true, $p );
			if (count ( $ranges ) > 0) {
				$ini = $ranges [0] [0];
				$fin = $ranges [0] [1];
				$s = substr ( $this->__src, $ini + $l1, $fin - $ini - $l1 );
				
				$pp = strpos ( $s, DIV_TAG_MULTI_MODIFIERS_OPERATOR );
				if ($pp === false) {
					$p = $ini + 1;
					continue;
				}
				$varname = trim ( substr ( $s, 0, $pp ) );
				$s = substr ( $s, $pp + 1 );
				$parts = explode ( DIV_TAG_MULTI_MODIFIERS_SEPARATOR, $s );
				
				$newcode = '';
				if (count ( $parts ) > 0) {
					
					$newvar = "var" . uniqid ();
					self::$__dont_remember_it [$newvar] = true;
					
					$newcode = DIV_TAG_STRIP_BEGIN . ' ' . DIV_TAG_TPLVAR_BEGIN . ' ' . $newvar . DIV_TAG_TPLVAR_ASSIGN_OPERATOR . ' $' . $varname . ' ' . DIV_TAG_TPLVAR_END . "\n";
					$ignore = false;
					foreach ( $parts as $part ) {
						if (trim ( $part ) !== "") {
							if ($part [0] == DIV_TAG_MODIFIER_TRUNCATE || $part [0] == DIV_TAG_MODIFIER_WORDWRAP || strpos ( $part, "," ) !== false) {
								$newcode .= DIV_TAG_TPLVAR_BEGIN . ' ' . $newvar . DIV_TAG_TPLVAR_ASSIGN_OPERATOR . ' ' . DIV_TAG_REPLACEMENT_PREFIX . DIV_TAG_MODIFIER_SIMPLE . $newvar . DIV_TAG_SUBMATCH_SEPARATOR . $part . DIV_TAG_REPLACEMENT_SUFFIX . ' ' . DIV_TAG_TPLVAR_END . "\n";
							} elseif (array_search ( $part . ':', self::$__modifiers )) {
								$newcode .= DIV_TAG_TPLVAR_BEGIN . ' ' . $newvar . DIV_TAG_TPLVAR_ASSIGN_OPERATOR . ' ' . DIV_TAG_REPLACEMENT_PREFIX . DIV_TAG_MODIFIER_SIMPLE . $newvar . DIV_TAG_SUBMATCH_SEPARATOR . $part . DIV_TAG_REPLACEMENT_SUFFIX . ' ' . DIV_TAG_TPLVAR_END . "\n";
							} elseif (array_search ( $part, self::$__modifiers ) !== false) {
								$newcode .= DIV_TAG_TPLVAR_BEGIN . ' ' . $newvar . DIV_TAG_TPLVAR_ASSIGN_OPERATOR . ' ' . DIV_TAG_REPLACEMENT_PREFIX . $part . $newvar . DIV_TAG_REPLACEMENT_SUFFIX . ' ' . DIV_TAG_TPLVAR_END . "\n";
							} else {
								$p = $ini + 1;
								$ignore = true;
								break;
							}
						}
					}
					if ($ignore)
						continue;
					$newcode .= DIV_TAG_REPLACEMENT_PREFIX . DIV_TAG_MODIFIER_SIMPLE . $newvar . DIV_TAG_REPLACEMENT_SUFFIX . ' ' . DIV_TAG_STRIP_END . "\n";
				}
				
				$this->__src = substr ( $this->__src, 0, $ini ) . $newcode . substr ( $this->__src, $fin + $l2 );
				$p = $ini + 1;
			} else
				break;
		}
	}
	
	/**
	 * Parse all matches
	 *
	 * @param array $items        	
	 */
	final public function parseMatches(&$items = null, $ignore_logical_order = false) {
		if (self::$__log_mode)
			$this->logger ( "Parsing matches..." );
		if (is_null ( $items ))
			$items = &$this->__items;
		
		$restore = array ();
		$lastpos = 0;
		
		if (strpos ( $this->__src, DIV_TAG_LOOP_BEGIN_PREFIX ) !== false) {
			
			$lprefix = strlen ( DIV_TAG_LOOP_BEGIN_PREFIX );
			$lsuffix = strlen ( DIV_TAG_LOOP_BEGIN_SUFFIX );
			
			while ( true ) {
				if ($lastpos > strlen ( $this->__src ) - 1)
					break;
				
				$ranges = $this->getBlockRanges ( null, DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_BEGIN_SUFFIX, DIV_TAG_LOOP_END_PREFIX, DIV_TAG_LOOP_END_SUFFIX, $lastpos, null, true );
				
				if (! isset ( $ranges [0] ))
					break;
				
				$ini = $ranges [0] [0];
				$fin = $ranges [0] [1];
				$lastpos = $ini + 1;
				$ukey = uniqid ();
				$restore [$ukey] = $ranges [0] [3];
				$this->__src = substr ( $this->__src, 0, $ini + $lprefix + strlen ( $ranges [0] [2] ) + $lsuffix ) . $ukey . substr ( $this->__src, $ranges [0] [1] );
			}
		}
		
		$engine = self::getAuxiliaryEngineClone ( $items );
		if (is_array ( $items ))
			foreach ( $items as $key => $value )
				$this->scanMatch ( $key, $value, $engine, $items, $ignore_logical_order );
		
		foreach ( $restore as $ukey => $part )
			$this->__src = str_replace ( $ukey, $part, $this->__src );
	}
	
	/**
	 * Parse formulas
	 *
	 * @param
	 *        	s array $items
	 */
	final public function parseFormulas(&$items = array()) {
		if (self::$__log_mode)
			$this->logger ( "Parsing formulas..." );
		
		$p1 = strpos ( $this->__src, DIV_TAG_TPLVAR_BEGIN );
		
		$engine = self::getAuxiliaryEngineClone ( $items );
		$lprefix = strlen ( DIV_TAG_FORMULA_BEGIN );
		$lsuffix = strlen ( DIV_TAG_FORMULA_END );
		
		while ( true ) {
			$ranges = $this->getRanges ( DIV_TAG_FORMULA_BEGIN, DIV_TAG_FORMULA_END, null, true );
			if (count ( $ranges ) > 0) {
				$ini = $ranges [0] [0];
				$fin = $ranges [0] [1];
				
				if ($ini > $p1 && $p1 !== false)
					return true;
				
				$formula = substr ( $this->__src, $ini + $lprefix, $fin - ($ini + $lprefix) );
				$formula_orig = $formula;
				
				if (self::$__log_mode)
					$this->logger ( "Parsing the formula (from {$ini} to {$fin}): $formula" );
				
				$engine->__src = $formula;
				
				$engine->parse ( false );
				
				$formula = $engine->__src;
				
				// Get the number format
				$pos = strrpos ( $formula, DIV_TAG_FORMULA_FORMAT_SEPARATOR );
				$format = "";
				
				if ($pos !== false && isset ( $formula [$pos + 1] )) {
					$format = trim ( substr ( $formula, $pos + 1 ) );
					$formula = substr ( $formula, 0, $pos );
				}
				
				$r = null;
				
				if (self::isValidExpression ( $formula )) {
					if (! self::haveVarsThisCode ( $formula )) {
						// Save the error reporting configurarion
						
						$error_reporting = ini_get ( "error_reporting" );
						ini_set ( "error_reporting", ~ E_ALL );
						
						eval ( '$r = ' . $formula . ";" );
						
						// Restore the error reporting configurarion
						ini_set ( "error_reporting", $error_reporting );
						$random_var = uniqid ();
					}
				}
				
				if (is_null ( $r )) {
					$restore_id = uniqid ();
					$this->__restore [$restore_id] = DIV_TAG_FORMULA_BEGIN . ' ' . $formula_orig . DIV_TAG_FORMULA_END;
					$this->__src = substr ( $this->__src, 0, $ini ) . '{' . "$restore_id" . '}' . substr ( $this->__src, $fin + $lsuffix );
					continue;
				}
				
				if ($format != "" && is_numeric ( $r )) {
					$this->__src = substr ( $this->__src, 0, $ini ) . DIV_TAG_NUMBER_FORMAT_PREFIX . $random_var . DIV_TAG_NUMBER_FORMAT_SEPARATOR . $format . DIV_TAG_NUMBER_FORMAT_SUFFIX . substr ( $this->__src, $fin + $lsuffix );
					$this->numberFormat ( $random_var, $r );
				} else
					$this->__src = substr ( $this->__src, 0, $ini ) . $r . substr ( $this->__src, $fin + $lsuffix );
			} else
				break;
		}
	}
	
	/**
	 * Parse conditions
	 *
	 * @param array $items        	
	 * @param bool $cleanorphan        	
	 */
	final public function parseConditions(&$items, $cleanorphan = false) {
		if (self::$__log_mode)
			$this->logger ( "Parsing conditions..." );
		
		$classname = get_class ( $this );
		$pos = 0;
		
		$lprefix = strlen ( DIV_TAG_CONDITIONS_BEGIN_PREFIX );
		$lsuffix = strlen ( DIV_TAG_CONDITIONS_BEGIN_SUFFIX );
		$lend = strlen ( DIV_TAG_CONDITIONS_END );
		$lelse = strlen ( DIV_TAG_ELSE );
		
		while ( true ) {
			$ranges = $this->getRanges ( DIV_TAG_CONDITIONS_BEGIN_PREFIX, DIV_TAG_CONDITIONS_END, null, true, $pos );
			
			if (count ( $ranges ) > 0) {
				$ini = $ranges [0] [0];
				$fin = $ranges [0] [1];
				
				if ($this->searchInRanges ( $this->getConditionalRanges ( true ), $pos, true )) {
					$pos = $ini + 1;
					continue;
				}
				
				if ($this->searchInListRanges ( $ini )) {
					$pos = $ini + 1;
					continue;
				}
				
				if ($this->searchInListRanges ( $this->getRanges ( DIV_TAG_ITERATION_BEGIN_PREFIX, DIV_TAG_ITERATION_END ), $ini )) {
					$pos = $ini + 1;
					continue;
				}
				
				$body = substr ( $this->__src, $ini + $lprefix, $fin - $ini - $lprefix );
				
				$p = strpos ( $body, DIV_TAG_CONDITIONS_BEGIN_SUFFIX );
				
				$condition = '';
				
				if ($p !== false) {
					$condition = substr ( $body, 0, $p );
					
					$body = substr ( $body, $p + $lsuffix );
					$else = $this->getElseTag ( $body );
					if ($else != false) {
						$body_parts = array (
								substr ( $body, 0, $else ),
								substr ( $body, $else + $lelse ) 
						);
					} else
						$body_parts = array (
								$body,
								"" 
						);
					
					if ($body_parts [0] != "") {
						if ($body_parts [0] [0] == ' ')
							$body_parts [0] = substr ( $body_parts [0], 1 );
						if (substr ( $body_parts [0], - 1 ) == ' ')
							$body_parts [0] = substr_replace ( $body_parts [0], "", - 1 );
					}
					
					if ($body_parts [1] != "") {
						if ($body_parts [1] [0] == ' ')
							$body_parts [1] = substr ( $body_parts [1], 1 );
						if (substr ( $body_parts [1], - 1 ) == ' ')
							$body_parts [1] = substr_replace ( $body_parts [1], "", - 1 );
					}
					
					$r = false;
					
					if (self::$__log_mode)
						$this->logger ( "Parsing condition (from $ini to $fin): $condition" );
					
					$engine = self::getAuxiliaryEngineClone ( $items );
					$engine->__src = $condition;
					$engine->parse ( false );
					$condition = $engine->__src;
					
					if (self::isValidExpression ( $condition )) {
						if (! self::haveVarsThisCode ( $condition )) {
							$error_reporting = ini_get ( "error_reporting" );
							ini_set ( "error_reporting", ~ E_ALL );
							eval ( '$r = ' . $condition . ';' );
							$r = self::mixedBool ( $r );
							ini_set ( "error_reporting", $error_reporting );
						} else {
							if ($cleanorphan === false) {
								$pos = $ini + $lprefix;
								continue;
							}
						}
					} else {
						if (self::$__log_mode)
							$this->logger ( "The condition $condition is not valid" );
						
						if ($cleanorphan === false) {
							$pos = $ini + $lprefix;
							continue;
						}
					}
					
					if ($r === true) {
						$body = $body_parts [0];
						if (self::$__log_mode)
							$this->logger ( "The condition $condition is true" );
					} else {
						$body = $body_parts [1];
						if (self::$__log_mode)
							$this->logger ( "The condition $condition is false" );
					}
					
					$this->__src = substr ( $this->__src, 0, $ini ) . $body . substr ( $this->__src, $fin + $lend );
				} else
					self::error ( "Parse error on <b>conditions</b>: " . substr ( $condition, 0, 50 ) . "...", DIV_ERROR_FATAL );
			} else
				break;
		}
	}
	
	/**
	 * Parse conditional parts
	 *
	 * @param array $items        	
	 */
	final public function parseConditional(&$items = array()) {
		if (self::$__log_mode)
			$this->logger ( "Parsing conditional parts..." );
		
		$r = array_merge ( $this->getBlockRanges ( null, DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX, DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX, DIV_TAG_CONDITIONAL_TRUE_END_PREFIX, DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX ), $this->getBlockRanges ( null, DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX, DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX, DIV_TAG_CONDITIONAL_FALSE_END_PREFIX, DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX ) );
		
		$vars = array ();
		$ii = $this->getAllItems ( $items );
		foreach ( $r as $tag ) {
			
			$otag = $tag;
			$tag [2] = self::div ( $tag [2], $ii );
			
			if (self::issetVar ( $tag [2], $items )) {
				$vars [$otag [2]] = self::getVarValue ( $tag [2], $items );
			} else {
				$arr = explode ( '-', $tag [2] );
				if (isset ( $arr [1] ) && ! isset ( $arr [2] )) { // count($arr) == 2
					if (self::issetVar ( $arr [0], $items )) {
						$v = self::getVarValue ( $arr [0], $items );
						if (is_object ( $v ))
							$v = get_object_vars ( $v );
						if (is_array ( $v )) {
							foreach ( $v as $kk => $iv ) {
								$vv = self::getVarValue ( $arr [1], $iv );
								if (isset ( $vars [$nv] ))
									$vars [$otag [2]] = self::mixedBool ( $vars [$otag [2]] ) || self::mixedBool ( $vv );
								else
									$vars [$otag [2]] = self::mixedBool ( $vv );
							}
						}
					}
				}
			}
		}
		
		$varsx = $this->getActiveVars ( $items );
		foreach ( $varsx as $var )
			$vars [$var] = self::mixedBool ( self::getVarValue ( $var, $items ) );
		
		if (! empty ( $vars )) {
			$keys = array_keys ( $vars );
			$nkeys = array ();
			foreach ( $keys as $k => $v )
				$nkeys [$v] = strlen ( $v );
			arsort ( $nkeys );
			foreach ( $nkeys as $var => $l )
				$this->parseConditionalBlock ( $var, $vars [$var] );
		}
	}
	
	/**
	 * Parse orphan parts
	 */
	final public function parseOrphanParts() {
		if (self::$__log_mode)
			$this->logger ( "Parsing orphan parts..." );
		
		$keys = $this->getConditionalKeys ();
		
		foreach ( $keys as $key )
			$this->parseConditionalBlock ( $key, false );
	}
	
	/**
	 * Return a list of conditional parts's tags
	 *
	 * @return array
	 */
	final public function getConditionalKeys() {
		$ranges = $this->getConditionalRanges ();
		
		$keys = array ();
		foreach ( $ranges as $rang )
			$keys [] = $rang [2];
		
		return $keys;
	}
	
	/**
	 * Return true if $src have a div code
	 */
	final static function haveDivCode($src) {
		$all_tags = array (
				DIV_TAG_REPLACEMENT_PREFIX,
				DIV_TAG_REPLACEMENT_SUFFIX,
				DIV_TAG_MULTI_MODIFIERS_PREFIX,
				DIV_TAG_MULTI_MODIFIERS_OPERATOR,
				DIV_TAG_MULTI_MODIFIERS_SEPARATOR,
				DIV_TAG_MULTI_MODIFIERS_SUFFIX,
				DIV_TAG_SUBMATCH_SEPARATOR,
				DIV_TAG_MODIFIER_SIMPLE,
				DIV_TAG_MODIFIER_CAPITALIZE_FIRST,
				DIV_TAG_MODIFIER_CAPITALIZE_WORDS,
				DIV_TAG_MODIFIER_UPPERCASE,
				DIV_TAG_MODIFIER_LOWERCASE,
				DIV_TAG_MODIFIER_LENGTH,
				DIV_TAG_MODIFIER_COUNT_WORDS,
				DIV_TAG_MODIFIER_COUNT_SENTENCES,
				DIV_TAG_MODIFIER_COUNT_PARAGRAPHS,
				DIV_TAG_MODIFIER_ENCODE_URL,
				DIV_TAG_MODIFIER_ENCODE_RAW_URL,
				DIV_TAG_MODIFIER_ENCODE_JSON,
				DIV_TAG_MODIFIER_HTML_ENTITIES,
				DIV_TAG_MODIFIER_NL2BR,
				DIV_TAG_MODIFIER_TRUNCATE,
				DIV_TAG_MODIFIER_WORDWRAP,
				DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR,
				DIV_TAG_MODIFIER_SINGLE_QUOTES,
				DIV_TAG_MODIFIER_JS,
				DIV_TAG_MODIFIER_FORMAT,
				DIV_TAG_DATE_FORMAT_PREFIX,
				DIV_TAG_DATE_FORMAT_SUFFIX,
				DIV_TAG_DATE_FORMAT_SEPARATOR,
				DIV_TAG_NUMBER_FORMAT_PREFIX,
				DIV_TAG_NUMBER_FORMAT_SUFFIX,
				DIV_TAG_NUMBER_FORMAT_SEPARATOR,
				DIV_TAG_FORMULA_BEGIN,
				DIV_TAG_FORMULA_END,
				DIV_TAG_FORMULA_FORMAT_SEPARATOR,
				DIV_TAG_SUBPARSER_BEGIN_PREFIX,
				DIV_TAG_SUBPARSER_BEGIN_SUFFIX,
				DIV_TAG_SUBPARSER_END_PREFIX,
				DIV_TAG_SUBPARSER_END_SUFFIX,
				DIV_TAG_IGNORE_BEGIN,
				DIV_TAG_IGNORE_END,
				DIV_TAG_COMMENT_BEGIN,
				DIV_TAG_COMMENT_END,
				DIV_TAG_TXT_BEGIN,
				DIV_TAG_TXT_END,
				DIV_TAG_TXT_WIDTH_SEPARATOR,
				DIV_TAG_STRIP_BEGIN,
				DIV_TAG_STRIP_END,
				DIV_TAG_LOOP_BEGIN_PREFIX,
				DIV_TAG_LOOP_BEGIN_SUFFIX,
				DIV_TAG_LOOP_END_PREFIX,
				DIV_TAG_LOOP_END_SUFFIX,
				DIV_TAG_EMPTY,
				DIV_TAG_BREAK,
				DIV_TAG_LOOP_VAR_SEPARATOR,
				DIV_TAG_ITERATION_BEGIN_PREFIX,
				DIV_TAG_ITERATION_BEGIN_SUFFIX,
				DIV_TAG_ITERATION_END,
				DIV_TAG_ITERATION_PARAM_SEPARATOR,
				DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX,
				DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX,
				DIV_TAG_CONDITIONAL_TRUE_END_PREFIX,
				DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX,
				DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX,
				DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX,
				DIV_TAG_CONDITIONAL_FALSE_END_PREFIX,
				DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX,
				DIV_TAG_ELSE,
				DIV_TAG_CONDITIONS_BEGIN_PREFIX,
				DIV_TAG_CONDITIONS_BEGIN_SUFFIX,
				DIV_TAG_CONDITIONS_END,
				DIV_TAG_TPLVAR_BEGIN,
				DIV_TAG_TPLVAR_END,
				DIV_TAG_TPLVAR_ASSIGN_OPERATOR,
				DIV_TAG_TPLVAR_PROTECTOR,
				DIV_TAG_DEFAULT_REPLACEMENT_BEGIN,
				DIV_TAG_DEFAULT_REPLACEMENT_END,
				DIV_TAG_INCLUDE_BEGIN,
				DIV_TAG_INCLUDE_END,
				DIV_TAG_PREPROCESSED_BEGIN,
				DIV_TAG_PREPROCESSED_END,
				DIV_TAG_PREPROCESSED_SEPARATOR,
				DIV_TAG_CAPSULE_BEGIN_PREFIX,
				DIV_TAG_CAPSULE_BEGIN_SUFFIX,
				DIV_TAG_CAPSULE_END_PREFIX,
				DIV_TAG_CAPSULE_END_SUFFIX,
				DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX,
				DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX,
				DIV_TAG_MULTI_REPLACEMENT_END_PREFIX,
				DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX,
				DIV_TAG_FRIENDLY_BEGIN,
				DIV_TAG_FRIENDLY_END,
				DIV_TAG_AGGREGATE_FUNCTION_COUNT,
				DIV_TAG_AGGREGATE_FUNCTION_MAX,
				DIV_TAG_AGGREGATE_FUNCTION_MIN,
				DIV_TAG_AGGREGATE_FUNCTION_SUM,
				DIV_TAG_AGGREGATE_FUNCTION_AVG,
				DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR,
				DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR,
				DIV_TAG_LOCATION_BEGIN,
				DIV_TAG_LOCATION_END,
				DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX,
				DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX,
				DIV_TAG_LOCATION_CONTENT_END_PREFIX,
				DIV_TAG_LOCATION_CONTENT_END_SUFFIX,
				DIV_TAG_MACRO_BEGIN,
				DIV_TAG_MACRO_END,
				DIV_TAG_SPECIAL_REPLACE_NEW_LINE,
				DIV_TAG_SPECIAL_REPLACE_CAR_RETURN,
				DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE,
				DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL 
		);
		
		foreach ( $all_tags as $tag ) {
			if ($tag !== '')
				if (strpos ( $src, $tag ) !== false)
					return true;
		}
		
		return false;
	}
	
	/**
	 * Return a list of conditional parts ranges
	 *
	 * @param boolean $orphans        	
	 * @return array
	 */
	final public function getConditionalRanges($orphans = true, $src = null, $strict = false) {
		$ranges = $this->getBlockRanges ( $src, DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX, DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX, DIV_TAG_CONDITIONAL_FALSE_END_PREFIX, DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX );
		
		$ranges = array_merge ( $ranges, $this->getBlockRanges ( $src, DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX, DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX, DIV_TAG_CONDITIONAL_TRUE_END_PREFIX, DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX ) );
		
		if (! $orphans) {
			$nranges = array ();
			foreach ( $ranges as $rang )
				if (self::varExists ( $rang [2], $this->__items ))
					$nranges [] = $rang;
			$ranges = $nranges;
		}
		
		if ($strict !== false) {
			$nranges = array ();
			foreach ( $ranges as $rang )
				if (! self::haveDivCode ( $rang [2] ))
					$nranges [] = $rang;
			$ranges = $nranges;
		}
		
		return $ranges;
	}
	
	/**
	 * Parse date formats
	 *
	 * @param array $items        	
	 */
	final public function parseDateFormat(&$items = array()) {
		if (self::$__log_mode)
			$this->logger ( "Parsing date's formats..." );
		
		$lprefix = strlen ( DIV_TAG_DATE_FORMAT_PREFIX );
		$lsuffix = strlen ( DIV_TAG_DATE_FORMAT_SUFFIX );
		$ranges = $this->getRanges ( DIV_TAG_DATE_FORMAT_PREFIX, DIV_TAG_DATE_FORMAT_SUFFIX );
		$vars = array ();
		
		$temp = '{' . uniqid () . '}';
		
		foreach ( $ranges as $range ) {
			$s = substr ( $this->__src, $ranges [0] [0] + $lprefix, $ranges [0] [1] - $ranges [0] [0] - $lprefix );
			$s = str_replace ( '\\' . DIV_TAG_DATE_FORMAT_SEPARATOR, $temp, $s );
			
			$p = strpos ( $s, DIV_TAG_DATE_FORMAT_SEPARATOR );
			if ($p !== false) {
				$var = substr ( $s, 0, $p );
				if (! isset ( $items [$var] ))
					$items [$var] = $var;
				$vars [] = $var;
			}
		}
		
		foreach ( $vars as $var ) {
			$value = $items [$var];
			if (is_scalar ( $value ))
				$this->dateFormat ( $var, $value );
		}
	}
	
	/**
	 * Giving formats to the dates
	 *
	 * @param string $key        	
	 * @param integer $value        	
	 * @return boolean
	 */
	final public function dateFormat($key, $value) {
		$tag_begin = DIV_TAG_DATE_FORMAT_PREFIX . $key . DIV_TAG_DATE_FORMAT_SEPARATOR;
		$tag_end = DIV_TAG_DATE_FORMAT_SUFFIX;
		$l1 = strlen ( $tag_begin );
		$l2 = strlen ( $tag_end );
		
		if (strpos ( $this->__src, $tag_begin ) === false)
			return false;
		if (strpos ( $this->__src, $tag_end ) === false)
			return false;
		
		while ( true ) {
			$ranges = $this->getRanges ( $tag_begin, $tag_end, null, true );
			if (count ( $ranges ) < 1)
				break;
			
			$ini = $ranges [0] [0];
			$fin = $ranges [0] [1];
			$format = substr ( $this->__src, $ini + $l1, $fin - ($ini + $l1) );
			
			if (trim ( $format ) == "")
				$format = "Y-m-d";
			if (! is_numeric ( $value ))
				$value = @strtotime ( "$value" );
			$this->__src = substr ( $this->__src, 0, $ini ) . date ( $format, $value ) . substr ( $this->__src, $fin + $l2 );
		}
		
		return true;
	}
	
	/**
	 * Parsing capsules
	 *
	 * @param array $items        	
	 */
	final public function parseCapsules(&$items = array()) {
		if (self::$__log_mode)
			$this->logger ( "Parsing capsules..." );
		
		$classname = get_class ( $this );
		
		$pos = 0;
		while ( true ) {
			$ranges = $this->getBlockRanges ( null, DIV_TAG_CAPSULE_BEGIN_PREFIX, DIV_TAG_CAPSULE_BEGIN_SUFFIX, DIV_TAG_CAPSULE_END_PREFIX, DIV_TAG_CAPSULE_END_SUFFIX, $pos, null, true );
			
			if (count ( $ranges ) < 1)
				break;
			
			$key = $ranges [0] [2];
			if (! isset ( $items [$key] )) {
				$pos = $ranges [0] [0] + 1;
				continue;
			}
			$value = $items [$key];
			
			$ini = $ranges [0] [0];
			$fin = $ranges [0] [1];
			$subsrc = $ranges [0] [3];
			
			if (is_object ( $value )) {
				if (method_exists ( $value, '__toString' )) {
					$itemstr = "$value";
					if (! isset ( $value->value ))
						$value->value = $itemstr;
					$value->_to_string = $itemstr;
				}
				$value = get_object_vars ( $value );
			}
			
			if (is_scalar ( $value ))
				$value = array (
						"value" => $value 
				);
			
			$value = array_merge ( $items, $value );
			
			$tempglobal = self::$__globals_design; // priority to item's properties
			                                       // Save similar global design vars
			
			if (is_array ( $value ))
				foreach ( $value as $kkk => $vvv )
					if (isset ( self::$__globals_design [$kkk] )) {
						unset ( self::$__globals_design [$kkk] );
					}
			
			$engine = self::getAuxiliaryEngineClone ( $items );
			$engine->__src = $subsrc;
			$engine->__items = $value;
			
			if (is_array ( $this->__items_orig )) {
				if (isset ( $this->__items_orig [$key] ))
					$engine->__items_orig = $this->__items_orig [$key];
			} elseif (is_object ( $this->__items_orig )) {
				if (isset ( $this->__items_orig->$key ))
					$engine->__items_orig = $this->__items_orig->$key;
			}
			
			$engine->parse ( false );
			$hh = $engine->__src;
			
			// Restore global design vars
			self::$__globals_design = $tempglobal;
			
			$this->__src = substr ( $this->__src, 0, $ini ) . $hh . substr ( $this->__src, $fin + strlen ( DIV_TAG_CAPSULE_END_PREFIX . $key . DIV_TAG_CAPSULE_END_SUFFIX ) );
			
			$pos = $ini;
		}
	}
	
	/**
	 * Parsing a code if method call and invoke the method
	 *
	 * @param string $code        	
	 * @param array $items        	
	 * @return mixed
	 */
	final public function getMethodResult($code, &$items = null) {
		if (is_null ( $items ))
			$items = &$this->__items;
			
			// Creating auxiliary engine
		$engine = self::getAuxiliaryEngineClone ( $items );
		
		// Detect method name
		$p = strpos ( $code, "(" );
		$method = substr ( $code, 0, $p );
		$engine->__src = $method;
		$engine->parse ( false );
		$method = trim ( $engine->__src );
		
		$objs = array ();
		
		if (strpos ( $method, DIV_TAG_VAR_MEMBER_DELIMITER )) {
			$temp = explode ( DIV_TAG_VAR_MEMBER_DELIMITER, $method );
			$method = $temp [count ( $temp ) - 1];
			unset ( $temp [count ( $temp ) - 1] );
			$path = implode ( DIV_TAG_VAR_MEMBER_DELIMITER, $temp );
			$it = $this->getItem ( $path );
			
			if (is_object ( $it ))
				$objs = array (
						$it 
				);
		} elseif (is_object ( $this->__items_orig )) {
			$objs = array (
					$this->__items_orig,
					$this 
			);
		} else {
			$objs = array (
					$this 
			);
		}
		
		foreach ( $objs as $obj ) {
			
			if (! is_object ( $obj ))
				continue;
			
			$classname = get_class ( $obj );
			
			$methods = get_class_methods ( $classname );
			$ms = array ();
			foreach ( $methods as $m )
				if (array_search ( $m, self::$__parent_method_names ) === false)
					$ms [] = $m;
			
			if (array_search ( $method, $ms ) !== false) {
				
				$params = substr ( $code, $p + 1 );
				$params = substr ( $params, 0, strlen ( $params ) - 1 );
				
				if (self::isValidMacro ( $params )) {
					
					$engine->__src = $params;
					
					$engine->parse ( false );
					
					$params = trim ( $engine->__src );
					
					if (substr ( $params, 0, 1 ) != "{" && substr ( $params, 0, 1 ) != "[") {
						$r = null;
						
						// Save the error reporting configurarion
						$error_reporting = ini_get ( "error_reporting" );
						ini_set ( "error_reporting", ~ E_ALL );
						
						eval ( '$r = $obj->' . $method . '(' . $params . ');' );
						
						// Restore the error reporting configurarion
						ini_set ( "error_reporting", $error_reporting );
						
						return $r;
					} else {
						$params = self::jsonDecode ( $params, $this->getAllItems ( $items ) );
						return $obj->$method ( $params );
					}
				} else
					self::error ( "Wrong params or obtrusive code in method call: $method", DIV_ERROR_FATAL );
			}
		}
		return DIV_METHOD_NOT_EXISTS;
	}
	
	/**
	 * Return a list of vars that are active in template
	 *
	 * @param mixed $items        	
	 * @return array
	 */
	final public function getActiveVars($items, $superkey = '', $src = null) {
		if (is_null ( $src ))
			$src = &$this->__src;
		
		if ($superkey != '' && strpos ( $src, $superkey ) === false)
			return array ();
		
		if (is_object ( $items ))
			$itemsx = get_object_vars ( $items );
		elseif (is_array ( $items ))
			$itemsx = $items;
		else
			return array ();
		
		$vars = array ();
		
		foreach ( $itemsx as $key => $value ) {
			if ($superkey . $key !== '')
				if (strpos ( $src, $superkey . $key ) !== false) {
					$vars [] = $superkey . $key;
					if (! is_scalar ( $itemsx [$key] ))
						$vars = array_merge ( $vars, $this->getActiveVars ( $itemsx [$key], $superkey . $key . DIV_TAG_VAR_MEMBER_DELIMITER, $src ) );
				}
		}
		
		return $vars;
	}
	
	/**
	 * Remember the inactive items
	 *
	 * @param array $memory        	
	 * @param array $items        	
	 */
	final private function memory(&$items) {
		$vars = $this->getActiveVars ( $items );
		
		foreach ( $vars as $var )
			if (! isset ( $items [$var] ) || strpos ( $var, DIV_TAG_VAR_MEMBER_DELIMITER ) !== false)
				$items [$var] = self::getVarValue ( $var, $items );
		
		$this->__memory = array_merge ( $this->__memory, $items );
		
		$items = array ();
		
		$vars = $this->getActiveVars ( $this->__memory );
		
		foreach ( $vars as $var )
			$items [$var] = self::getVarValue ( $var, $this->__memory );
	}
	
	/**
	 * Search the locations in the template
	 *
	 * @return array
	 */
	final public function getLocations() {
		$r = $this->getRanges ( DIV_TAG_LOCATION_BEGIN, DIV_TAG_LOCATION_END );
		
		$lprefix = strlen ( DIV_TAG_LOCATION_BEGIN );
		$lsuffix = strlen ( DIV_TAG_LOCATION_END );
		
		$locations = array ();
		$tags = array ();
		foreach ( $r as $item ) {
			$tagname = substr ( $this->__src, $item [0] + $lprefix, $item [1] - $item [0] - $lprefix );
			
			if (! isset ( $locations [$tagname] ))
				$locations [$tagname] = array ();
			$locations [$tagname] [] = $item [0];
			$tags [$tagname] = strlen ( $tagname );
		}
		
		arsort ( $tags );
		$ntags = array ();
		
		foreach ( $tags as $tagname => $v )
			$ntags [$tagname] = $locations [$tagname];
		
		return $ntags;
	}
	
	/**
	 * Parse the locations in the template
	 */
	final public function parseLocations() {
		$locations = $this->getLocations ();
		$newcontent = array ();
		
		foreach ( $locations as $location => $posis ) {
			$content = "";
			$pos = 0;
			while ( true ) {
				$tag_begin = DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX . $location . DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX;
				$tag_end = DIV_TAG_LOCATION_CONTENT_END_PREFIX . $location . DIV_TAG_LOCATION_CONTENT_END_SUFFIX;
				$l1 = strlen ( $tag_begin );
				$l2 = strlen ( $tag_end );
				
				$r = $this->getRanges ( $tag_begin, $tag_end, null, true, $pos );
				
				if (count ( $r ) == 0)
					break;
				
				$ini = $r [0] [0];
				$end = $r [0] [1];
				
				// Get the content
				$newcontent = substr ( $this->__src, $ini + $l1, $end - $ini - $l1 );
				if ($newcontent != "") {
					if ($newcontent [0] == ' ')
						$newcontent = substr ( $newcontent, 1 );
					if (substr ( $newcontent, - 1 ) == ' ')
						$newcontent = substr_replace ( $newcontent, "", - 1 );
				}
				
				$content .= $newcontent;
				
				// Remove declaration
				$this->__src = substr ( $this->__src, 0, $ini ) . substr ( $this->__src, $end + $l2 );
				
				// Update the locations (to left)
				foreach ( $locations as $k => $v ) {
					foreach ( $v as $kk => $p )
						if ($p > $end)
							$locations [$k] [$kk] -= $l1 + $l2 + ($end - $ini - $l1);
				}
			}
			
			// Inject the content in the locations
			$tag = DIV_TAG_LOCATION_BEGIN . $location . DIV_TAG_LOCATION_END;
			$this->__src = str_replace ( $tag, $content . $tag, $this->__src );
		}
	}
	
	/**
	 * Clear location's tags
	 */
	final private function clearLocations() {
		$locations = $this->getLocations ();
		foreach ( $locations as $location => $posis ) {
			$this->__src = str_replace ( DIV_TAG_LOCATION_BEGIN . $location . DIV_TAG_LOCATION_END, '', $this->__src );
		}
	}
	
	/**
	 * Parse the sub-parsers
	 */
	final public function parseSubParsers(&$items = null, $flags = array()) {
		if (is_null ( $items ))
			$items = &$this->__items;
		
		$itemsx = array_merge ( $this->__memory, $items );
		if (! isset ( $flags ['level'] ))
			$flags ['level'] = self::$__parse_level;
		
		foreach ( self::$__sub_parsers as $parser => $function ) {
			
			// Checking the moment/event
			if (isset ( $flags ['moment'] )) {
				$arr = explode ( ":", $parser );
				if (isset ( $arr [1] )) {
					$last = array_pop ( $arr );
					if ($last == 'beforeParse' && $flags ['moment'] != DIV_MOMENT_BEFORE_PARSE)
						continue;
					if ($last == 'afterInclude' && $flags ['moment'] != DIV_MOMENT_AFTER_INCLUDE)
						continue;
					if ($last == 'afterParse' && $flags ['moment'] != DIV_MOMENT_AFTER_PARSE)
						continue;
					if ($last == 'afterReplace' && $flags ['moment'] != DIV_MOMENT_AFTER_REPLACE)
						continue;
				}
			}
			
			$pini = DIV_TAG_SUBPARSER_BEGIN_PREFIX . $parser . DIV_TAG_SUBPARSER_BEGIN_SUFFIX;
			$pfin = DIV_TAG_SUBPARSER_END_PREFIX . $parser . DIV_TAG_SUBPARSER_END_SUFFIX;
			$lpini = strlen ( $pini );
			
			$code = $function;
			if (method_exists ( $this, $function ))
				$code = '$this->' . $function;
			
			if (self::$__log_mode)
				$this->logger ( "Parsing the subparser $parser ..." );
			
			$ignore = false;
			$p = 0;
			while ( true ) {
				$ranges = $this->getRanges ( $pini, $pfin, null, true, $p );
				
				if (count ( $ranges ) < 1)
					break;
				
				$ini = $ranges [0] [0];
				$fin = $ranges [0] [1];
				
				if ($this->searchInListRanges ( $ini )) {
					$p = $ini + 1;
					$ignore = true;
					continue;
				}
				
				if (DIV_TAG_SUBPARSER_BEGIN_SUFFIX == '' && strpos ( "\n\t <>", substr ( $this->__src, $ini + $pini, 1 ) ) === false) {
					$p = $ini + 1;
					continue;
				}
				if (DIV_TAG_SUBPARSER_END_PREFIX == '' && strpos ( "\n\t <>", substr ( $this->__src, $fin - 1, 1 ) ) === false) {
					$p = $ini + 1;
					continue;
				}
				
				$subsrc = substr ( $this->__src, $ini + $lpini, $fin - $ini - $lpini );
				$r = "";
				eval ( '$r = ' . $code . '($subsrc, $itemsx, $flags);' );
				if ($r !== false) {
					$this->__src = substr ( $this->__src, 0, $ini ) . $r . substr ( $this->__src, $fin + $lpini + 1 );
					$p = $ini;
				} else {
					$p = $ini + 1;
					$ignore = true;
				}
			}
			
			if (strpos ( $this->__src, $pini ) !== false && ! $ignore) {
				$r = "";
				eval ( '$r = ' . $code . '(false, $itemsx, $flags);' );
				if ($r !== false)
					$this->__src = str_replace ( $pini, $r, $this->__src, $rcount );
			}
		}
		
		$this->memory ( $itemsx );
	}
	
	/**
	 * Checking logical order
	 *
	 * @param integer $ini        	
	 * @param string $var        	
	 * @param bool $chkloops        	
	 * @param bool $chkmatchs        	
	 * @param bool $chkformats        	
	 * @return mixed
	 */
	final public function checkLogicalOrder($ini = 0, $var = "", $chkloops = false, $chkmatchs = false, $chkformats = false, $chkdata = false) {
		if (self::$__log_mode)
			$this->logger ( "Checking logical order at $ini..." );
		
		if ($chkdata) {
			$rang = $this->getRanges ( DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END, null, true );
			if (count ( $rang ) > 0)
				if ($rang [0] [0] < $ini) {
					
					return $ini + 1;
				}
		}
		
		if ($chkloops) {
			if ($var != "")
				$prev_use = strpos ( $this->__src, DIV_TAG_LOOP_BEGIN_PREFIX . $var . DIV_TAG_LOOP_BEGIN_SUFFIX );
			else
				$prev_use = $this->searchPreviousLoops ( $ini );
			
			if ($prev_use !== false && $prev_use < $ini) {
				
				return $ini + 1;
			}
		}
		
		if ($chkmatchs)
			foreach ( self::$__modifiers as $m ) {
				$prev_use = strpos ( $this->__src, DIV_TAG_REPLACEMENT_PREFIX . $m . $var );
				if ($prev_use !== false && $prev_use < $ini) {
					return $ini + 1;
				}
			}
		
		if ($chkformats) {
			$prev_use = strpos ( $this->__src, DIV_TAG_NUMBER_FORMAT_PREFIX . $var );
			if ($prev_use !== false && $prev_use < $ini)
				return $ini + 1;
			$prev_use = strpos ( $this->__src, DIV_TAG_DATE_FORMAT_PREFIX . $var );
			if ($prev_use !== false && $prev_use < $ini)
				return $ini + 1;
		}
		
		return false;
	}
	
	/**
	 * Parsing the macros
	 *
	 * @param mixed $items        	
	 */
	final public function parseMacros(&$items = null, $ignore_previous_match = false) {
		if (self::$__log_mode)
			$this->logger ( "Parsing macros..." );
		
		if (is_null ( $items ))
			$items = &$this->__items;
			
			// Free the macro's scope and protect the scope of this method
		$this->__temp = array ();
		
		$this->__temp ['p'] = 0;
		
		$l1 = strlen ( DIV_TAG_MACRO_BEGIN );
		$l2 = strlen ( DIV_TAG_MACRO_END );
		
		while ( true ) {
			
			$classname = get_class ( $this );
			
			$this->__temp ['ranges'] = $this->getRanges ( DIV_TAG_MACRO_BEGIN, DIV_TAG_MACRO_END, null, true, $this->__temp ['p'] );
			
			if (count ( $this->__temp ['ranges'] ) < 1)
				break;
			
			$this->__temp ['ini'] = $this->__temp ['ranges'] [0] [0];
			$this->__temp ['fin'] = $this->__temp ['ranges'] [0] [1];
			
			if (! $ignore_previous_match) {
				$this->__temp ['r'] = $this->checkLogicalOrder ( $this->__temp ['ini'], "", true, ! $ignore_previous_match, true, false );
				
				if ($this->searchInListRanges ( $this->__temp ['ini'] )) {
					$this->__temp ['p'] = $this->__temp ['ini'] + 1;
					continue;
				}
				
				if ($this->__temp ['r'] !== false) {
					$this->__temp ['p'] = $this->__temp ['r'];
					continue;
				}
			} else {
				$this->__temp ['r'] = false;
			}
			$this->__temp ['code'] = trim ( substr ( $this->__src, $this->__temp ['ini'] + $l1, $this->__temp ['fin'] - $this->__temp ['ini'] - $l1 ) );
			$this->__temp ['temp'] = uniqid ();
			
			$this->__src = substr ( $this->__src, 0, $this->__temp ['ini'] ) . '{' . $this->__temp ['temp'] . '}' . substr ( $this->__src, $this->__temp ['fin'] + $l2 );
			
			if (substr ( $this->__temp ['code'], 0, 3 ) == "php")
				$__code = substr ( $this->__temp ['code'], 3 );
			
			ob_start ();
			
			$this->__temp ['invalid_macro'] = false;
			
			if (self::isValidMacro ( $this->__temp ['code'] )) {
				
				// Preparing methods
				$this->__temp ['validmethods'] = implode ( ',', self::$__allowed_methods );
				$this->__temp ['validmethods'] = str_replace ( ',', '(,' . $classname . '::', $classname . '::' . $this->__temp ['validmethods'] ) . '(';
				$this->__temp ['methods'] = explode ( ',', str_replace ( $classname . '::', '', $this->__temp ['validmethods'] ) );
				$this->__temp ['methodsx'] = explode ( ',', $this->__temp ['validmethods'] );
				$this->__temp ['code'] = str_replace ( $this->__temp ['methods'], $this->__temp ['methodsx'], $this->__temp ['code'] );
				
				// Preparing variables
				foreach ( $items as $key => $value ) {
					if (strpos ( $key, DIV_TAG_VAR_MEMBER_DELIMITER ) !== false) {
						self::setVarValue ( $key, $value, $items );
						unset ( $items [$key] );
					}
				}
				
				$this->__temp ['codevars'] = '';
				
				foreach ( $items as $key => $value ) {
					if (self::isValidVarName ( $key ))
						$this->__temp ['codevars'] .= '$' . $key . ' = $items["' . $key . '"];';
				}
				
				$this->__temp ['items'] = $items;
				
				unset ( $key );
				unset ( $value );
				unset ( $classname );
				
				if ($this->__temp ['codevars'] != '')
					eval ( $this->__temp ['codevars'] );
				
				unset ( $items );
				
				// Executing the macro
				
				eval ( $this->__temp ['code'] );
				
				// Div 4.5: chanve $vars with temporal var, ...important!
				// becasuse get_defined_vars return also 'vars'
				
				$this->__temp ['vars'] = get_defined_vars ();
				
				$items = $this->__temp ['items'];
				
				foreach ( $this->__temp ['vars'] as $var => $value ) {
					if ($var == 'this')
						continue; // Very very important!!
					
					if (! isset ( $items [$var] ) || isset ( self::$__globals_design [$var] ))
						self::$__globals_design [$var] = $value;
					
					$items [$var] = $value;
				}
			} else {
				$this->__temp ['invalid_macro'] = true;
			}
			
			$this->__src = str_replace ( '{' . $this->__temp ['temp'] . '}', ob_get_contents (), $this->__src );
			
			ob_end_clean ();
			
			if ($this->__temp ['invalid_macro']) {
				$this->__temp ['msgs'] = self::getInternalMsg ( 'php_validations' );
				$this->__temp ['details'] = "<ul>\n";
				foreach ( $this->__temp ['msgs'] as $msg ) {
					$this->__temp ['details'] .= '<li>' . $msg ['msg'] . "</li>\n";
				}
				$this->__temp ['details'] .= '</ul>';
				
				self::error ( "Invalid macro: \n\n <br/> <pre width=\"80\">" . substr ( $this->__temp ['code'], 0, 300 ) . '(...)</pre><br/>' . $this->__temp ['details'] );
			}
			
			$this->__temp ['p'] = $this->__temp ['ini'] + 1;
		}
		
		// Free all temporal vars
		$this->__temp = array ();
		
		return $items;
	}
	
	/**
	 * Extract sections or source that will be restored in the future
	 *
	 * @param string $begin_prefix        	
	 * @param string $begin_suffix        	
	 * @param string $end_prefix        	
	 * @param string $end_suffix        	
	 * @param string $src        	
	 * @return array
	 */
	final public function saveSections($begin_prefix, $begin_suffix, $end_prefix, $end_suffix, $src = null) {
		if (is_null ( $src ))
			$src = $this->__src;
		
		$pos = 0;
		
		$saved_sections = array ();
		
		while ( true ) {
			$r = $this->getBlockRanges ( $src, $begin_prefix, $begin_suffix, $end_prefix, $end_suffix, $pos, null, true );
			
			if (count ( $r ) < 1)
				break;
			
			$ini = $r [0] [0] + strlen ( $begin_prefix ) + strlen ( $r [0] [2] ) + strlen ( $begin_suffix );
			$length = $r [0] [1] - $ini;
			
			$uid = '{' . uniqid () . '}';
			$section = substr ( $src, $ini, $length );
			$saved_sections [$uid] = $section;
			
			$src = substr ( $src, 0, $ini ) . $uid . substr ( $src, $ini + $length );
			
			$pos = $ini + 1;
		}
		
		return array (
				'src' => $src,
				'sections' => $saved_sections 
		);
	}
	
	/**
	 * Restoring saved sections
	 *
	 * @param string $src        	
	 * @param array $sections        	
	 * @return string
	 */
	final public function restoreSavedSections($src, $sections) {
		foreach ( $sections as $uid => $section ) {
			$src = str_replace ( $uid, $section, $src );
		}
		return $src;
	}
	
	/**
	 * Making that remembered
	 *
	 * @param integer $checksum        	
	 */
	final private function makeItAgain($checksum, &$items) {
		if (self::$__log_mode === true)
			$this->logger ( "Making again some remembered tasks..." );
		
		$simple = DIV_TAG_REPLACEMENT_PREFIX . DIV_TAG_MODIFIER_SIMPLE;
		
		// Save some sections (to ignore)
		
		$saved_sections = array ();
		
		// ... saving loops
		$r = $this->saveSections ( DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_BEGIN_SUFFIX, DIV_TAG_LOOP_END_PREFIX, DIV_TAG_LOOP_END_SUFFIX, $this->__src );
		$this->__src = $r ['src'];
		$saved_sections = $r ['sections'];
		
		// ... saving capsules
		$r = $this->saveSections ( DIV_TAG_CAPSULE_BEGIN_PREFIX, DIV_TAG_CAPSULE_BEGIN_SUFFIX, DIV_TAG_CAPSULE_END_PREFIX, DIV_TAG_CAPSULE_END_SUFFIX, $this->__src );
		$this->__src = $r ['src'];
		$saved_sections = array_merge ( $saved_sections, $r ['sections'] );
		
		foreach ( self::$__remember [$checksum] as $params ) {
			
			$literal = $this->isLiteral ( $params ['key'] );
			
			$vpx = '';
			$vsx = '';
			
			if ($literal === true) {
				$vpx = '{' . $this->__ignore_secret_tag . '}';
				$vsx = '{/' . $this->__ignore_secret_tag . '}';
			}
			
			switch ($params ['o']) {
				case 'replace_submatch_teaser' :
					$value = self::getVarValue ( $params ['key'], $items );
					$value = self::anyToStr ( $value );
					if (is_null ( $value ))
						continue;
					$value = self::teaser ( "{$value}", intval ( $params ['param'] ) );
					
					$search = DIV_TAG_REPLACEMENT_PREFIX . $params ['modifier'] . $params ['key'] . DIV_TAG_SUBMATCH_SEPARATOR . $params ['param'] . DIV_TAG_REPLACEMENT_SUFFIX;
					$this->__src = str_replace ( $search, $vpx . $value . $vsx, $this->__src );
					break;
				
				case 'replace_submatch_substr' :
					$value = self::getVarValue ( $params ['key'], $items );
					if (is_null ( $value ))
						continue;
					$value = self::anyToStr ( $value );
					$this->__src = str_replace ( $simple . $params ['key'] . DIV_TAG_SUBMATCH_SEPARATOR . $params ['param'] . DIV_TAG_REPLACEMENT_SUFFIX, $vpx . substr ( $value, $params ['from'], $params ['for'] ) . $vsx, $this->__src );
					break;
				
				case 'replace_submatch_wordwrap' :
					$value = self::getVarValue ( $params ['key'], $items );
					if (is_null ( $value ))
						continue;
					$value = self::anyToStr ( $value );
					$this->__src = str_replace ( $simple . $params ['key'] . DIV_TAG_SUBMATCH_SEPARATOR . $params ['param'] . DIV_TAG_REPLACEMENT_SUFFIX, $vpx . wordwrap ( "{$value}", intval ( substr ( $params ['param'], strlen ( DIV_TAG_MODIFIER_WORDWRAP ) ) ), "\n", 1 ) . $vsx, $this->__src );
					break;
				
				case 'replace_submatch_sprintf' :
					$value = self::getVarValue ( $params ['key'], $items );
					if (is_null ( $value ))
						continue;
					$value = self::anyToStr ( $value );
					$this->__src = str_replace ( $simple . $params ['key'] . DIV_TAG_SUBMATCH_SEPARATOR . $params ['param'] . DIV_TAG_REPLACEMENT_SUFFIX, $vpx . sprintf ( $params ['param'], $value ) . $vsx, $this->__src );
					break;
				
				case 'json_encode' :
					$value = self::getVarValue ( $params ['key'], $items );
					if (is_null ( $value ))
						continue;
					$this->__src = str_replace ( DIV_TAG_REPLACEMENT_PREFIX . DIV_TAG_MODIFIER_ENCODE_JSON . $params ['key'] . DIV_TAG_REPLACEMENT_SUFFIX, $vpx . self::jsonEncode ( $value ) . $vsx, $this->__src );
					break;
				
				case 'simple_replacement' :
					$value = self::getVarValue ( $params ['key'], $items );
					
					if (is_null ( $value ))
						continue;
					
					$value = self::anyToStr ( $value );
					
					switch ($params ['modifier']) {
						case DIV_TAG_MODIFIER_CAPITALIZE_FIRST :
							$value = ucfirst ( $value );
							break;
						case DIV_TAG_MODIFIER_CAPITALIZE_WORDS :
							$value = ucwords ( $value );
							break;
						case DIV_TAG_MODIFIER_UPPERCASE :
							$value = strtoupper ( $value );
							break;
						case DIV_TAG_MODIFIER_LENGTH :
							$value = strlen ( $value );
							break;
						case DIV_TAG_MODIFIER_COUNT_WORDS :
							$value = self::getCountOfWords ( $value );
							break;
						case DIV_TAG_MODIFIER_COUNT_SENTENCES :
							$value = self::getCountOfSentences ( $value );
							break;
						case DIV_TAG_MODIFIER_COUNT_PARAGRAPHS :
							$value = self::getCountOfParagraphs ( $value );
							break;
						case DIV_TAG_MODIFIER_ENCODE_URL :
							$value = urlencode ( $value );
							break;
						case DIV_TAG_MODIFIER_ENCODE_RAW_URL :
							$value = rawurlencode ( $value );
							break;
					}
					
					if ($params ['before'] === false) {
						$this->__src = str_replace ( DIV_TAG_REPLACEMENT_PREFIX . $params ['modifier'] . $params ['key'] . DIV_TAG_REPLACEMENT_SUFFIX, $vpx . $value . $vsx, $this->__src );
					} else {
						$substr = substr ( $this->__src, 0, $params ['before'] );
						$substr = str_replace ( DIV_TAG_REPLACEMENT_PREFIX . $params ['modifier'] . $params ['key'] . DIV_TAG_REPLACEMENT_SUFFIX, $vpx . $value . $vsx, $substr );
						$this->__src = $substr . substr ( $this->__src, $params ['before'] );
					}
					break;
			}
		}
		
		// Restoring saved sections
		$this->__src = $this->restoreSavedSections ( $this->__src, $saved_sections );
	}
	
	/**
	 * Return the template's properties
	 *
	 * @param string $src        	
	 * @return string
	 */
	final public function getTemplateProperties(&$src = null) {
		$update = false;
		
		if (is_null ( $src )) {
			$src = &$this->__src;
			$update = true;
		}
		
		$properties = array ();
		
		if (strpos ( $src, '@_' ) !== false) {
			$src = str_replace ( "\n\r", "\n", $src );
			$lines = explode ( "\n", $src );
			$nsrc = '';
			$engine = self::getAuxiliaryEngineClone ( $this->__memory );
			foreach ( $lines as $line ) {
				$line = trim ( $line );
				if (substr ( $line, 0, 2 ) == '@_') {
					$s = substr ( $line, 2 );
					$s = trim ( $s );
					if ($s !== "") {
						$arr = explode ( '=', $s );
						if (count ( $arr ) > 1) {
							$var = strtoupper ( trim ( $arr [0] ) );
							if (! isset ( $properties [$var] )) {
								array_shift ( $arr );
								$value = implode ( '=', $arr );
								$engine->__src = $value;
								$engine->parse ( false );
								$value = $engine->__src;
								$vvalue = self::jsonDecode ( $value, $this->getAllItems () );
								if (! is_null ( $vvalue ))
									$value = $vvalue;
								else
									$value = trim ( $value );
								$properties [$var] = $value;
								$line = '';
							}
						}
					}
				}
				$nsrc .= $line . ($line == '' ? '' : "\n");
			}
			$src = $nsrc;
		}
		
		if ($update)
			$this->__src = $src;
		
		if (self::$__docs_on) {
			$section = trim ( $this->__path );
			if ($section !== '') {
				if (substr ( $section, 0, 2 ) == './')
					$section = substr ( $this->__path, 2 );
				if ($section !== '')
					self::$__docs [$section] ['properties'] = $properties;
			}
		}
		
		return $properties;
	}
	
	/**
	 * Load properties from template code
	 */
	final private function loadTemplateProperties() {
		$this->__properties = $this->getTemplateProperties ();
	}
	
	/**
	 * Preparing template's dialect
	 *
	 * @param string $src        	
	 * @param array $properties        	
	 */
	final public function prepareDialect($src = null, $properties = null) {
		if (is_null ( $src ))
			$src = $this->__src;
		if (is_null ( $properties ))
			$properties = $this->__properties;
		
		if (isset ( $properties ['DIALECT'] )) {
			$f = trim ( $properties ['DIALECT'] );
			
			if (self::$__log_mode === true)
				$this->logger ( "Preparing the dialect..." );
			
			$json = DIV_DEFAULT_DIALECT;
			
			if (self::fileExists ( $f ) && $f !== '') {
				$json = self::getFileContents ( $f );
			}
			
			if (! is_null ( $json )) {
				$src = $this->translateFrom ( $json, $src );
			} else if (self::$__log_mode)
				$this->log ( 'The dialect ' . $f . ' is corrupt or invalid' );
		}
		return $src;
	}
	
	/**
	 * Parse the template
	 *
	 * @param boolean $from_original        	
	 * @param mixed $index_item        	
	 * @param integer $min_level        	
	 * @return string
	 */
	final public function parse($from_original = true, $index_item = null, $min_level = 1) {
		
		// Generate internal and random ignore tag (security reasons)
		if (is_null ( $this->__ignore_secret_tag ))
			$this->__ignore_secret_tag = uniqid ();
		
		self::createAuxiliarEngine ( $this );
		self::$__parse_level ++;
		
		if (self::$__log_mode)
			$this->logger ( "Parsing all..." );
		
		$time_start = microtime ( true );
		
		self::repairSubparsers ();
		
		// Calling the beforeParse hook
		$this->beforeParse ();
		
		if ($from_original == true) {
			if (self::$__log_mode)
				$this->logger ( "Parsing from the original source" );
			if (is_null ( $this->__src_original )) {
				$this->__src_original = $this->__src;
			} else
				$this->__src = $this->__src_original;
		}
		
		$subparsers_restore = array ();
		
		if (trim ( $this->__src ) != "") {
			
			if (! is_null ( $index_item )) {
				if (self::$__log_mode)
					$this->logger ( "Parsing with '$index_item' index of __items" );
				$items = $this->__items [$index_item];
			} else
				$items = $this->__items;
			
			if (is_null ( $items ))
				$items = array ();
				
				// Add global vars (self::$globals)
			foreach ( self::$__globals as $var => $value )
				if (! isset ( $items [$var] ))
					$items [$var] = $value;
			
			$items = array_merge ( $items, self::$__globals_design );
			$items = array_merge ( $items, self::getSystemData () );
			
			// Add properties
			$props = get_object_vars ( $this );
			foreach ( $props as $prop => $value )
				if (substr ( $prop, 0, 2 ) != "__")
					$items [$prop] = $value;
				
				// Reserved vars
			if (! isset ( $items ['_empty'] ))
				$items ['_empty'] = array ();
			if (! isset ( $items ['_'] ))
				$items ['_'] = array ();
			
			if (strpos ( $this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX ) !== false)
				$this->parseSubParsers ( $items, array (
						'moment' => DIV_MOMENT_BEFORE_PARSE 
				) );
				
				// Template's properties
			$this->loadTemplateProperties ();
			
			// Preparing dialect
			$this->__src = $this->prepareDialect ();
			
			if (strpos ( $this->__src, DIV_TAG_IGNORE_BEGIN ) !== false || strpos ( $this->__src, '{' . $this->__ignore_secret_tag . '}' ) !== false)
				$this->parseIgnore ();
			
			if (strpos ( $this->__src, DIV_TAG_COMMENT_BEGIN ) !== false)
				$this->parseComments ();
			
			if (strpos ( $this->__src, DIV_TAG_FRIENDLY_BEGIN ) !== false)
				$this->parseFriendly ();
			
			$cycles2 = 0;
			
			$this->memory ( $items );
			
			$msg_infinite_cycle = 'Too many iterations of the parser: possible infinite cycle. Review your template code.';
			
			$last_action = false;
			
			do {
				
				$cycles1 = 0;
				$cycles2 ++;
				
				if ($cycles2 > DIV_MAX_PARSE_CYCLES)
					$this->error ( $msg_infinite_cycle, "FATAL" );
				
				do {
					
					$checksum = crc32 ( $this->__src );
					$this->__crc = $checksum;
					
					if (self::$__log_mode === true) {
						$this->logger ( 'Template | size: ' . strlen ( $this->__src ) );
						if (isset ( $this->__src [100] ))
							$this->logger ( 'Template [checksum=' . $checksum . ']:' . htmlentities ( str_replace ( "\n", " ", substr ( $this->__src, 0, 100 ) ) . "..." . substr ( $this->__src, strlen ( $this->__src ) - 100 ) ) );
						else
							$this->logger ( 'Template [checksum=' . $checksum . ']: ' . htmlentities ( $this->__src ) );
					}
					
					$cycles1 ++;
					
					if ($cycles1 > DIV_MAX_PARSE_CYCLES)
						$this->error ( $msg_infinite_cycle, "FATAL" );
					$this->memory ( $items );
					
					// Conditional
					if (strpos ( $this->__src, DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX ) !== false || strpos ( $this->__src, DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX ) !== false)
						$this->parseConditional ( $items );
						
						// Conditions
					if (strpos ( $this->__src, DIV_TAG_CONDITIONS_BEGIN_PREFIX ) !== false)
						$this->parseConditions ( $items );
						
						// Include
					if (strpos ( $this->__src, DIV_TAG_INCLUDE_BEGIN ) !== false) {
						$this->parseInclude ( $items );
						
						if (strpos ( $this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX ) !== false)
							$this->parseSubParsers ( $items, array (
									'moment' => DIV_MOMENT_AFTER_INCLUDE 
							) );
						
						if (strpos ( $this->__src, DIV_TAG_IGNORE_BEGIN ) !== false || strpos ( $this->__src, '{' . $this->__ignore_secret_tag . '}' ) !== false)
							$this->parseIgnore ();
						
						if (strpos ( $this->__src, DIV_TAG_COMMENT_BEGIN ) !== false)
							$this->parseComments ();
						if (strpos ( $this->__src, DIV_TAG_FRIENDLY_BEGIN ) !== false)
							$this->parseFriendly ();
						
						$this->memory ( $items );
					}
					
					// Multiple variable's modifiers
					if (strpos ( $this->__src, DIV_TAG_MULTI_MODIFIERS_PREFIX ) !== false && strpos ( $this->__src, DIV_TAG_MULTI_MODIFIERS_SUFFIX ) !== false)
						$this->parseMultipleModifiers ();
						
						// Data in templates
					if (strpos ( $this->__src, DIV_TAG_TPLVAR_BEGIN ) !== false)
						if (strpos ( $this->__src, DIV_TAG_TPLVAR_END ) !== false) {
							$items = array_merge ( $this->__memory, $items );
							$this->parseData ( $items );
							$this->memory ( $items );
						}
						
						// Number format
					if (strpos ( $this->__src, DIV_TAG_NUMBER_FORMAT_PREFIX ) !== false)
						$this->parseNumberFormat ( $items );
						
						// Preprocessed
					if (strpos ( $this->__src, DIV_TAG_PREPROCESSED_BEGIN ) !== false) {
						$this->parsePreprocessed ( $items );
						$this->memory ( $items );
					}
					
					$items = array_merge ( $items, self::$__globals_design );
					
					// Default values in templates
					if (strpos ( $this->__src, DIV_TAG_DEFAULT_REPLACEMENT_BEGIN ) !== false)
						$this->parseDefaults ( $items );
						
						// Macros
					if (strpos ( $this->__src, DIV_TAG_MACRO_BEGIN ) !== false) {
						$items = array_merge ( $this->__memory, $items );
						$items = $this->parseMacros ( $items, $last_action );
						$this->memory ( $items );
					}
					
					// Lists
					if (strpos ( $this->__src, DIV_TAG_LOOP_BEGIN_PREFIX ) !== false)
						if (strpos ( $this->__src, DIV_TAG_LOOP_END_PREFIX ) !== false)
							if (strpos ( $this->__src, DIV_TAG_LOOP_END_SUFFIX ) !== false) {
								$this->parseList ( $items );
							}
					
					$items = array_merge ( $items, self::$__globals_design );
					
					// Capsules
					if (strpos ( $this->__src, DIV_TAG_CAPSULE_BEGIN_PREFIX ) !== false)
						if (strpos ( $this->__src, DIV_TAG_CAPSULE_END_SUFFIX ) !== false)
							$this->parseCapsules ( $items );
					
					$items = array_merge ( $items, self::$__globals_design );
					
					// Make it again
					if (isset ( self::$__remember [$checksum] )) {
						
						$this->makeItAgain ( $checksum, $items );
						
						if (strpos ( $this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX ) !== false)
							$this->parseSubParsers ( $items, array (
									'moment' => DIV_MOMENT_AFTER_REPLACE 
							) );
					}
					
					// Sub-Matches
					if (self::atLeastOneString ( $this->__src, self::$__modifiers ))
						$this->parseSubmatches ( $items );
						
						// Matches
					if (self::atLeastOneString ( $this->__src, self::$__modifiers ) || (strpos ( $this->__src, DIV_TAG_NUMBER_FORMAT_PREFIX ) !== false && strpos ( $this->__src, DIV_TAG_NUMBER_FORMAT_SUFFIX ) !== false))
						$this->parseMatches ( $items, $last_action );
						
						// Discard literal vars
					if (strpos ( $this->__src, DIV_TAG_IGNORE_BEGIN ) !== false || strpos ( $this->__src, '{' . $this->__ignore_secret_tag . '}' ) !== false)
						$this->parseIgnore ();
						
						// Subparse: after replace
					if (strpos ( $this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX ) !== false)
						$this->parseSubParsers ( $items, array (
								'moment' => DIV_MOMENT_AFTER_REPLACE 
						) );
						
						// Iterations
					if (strpos ( $this->__src, DIV_TAG_ITERATION_BEGIN_PREFIX ) !== false)
						if (strpos ( $this->__src, DIV_TAG_ITERATION_END ) !== false)
							$this->parseIterations ( $items );
					
					$nowcrc = crc32 ( $this->__src );
					
					if ($checksum != $nowcrc)
						$last_action = false;
				} while ( $checksum != $nowcrc );
				
				// Computing
				if (strpos ( $this->__src, DIV_TAG_FORMULA_BEGIN ) !== false)
					if (strpos ( $this->__src, DIV_TAG_FORMULA_END ) !== false)
						$this->parseFormulas ( $items );
					
					// Date format
				if (strpos ( $this->__src, DIV_TAG_DATE_FORMAT_PREFIX ) !== false)
					if (strpos ( $this->__src, DIV_TAG_DATE_FORMAT_SUFFIX ) !== false)
						$this->parseDateFormat ( $items );
					
					// Multiple replacements
				if (strpos ( $this->__src, DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX ) !== false)
					if (strpos ( $this->__src, DIV_TAG_MULTI_REPLACEMENT_END_PREFIX ) !== false)
						if (strpos ( $this->__src, DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX ) !== false)
							$this->parseMultiReplace ( $items );
					
					// Searching orphan parts (conditions)
				if (strpos ( $this->__src, DIV_TAG_CONDITIONS_BEGIN_PREFIX ) !== false)
					$this->parseConditions ( $items, true );
					
					// Div 4.5: One more time? Parsing orphans's parts while checksum not change.
					// (do it because the orphan's parts stop the parser and the results are ugly)
					// TODO: research best solution for this! (this is the second solution found)
				
				if ($checksum == crc32 ( $this->__src ) && self::$__parse_level <= $min_level) {
					$this->parseOrphanParts ();
				}
				
				$nowcrc = crc32 ( $this->__src );
				
				// Last action?
				
				$last_action = ($last_action === false && $nowcrc == $checksum);
			} while ( $checksum != $nowcrc || $last_action === true );
			
			// Searching orphan parts (conditionals)
			if (strpos ( $this->__src, DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX ) !== false || strpos ( $this->__src, DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX ) !== false)
				$this->parseOrphanParts ();
			
			if (strpos ( $this->__src, DIV_TAG_IGNORE_BEGIN ) !== false || strpos ( $this->__src, '{' . $this->__ignore_secret_tag . '}' ) !== false)
				$this->parseIgnore ();
			if (strpos ( $this->__src, DIV_TAG_COMMENT_BEGIN ) !== false)
				$this->parseComments ();
			if (strpos ( $this->__src, DIV_TAG_FRIENDLY_BEGIN ) !== false)
				$this->parseFriendly ();
				
				// Locations
			if (strpos ( $this->__src, DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX ) !== false)
				if (strpos ( $this->__src, DIV_TAG_LOCATION_BEGIN ) !== false)
					$this->parseLocations ();
				
				// Clear location's tags
			$allitems = null;
			
			if (strpos ( $this->__src, DIV_TAG_LOCATION_BEGIN ) !== false) {
				$allitems = $this->getAllItems ( $items );
				$clear = self::getVarValue ( 'div' . DIV_TAG_VAR_MEMBER_DELIMITER . 'clear_locations', $allitems );
				
				if (is_null ( $clear ))
					$clear = true;
				
				if ($clear)
					$this->clearLocations ();
			}
			
			// Restoring parsers requests
			foreach ( $this->__restore as $restore_id => $rest )
				$this->__src = str_replace ( '{' . $restore_id . '}', $rest, $this->__src );
			
			$this->clean ();
			
			// The last action
			if (self::$__parse_level <= 1) {
				
				// Clear location's tags
				if (strpos ( $this->__src, DIV_TAG_LOCATION_BEGIN ) !== false)
					$this->clearLocations ();
				
				$this->parseSpecialChars ();
				
				// Restoring ignored parts
				foreach ( self::$__ignored_parts as $id => $ignore ) {
					
					foreach ( self::$__sub_parsers as $subparser => $function ) {
						$tempunique = uniqid ();
						
						$rcount = 0;
						
						$tagsearch = DIV_TAG_SUBPARSER_BEGIN_PREFIX . $subparser . DIV_TAG_SUBPARSER_BEGIN_SUFFIX;
						$tagreplace = DIV_TAG_SUBPARSER_BEGIN_PREFIX . $tempunique . DIV_TAG_SUBPARSER_BEGIN_SUFFIX;
						
						$ignore = str_replace ( $tagsearch, $tagreplace, $ignore, $rcount );
						
						if ($rcount > 0)
							$subparsers_restore [$tagsearch] = $tagreplace;
					}
					
					$this->__src = str_replace ( '{' . $id . '}', $ignore, $this->__src );
				}
				
				// Restoring ignored parts inside values
				
				$items = $this->__memory;
				$vars = $this->getVars ( $items );
				foreach ( $vars as $var ) {
					$exp = self::getVarValue ( $var, $items );
					if (is_string ( $exp )) {
						foreach ( self::$__ignored_parts as $id => $ignore )
							$exp = str_replace ( '{' . $id . '}', $ignore, $exp );
						self::setVarValue ( $var, $exp, $items );
					}
				}
				$this->__memory = $items;
			}
		}
		
		$this->txt ();
		
		if (strpos ( $this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX ) !== false) {
			$items = array_merge ( $this->__memory, $items );
			$this->parseSubParsers ( $items, array (
					'moment' => DIV_MOMENT_AFTER_PARSE,
					'level' => self::$__parse_level 
			) );
			$this->memory ( $items );
		}
		
		// Restore subparsers ignored
		foreach ( $subparsers_restore as $tagsearch => $tag_replace ) {
			$this->__src = str_replace ( $tagreplace, $tagsearch );
		}
		
		$time_end = microtime ( true );
		
		if (self::$__log_mode)
			$this->logger ( "Parser duration: " . number_format ( $time_end - $time_start, 5 ) . " secs" );
		
		self::$__parse_duration = $time_end - $time_start;
		self::$__parse_level --;
		
		if (self::$__parse_level == 0) {
			$this->__items = array_merge ( $this->__items, $this->__memory );
			$this->__items = array_merge ( $this->__items, self::$__globals_design );
			self::$__globals_design = array ();
			self::$__globals_design_protected = array ();
		}
		
		// Calling the afterParse hook
		$this->afterParse ();
	}
	
	/**
	 * Parsing SpecialChars
	 */
	final public function parseSpecialChars() {
		$this->__src = str_replace ( DIV_TAG_SPECIAL_REPLACE_NEW_LINE . "\n\n", DIV_TAG_SPECIAL_REPLACE_NEW_LINE . "\n", $this->__src );
		$this->__src = str_replace ( DIV_TAG_SPECIAL_REPLACE_NEW_LINE, "\n", $this->__src );
		$this->__src = str_replace ( DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB, "\t", $this->__src );
		$this->__src = str_replace ( DIV_TAG_SPECIAL_REPLACE_CAR_RETURN, "\r", $this->__src );
		$this->__src = str_replace ( DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB, "\v", $this->__src );
		$this->__src = str_replace ( DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE, "\f", $this->__src );
		$this->__src = str_replace ( DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL, "\$", $this->__src );
	}
	
	/**
	 * Multiple replacement
	 *
	 * @param array $items        	
	 */
	final public function parseMultiReplace(&$items = null) {
		if (is_null ( $items ))
			$items = $this->__items;
		if (is_array ( $items ))
			foreach ( $items as $key => $value ) {
				if (self::isArrayOfArray ( $value )) {
					
					$pos = 0;
					while ( true ) {
						$ranges = $this->getRanges ( "{:$key}", "{:/$key}", null, true, $pos );
						
						if (count ( $ranges ) < 1)
							break;
						
						$l = strlen ( $key ) + 4;
						$ini = $ranges [0] [0];
						$fin = $ranges [0] [1];
						
						$subsrc = substr ( $this->__src, $ini + $l - 1, $fin - $ini - $l + 1 );
						$engine = self::getAuxiliaryEngineClone ( $items );
						$engine->__src = $subsrc;
						
						$engine->parse ( false );
						
						$subsrc = $engine->__src;
						
						foreach ( $value as $vv ) {
							if (isset ( $vv [0] ) && isset ( $vv [1] )) {
								$regexp = false;
								if (isset ( $vv [2] ))
									if ($vv [2] == true)
										$regexp = true;
								if ($regexp) {
									$subsrc = preg_replace ( $vv [0], $vv [1], $subsrc );
								} else {
									$subsrc = str_replace ( $vv [0], $vv [1], $subsrc );
								}
							}
						}
						
						$this->__src = substr ( $this->__src, 0, $ini ) . $subsrc . substr ( $this->__src, $fin + $l );
					}
				}
			}
	}
	
	/**
	 * Clean the output: parsing the strip tags
	 */
	final public function clean() {
		$restore = array ();
		$this->__src = preg_replace ( "/\015\012|\015|\012/", "\n", $this->__src );
		$l1 = strlen ( DIV_TAG_STRIP_BEGIN );
		$l2 = strlen ( DIV_TAG_STRIP_END );
		while ( true ) {
			$ranges = $this->getRanges ( DIV_TAG_STRIP_BEGIN, DIV_TAG_STRIP_END, null, true );
			
			if (count ( $ranges ) < 1)
				break;
			
			$ini = $ranges [0] [0];
			$fin = $ranges [0] [1];
			$subsrc = substr ( $this->__src, $ini + $l1, $fin - $ini - $l1 );
			
			while ( strpos ( $subsrc, "\n\n" ) !== false )
				$subsrc = str_replace ( "\n\n", "\n", $subsrc );
			
			$lines = explode ( "\n", $subsrc );
			
			$subsrc = "";
			foreach ( $lines as $line ) {
				$line = trim ( $line );
				if ($line == "")
					continue;
				$subsrc .= $line . "\n";
			}
			$subsrc = trim ( $subsrc );
			$this->__src = substr ( $this->__src, 0, $ini ) . $subsrc . substr ( $this->__src, $fin + $l2 );
		}
	}
	
	/**
	 * Parse txt tags and convert HTML to readable text
	 */
	final public function txt() {
		$l1 = strlen ( DIV_TAG_TXT_BEGIN );
		$l2 = strlen ( DIV_TAG_TXT_END );
		$lsep = strlen ( DIV_TAG_TXT_WIDTH_SEPARATOR );
		
		while ( true ) {
			$ranges = $this->getRanges ( DIV_TAG_TXT_BEGIN, DIV_TAG_TXT_END, null, true );
			if (count ( $ranges ) < 1)
				break;
			
			$ini = $ranges [0] [0];
			$fin = $ranges [0] [1];
			
			$subsrc = substr ( $this->__src, $ini + $l1, $fin - $ini - $l1 );
			
			$width = 100;
			$p = strpos ( $subsrc, DIV_TAG_TXT_WIDTH_SEPARATOR );
			if ($p !== false) {
				$width = intval ( trim ( substr ( $subsrc, 0, $p ) ) );
				$subsrc = substr ( $subsrc, $p + $lsep );
			}
			
			$subsrc = self::htmlToText ( $subsrc, $width );
			$this->__src = substr ( $this->__src, 0, $ini ) . $subsrc . substr ( $this->__src, $fin + $l2 );
		}
	}
	
	/**
	 * Translate and change de original template
	 *
	 * @param mixed $dialectFrom        	
	 * @param string $src        	
	 * @param mixed $items        	
	 * @return string
	 */
	final public function translateAndChange($dialectFrom, $src = null, $items = null) {
		$translation = $this->translateFrom ( $dialectFrom, $src, $items );
		$this->changeTemplate ( $translation );
		return $translation;
	}
	
	/**
	 * Translate simple blocks
	 *
	 * @param string $src        	
	 * @param string $begin        	
	 * @param string $end        	
	 * @param string $tbegin        	
	 * @param string $end        	
	 */
	final private function translateSimpleBlocks($src, $begin, $end, $tbegin, $tend, $separator = "", $tseparator = "", $first = true) {
		$lbegin = strlen ( $begin );
		$lend = strlen ( $end );
		$lsep = strlen ( trim ( "$separator" ) );
		$p = 0;
		while ( true ) {
			$r = $this->getRanges ( $begin, $end, $src, true, $p );
			
			if (count ( $r ) < 1)
				break;
			
			$ini = $r [0] [0];
			$end = $r [0] [1];
			$subsrc = substr ( $src, $ini + $lbegin, $end - $ini - $lbegin );
			
			if ($lsep > 0) {
				if ($first)
					$po = strpos ( $subsrc, $separator );
				else
					$po = strrpos ( $subsrc, $separator );
				if ($po !== false)
					$subsrc = substr ( $subsrc, 0, $po ) . $tseparator . substr ( $subsrc, $po + $lsep );
			}
			
			$src = substr ( $src, 0, $ini ) . $tbegin . $subsrc . $tend . substr ( $src, $end + $lend );
			
			$p = $ini + 1;
		}
		
		return $src;
	}
	
	/**
	 * Translate key blocks
	 *
	 * @param string $src        	
	 * @param string $begin_prefix        	
	 * @param string $begin_suffix        	
	 * @param string $end_prefix        	
	 * @param string $end_suffix        	
	 * @param string $tbegin_prefix        	
	 * @param string $tbegin_suffix        	
	 * @param string $tend_prefix        	
	 * @param string $tend_suffix        	
	 */
	final private function translateKeyBlocks($src, $begin_prefix, $begin_suffix, $end_prefix, $end_suffix, $tbegin_prefix, $tbegin_suffix, $tend_prefix, $tend_suffix, $var_member_delimiter, $tvar_member_delimiter) {
		$p = 0;
		while ( true ) {
			
			$r = $this->getBlockRanges ( $src, $begin_prefix, $begin_suffix, $end_prefix, $end_suffix, $p, null, true, $var_member_delimiter );
			
			if (count ( $r ) < 1)
				break;
			
			$ini = $r [0] [0];
			$end = $r [0] [1];
			$key = $r [0] [2];
			$subsrc = $r [0] [3];
			$prefix = $begin_prefix . $key . $begin_suffix;
			$suffix = $end_prefix . $key . $end_suffix;
			$lprefix = strlen ( $prefix );
			$lsuffix = strlen ( $suffix );
			
			$key = str_replace ( $var_member_delimiter, $tvar_member_delimiter, $key );
			$src = substr ( $src, 0, $ini ) . $tbegin_prefix . $key . $tbegin_suffix . $subsrc . $tend_prefix . $key . $tend_suffix . substr ( $src, $end + $lsuffix );
			
			$p = $ini + 1;
		}
		return $src;
	}
	
	/**
	 * Translate dialects
	 *
	 * @param string $src        	
	 * @param mixed $dialectFrom        	
	 * @param mixed $dialectTo        	
	 * @return string
	 */
	final public function translateFrom($dialectFrom, $src = null, $items = null) {
		if (self::$__log_mode === true)
			$this->logger ( "Translating to current dialect..." );
		
		$update = false;
		if (is_null ( $src )) {
			$src = &$this->__src;
			$update = true;
		}
		if (is_null ( $items ))
			$items = &$this->__items;
		
		$constants = get_defined_constants ( true );
		$constants = $constants ['user'];
		$nconst = array ();
		foreach ( $constants as $c => $v )
			if (substr ( $c, 0, 8 ) == 'DIV_TAG_')
				$nconst [$c] = $v;
		$constants = $nconst;
		
		// Preparing dialect from ...
		if (is_string ( $dialectFrom ))
			$dialectFrom = self::jsonDecode ( $dialectFrom );
		if (is_object ( $dialectFrom ))
			$dialectFrom = get_object_vars ( $dialectFrom );
		if (! is_array ( $dialectFrom ))
			return false;
		
		foreach ( $constants as $c => $v )
			if (! isset ( $dialectFrom [$c] ))
				$dialectFrom [$c] = $v;
		
		foreach ( $dialectFrom as $c => $v )
			eval ( '$' . $c . ' = $v;' );
			
			// Searching differences
		$different = false;
		
		foreach ( $dialectFrom as $c => $v ) {
			if ($v !== constant ( $c )) {
				$different = true;
				break;
			}
		}
		
		if (! $different)
			return $src; // The dialects are equals
		
		$order = array (
				'replacement' => strlen ( $DIV_TAG_REPLACEMENT_PREFIX ),
				'multimodifiers' => strlen ( $DIV_TAG_MULTI_MODIFIERS_PREFIX ),
				'dateformat' => strlen ( $DIV_TAG_DATE_FORMAT_PREFIX ),
				'numberformat' => strlen ( $DIV_TAG_NUMBER_FORMAT_PREFIX ),
				'formulas' => strlen ( $DIV_TAG_FORMULA_BEGIN ),
				'subparsers' => strlen ( $DIV_TAG_SUBPARSER_BEGIN_PREFIX ),
				'ignore' => strlen ( $DIV_TAG_IGNORE_BEGIN ),
				'comment' => strlen ( $DIV_TAG_COMMENT_BEGIN ),
				'html2txt' => strlen ( $DIV_TAG_TXT_BEGIN ),
				'strip' => strlen ( $DIV_TAG_STRIP_BEGIN ),
				'loops' => strlen ( $DIV_TAG_LOOP_BEGIN_PREFIX ),
				'iterations' => strlen ( $DIV_TAG_ITERATION_BEGIN_PREFIX ),
				'conditionals' => strlen ( $DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX ),
				'conditions' => strlen ( $DIV_TAG_CONDITIONS_BEGIN_PREFIX ),
				'tplvars' => strlen ( $DIV_TAG_TPLVAR_BEGIN ),
				'defaultreplace' => strlen ( $DIV_TAG_DEFAULT_REPLACEMENT_BEGIN ),
				'include' => strlen ( $DIV_TAG_IGNORE_BEGIN ),
				'preprocessed' => strlen ( $DIV_TAG_PREPROCESSED_BEGIN ),
				'capsules' => strlen ( $DIV_TAG_CAPSULE_BEGIN_PREFIX ),
				'multireplace' => strlen ( $DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX ),
				'friendlytags' => strlen ( $DIV_TAG_FRIENDLY_BEGIN ),
				'macros' => strlen ( $DIV_TAG_MACRO_BEGIN ),
				'location' => strlen ( $DIV_TAG_LOCATION_BEGIN ),
				'locontent' => strlen ( $DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX ) 
		);
		
		arsort ( $order );
		
		$modifiers = array (
				'DIV_TAG_MODIFIER_SIMPLE',
				'DIV_TAG_MODIFIER_CAPITALIZE_FIRST',
				'DIV_TAG_MODIFIER_CAPITALIZE_WORDS',
				'DIV_TAG_MODIFIER_UPPERCASE',
				'DIV_TAG_MODIFIER_LOWERCASE',
				'DIV_TAG_MODIFIER_LENGTH',
				'DIV_TAG_MODIFIER_COUNT_WORDS',
				'DIV_TAG_MODIFIER_COUNT_SENTENCES',
				'DIV_TAG_MODIFIER_COUNT_PARAGRAPHS',
				'DIV_TAG_MODIFIER_ENCODE_URL',
				'DIV_TAG_MODIFIER_ENCODE_RAW_URL',
				'DIV_TAG_MODIFIER_HTML_ENTITIES',
				'DIV_TAG_MODIFIER_NL2BR',
				'DIV_TAG_MODIFIER_ENCODE_JSON',
				'DIV_TAG_MODIFIER_SINGLE_QUOTES',
				'DIV_TAG_MODIFIER_JS' 
		);
		
		$xmod = array ();
		$ymod = array ();
		foreach ( $modifiers as $mod ) {
			$xmod [$mod] = array ();
			eval ( '$xmod[$mod][0] = $' . $mod . ';' );
			eval ( '$xmod[$mod][1] = ' . $mod . ';' );
			$ymod [$xmod [$mod] [0]] = $xmod [$mod] [1];
		}
		
		$temp = $DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR;
		
		$agfuncs = array (
				DIV_TAG_AGGREGATE_FUNCTION_COUNT . $temp => $DIV_TAG_AGGREGATE_FUNCTION_COUNT . $temp,
				DIV_TAG_AGGREGATE_FUNCTION_MAX . $temp => $DIV_TAG_AGGREGATE_FUNCTION_MAX . $temp,
				DIV_TAG_AGGREGATE_FUNCTION_MIN . $temp => $DIV_TAG_AGGREGATE_FUNCTION_MIN . $temp,
				DIV_TAG_AGGREGATE_FUNCTION_SUM . $temp => $DIV_TAG_AGGREGATE_FUNCTION_SUM . $temp,
				DIV_TAG_AGGREGATE_FUNCTION_AVG . $temp => $DIV_TAG_AGGREGATE_FUNCTION_AVG . $temp 
		);
		
		asort ( $agfuncs );
		$agfuncs_keys = array_keys ( $agfuncs );
		
		foreach ( $order as $o => $priority ) {
			switch ($o) {
				case 'replacement' :
					foreach ( $xmod as $modifier => $values ) {
						$lprefix = strlen ( $DIV_TAG_REPLACEMENT_PREFIX . $values [0] );
						$lsuffix = strlen ( $DIV_TAG_REPLACEMENT_SUFFIX );
						$p = 0;
						while ( true ) {
							
							$r = $this->getRanges ( $DIV_TAG_REPLACEMENT_PREFIX . $values [0], $DIV_TAG_REPLACEMENT_SUFFIX, $src, true, $p );
							if (count ( $r ) < 1)
								break;
							
							$ini = $r [0] [0];
							$end = $r [0] [1];
							$subsrc = substr ( $src, $ini + $lprefix, $end - $ini - $lprefix );
							
							if (strpos ( $subsrc, "\n" ) !== false) {
								$p = $ini + 1;
								continue;
							}
							if (strpos ( $subsrc, "\t" ) !== false) {
								$p = $ini + 1;
								continue;
							}
							if (strpos ( $subsrc, "\r" ) !== false) {
								$p = $ini + 1;
								continue;
							}
							if (strpos ( $subsrc, " " ) !== false) {
								$p = $ini + 1;
								continue;
							}
							
							// Aggregate functions
							$subsrc = str_replace ( $DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR, DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR, $subsrc );
							$subsrc = str_replace ( $agfuncs, $agfuncs_keys, $subsrc );
							
							// Teaser or truncate
							$subsrc = str_replace ( $DIV_TAG_SUBMATCH_SEPARATOR . $DIV_TAG_MODIFIER_TRUNCATE, DIV_TAG_SUBMATCH_SEPARATOR . DIV_TAG_MODIFIER_TRUNCATE, $subsrc );
							
							// Word wrap
							$subsrc = str_replace ( $DIV_TAG_SUBMATCH_SEPARATOR . $DIV_TAG_MODIFIER_WORDWRAP, DIV_TAG_SUBMATCH_SEPARATOR . DIV_TAG_MODIFIER_WORDWRAP, $subsrc );
							
							// Format (sprintf)
							$subsrc = str_replace ( $DIV_TAG_SUBMATCH_SEPARATOR . $DIV_TAG_MODIFIER_FORMAT, DIV_TAG_SUBMATCH_SEPARATOR . DIV_TAG_MODIFIER_FORMAT, $subsrc );
							
							// Substring
							$subsrc = str_replace ( $DIV_TAG_SUBMATCH_SEPARATOR, DIV_TAG_SUBMATCH_SEPARATOR, $subsrc );
							
							// Member delimiters
							$subsrc = str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, $subsrc );
							
							$src = substr ( $src, 0, $ini ) . DIV_TAG_REPLACEMENT_PREFIX . $values [1] . $subsrc . DIV_TAG_REPLACEMENT_SUFFIX . substr ( $src, $end + $lsuffix );
							
							$p = $ini + 1; // IMPORTANT!
						}
					}
					break;
				case 'multimodifiers' :
					$lprefix = strlen ( $DIV_TAG_MULTI_MODIFIERS_PREFIX );
					$lsuffix = strlen ( $DIV_TAG_MULTI_MODIFIERS_SUFFIX );
					$p = 0;
					while ( true ) {
						$r = $this->getRanges ( $DIV_TAG_MULTI_MODIFIERS_PREFIX, $DIV_TAG_MULTI_MODIFIERS_SUFFIX, $src, true, $p );
						if (count ( $r ) < 1)
							break;
						
						$ini = $r [0] [0];
						$end = $r [0] [1];
						$subsrc = substr ( $src, $ini + $lprefix, $end - $ini - $lprefix );
						
						if (strpos ( $subsrc, "\n" ) !== false) {
							$p = $ini + 1;
							continue;
						}
						if (strpos ( $subsrc, "\t" ) !== false) {
							$p = $ini + 1;
							continue;
						}
						if (strpos ( $subsrc, "\r" ) !== false) {
							$p = $ini + 1;
							continue;
						}
						if (strpos ( $subsrc, " " ) !== false) {
							$p = $ini + 1;
							continue;
						}
						
						$po = strpos ( $subsrc, $DIV_TAG_MULTI_MODIFIERS_OPERATOR );
						
						if ($po === false) {
							$p = $ini + 1;
							continue;
						}
						
						$temp = substr ( $subsrc, $po + 1 );
						$parts = explode ( $DIV_TAG_MULTI_MODIFIERS_SEPARATOR, $temp );
						
						foreach ( $parts as $k => $v ) {
							if (isset ( $ymod [$v] ))
								$parts [$k] = $ymod [$v];
							if (isset ( $ymod [$v . ':'] ))
								$parts [$k] = $ymod [$v . ':'];
						}
						
						$temp = implode ( DIV_TAG_MULTI_MODIFIERS_SEPARATOR, $parts );
						$subsrc = str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, substr ( $subsrc, 0, $po ) ) . DIV_TAG_MULTI_MODIFIERS_OPERATOR . $temp;
						
						// Member delimiters
						$subsrc = str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, $subsrc );
						
						$src = substr ( $src, 0, $ini ) . DIV_TAG_MULTI_MODIFIERS_PREFIX . $subsrc . DIV_TAG_MULTI_MODIFIERS_SUFFIX . substr ( $src, $end + $lsuffix );
						$p = $ini + 1; // IMPORTANT!
					}
					break;
				case 'dateformat' :
					$lprefix = strlen ( $DIV_TAG_DATE_FORMAT_PREFIX );
					$lsuffix = strlen ( $DIV_TAG_DATE_FORMAT_SUFFIX );
					$lsep = strlen ( $DIV_TAG_DATE_FORMAT_SEPARATOR );
					$p = 0;
					while ( true ) {
						$r = $this->getRanges ( $DIV_TAG_DATE_FORMAT_PREFIX, $DIV_TAG_DATE_FORMAT_SUFFIX, $src, true, $p );
						if (count ( $r ) < 1)
							break;
						
						$ini = $r [0] [0];
						$end = $r [0] [1];
						$subsrc = substr ( $src, $ini + $lprefix, $end - $ini - $lprefix );
						
						$po = strpos ( $subsrc, $DIV_TAG_DATE_FORMAT_SEPARATOR );
						if ($po !== false)
							$subsrc = str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, substr ( $subsrc, 0, $po ) ) . DIV_TAG_DATE_FORMAT_SEPARATOR . substr ( $subsrc, $po + $lsep );
						
						$src = substr ( $src, 0, $ini ) . DIV_TAG_DATE_FORMAT_PREFIX . $subsrc . DIV_TAG_DATE_FORMAT_SUFFIX . substr ( $src, $end + $lsuffix );
						$p = $ini + 1; // IMPORTANT!
					}
					break;
				case 'numberformat' :
					$lprefix = strlen ( $DIV_TAG_NUMBER_FORMAT_PREFIX );
					$lsuffix = strlen ( $DIV_TAG_NUMBER_FORMAT_SUFFIX );
					$lsep = strlen ( $DIV_TAG_NUMBER_FORMAT_SEPARATOR );
					$p = 0;
					while ( true ) {
						$r = $this->getRanges ( $DIV_TAG_NUMBER_FORMAT_PREFIX, $DIV_TAG_NUMBER_FORMAT_SUFFIX, $src, true, $p );
						if (count ( $r ) < 1)
							break;
						
						$ini = $r [0] [0];
						$end = $r [0] [1];
						$subsrc = substr ( $src, $ini + $lprefix, $end - $ini - $lprefix );
						
						$po = strpos ( $subsrc, $DIV_TAG_NUMBER_FORMAT_SEPARATOR );
						if ($po !== false)
							$subsrc = str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, substr ( $subsrc, 0, $po ) ) . DIV_TAG_NUMBER_FORMAT_SEPARATOR . substr ( $subsrc, $po + $lsep );
						
						$src = substr ( $src, 0, $ini ) . DIV_TAG_NUMBER_FORMAT_PREFIX . $subsrc . DIV_TAG_NUMBER_FORMAT_SUFFIX . substr ( $src, $end + $lsuffix );
						$p = $ini + 1; // IMPORTANT!
					}
					
					break;
				case 'formulas' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_FORMULA_BEGIN, $DIV_TAG_FORMULA_END, DIV_TAG_FORMULA_BEGIN, DIV_TAG_FORMULA_END, $DIV_TAG_FORMULA_FORMAT_SEPARATOR, DIV_TAG_FORMULA_FORMAT_SEPARATOR, false );
					break;
				case 'subparsers' :
					
					foreach ( self::$__sub_parsers as $subparser => $function ) {
						$src = str_replace ( $DIV_TAG_SUBPARSER_BEGIN_PREFIX . $subparser . $DIV_TAG_SUBPARSER_BEGIN_SUFFIX, DIV_TAG_SUBPARSER_BEGIN_PREFIX . $subparser . DIV_TAG_SUBPARSER_BEGIN_SUFFIX, $src );
						$src = str_replace ( $DIV_TAG_SUBPARSER_END_PREFIX . $subparser . $DIV_TAG_SUBPARSER_END_SUFFIX, DIV_TAG_SUBPARSER_END_PREFIX . $subparser . DIV_TAG_SUBPARSER_END_SUFFIX, $src );
					}
					
					break;
				case 'ignore' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_IGNORE_BEGIN, $DIV_TAG_IGNORE_END, DIV_TAG_IGNORE_BEGIN, DIV_TAG_IGNORE_END );
					break;
				
				case 'comment' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_COMMENT_BEGIN, $DIV_TAG_COMMENT_END, DIV_TAG_COMMENT_BEGIN, DIV_TAG_COMMENT_END );
					break;
				
				case 'html2txt' :
					$lprefix = strlen ( $DIV_TAG_TXT_BEGIN );
					$lsuffix = strlen ( $DIV_TAG_TXT_END );
					$lsep = strlen ( $DIV_TAG_TXT_WIDTH_SEPARATOR );
					$p = 0;
					while ( true ) {
						$r = $this->getRanges ( $DIV_TAG_TXT_BEGIN, $DIV_TAG_TXT_END, $src, true, $p );
						if (count ( $r ) < 1)
							break;
						
						$ini = $r [0] [0];
						$end = $r [0] [1];
						
						$subsrc = substr ( $src, $ini + $lprefix, $end - $ini - $lprefix );
						
						$po = strpos ( $subsrc, $DIV_TAG_TXT_WIDTH_SEPARATOR );
						if ($po !== false)
							$subsrc = substr ( $subsrc, 0, $po ) . DIV_TAG_TXT_WIDTH_SEPARATOR . substr ( $subsrc, $po + $lsep );
						
						$src = substr ( $src, 0, $ini ) . DIV_TAG_TXT_BEGIN . $subsrc . DIV_TAG_TXT_END . substr ( $src, $end + $lsuffix );
						
						$p = $ini + 1;
					}
					break;
				case 'strip' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_STRIP_BEGIN, $DIV_TAG_STRIP_END, DIV_TAG_STRIP_BEGIN, DIV_TAG_STRIP_END );
					break;
				
				case 'loops' :
					$lsep = strlen ( $DIV_TAG_LOOP_VAR_SEPARATOR );
					$p = 0;
					while ( true ) {
						
						$r = $this->getBlockRanges ( $src, $DIV_TAG_LOOP_BEGIN_PREFIX, $DIV_TAG_LOOP_BEGIN_SUFFIX, $DIV_TAG_LOOP_END_PREFIX, $DIV_TAG_LOOP_END_SUFFIX, $p, null, true, $DIV_TAG_VAR_MEMBER_DELIMITER );
						
						if (count ( $r ) < 1)
							break;
						
						$ini = $r [0] [0];
						$end = $r [0] [1];
						$key = $r [0] [2];
						$subsrc = $r [0] [3];
						$prefix = $DIV_TAG_LOOP_BEGIN_PREFIX . $key . $DIV_TAG_LOOP_BEGIN_SUFFIX;
						$suffix = $DIV_TAG_LOOP_END_PREFIX . $key . $DIV_TAG_LOOP_END_SUFFIX;
						$lprefix = strlen ( $prefix );
						$lsuffix = strlen ( $suffix );
						
						$po = strpos ( $subsrc, $DIV_TAG_LOOP_VAR_SEPARATOR );
						if ($po !== false)
							$subsrc = substr ( $subsrc, 0, $po ) . DIV_TAG_LOOP_VAR_SEPARATOR . substr ( $subsrc, $po + $lsep );
						
						$subsrc = str_replace ( array (
								$DIV_TAG_EMPTY,
								$DIV_TAG_BREAK 
						), array (
								DIV_TAG_EMPTY,
								DIV_TAG_BREAK 
						), $subsrc );
						
						$key = str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, $key );
						$src = substr ( $src, 0, $ini ) . DIV_TAG_LOOP_BEGIN_PREFIX . $key . DIV_TAG_LOOP_BEGIN_SUFFIX . $subsrc . DIV_TAG_LOOP_END_PREFIX . $key . DIV_TAG_LOOP_END_SUFFIX . substr ( $src, $end + $lsuffix );
						
						$p = $ini + 1;
					}
					break;
				case 'iterations' :
					$lprefix = strlen ( $DIV_TAG_ITERATION_BEGIN_PREFIX );
					$lsuffix = strlen ( $DIV_TAG_ITERATION_BEGIN_SUFFIX );
					$lend = strlen ( $DIV_TAG_ITERATION_END );
					$lsep = strlen ( $DIV_TAG_LOOP_VAR_SEPARATOR );
					$p = 0;
					
					while ( true ) {
						$ranges = $this->getRanges ( $DIV_TAG_ITERATION_BEGIN_PREFIX, $DIV_TAG_ITERATION_END, $src, true, $p );
						if (count ( $ranges ) < 1)
							break;
						
						$ini = $ranges [0] [0];
						$end = $ranges [0] [1];
						$p1 = strpos ( $src, $DIV_TAG_ITERATION_BEGIN_SUFFIX, $ini + 1 );
						
						$s = substr ( $src, $ini + $lprefix, $p1 - ($ini + $lprefix) );
						
						$parts = explode ( $DIV_TAG_ITERATION_PARAM_SEPARATOR, $s );
						
						$s = implode ( DIV_TAG_ITERATION_PARAM_SEPARATOR, $parts );
						
						$subsrc = substr ( $src, $p1 + $lsuffix, $end - ($p1 + $lsuffix) );
						
						$po = strpos ( $subsrc, $DIV_TAG_LOOP_VAR_SEPARATOR );
						if ($po !== false)
							$subsrc = substr ( $subsrc, 0, $po ) . DIV_TAG_LOOP_VAR_SEPARATOR . substr ( $subsrc, $po + $lsep );
						
						$src = substr ( $src, 0, $ini ) . DIV_TAG_ITERATION_BEGIN_PREFIX . $s . DIV_TAG_ITERATION_BEGIN_SUFFIX . $subsrc . DIV_TAG_ITERATION_END . substr ( $src, $end + $lend );
						
						$p = $ini + 1;
					}
					break;
				case 'conditionals' :
					
					// true
					
					$p = 0;
					while ( true ) {
						
						$r = $this->getBlockRanges ( $src, $DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX, $DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX, $DIV_TAG_CONDITIONAL_TRUE_END_PREFIX, $DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX, $p, null, true, $DIV_TAG_VAR_MEMBER_DELIMITER );
						
						if (count ( $r ) < 1)
							break;
						
						$ini = $r [0] [0];
						$end = $r [0] [1];
						$key = $r [0] [2];
						
						$subsrc = $r [0] [3];
						$prefix = $DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX . $key . $DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX;
						$suffix = $DIV_TAG_CONDITIONAL_TRUE_END_PREFIX . $key . $DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX;
						$lprefix = strlen ( $prefix );
						$lsuffix = strlen ( $suffix );
						
						$subsrc = str_replace ( $DIV_TAG_ELSE, DIV_TAG_ELSE, $subsrc );
						$key = str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, $key );
						$src = substr ( $src, 0, $ini ) . DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX . str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, $key ) . DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX . $subsrc . DIV_TAG_CONDITIONAL_TRUE_END_PREFIX . $key . DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX . substr ( $src, $end + $lsuffix );
						
						$p = $ini + 1;
					}
					
					// false
					
					$p = 0;
					while ( true ) {
						
						$r = $this->getBlockRanges ( $src, $DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX, $DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX, $DIV_TAG_CONDITIONAL_FALSE_END_PREFIX, $DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX, $p, null, true, $DIV_TAG_VAR_MEMBER_DELIMITER );
						
						if (count ( $r ) < 1)
							break;
						
						$ini = $r [0] [0];
						$end = $r [0] [1];
						$key = $r [0] [2];
						
						$subsrc = $r [0] [3];
						$prefix = $DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX . $key . $DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX;
						$suffix = $DIV_TAG_CONDITIONAL_FALSE_END_PREFIX . $key . $DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX;
						$lprefix = strlen ( $prefix );
						$lsuffix = strlen ( $suffix );
						
						$subsrc = str_replace ( $DIV_TAG_ELSE, DIV_TAG_ELSE, $subsrc );
						$key = str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, $key );
						$src = substr ( $src, 0, $ini ) . DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX . $key . DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX . $subsrc . DIV_TAG_CONDITIONAL_FALSE_END_PREFIX . $key . DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX . substr ( $src, $end + $lsuffix );
						
						$p = $ini + 1;
					}
					break;
				
				case 'conditions' :
					$lprefix = strlen ( $DIV_TAG_CONDITIONS_BEGIN_PREFIX );
					$lsuffix = strlen ( $DIV_TAG_CONDITIONS_BEGIN_SUFFIX );
					$lend = strlen ( $DIV_TAG_CONDITIONS_END );
					$lelse = strlen ( $DIV_TAG_ELSE );
					$p = 0;
					while ( true ) {
						$r = $this->getRanges ( $DIV_TAG_CONDITIONS_BEGIN_PREFIX, $DIV_TAG_CONDITIONS_END, $src, true, $p );
						
						if (count ( $r ) < 1)
							break;
						
						$ini = $r [0] [0];
						$end = $r [0] [1];
						$p1 = strpos ( $src, $DIV_TAG_CONDITIONS_BEGIN_SUFFIX, $ini + 1 );
						
						$s = substr ( $src, $ini + $lprefix, $p1 - ($ini + $lprefix) );
						
						$subsrc = substr ( $src, $p1 + $lsuffix, $end - ($p1 + $lsuffix) );
						
						$po = strpos ( $subsrc, $DIV_TAG_ELSE );
						if ($po !== false)
							$subsrc = substr ( $subsrc, 0, $po ) . DIV_TAG_ELSE . substr ( $subsrc, $po + $lelse );
						
						$src = substr ( $src, 0, $ini ) . DIV_TAG_CONDITIONS_BEGIN_PREFIX . $s . DIV_TAG_CONDITIONS_BEGIN_SUFFIX . $subsrc . DIV_TAG_CONDITIONS_END . substr ( $src, $end + $lend );
						
						$p = $ini + 1;
					}
					break;
				
				case 'tplvars' :
					$lbegin = strlen ( $DIV_TAG_TPLVAR_BEGIN );
					$lend = strlen ( $DIV_TAG_TPLVAR_END );
					$loperator = strlen ( $DIV_TAG_TPLVAR_ASSIGN_OPERATOR );
					$lprotector = strlen ( $DIV_TAG_TPLVAR_PROTECTOR );
					
					$p = 0;
					while ( true ) {
						$r = $this->getRanges ( $DIV_TAG_TPLVAR_BEGIN, $DIV_TAG_TPLVAR_END, $src, true, $p );
						
						if (count ( $r ) < 1)
							break;
						
						$ini = $r [0] [0];
						$end = $r [0] [1];
						$p1 = strpos ( $src, $DIV_TAG_TPLVAR_ASSIGN_OPERATOR, $ini + $lbegin );
						
						if ($p1 === false) {
							$p = $ini + 1;
							continue;
						}
						
						$tplvarvalue = substr ( $src, $p1 + $loperator, $end - ($p1 + $loperator) );
						
						$missing_vars = array ();
						$json = $this->jsonDecode ( $tplvarvalue, array (), $missing_vars );
						
						foreach ( $missing_vars as $missing )
							$tplvarvalue = str_replace ( '$' . $missing, '$' . str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, $missing ), $tplvarvalue );
						
						$src = substr ( $src, 0, $ini ) . DIV_TAG_TPLVAR_BEGIN . str_replace ( $DIV_TAG_TPLVAR_PROTECTOR, DIV_TAG_TPLVAR_PROTECTOR, str_replace ( $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER, substr ( $src, $ini + $lbegin, $p1 - ($ini + $lbegin) ) ) ) . DIV_TAG_TPLVAR_ASSIGN_OPERATOR . $tplvarvalue . DIV_TAG_TPLVAR_END . substr ( $src, $end + $lend );
						
						$p = $ini + 1;
					}
					break;
				
				case 'defaultreplace' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_DEFAULT_REPLACEMENT_BEGIN, $DIV_TAG_DEFAULT_REPLACEMENT_END, DIV_TAG_DEFAULT_REPLACEMENT_BEGIN, DIV_TAG_DEFAULT_REPLACEMENT_END );
					break;
				
				case 'include' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_INCLUDE_BEGIN, $DIV_TAG_INCLUDE_END, DIV_TAG_INCLUDE_BEGIN, DIV_TAG_INCLUDE_END );
					break;
				
				case 'preprocessed' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_PREPROCESSED_BEGIN, $DIV_TAG_PREPROCESSED_END, DIV_TAG_PREPROCESSED_BEGIN, DIV_TAG_PREPROCESSED_END, DIV_TAG_PREPROCESSED_SEPARATOR );
					break;
				
				case 'capsules' :
					$src = $this->translateKeyBlocks ( $src, $DIV_TAG_CAPSULE_BEGIN_PREFIX, $DIV_TAG_CAPSULE_BEGIN_SUFFIX, $DIV_TAG_CAPSULE_END_PREFIX, $DIV_TAG_CAPSULE_END_SUFFIX, DIV_TAG_CAPSULE_BEGIN_PREFIX, DIV_TAG_CAPSULE_BEGIN_SUFFIX, DIV_TAG_CAPSULE_END_PREFIX, DIV_TAG_CAPSULE_END_SUFFIX, $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER );
					break;
				
				case 'multireplace' :
					$src = $this->translateKeyBlocks ( $src, $DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX, $DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX, $DIV_TAG_MULTI_REPLACEMENT_END_PREFIX, $DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX, DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX, DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX, DIV_TAG_MULTI_REPLACEMENT_END_PREFIX, DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX, $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER );
					break;
				
				case 'friendlytags' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_FRIENDLY_BEGIN, $DIV_TAG_FRIENDLY_END, DIV_TAG_FRIENDLY_BEGIN, DIV_TAG_FRIENDLY_END );
					break;
				
				case 'macros' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_MACRO_BEGIN, $DIV_TAG_MACRO_END, DIV_TAG_MACRO_BEGIN, DIV_TAG_MACRO_END );
					break;
				
				case 'location' :
					$src = $this->translateSimpleBlocks ( $src, $DIV_TAG_LOCATION_BEGIN, $DIV_TAG_LOCATION_END, DIV_TAG_LOCATION_BEGIN, DIV_TAG_LOCATION_END );
					break;
				
				case 'locontent' :
					$src = $this->translateKeyBlocks ( $src, $DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX, $DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX, $DIV_TAG_LOCATION_CONTENT_END_PREFIX, $DIV_TAG_LOCATION_CONTENT_END_SUFFIX, DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX, DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX, DIV_TAG_LOCATION_CONTENT_END_PREFIX, DIV_TAG_LOCATION_CONTENT_END_SUFFIX, $DIV_TAG_VAR_MEMBER_DELIMITER, DIV_TAG_VAR_MEMBER_DELIMITER );
					break;
			}
		}
		
		$order = array (
				DIV_TAG_SPECIAL_REPLACE_NEW_LINE => $DIV_TAG_SPECIAL_REPLACE_NEW_LINE,
				DIV_TAG_SPECIAL_REPLACE_CAR_RETURN => $DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB => $DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB => $DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE => $DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE,
				DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL => $DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL,
				DIV_TAG_TEASER_BREAK => $DIV_TAG_TEASER_BREAK 
		);
		asort ( $order );
		$src = str_replace ( $order, array_keys ( $order ), $src );
		
		if ($update)
			$this->__src = $src;
		
		return $src;
	}
	
	/**
	 * Convert div to string | Return the parsed template
	 *
	 * @return string
	 */
	final public function __toString() {
		$this->parse ();
		return $this->__src.'';
	}
	
	// ------------------------ PREDEFINED SUBPARSERS ---------------------------- //
	
	/**
	 * Parse this
	 *
	 * @param string $src        	
	 * @param mixed $items        	
	 * @return string
	 */
	final private function subParse_parse($src, $items) {
		$tpl = self::getAuxiliaryEngineClone ( $this->__items );
		$tpl->__src = $src;
		$tpl->__src_original = $src;
		$tpl->parse ();
		return $tpl->__src;
	}
	
	/**
	 * Convert all chars to HTML entities/codes.
	 *
	 * @param string $src        	
	 * @param mixed $items        	
	 * @return string
	 */
	final private function subParse_html_wysiwyg($src) {
		$l = strlen ( $src );
		$newcode = '';
		for($i = 0; $i < $l; $i ++) {
			if ($src [$i] != "\n" && $src [$i] != "\t" && $src [$i] != "\r")
				$newcode .= '&#' . ord ( $src [$i] ) . ';';
			else
				$newcode .= $src [$i];
		}
		return $newcode;
	}
	
	// -------------------------------- HOOKS ------------------------------------- //
	
	/**
	 * The hooks
	 */
	public function beforeBuild(&$src = null, &$items = null) {
	}
	public function afterBuild() {
	}
	public function beforeParse() {
	}
	public function afterParse() {
	}
	
	/**
	 * Output the parsed template
	 *
	 * @param string $template        	
	 */
	public function show($template = null) {
		if (! is_null ( $template )) {
			$this->changeTemplate ( $template );
		}
		
		$this->parse ();
		echo $this->__src;
	}
	
	// -------------------------------- Functions ------------------------------------- //
	/**
	 * Parse a template
	 *
	 * @param string $src        	
	 * @param string $items        	
	 * @param array $ignore        	
	 * @param number $min_level        	
	 * @return string
	 */
	final static function div($src = null, $items = null, $ignore = array(), $min_level = 1) {
		$class = get_class ();
		$engine = new $class ( $src, $items, $ignore );
		$engine->parse ( false, null, $min_level );
		return $engine->__src;
	}
	
	/**
	 * Enable documentation
	 */
	final static function docsOn() {
		self::$__docs_on = true;
	}
	
	/**
	 * Disable documentation
	 */
	final static function docsOff() {
		self::$__docs_off = false;
	}
	
	/**
	 * Get documentation's data
	 *
	 * @return array
	 */
	final static function getDocs() {
		return self::$__docs;
	}
	
	/**
	 * Get a redeable documentation
	 *
	 * @param string $tpl        	
	 * @param array $items        	
	 * @return string
	 */
	final static function getDocsReadable($tpl = null, $items = null) {
		$docs = self::$__docs;
		$keys = array_keys ( $docs );
		
		asort ( $keys );
		$docsx = array ();
		
		foreach ( $keys as $key )
			$docsx [$key] = $docs [$key];
		
		if (is_null ( $items ))
			$items = array (
					'title' => "Templates's documentation" 
			);
		elseif (is_object ( $items ))
			$items = get_object_vars ( $items );
		
		$items = array_merge ( $items, array (
				'docs' => $docsx 
		) );
		
		if (is_null ( $tpl ))
			$tpl = DIV_TEMPLATE_FOR_DOCS;
		
		$obj = self::getAuxiliaryEngineClone ( $items );
		$obj->__src = $tpl;
		$obj->parse ( false );
		
		return $obj->__src;
	}
	
	/**
	 * Clear all empty/null values and return a compact mixed
	 *
	 * @param mixed $mixed        	
	 * @return mixed
	 */
	final static function compact($mixed) {
		if (empty ( $mixed ))
			return null;
		if (is_null ( $mixed ))
			return null;
		if (is_scalar ( $mixed ))
			if ("$mixed" == '')
				return null;
		
		if (is_object ( $mixed )) {
			$vars = get_object_vars ( $mixed );
			foreach ( $vars as $var => $value ) {
				$value = self::compact ( $value );
				if (is_null ( $value ))
					unset ( $mixed->$var );
			}
			$vars = get_object_vars ( $mixed );
			if (count ( $vars ) < 1)
				return null;
		}
		
		if (is_array ( $mixed )) {
			$arr = array ();
			foreach ( $mixed as $var => $value ) {
				$value = self::compact ( $value );
				if (! is_null ( $value ))
					$arr [$var] = $value;
			}
			if (count ( $arr ) < 1)
				return null;
			$mixed = $arr;
		}
		
		return $mixed;
	}
	
	/**
	 * Convert any value to string (with Div method)
	 *
	 * @param mixed $value        	
	 * @return string
	 */
	final static function anyToStr($value) {
		if (self::isString ( $value ))
			return "$value";
		if (is_bool ( $value ))
			return $value ? 'true' : 'false';
		if (is_numeric ( $value ))
			return "$value";
		if (is_object ( $value ))
			return "" . count ( get_object_vars ( $value ) );
		if (is_array ( $value ))
			return "" . count ( $value );
		if (is_null ( $value ))
			return "";
		return "$value";
	}
	
	/**
	 * Complete object/array properties
	 *
	 * @param mixed $obj        	
	 * @param mixed $prop        	
	 * @return mixed
	 */
	final static function cop(&$source, $complement, $level = 0) {
		$null = null;
		
		if (is_null ( $source ))
			return $complement;
		
		if (is_null ( $complement ))
			return $source;
		
		if (is_scalar ( $source ) && is_scalar ( $complement ))
			return $complement;
		
		if (is_scalar ( $complement ) || is_scalar ( $source ))
			return $source;
		
		if ($level < 100) { // prevent infinite loop
			if (is_object ( $complement ))
				$complement = get_object_vars ( $complement );
			
			foreach ( $complement as $key => $value ) {
				if (is_object ( $source )) {
					if (isset ( $source->$key ))
						$source->$key = self::cop ( $source->$key, $value, $level + 1 );
					else
						$source->$key = self::cop ( $null, $value, $level + 1 );
				}
				if (is_array ( $source )) {
					if (isset ( $source [$key] ))
						$source [$key] = self::cop ( $source [$key], $value, $level + 1 );
					else
						$source [$key] = self::cop ( $null, $value, $level + 1 );
				}
			}
		}
		return $source;
	}
	
	/**
	 * Safe substring
	 *
	 * @param string $string        	
	 * @param integer $max_length        	
	 * @return string
	 */
	final static function substr($string, $max_length) {
		$max_length = max ( $max_length, 0 );
		if (strlen ( $string ) <= $max_length)
			return $string;
		$string = substr ( $string, 0, $max_length );
		return $string;
	}
	
	/**
	 * Return the teaser of a text
	 *
	 * @param string $text        	
	 * @param integer $maxlength        	
	 * @return string
	 */
	final static function teaser($text, $maxlength = 600) {
		$delimiter = strpos ( $text, DIV_TAG_TEASER_BREAK );
		if ($maxlength == 0 && $delimiter === false)
			return $text;
		if ($delimiter !== false)
			return substr ( $text, 0, $delimiter );
		if (strlen ( $text ) <= $maxlength)
			return $text;
		
		$summary = self::substr ( $text, $maxlength );
		
		$max_rpos = strlen ( $summary );
		$min_rpos = $max_rpos;
		$reversed = strrev ( $summary );
		$break_points = array ();
		$break_points [] = array (
				'</p>' => 0 
		);
		$line_breaks = array (
				'<br />' => 6,
				'<br>' => 4 
		);
		if (isset ( $filters ['filter_autop'] ))
			$line_breaks ["\n"] = 1;
		$break_points [] = $line_breaks;
		$break_points [] = array (
				'. ' => 1,
				'! ' => 1,
				'? ' => 1,
				'?' => 0,
				'? ' => 1 
		);
		
		foreach ( $break_points as $points ) {
			foreach ( $points as $point => $offset ) {
				$rpos = strpos ( $reversed, strrev ( $point ) );
				if ($rpos !== false)
					$min_rpos = min ( $rpos + $offset, $min_rpos );
			}
			if ($min_rpos !== $max_rpos) {
				$summary = ($min_rpos === 0) ? $summary : substr ( $summary, 0, 0 - $min_rpos );
				break;
			}
		}
		
		return $summary;
	}
	
	/**
	 * UTF utility
	 *
	 * @param string $utf16        	
	 * @return string
	 */
	final static function utf162utf8($utf16) {
		if (function_exists ( 'mb_convert_encoding' ))
			return mb_convert_encoding ( $utf16, 'UTF-8', 'UTF-16' );
		$bytes = (ord ( $utf16 {0} ) << 8) | ord ( $utf16 {1} );
		
		if ((0x7F & $bytes) == $bytes)
			return chr ( 0x7F & $bytes );
		if ((0x07FF & $bytes) == $bytes)
			return chr ( 0xC0 | (($bytes >> 6) & 0x1F) ) . chr ( 0x80 | ($bytes & 0x3F) );
		if ((0xFFFF & $bytes) == $bytes)
			return chr ( 0xE0 | (($bytes >> 12) & 0x0F) ) . chr ( 0x80 | (($bytes >> 6) & 0x3F) ) . chr ( 0x80 | ($bytes & 0x3F) );
		
		return '';
	}
	
	/**
	 * JSON Decode
	 *
	 * @param string $str        	
	 * @return mixed
	 */
	final static function jsonDecode($str, $items = array(), &$missing_vars = array
        ()) {
		$str = trim ( preg_replace ( array (
				'#^\s*//(.+)$#m',
				'#^\s*/\*(.+)\*/#Us',
				'#/\*(.+)\*/\s*$#Us' 
		), '', $str ) );
		
		// Syntax specific for div
		if (isset ( $str [0] )) {
			if ($str [0] == '$') {
				$str = substr ( $str, 1 );
				
				$r = null;
				
				if (self::issetVar ( $str, $items ))
					$r = self::getVarValue ( $str, $items );
				else
					$missing_vars [] = $str;
				
				return $r;
			}
		}
		
		switch (strtolower ( $str )) {
			case 'true' :
				return true;
			case 'false' :
				return false;
			case 'null' :
				return null;
			default :
				$m = array ();
				
				if (is_numeric ( $str )) {
					return (( float ) $str == ( integer ) $str) ? ( integer ) $str : ( float ) $str;
				} elseif (preg_match ( '/^("|\').*(\1)$/s', $str, $m ) && $m [1] == $m [2]) {
					$delim = substr ( $str, 0, 1 );
					$chrs = substr ( $str, 1, - 1 );
					$utf8 = '';
					$strlen_chrs = strlen ( $chrs );
					
					for($c = 0; $c < $strlen_chrs; ++ $c) {
						
						$substr_chrs_c_2 = substr ( $chrs, $c, 2 );
						$ord_chrs_c = ord ( $chrs {$c} );
						
						switch (true) {
							case $substr_chrs_c_2 == '\b' :
								$utf8 .= chr ( 0x08 );
								++ $c;
								break;
							case $substr_chrs_c_2 == '\t' :
								$utf8 .= chr ( 0x09 );
								++ $c;
								break;
							case $substr_chrs_c_2 == '\n' :
								$utf8 .= chr ( 0x0A );
								++ $c;
								break;
							case $substr_chrs_c_2 == '\f' :
								$utf8 .= chr ( 0x0C );
								++ $c;
								break;
							case $substr_chrs_c_2 == '\r' :
								$utf8 .= chr ( 0x0D );
								++ $c;
								break;
							case $substr_chrs_c_2 == '\\"' :
							case $substr_chrs_c_2 == '\\\'' :
							case $substr_chrs_c_2 == '\\\\' :
							case $substr_chrs_c_2 == '\\/' :
								if (($delim == '"' && $substr_chrs_c_2 != '\\\'') || ($delim == "'" && $substr_chrs_c_2 != '\\"'))
									$utf8 .= $chrs {++ $c};
								break;
							case preg_match ( '/\\\u[0-9A-F]{4}/i', substr ( $chrs, $c, 6 ) ) :
								$utf16 = chr ( hexdec ( substr ( $chrs, ($c + 2), 2 ) ) ) . chr ( hexdec ( substr ( $chrs, ($c + 4), 2 ) ) );
								$utf8 .= self::utf162utf8 ( $utf16 );
								$c += 5;
								break;
							case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F) :
								$utf8 .= $chrs {$c};
								break;
							case ($ord_chrs_c & 0xE0) == 0xC0 :
								$utf8 .= substr ( $chrs, $c, 2 );
								++ $c;
								break;
							case ($ord_chrs_c & 0xF0) == 0xE0 :
								$utf8 .= substr ( $chrs, $c, 3 );
								$c += 2;
								break;
							case ($ord_chrs_c & 0xF8) == 0xF0 :
								$utf8 .= substr ( $chrs, $c, 4 );
								$c += 3;
								break;
							case ($ord_chrs_c & 0xFC) == 0xF8 :
								$utf8 .= substr ( $chrs, $c, 5 );
								$c += 4;
								break;
							case ($ord_chrs_c & 0xFE) == 0xFC :
								$utf8 .= substr ( $chrs, $c, 6 );
								$c += 5;
								break;
						}
					}
					return $utf8;
				} elseif (preg_match ( '/^\[.*\]$/s', $str ) || preg_match ( '/^\{.*\}$/s', $str )) {
					if ($str {0} == '[') {
						$stk = array (
								3 
						);
						$arr = array ();
					} else {
						if (true & 16) {
							$stk = array (
									4 
							);
							$obj = array ();
						} else {
							$stk = array (
									4 
							);
							$obj = new stdClass ();
						}
					}
					
					array_push ( $stk, array (
							'what' => 1,
							'where' => 0,
							'delim' => false 
					) );
					
					$chrs = substr ( $str, 1, - 1 );
					$chrs = trim ( preg_replace ( array (
							'#^\s*//(.+)$#m',
							'#^\s*/\*(.+)\*/#Us',
							'#/\*(.+)\*/\s*$#Us' 
					), '', $chrs ) );
					
					if ($chrs == '')
						if (reset ( $stk ) == 3)
							return $arr;
						else
							return $obj;
					
					$strlen_chrs = strlen ( $chrs );
					
					for($c = 0; $c <= $strlen_chrs; ++ $c) {
						$top = end ( $stk );
						$substr_chrs_c_2 = substr ( $chrs, $c, 2 );
						
						if (($c == $strlen_chrs) || (($chrs {$c} == ',') && ($top ['what'] == 1))) {
							$slice = substr ( $chrs, $top ['where'], ($c - $top ['where']) );
							array_push ( $stk, array (
									'what' => 1,
									'where' => ($c + 1),
									'delim' => false 
							) );
							
							if (reset ( $stk ) == 3) {
								array_push ( $arr, self::jsonDecode ( $slice, $items ) );
							} elseif (reset ( $stk ) == 4) {
								$parts = array ();
								if (preg_match ( '/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts )) {
									$key = self::jsonDecode ( $parts [1], $items );
									$val = self::jsonDecode ( $parts [2], $items );
									
									if (true & 16)
										$obj [$key] = $val;
									else
										$obj->$key = $val;
								} elseif (preg_match ( '/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts )) {
									$key = $parts [1];
									$val = self::jsonDecode ( $parts [2], $items );
									
									if (true & 16) {
										$obj [$key] = $val;
									} else {
										$obj->$key = $val;
									}
								}
							}
						} elseif ((($chrs {$c} == '"') || ($chrs {$c} == "'")) && ($top ['what'] != 2)) {
							array_push ( $stk, array (
									'what' => 2,
									'where' => $c,
									'delim' => $chrs {$c} 
							) );
						} elseif (($chrs {$c} == $top ['delim']) && ($top ['what'] == 2) && ((strlen ( substr ( $chrs, 0, $c ) ) - strlen ( rtrim ( substr ( $chrs, 0, $c ), '\\' ) )) % 2 != 1)) {
							array_pop ( $stk );
						} elseif (($chrs {$c} == '[') && in_array ( $top ['what'], array (
								1,
								3,
								4 
						) )) {
							array_push ( $stk, array (
									'what' => 3,
									'where' => $c,
									'delim' => false 
							) );
						} elseif (($chrs {$c} == ']') && ($top ['what'] == 3)) {
							array_pop ( $stk );
						} elseif (($chrs {$c} == '{') && in_array ( $top ['what'], array (
								1,
								3,
								4 
						) )) {
							array_push ( $stk, array (
									'what' => 4,
									'where' => $c,
									'delim' => false 
							) );
						} elseif (($chrs {$c} == '}') && ($top ['what'] == 4)) {
							array_pop ( $stk );
						} elseif (($substr_chrs_c_2 == '/*') && in_array ( $top ['what'], array (
								1,
								3,
								4 
						) )) {
							array_push ( $stk, array (
									'what' => 5,
									'where' => $c,
									'delim' => false 
							) );
							$c ++;
						} elseif (($substr_chrs_c_2 == '*/') && ($top ['what'] == 5)) {
							array_pop ( $stk );
							$c ++;
							for($i = $top ['where']; $i <= $c; ++ $i)
								$chrs = substr_replace ( $chrs, ' ', $i, 1 );
						}
					}
					
					if (reset ( $stk ) == 3) {
						return $arr;
					} elseif (reset ( $stk ) == 4) {
						return $obj;
					}
				}
		}
	}
	
	/**
	 * JSON Encode
	 *
	 * @param mixed $data        	
	 * @return string
	 */
	final static function jsonEncode($data) {
		if (is_array ( $data ) || is_object ( $data )) {
			$islist = is_array ( $data ) && (empty ( $data ) || array_keys ( $data ) === range ( 0, count ( $data ) - 1 ));
			
			if ($islist)
				$json = '[' . implode ( ',', array_map ( 'div::jsonEncode', $data ) ) . ']';
			else {
				$items = array ();
				foreach ( $data as $key => $value ) {
					$items [] = self::jsonEncode ( "$key" ) . ':' . self::jsonEncode ( $value );
				}
				$json = '{' . implode ( ',', $items ) . '}';
			}
		} elseif (self::isString ( $data )) {
			$string = '"' . addcslashes ( $data, "\\\"\n\r\t/" . chr ( 8 ) . chr ( 12 ) ) . '"';
			$json = '';
			$len = strlen ( $string );
			for($i = 0; $i < $len; $i ++) {
				$char = $string [$i];
				$c1 = ord ( $char );
				if ($c1 < 128) {
					$json .= ($c1 > 31) ? $char : sprintf ( "\\u%04x", $c1 );
					continue;
				}
				$c2 = ord ( $string [++ $i] );
				if (($c1 & 32) === 0) {
					$json .= sprintf ( "\\u%04x", ($c1 - 192) * 64 + $c2 - 128 );
					continue;
				}
				$c3 = ord ( $string [++ $i] );
				if (($c1 & 16) === 0) {
					$json .= sprintf ( "\\u%04x", (($c1 - 224) << 12) + (($c2 - 128) << 6) + ($c3 - 128) );
					continue;
				}
				$c4 = ord ( $string [++ $i] );
				if (($c1 & 8) === 0) {
					$u = (($c1 & 15) << 2) + (($c2 >> 4) & 3) - 1;
					
					$w1 = (54 << 10) + ($u << 6) + (($c2 & 15) << 2) + (($c3 >> 4) & 3);
					$w2 = (55 << 10) + (($c3 & 15) << 6) + ($c4 - 128);
					$json .= sprintf ( "\\u%04x\\u%04x", $w1, $w2 );
				}
			}
		} else
			$json = strtolower ( var_export ( $data, true ) );
		
		return $json;
	}
	
	/**
	 * Convert HTML to plain and formated text
	 *
	 * @param string $html        	
	 * @return string
	 */
	static function htmlToText($html, $width = 50) {
		
		// Special strip tags
		$newhtml = '';
		do {
			$p1 = strpos ( $html, "<style" );
			$p2 = strpos ( $html, "</style>" );
			
			if ($p1 !== false && $p2 !== false) {
				if ($p2 > $p1) {
					$newhtml .= substr ( $html, 0, $p1 );
					$newhtml .= substr ( $html, $p2 + 8 );
					$html = substr ( $html, $p2 + 8 );
				} else
					break;
			}
		} while ( $p1 !== false && $p2 !== false );
		
		if ($newhtml != '')
			$html = $newhtml;
			
			// Other stuffs
		
		$html = str_replace ( "<br>", "\n", $html );
		$html = str_replace ( "<br/>", "\n", $html );
		$html = str_replace ( "<br />", "\n", $html );
		$html = str_replace ( "</tr>", "\n", $html );
		$html = str_replace ( "<td", "\t</td", $html );
		$html = str_replace ( "<th", "\t</th", $html );
		$html = str_replace ( "</table>", "\n", $html );
		$hr = str_repeat ( "-", $width ) . "\n";
		$html = str_replace ( "<hr>", $hr, $html );
		$html = str_replace ( "<hr/>", $hr, $html );
		$html = str_replace ( "</p>", "\n", $html );
		$html = str_replace ( "<h1", "- <h1" . $hr, $html );
		$html = str_replace ( "<h2", "-- <h2" . $hr, $html );
		$html = str_replace ( "<h3", "--- <h3" . $hr, $html );
		$html = str_replace ( "<li", "* <li" . $hr, $html );
		
		for($i = 1; $i < 5; $i ++) {
			$html = str_replace ( "</h$i>\n", "</h$i>" . $hr, $html );
			$html = str_replace ( "</h$i>", "</h$i>\n\n" . $hr, $html );
		}
		
		$html = html_entity_decode ( $html );
		$html = preg_replace ( '!<[^>]*?>!', ' ', $html );
		$html = str_replace ( "\t", ' ', $html );
		
		// Strip tags
		$html = preg_replace ( "/\015\012|\015|\012/", "\n", $html );
		$html = strip_tags ( $html );
		
		while ( strpos ( $html, '  ' ) !== false )
			$html = str_replace ( '  ', ' ', $html );
		$html = str_replace ( ' ' . "\n", "\n", $html );
		$html = str_replace ( "\n ", "\n", $html );
		
		while ( strpos ( $html, '  ' ) !== false )
			$html = str_replace ( '  ', ' ', $html );
		$html = trim ( $html );
		if (! is_null ( $width ) && $width !== 0)
			$html = wordwrap ( $html, $width, "\n" );
		
		return $html;
	}
	
	/**
	 * Return true if at least one needle is contained in the haystack
	 *
	 * @param string $haystack        	
	 * @param array $needles        	
	 * @return boolean
	 */
	final static function atLeastOneString($haystack, $needles = array()) {
		foreach ( $needles as $needle ) {
			if (strpos ( $haystack, $needle ) !== false)
				return true;
		}
		return false;
	}
	
	/**
	 * Return the last key of array or null if not exists
	 *
	 * @param array $arr        	
	 * @return mixed
	 */
	final static function getLastKeyOfArray($arr) {
		if (is_array ( $arr )) {
			if (count ( $arr ) > 0) {
				$keys = array_keys ( $arr );
				$keys = array_reverse ( $keys );
				return $keys [0];
			}
		}
	}
	
	/**
	 * Return true if var exists in the template's items recursively
	 *
	 * @param string $var        	
	 * @param mixed $items        	
	 * @return boolean
	 */
	final static function varExists($var, &$items = null) {
		if (is_null ( $items ))
			return false;
		
		$subvars = explode ( DIV_TAG_VAR_MEMBER_DELIMITER, $var );
		
		if (count ( $subvars ) === 1) {
			if (is_array ( $items ))
				return isset ( $items [$var] );
			if (is_object ( $items ))
				return isset ( $items->$var );
		} else {
			$l = strlen ( $subvars [0] );
			if ($l + 1 < strlen ( $var )) {
				if (is_array ( $items ))
					return self::varExists ( substr ( $var, $l + 1 ), $items [$subvars [0]] );
				if (is_object ( $items ))
					return self::varExists ( substr ( $var, $l + 1 ), $items->$subvars [0] );
			}
		}
		
		return false;
	}
	
	/**
	 * Return the first instance of $this->__packages
	 */
	static function getPackagesPath() {
		$class = get_class ();
		if (isset ( self::$__packages_by_class [$class] ))
			return self::$__packages_by_class [$class];
		return PACKAGES;
	}
	
	/**
	 * Secure 'file exists' method
	 *
	 * @param string $filename        	
	 * @return boolean
	 */
	final static function fileExists($filename) {
		if (substr ( strtolower ( $filename ), 0, 7 ) == 'http://' || substr ( strtolower ( $filename ), 0, 8 ) == 'https://' || substr ( strtolower ( $filename ), 0, 6 ) == 'ftp://')
			return false;
		
		if (strlen ( $filename ) > DIV_MAX_FILENAME_SIZE)
			return false;
		
		if (file_exists ( $filename ))
			if (is_file ( $filename ))
				return true;
		
		$ipaths = self::getIncludePaths ( self::getPackagesPath () );
		foreach ( $ipaths as $ipath ) {
			
			$pathx = str_replace ( "\\", "/", $ipath . "/" . $filename );
			while ( strpos ( $pathx, "//" ) !== false ) {
				$pathx = str_replace ( "//", "/", $pathx );
			}
			$pathx = str_replace ( "/./", "/", $pathx );
			if (substr ( $pathx, 0, 2 ) == "./")
				$pathx = substr ( $pathx, 2 );
			
			if (@file_exists ( $pathx ))
				if (@is_file ( $pathx ))
					return true;
		}
		
		return false;
	}
	
	/**
	 * Secure 'is dir' method
	 *
	 * @param string $filename        	
	 * @return boolean
	 */
	final static function isDir($dirname) {
		if (substr ( strtolower ( $dirname ), 0, 7 ) == 'http://' || substr ( strtolower ( $dirname ), 0, 8 ) == 'https://' || substr ( strtolower ( $dirname ), 0, 6 ) == 'ftp://')
			return false;
		
		if (strlen ( $dirname ) > DIV_MAX_FILENAME_SIZE)
			return false;
		return is_dir ( $dirname );
	}
	
	/**
	 * Secure 'file get contents' method
	 *
	 * @param string $filename        	
	 * @return string
	 */
	final static function getFileContents($filename) {
		if (substr ( strtolower ( $filename ), 0, 7 ) == 'http://' || substr ( strtolower ( $filename ), 0, 8 ) == 'https://' || substr ( strtolower ( $filename ), 0, 6 ) == 'ftp://')
			return $filename;
		
		if (file_exists ( $filename ))
			return file_get_contents ( $filename );
		
		$ipaths = self::getIncludePaths ( self::getPackagesPath () );
		
		foreach ( $ipaths as $ipath ) {
			$pathx = str_replace ( "\\", "/", $ipath . "/" . $filename );
			while ( strpos ( $pathx, "//" ) !== false ) {
				$pathx = str_replace ( "//", "/", $pathx );
			}
			$pathx = str_replace ( "/./", "/", $pathx );
			if (substr ( $pathx, 0, 2 ) == "./")
				$pathx = substr ( $pathx, 2 );
			
			if (file_exists ( $pathx ))
				if (is_file ( $pathx ))
					return file_get_contents ( $pathx );
		}
		
		return null;
	}
	
	/**
	 * Get folder of path/file
	 *
	 * @param string $filename        	
	 * @return string
	 */
	final static function getFolderOf($filename) {
		if (is_dir ( $filename ))
			return $filename;
		$p = strrpos ( $filename, "/" );
		if ($p === false)
			return "./";
		$folder = substr ( $filename, 0, $p );
		return $folder;
	}
	
	/**
	 * Return mixed value as HTML format, (util for debug and fast presentation)
	 *
	 * @param mixed $mixed        	
	 * @return string
	 */
	final static function asThis($mixed) {
		$html = "";
		if (is_array ( $mixed )) {
			if (self::isArrayOfArray ( $mixed ) === true) {
				$html = "<table>";
				
				// header
				foreach ( $mixed as $key_row => $row ) {
					$html .= "<tr>";
					foreach ( $row as $key_col => $col )
						$html .= "<th>$key_col</th>";
					$html .= "</tr>";
					break;
				}
				
				// rows
				foreach ( $mixed as $key_row => $row ) {
					$html .= "<tr>";
					foreach ( $row as $key_col => $col ) {
						$html .= "<td>" . self::asThis ( $col ) . "</td>";
					}
					$html .= "</tr>";
				}
				$html .= "</table>";
			} elseif (self::isArrayOfObjects ( $mixed )) {
				
				$html = "<table>";
				
				// header
				foreach ( $mixed as $key_row => $row ) {
					$html .= "<tr>";
					$vars = get_object_vars ( $row );
					
					foreach ( $vars as $key_col => $col ) {
						$html .= "<th>$key_col</th>";
					}
					$html .= "</tr>";
					break;
				}
				
				// rows
				foreach ( $mixed as $key_row => $row ) {
					$vars = get_object_vars ( $row );
					$html .= "<tr>";
					foreach ( $vars as $key_col => $col ) {
						$html .= "<td>" . self::asThis ( $col ) . "</td>";
					}
					$html .= "</tr>";
				}
				$html .= "</table>";
			} elseif (self::isNumericList ( $mixed )) {
				$html = "<table class \"numeric-list\">";
				foreach ( $mixed as $key => $v ) {
					$html .= "<td>$v</td>";
				}
				$html .= "</table>";
			} else {
				$html = "<ul class = \"array\">";
				foreach ( $mixed as $key => $value ) {
					$t = "";
					if (! is_numeric ( $key ) && trim ( "$key" ) != "" && $key != null)
						$t = "$key: <br>";
					$html .= "<li> " . self::asThis ( $value ) . "</li>";
				}
				$html .= "</ul>";
			}
		} else {
			if (is_object ( $mixed )) {
				$html = get_class ( $mixed ) . ": <table>";
				$vars = get_object_vars ( $mixed );
				
				foreach ( $vars as $var => $value ) {
					$html .= "<li>" . self::asThis ( $mixed->$var ) . "</li>";
				}
				$html .= "</ul>";
			} else {
				if (is_bool ( $mixed ))
					$html = ($mixed === true ? "TRUE" : "FALSE");
				else {
					$html = "<label>$mixed</label>";
				}
			}
		}
		
		return $html;
	}
	
	/**
	 * Count a number of paragraphs in a text
	 *
	 * @param string $value        	
	 * @return integer
	 */
	static function getCountOfParagraphs($text) {
		return count ( preg_split ( '/[\r\n]+/', $text ) );
	}
	
	/**
	 * Count a number of sentences in a text
	 *
	 * @param string $value        	
	 * @return integer
	 */
	static function getCountOfSentences($text) {
		return preg_match_all ( '/[^\s]\.(?!\w)/', $text, $match );
	}
	
	/**
	 * Count a number of words in a text
	 *
	 * @param string $value        	
	 * @return integer
	 */
	static function getCountOfWords($text) {
		$split_array = preg_split ( '/\s+/', $text );
		$word_count = preg_grep ( '/[a-zA-Z0-9\\x80-\\xff]/', $split_array );
		return count ( $word_count );
	}
	
	/**
	 * Return true if $arr is array of array
	 *
	 * @param array $arr        	
	 * @return boolean
	 */
	final static function isArrayOfArray($arr) {
		$is = false;
		if (is_array ( $arr )) {
			$is = true;
			foreach ( $arr as $v ) {
				if (! is_array ( $v )) {
					$is = false;
					break;
				}
			}
		}
		return $is;
	}
	
	/**
	 * Return true if $arr is array of objects
	 *
	 * @param array $arr        	
	 * @return boolean
	 */
	final static function isArrayOfObjects($arr) {
		$is = false;
		if (is_array ( $arr )) {
			$is = true;
			foreach ( $arr as $v ) {
				if (! is_object ( $v )) {
					$is = false;
					break;
				}
			}
		}
		return $is;
	}
	
	/**
	 * Return true if $arr is array of numbers
	 *
	 * @param array $arr        	
	 * @return boolean
	 */
	final static function isNumericList($arr) {
		$is = false;
		if (is_array ( $arr )) {
			$is = true;
			foreach ( $arr as $v ) {
				if (! is_numeric ( $v )) {
					$is = false;
					break;
				}
			}
		}
		return $is;
	}
	
	/**
	 * Return a list of vars from PHP code
	 *
	 * @param string $code        	
	 * @return array
	 */
	final static function getVarsFromCode($code) {
		$t = token_get_all ( "<?php $code ?>" );
		$vars = array ();
		foreach ( $t as $key => $value ) {
			if (is_array ( $value )) {
				if ($value [0] == T_VARIABLE) {
					$vars [] = substr ( $value [1], 1 );
				}
			}
		}
		return $vars;
	}
	
	/**
	 * Return true if the PHP code have any var
	 *
	 * @param string $code        	
	 * @return bool
	 */
	final static function haveVarsThisCode($code) {
		$vars = self::getVarsFromCode ( $code );
		if (count ( $vars ) > 0)
			return true;
		return false;
	}
	
	/**
	 * Return true if current dialect is valid
	 *
	 * @return mixed
	 */
	final static function isValidCurrentDialect() {
		
		// TODO: Improve this syntax checker
		$all_tags = array (
				DIV_TAG_REPLACEMENT_PREFIX,
				DIV_TAG_REPLACEMENT_SUFFIX,
				DIV_TAG_MULTI_MODIFIERS_PREFIX,
				DIV_TAG_MULTI_MODIFIERS_OPERATOR,
				DIV_TAG_MULTI_MODIFIERS_SEPARATOR,
				DIV_TAG_MULTI_MODIFIERS_SUFFIX,
				DIV_TAG_SUBMATCH_SEPARATOR,
				DIV_TAG_MODIFIER_SIMPLE,
				DIV_TAG_MODIFIER_CAPITALIZE_FIRST,
				DIV_TAG_MODIFIER_CAPITALIZE_WORDS,
				DIV_TAG_MODIFIER_UPPERCASE,
				DIV_TAG_MODIFIER_LOWERCASE,
				DIV_TAG_MODIFIER_LENGTH,
				DIV_TAG_MODIFIER_COUNT_WORDS,
				DIV_TAG_MODIFIER_COUNT_SENTENCES,
				DIV_TAG_MODIFIER_COUNT_PARAGRAPHS,
				DIV_TAG_MODIFIER_ENCODE_URL,
				DIV_TAG_MODIFIER_ENCODE_RAW_URL,
				DIV_TAG_MODIFIER_ENCODE_JSON,
				DIV_TAG_MODIFIER_HTML_ENTITIES,
				DIV_TAG_MODIFIER_NL2BR,
				DIV_TAG_MODIFIER_TRUNCATE,
				DIV_TAG_MODIFIER_WORDWRAP,
				DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR,
				DIV_TAG_MODIFIER_SINGLE_QUOTES,
				DIV_TAG_MODIFIER_JS,
				DIV_TAG_MODIFIER_FORMAT,
				DIV_TAG_DATE_FORMAT_PREFIX,
				DIV_TAG_DATE_FORMAT_SUFFIX,
				DIV_TAG_DATE_FORMAT_SEPARATOR,
				DIV_TAG_NUMBER_FORMAT_PREFIX,
				DIV_TAG_NUMBER_FORMAT_SUFFIX,
				DIV_TAG_NUMBER_FORMAT_SEPARATOR,
				DIV_TAG_FORMULA_BEGIN,
				DIV_TAG_FORMULA_END,
				DIV_TAG_FORMULA_FORMAT_SEPARATOR,
				DIV_TAG_SUBPARSER_BEGIN_PREFIX,
				DIV_TAG_SUBPARSER_BEGIN_SUFFIX,
				DIV_TAG_SUBPARSER_END_PREFIX,
				DIV_TAG_SUBPARSER_END_SUFFIX,
				DIV_TAG_IGNORE_BEGIN,
				DIV_TAG_IGNORE_END,
				DIV_TAG_COMMENT_BEGIN,
				DIV_TAG_COMMENT_END,
				DIV_TAG_TXT_BEGIN,
				DIV_TAG_TXT_END,
				DIV_TAG_TXT_WIDTH_SEPARATOR,
				DIV_TAG_STRIP_BEGIN,
				DIV_TAG_STRIP_END,
				DIV_TAG_LOOP_BEGIN_PREFIX,
				DIV_TAG_LOOP_BEGIN_SUFFIX,
				DIV_TAG_LOOP_END_PREFIX,
				DIV_TAG_LOOP_END_SUFFIX,
				DIV_TAG_EMPTY,
				DIV_TAG_BREAK,
				DIV_TAG_LOOP_VAR_SEPARATOR,
				DIV_TAG_ITERATION_BEGIN_PREFIX,
				DIV_TAG_ITERATION_BEGIN_SUFFIX,
				DIV_TAG_ITERATION_END,
				DIV_TAG_ITERATION_PARAM_SEPARATOR,
				DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX,
				DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX,
				DIV_TAG_CONDITIONAL_TRUE_END_PREFIX,
				DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX,
				DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX,
				DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX,
				DIV_TAG_CONDITIONAL_FALSE_END_PREFIX,
				DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX,
				DIV_TAG_ELSE,
				DIV_TAG_CONDITIONS_BEGIN_PREFIX,
				DIV_TAG_CONDITIONS_BEGIN_SUFFIX,
				DIV_TAG_CONDITIONS_END,
				DIV_TAG_TPLVAR_BEGIN,
				DIV_TAG_TPLVAR_END,
				DIV_TAG_TPLVAR_ASSIGN_OPERATOR,
				DIV_TAG_TPLVAR_PROTECTOR,
				DIV_TAG_DEFAULT_REPLACEMENT_BEGIN,
				DIV_TAG_DEFAULT_REPLACEMENT_END,
				DIV_TAG_INCLUDE_BEGIN,
				DIV_TAG_INCLUDE_END,
				DIV_TAG_PREPROCESSED_BEGIN,
				DIV_TAG_PREPROCESSED_END,
				DIV_TAG_PREPROCESSED_SEPARATOR,
				DIV_TAG_CAPSULE_BEGIN_PREFIX,
				DIV_TAG_CAPSULE_BEGIN_SUFFIX,
				DIV_TAG_CAPSULE_END_PREFIX,
				DIV_TAG_CAPSULE_END_SUFFIX,
				DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX,
				DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX,
				DIV_TAG_MULTI_REPLACEMENT_END_PREFIX,
				DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX,
				DIV_TAG_FRIENDLY_BEGIN,
				DIV_TAG_FRIENDLY_END,
				DIV_TAG_AGGREGATE_FUNCTION_COUNT,
				DIV_TAG_AGGREGATE_FUNCTION_MAX,
				DIV_TAG_AGGREGATE_FUNCTION_MIN,
				DIV_TAG_AGGREGATE_FUNCTION_SUM,
				DIV_TAG_AGGREGATE_FUNCTION_AVG,
				DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR,
				DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR,
				DIV_TAG_LOCATION_BEGIN,
				DIV_TAG_LOCATION_END,
				DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX,
				DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX,
				DIV_TAG_LOCATION_CONTENT_END_PREFIX,
				DIV_TAG_LOCATION_CONTENT_END_SUFFIX,
				DIV_TAG_MACRO_BEGIN,
				DIV_TAG_MACRO_END,
				DIV_TAG_SPECIAL_REPLACE_NEW_LINE,
				DIV_TAG_SPECIAL_REPLACE_CAR_RETURN,
				DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE,
				DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL 
		);
		
		// Required tags
		$names = array (
				'DIV_TAG_REPLACEMENT_PREFIX',
				'DIV_TAG_REPLACEMENT_SUFFIX',
				'DIV_TAG_MULTI_MODIFIERS_PREFIX',
				'DIV_TAG_MULTI_MODIFIERS_OPERATOR',
				'DIV_TAG_MULTI_MODIFIERS_SEPARATOR',
				'DIV_TAG_MULTI_MODIFIERS_SUFFIX',
				'DIV_TAG_SUBMATCH_SEPARATOR',
				'DIV_TAG_MODIFIER_SIMPLE',
				'DIV_TAG_MODIFIER_CAPITALIZE_FIRST',
				'DIV_TAG_MODIFIER_CAPITALIZE_WORDS',
				'DIV_TAG_MODIFIER_UPPERCASE',
				'DIV_TAG_MODIFIER_LOWERCASE',
				'DIV_TAG_MODIFIER_LENGTH',
				'DIV_TAG_MODIFIER_COUNT_WORDS',
				'DIV_TAG_MODIFIER_COUNT_SENTENCES',
				'DIV_TAG_MODIFIER_COUNT_PARAGRAPHS',
				'DIV_TAG_MODIFIER_ENCODE_URL',
				'DIV_TAG_MODIFIER_ENCODE_RAW_URL',
				'DIV_TAG_MODIFIER_ENCODE_JSON',
				'DIV_TAG_MODIFIER_HTML_ENTITIES',
				'DIV_TAG_MODIFIER_NL2BR',
				'DIV_TAG_MODIFIER_TRUNCATE',
				'DIV_TAG_MODIFIER_WORDWRAP',
				'DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR',
				'DIV_TAG_MODIFIER_SINGLE_QUOTES',
				'DIV_TAG_MODIFIER_JS',
				'DIV_TAG_DATE_FORMAT_PREFIX',
				'DIV_TAG_DATE_FORMAT_SUFFIX',
				'DIV_TAG_DATE_FORMAT_SEPARATOR',
				'DIV_TAG_NUMBER_FORMAT_PREFIX',
				'DIV_TAG_NUMBER_FORMAT_SUFFIX',
				'DIV_TAG_NUMBER_FORMAT_SEPARATOR',
				'DIV_TAG_FORMULA_BEGIN',
				'DIV_TAG_FORMULA_END',
				'DIV_TAG_FORMULA_FORMAT_SEPARATOR',
				'DIV_TAG_SUBPARSER_BEGIN_PREFIX',
				'DIV_TAG_SUBPARSER_END_SUFFIX',
				'DIV_TAG_IGNORE_BEGIN',
				'DIV_TAG_IGNORE_END',
				'DIV_TAG_COMMENT_BEGIN',
				'DIV_TAG_COMMENT_END',
				'DIV_TAG_TXT_BEGIN',
				'DIV_TAG_TXT_END',
				'DIV_TAG_TXT_WIDTH_SEPARATOR',
				'DIV_TAG_STRIP_BEGIN',
				'DIV_TAG_STRIP_END',
				'DIV_TAG_LOOP_BEGIN_PREFIX',
				'DIV_TAG_LOOP_END_SUFFIX',
				'DIV_TAG_EMPTY',
				'DIV_TAG_BREAK',
				'DIV_TAG_LOOP_VAR_SEPARATOR',
				'DIV_TAG_ITERATION_BEGIN_PREFIX',
				'DIV_TAG_ITERATION_BEGIN_SUFFIX',
				'DIV_TAG_ITERATION_END',
				'DIV_TAG_ITERATION_PARAM_SEPARATOR',
				'DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX',
				'DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX',
				'DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX',
				'DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX',
				'DIV_TAG_ELSE',
				'DIV_TAG_CONDITIONS_BEGIN_PREFIX',
				'DIV_TAG_CONDITIONS_BEGIN_SUFFIX',
				'DIV_TAG_CONDITIONS_END',
				'DIV_TAG_TPLVAR_BEGIN',
				'DIV_TAG_TPLVAR_END',
				'DIV_TAG_TPLVAR_ASSIGN_OPERATOR',
				'DIV_TAG_TPLVAR_PROTECTOR',
				'DIV_TAG_DEFAULT_REPLACEMENT_BEGIN',
				'DIV_TAG_DEFAULT_REPLACEMENT_END',
				'DIV_TAG_INCLUDE_BEGIN',
				'DIV_TAG_INCLUDE_END',
				'DIV_TAG_PREPROCESSED_BEGIN',
				'DIV_TAG_PREPROCESSED_END',
				'DIV_TAG_PREPROCESSED_SEPARATOR',
				'DIV_TAG_CAPSULE_BEGIN_PREFIX',
				'DIV_TAG_CAPSULE_END_SUFFIX',
				'DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX',
				'DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX',
				'DIV_TAG_FRIENDLY_BEGIN',
				'DIV_TAG_FRIENDLY_END',
				'DIV_TAG_AGGREGATE_FUNCTION_COUNT',
				'DIV_TAG_AGGREGATE_FUNCTION_MAX',
				'DIV_TAG_AGGREGATE_FUNCTION_MIN',
				'DIV_TAG_AGGREGATE_FUNCTION_SUM',
				'DIV_TAG_AGGREGATE_FUNCTION_AVG',
				'DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR',
				'DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR',
				'DIV_TAG_LOCATION_BEGIN',
				'DIV_TAG_LOCATION_END',
				'DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX',
				'DIV_TAG_LOCATION_CONTENT_END_SUFFIX',
				'DIV_TAG_MACRO_BEGIN',
				'DIV_TAG_MACRO_END',
				'DIV_TAG_SPECIAL_REPLACE_NEW_LINE',
				'DIV_TAG_SPECIAL_REPLACE_CAR_RETURN',
				'DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB',
				'DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB',
				'DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE',
				'DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL',
				'DIV_TAG_TEASER_BREAK' 
		);
		
		$r = array (
				DIV_TAG_REPLACEMENT_PREFIX,
				DIV_TAG_REPLACEMENT_SUFFIX,
				DIV_TAG_MULTI_MODIFIERS_PREFIX,
				DIV_TAG_MULTI_MODIFIERS_OPERATOR,
				DIV_TAG_MULTI_MODIFIERS_SEPARATOR,
				DIV_TAG_MULTI_MODIFIERS_SUFFIX,
				DIV_TAG_SUBMATCH_SEPARATOR,
				DIV_TAG_MODIFIER_SIMPLE,
				DIV_TAG_MODIFIER_CAPITALIZE_FIRST,
				DIV_TAG_MODIFIER_CAPITALIZE_WORDS,
				DIV_TAG_MODIFIER_UPPERCASE,
				DIV_TAG_MODIFIER_LOWERCASE,
				DIV_TAG_MODIFIER_LENGTH,
				DIV_TAG_MODIFIER_COUNT_WORDS,
				DIV_TAG_MODIFIER_COUNT_SENTENCES,
				DIV_TAG_MODIFIER_COUNT_PARAGRAPHS,
				DIV_TAG_MODIFIER_ENCODE_URL,
				DIV_TAG_MODIFIER_ENCODE_RAW_URL,
				DIV_TAG_MODIFIER_ENCODE_JSON,
				DIV_TAG_MODIFIER_HTML_ENTITIES,
				DIV_TAG_MODIFIER_NL2BR,
				DIV_TAG_MODIFIER_TRUNCATE,
				DIV_TAG_MODIFIER_WORDWRAP,
				DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR,
				DIV_TAG_MODIFIER_SINGLE_QUOTES,
				DIV_TAG_MODIFIER_JS,
				DIV_TAG_DATE_FORMAT_PREFIX,
				DIV_TAG_DATE_FORMAT_SUFFIX,
				DIV_TAG_DATE_FORMAT_SEPARATOR,
				DIV_TAG_NUMBER_FORMAT_PREFIX,
				DIV_TAG_NUMBER_FORMAT_SUFFIX,
				DIV_TAG_NUMBER_FORMAT_SEPARATOR,
				DIV_TAG_FORMULA_BEGIN,
				DIV_TAG_FORMULA_END,
				DIV_TAG_FORMULA_FORMAT_SEPARATOR,
				DIV_TAG_SUBPARSER_BEGIN_PREFIX,
				DIV_TAG_SUBPARSER_END_SUFFIX,
				DIV_TAG_IGNORE_BEGIN,
				DIV_TAG_IGNORE_END,
				DIV_TAG_COMMENT_BEGIN,
				DIV_TAG_COMMENT_END,
				DIV_TAG_TXT_BEGIN,
				DIV_TAG_TXT_END,
				DIV_TAG_TXT_WIDTH_SEPARATOR,
				DIV_TAG_STRIP_BEGIN,
				DIV_TAG_STRIP_END,
				DIV_TAG_LOOP_BEGIN_PREFIX,
				DIV_TAG_LOOP_END_SUFFIX,
				DIV_TAG_EMPTY,
				DIV_TAG_BREAK,
				DIV_TAG_LOOP_VAR_SEPARATOR,
				DIV_TAG_ITERATION_BEGIN_PREFIX,
				DIV_TAG_ITERATION_BEGIN_SUFFIX,
				DIV_TAG_ITERATION_END,
				DIV_TAG_ITERATION_PARAM_SEPARATOR,
				DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX,
				DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX,
				DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX,
				DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX,
				DIV_TAG_ELSE,
				DIV_TAG_CONDITIONS_BEGIN_PREFIX,
				DIV_TAG_CONDITIONS_BEGIN_SUFFIX,
				DIV_TAG_CONDITIONS_END,
				DIV_TAG_TPLVAR_BEGIN,
				DIV_TAG_TPLVAR_END,
				DIV_TAG_TPLVAR_ASSIGN_OPERATOR,
				DIV_TAG_TPLVAR_PROTECTOR,
				DIV_TAG_DEFAULT_REPLACEMENT_BEGIN,
				DIV_TAG_DEFAULT_REPLACEMENT_END,
				DIV_TAG_INCLUDE_BEGIN,
				DIV_TAG_INCLUDE_END,
				DIV_TAG_PREPROCESSED_BEGIN,
				DIV_TAG_PREPROCESSED_END,
				DIV_TAG_PREPROCESSED_SEPARATOR,
				DIV_TAG_CAPSULE_BEGIN_PREFIX,
				DIV_TAG_CAPSULE_END_SUFFIX,
				DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX,
				DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX,
				DIV_TAG_FRIENDLY_BEGIN,
				DIV_TAG_FRIENDLY_END,
				DIV_TAG_AGGREGATE_FUNCTION_COUNT,
				DIV_TAG_AGGREGATE_FUNCTION_MAX,
				DIV_TAG_AGGREGATE_FUNCTION_MIN,
				DIV_TAG_AGGREGATE_FUNCTION_SUM,
				DIV_TAG_AGGREGATE_FUNCTION_AVG,
				DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR,
				DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR,
				DIV_TAG_LOCATION_BEGIN,
				DIV_TAG_LOCATION_END,
				DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX,
				DIV_TAG_LOCATION_CONTENT_END_SUFFIX,
				DIV_TAG_MACRO_BEGIN,
				DIV_TAG_MACRO_END,
				DIV_TAG_SPECIAL_REPLACE_NEW_LINE,
				DIV_TAG_SPECIAL_REPLACE_CAR_RETURN,
				DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE,
				DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL,
				DIV_TAG_TEASER_BREAK 
		);
		
		$p = array_search ( '', $r, true );
		if ($p !== false)
			return $names [$p] . ' is required';
		$p = array_search ( null, $r, true );
		if ($p !== false)
			return $names [$p] . ' is required';
			
			// Unique tags
		$names = array (
				'DIV_TAG_MODIFIER_SIMPLE',
				'DIV_TAG_MODIFIER_CAPITALIZE_FIRST',
				'DIV_TAG_MODIFIER_CAPITALIZE_WORDS',
				'DIV_TAG_MODIFIER_UPPERCASE',
				'DIV_TAG_MODIFIER_LOWERCASE',
				'DIV_TAG_MODIFIER_LENGTH',
				'DIV_TAG_MODIFIER_COUNT_WORDS',
				'DIV_TAG_MODIFIER_COUNT_SENTENCES',
				'DIV_TAG_MODIFIER_COUNT_PARAGRAPHS',
				'DIV_TAG_MODIFIER_ENCODE_URL',
				'DIV_TAG_MODIFIER_ENCODE_RAW_URL',
				'DIV_TAG_MODIFIER_ENCODE_JSON',
				'DIV_TAG_MODIFIER_HTML_ENTITIES',
				'DIV_TAG_MODIFIER_NL2BR',
				'DIV_TAG_MODIFIER_TRUNCATE',
				'DIV_TAG_MODIFIER_WORDWRAP',
				'DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR',
				'DIV_TAG_MODIFIER_SINGLE_QUOTES',
				'DIV_TAG_MODIFIER_JS',
				'DIV_TAG_MODIFIER_FORMAT',
				'DIV_TAG_EMPTY',
				'DIV_TAG_BREAK',
				'DIV_TAG_ELSE',
				'DIV_TAG_AGGREGATE_FUNCTION_COUNT',
				'DIV_TAG_AGGREGATE_FUNCTION_MAX',
				'DIV_TAG_AGGREGATE_FUNCTION_MIN',
				'DIV_TAG_AGGREGATE_FUNCTION_SUM',
				'DIV_TAG_AGGREGATE_FUNCTION_AVG',
				'DIV_TAG_SPECIAL_REPLACE_NEW_LINE',
				'DIV_TAG_SPECIAL_REPLACE_CAR_RETURN',
				'DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB',
				'DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB',
				'DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE',
				'DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL' 
		);
		
		$r = array (
				DIV_TAG_MODIFIER_SIMPLE,
				DIV_TAG_MODIFIER_CAPITALIZE_FIRST,
				DIV_TAG_MODIFIER_CAPITALIZE_WORDS,
				DIV_TAG_MODIFIER_UPPERCASE,
				DIV_TAG_MODIFIER_LOWERCASE,
				DIV_TAG_MODIFIER_LENGTH,
				DIV_TAG_MODIFIER_COUNT_WORDS,
				DIV_TAG_MODIFIER_COUNT_SENTENCES,
				DIV_TAG_MODIFIER_COUNT_PARAGRAPHS,
				DIV_TAG_MODIFIER_ENCODE_URL,
				DIV_TAG_MODIFIER_ENCODE_RAW_URL,
				DIV_TAG_MODIFIER_ENCODE_JSON,
				DIV_TAG_MODIFIER_HTML_ENTITIES,
				DIV_TAG_MODIFIER_NL2BR,
				DIV_TAG_MODIFIER_TRUNCATE,
				DIV_TAG_MODIFIER_WORDWRAP,
				DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR,
				DIV_TAG_MODIFIER_SINGLE_QUOTES,
				DIV_TAG_MODIFIER_JS,
				DIV_TAG_MODIFIER_FORMAT,
				DIV_TAG_EMPTY,
				DIV_TAG_BREAK,
				DIV_TAG_ELSE,
				DIV_TAG_AGGREGATE_FUNCTION_COUNT,
				DIV_TAG_AGGREGATE_FUNCTION_MAX,
				DIV_TAG_AGGREGATE_FUNCTION_MIN,
				DIV_TAG_AGGREGATE_FUNCTION_SUM,
				DIV_TAG_AGGREGATE_FUNCTION_AVG,
				DIV_TAG_SPECIAL_REPLACE_NEW_LINE,
				DIV_TAG_SPECIAL_REPLACE_CAR_RETURN,
				DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB,
				DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE,
				DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL 
		);
		
		foreach ( $r as $k => $t ) {
			$p = array_search ( $t, $r );
			if ($p !== false && $p !== $k)
				return $names [$k] . ' must be unique and not equal to ' . $names [$p];
		}
		
		// Teaser break must be unique
		if (array_search ( DIV_TAG_TEASER_BREAK, $all_tags ))
			return 'DIV_TAG_TEASER_BREAK must be unique';
		
		return true;
	}
	
	/**
	 * Return true if $varname is valid PHP var name
	 *
	 * @param string $varname        	
	 * @return boolean
	 */
	final static function isValidVarName($varname) {
		$r = preg_replace ( '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', '', $varname );
		return $r == '$' || $r == '';
	}
	
	/**
	 * Return true if code is not obtrusive
	 *
	 * @param string $code        	
	 * @param boolean $multi_lines        	
	 * @param mixed $extra_tokens        	
	 * @param array $invalid_vars        	
	 * @param boolean $ignore_not_callable        	
	 * @return boolean
	 */
	final static function isValidPHPCode($code, $multi_lines = true, $valid_tokens = null, $invalid_vars = array(
        '$this',
        '$_SESSION',
        '$GLOBALS',
        '$_POST',
        '$_GET',
        '$_FILES',
        '$_COOKIE',
        '$_ENV',
        '$_REQUEST'), $allow_classes = false, $allow_functions = false) {
		if (is_null ( $valid_tokens ))
			$valid_tokens = DIV_PHP_VALID_TOKENS_FOR_EXPRESSIONS . ',' . DIV_PHP_VALID_TOKENS_FOR_MACROS;
		
		$t = token_get_all ( "<?php $code ?>" );
		
		foreach ( $t as $key => $value )
			if (is_array ( $value ))
				$t [$key] [0] = token_name ( $value [0] );
		
		$count = count ( $t );
		
		if (is_string ( $valid_tokens ))
			$valid_tokens = explode ( ",", $valid_tokens );
		
		foreach ( $valid_tokens as $kk => $tk ) {
			$tk = strtoupper ( trim ( $tk ) );
			$valid_tokens [$tk] = $tk;
		}
		/*
		 * foreach ( $callable_enabled as $kk => $tk ) {
		 * $tk = strtoupper(trim($tk));
		 * $callable_enabled[$tk] = $tk;
		 * }
		 *
		 * foreach ( $callable_disabled as $kk => $tk ) {
		 * $tk = strtoupper(trim($tk));
		 * $callable_disabled[$tk] = $tk;
		 * }
		 */
		if (is_null ( self::$__allowed_php_functions )) {
			$keys = explode ( ",", DIV_PHP_ALLOWED_FUNCTIONS );
			self::$__allowed_php_functions = array_combine ( $keys, $keys );
		}
		
		$object_operator = false;
		$last_token = null;
		foreach ( $t as $idx => $token ) {
			
			if ($token == ';' && $multi_lines == false) {
				self::internalMsg ( "Multi-lines not allowed", "php_validations" );
				return false;
			}
			
			if (is_array ( $token )) {
				$n = $token [0];
				
				switch ($n) {
					case 'T_VARIABLE' :
						if (array_search ( $token [1], $invalid_vars ) !== false) {
							self::internalMsg ( "Access denied to {$token[1]}", "php_validations" );
							return false;
						}
						break;
					
					case 'T_OPEN_TAG' :
						if ($idx > 0) {
							self::internalMsg ( "Invalid token T_OPEN_TAG", "php_validations" );
							return false;
						}
						break;
					
					case 'T_CLOSE_TAG' :
						if ($idx < $count - 1) {
							self::internalMsg ( "Invalid token T_CLOSE_TAG", "php_validations" );
							return false;
						}
						break;
					
					case 'T_STRING' :
						
						$classname = false;
						$funcname = false;
						
						$f = $token [1];
						
						if ($last_token == 'T_CLASS')
							$classname = true;
						if ($last_token == 'T_FUNCTION')
							$funcname = true;
						
						$lw = strtolower ( $f );
						
						if (! isset ( self::$__allowed_methods [$f] )) {
							if ($lw != 'true' && $lw != 'false' && $lw != 'null') {
								if (is_callable ( $f )) {
									if (! isset ( self::$__allowed_php_functions [$f] )) {
										
										if (! isset ( self::$__allowed_functions [$f] )) {
											self::internalMsg ( "Invalid function $f", "php_validations" );
											return false;
										}
										
										if (self::$__allowed_functions [$f] === false) {
											self::internalMsg ( "Invalid function $f", "php_validations" );
											return false;
										}
									}
								} else {
									if ((($classname && $allow_classes) || ($funcname && $allow_functions)) === false) {
										self::internalMsg ( "$f is not callable", "php_validations" );
										return false;
									}
								}
							}
						}
						break;
					
					default :
						if (! isset ( $valid_tokens [$n] )) {
							self::internalMsg ( "Invalid token $n", "php_validations" );
							return false;
						}
				}
				
				if ($n != 'T_WHITESPACE')
					$last_token = $n;
			}
		}
		return true;
	}
	
	/**
	 * Check if code is a valid expression
	 *
	 * @param string $code        	
	 * @return boolean
	 */
	final static function isValidExpression($code) {
		return self::isValidPHPCode ( $code, false, DIV_PHP_VALID_TOKENS_FOR_EXPRESSIONS );
	}
	
	/**
	 * Check if code is a valid macro
	 *
	 * @param string $code        	
	 * @return boolean
	 */
	final static function isValidMacro($code) {
		return self::isValidPHPCode ( $code, true, DIV_PHP_VALID_TOKENS_FOR_EXPRESSIONS . ',' . DIV_PHP_VALID_TOKENS_FOR_MACROS );
	}
	
	/**
	 * Return true if the script was executed in the CLI enviroment
	 *
	 * @return boolean
	 */
	final static function isCli() {
		if (is_null ( self::$__is_cli )) {
			self::$__is_cli = (! isset ( $_SERVER ['SERVER_SOFTWARE'] ) && (php_sapi_name () == 'cli' || (is_numeric ( $_SERVER ['argc'] ) && $_SERVER ['argc'] > 0)));
		}
		return self::$__is_cli;
	}
	
	/**
	 * Save internal message
	 *
	 * @param string $msg        	
	 * @param string $category        	
	 * @return array
	 */
	static function internalMsg($msg, $category = 'global') {
		$d = debug_backtrace ();
		$caller = $d [0] ['function'];
		
		if (isset ( $d ['class'] ))
			$caller = $d ['class'] . "::" . $caller;
		
		self::$__internal_messages [$category] [] = array (
				"msg" => $msg,
				"date" => date ( "Y-m-d h:i:s" ),
				"caller" => $caller 
		);
	}
	
	/**
	 * Return a list of internal messages
	 *
	 * @param string $category        	
	 * @return array
	 */
	static function getInternalMsg($category) {
		return self::$__internal_messages [$category];
	}
	
	/**
	 * Show error and die
	 *
	 * @param string $errmsg        	
	 * @param string $level        	
	 */
	static function error($errmsg, $level = DIV_ERROR_WARNING) {
		self::$__errors [] = array (
				$errmsg,
				$level 
		);
		
		$iscli = self::isCli ();
		
		ob_start ();
		$func = 'warn';
		if ($iscli)
			$errmsg = self::htmlToText ( $errmsg, null );
		
		if ($iscli === false)
			echo "<div style = \"z-index:9999; position: absolute; top: " . ((count ( self::$__errors ) - 1) * 50 + 10) . "px; right: 20px; width: 600px;max-height: 600px; overflow:auto;; font-family: courier; padding: 10px;";
		
		switch ($level) {
			case DIV_ERROR_WARNING :
				if (! $iscli)
					echo "background: yellow; border: 1px solid black; color: black;\">[$level] $errmsg</div>";
				else
					echo "$level: $errmsg\n";
				$func = 'warn';
				break;
			case DIV_ERROR_FATAL :
				if (! $iscli)
					echo "background: red; border: 1px solid black; color: white;\">[$level] $errmsg</div>";
				else
					echo "$level: $errmsg\n";
				$func = 'error';
				break;
		}
		
		$msg = ob_get_contents ();
		ob_end_clean ();
		
		if ($iscli)
			echo '[[]]' . self::htmlToText ( $msg, null ) . "\n";
		else
			echo $msg;
		
		self::log ( $msg, $level );
		
		if ($level == DIV_ERROR_FATAL)
			die ();
	}
	
	/**
	 * Switch ON the debug mode
	 *
	 * @param string $log_file        	
	 */
	final static function logOn($log_file = 'div.log') {
		self::$__log_mode = true;
		self::$__log_file = $log_file;
		self::log ( 'Starting div with logs...' );
	}
	
	/**
	 * Global logger
	 *
	 * @param string $msg        	
	 * @param string $level        	
	 */
	static function log($msg, $level = 'INFO') {
		$msg = self::htmlToText ( $msg, null );
		
		$msg = str_replace ( "\n", "\\n", $msg );
		$msg = str_replace ( "\r", "\\r", $msg );
		$msg = '[' . $level . '] ' . date ( 'Y-m-d h:i:s' ) . ' - ' . $msg . "\n";
		
		if (self::$__log_mode) {
			$f = fopen ( self::$__log_file, 'a' );
			fputs ( $f, $msg );
			fclose ( $f );
		}
		
		$func = 'log';
		
		if ($level == DIV_ERROR_WARNING)
			$func = 'warn';
		elseif ($level == DIV_ERROR_FATAL)
			$func = 'error';
		
		if (! self::isCli ()) {
			$msg = str_replace ( "\n\r", " ", $msg );
			$msg = str_replace ( "\n", ' ', $msg );
			$msg = str_replace ( array (
					"\r",
					'"' 
			), "", $msg );
			echo "<script type=\"text/javascript\"> if (typeof console !== 'undefined') console.$func(\"[[]] $msg \");</script>\n";
		}
	}
	
	/**
	 * Logger of instance
	 *
	 * @param string $msg        	
	 * @param string $level        	
	 */
	public function logger($msg, $level = 'INFO') {
		$msg = "TPL-ID: " . $this->getId () . " > $msg";
		self::log ( $msg, $level );
	}
	
	/**
	 * Destructor
	 */
	public function __destruct() {
		if (self::$__log_mode)
			$this->logger ( "Destroying the instance #" . $this->getId () );
	}
}
