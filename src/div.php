<?php

namespace divengine;

/**
 * [[]] Div PHP Template Engine
 *
 * The div class is the complete implementation of Div: please, extends me!
 *
 * Div (division) is a template engine for PHP >=5.4
 * and it is a social project without spirit of lucre.
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
 * https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package divengine/div
 * @author  Rafa Rodriguez @rafageist [https://rafageist.github.io]
 * @version 5.1.6
 *
 * @link    https://divengine.com/div
 * @link    https://github.com/divengine/div
 * @link    https://github.com/divengine/div/wiki
 */

use ReflectionClass;
use stdClass;
use Exception;

// -- Constants --

// The path of templates's root directory
if (!defined('PACKAGES')) {
    define('PACKAGES', './');
}

// The path of templates's repository
if (!defined('DIV_REPO')) {
    define('DIV_REPO', './');
}

// The default extension for template files
if (!defined('DIV_DEFAULT_TPL_FILE_EXT')) {
    define('DIV_DEFAULT_TPL_FILE_EXT', 'tpl');
}

// The default extension for data files
if (!defined('DIV_DEFAULT_DATA_FILE_EXT')) {
    define('DIV_DEFAULT_DATA_FILE_EXT', 'json');
}

// The max number of cycles of the parser (prevent infinite loop and more)
if (!defined('DIV_MAX_PARSE_CYCLES')) {
    define('DIV_MAX_PARSE_CYCLES', 100);
}

// The max size of file name or dir name in your operating system
if (!defined('DIV_MAX_FILENAME_SIZE')) {
    define('DIV_MAX_FILENAME_SIZE', 250);
}

// PHP allowed functions for macros and formulas
define('DIV_PHP_ALLOWED_FUNCTIONS',
    'isset,empty,is_null,is_numeric,is_bool,is_integer,is_double,is_array,sizeof,is_finite,is_float,is_infinite,'.'is_int,is_long,is_nan,is_real,is_scalar,is_string,mt_rand,mt_srand,mt_getrandmax,rand,urlencode,urldecode,'
    .'uniqid,date,time,intval,htmlspecialchars,htmlspecialchars_decode,strtr,strpos,str_replace,str_ireplace,substr,'.'sprintf,abs,acos,acosh,asin,atan2,atanh,base_convert,bindec,ceil,cos,cosh,decbin,dechex,decoct,deg2rad,exp,expm1,'
    .'floor,fmod,getrandmax,hexdec,hypot,lcg_value,log10,log1p,log,max,min,octdec,pi,pow,rad2deg,rand,round,sin,sinh,'.'sqrt,srand,tan,tanh,cal_days_in_month,cal_from_jd,cal_info,cal_to_jd,easter_date,easter_days,frenchtojd,gregoriantojd,'
    .'jddayofweek,jdmonthname,jdtofrench,jdtogregorian,jdtojewish,jdtojulian,jdtounix,jewishtojd,jewishtojd,unixtojd,checkdate,'.'date_default_timezone_get,strtotime,date_sunset,gmdate,gmmktime,gmstrftime,idate,microtime,mktime,strftime,strptime,'
    .'strtotime,timezone_name_from_abbr,timezone_version_get,bcadd,bccomp,bcdiv,bcmod,bcmul,bcpow,bcpowmod,bcscale,bcsqrt,'.'bcsub,addcslashes,addslashes,bin2hex,chop,chr,chunk_split,convert_cyr_string,convert_uudecode,convert_uuencode,count,'
    .'count_chars,crc32,crypt,hebrev,hebrevc,hex2bin,html_entity_decode,htmlentities,htmlspecialchars_decode,htmlspecialchars,'.'lcfirst,levenshtein,ltrim,md5,metaphone,money_format,nl_langinfo,nl2br,number_format,ord,quoted_printable_decode,'
    .'quoted_printable_encode,quotemeta,rtrim,sha1,similar_text,soundex,sprintf,str_pad,str_repeat,str_rot13,str_shuffle,'.'strcasecmp,strchr,strcmp,strcoll,strcspn,strip_tags,stripcslashes,stripos,stripslashes,stristr,strlen,strnatcasecmp,'
    .'strnatcmp,strncasecmp,strncmp,strpbrk,strrchr,strrev,strripos,strrpos,strspn,strtolower,strtoupper,strtr,substr_compare,'
    .'substr_count,substr_replace,trim,ucfirst,ucwords,wordwrap,floatval,strval,implode,explode,array_keys,get_object_vars,is_object,file_exists,in_array');

// Valid PHP tokens in expressions
define('DIV_PHP_VALID_TOKENS_FOR_EXPRESSIONS',
    'T_ARRAY,T_ARRAY_CAST,T_BOOLEAN_AND,T_BOOLEAN_OR,T_BOOL_CAST,T_CHARACTER,T_CONSTANT_ENCAPSED_STRING,'.'T_DNUMBER,T_DOUBLE_CAST,T_EMPTY,T_INT_CAST,T_ISSET,T_IS_EQUAL,T_IS_GREATER_OR_EQUAL,T_SR,T_IS_IDENTICAL,T_IS_NOT_EQUAL,T_IS_NOT_IDENTICAL,'
    .'T_IS_SMALLER_OR_EQUAL,T_LNUMBER,T_LOGICAL_AND,T_LOGICAL_OR,T_LOGICAL_XOR,T_SL,T_SL_EQUAL,T_SR_EQUAL,T_STRING_CAST,T_STRING_VARNAME,'.'T_VARIABLE,T_WHITESPACE,T_CURLY_OPEN,T_INC,T_DEC,T_COMMENT,T_DOUBLE_ARROW,T_ENCAPSED_AND_WHITESPACE');

// Valid PHP tokens in macros
define('DIV_PHP_VALID_TOKENS_FOR_MACROS', 'T_AS,T_DO,T_DOUBLE_COLON,T_ECHO,T_ELSE,T_ELSEIF,T_FOR,T_FOREACH,T_IF,T_MOD_EQUAL,T_MUL_EQUAL,'.'T_OBJECT_OPERATOR,T_NUM_STRING,T_OR_EQUAL,T_PAAMAYIM_NEKUDOTAYIM,T_PLUS_EQUAL,T_PRINT,T_START_HEREDOC,T_SWITCH,'
    .'T_WHILE,T_ENDIF,T_ENDFOR,T_ENDFOREACH,T_ENDSWITCH,T_ENDWHILE,T_END_HEREDOC,T_PAAMAYIM_NEKUDOTAYIM,T_BREAK');

// Allowed Div methods in macros and formulas
define('DIV_PHP_ALLOWED_METHODS',
    'getRanges,asThis,atLeastOneString,getLastKeyOfArray,'.'getCountOfParagraphs,getCountOfSentences,getCountOfWords,htmlToText,'.'isArrayOfArray,isArrayOfObjects,isCli,isNumericList,jsonDecode,jsonEncode,isString,mixedBool,div');

// Other internal constants
define('DIV_ERROR_WARNING', 'WARNING');
define('DIV_ERROR_FATAL', 'FATAL');
define('DIV_METHOD_NOT_EXISTS', 'DIV_METHOD_NOT_EXISTS');
define('DIV_UNICODE_ERROR', -1);
define('DIV_MOMENT_BEFORE_PARSE', 'DIV_MOMENT_BEFORE_PARSE');
define('DIV_MOMENT_AFTER_PARSE', 'DIV_MOMENT_AFTER_PARSE');
define('DIV_MOMENT_AFTER_INCLUDE', 'DIV_MOMENT_AFTER_INCLUDE');
define('DIV_MOMENT_AFTER_REPLACE', 'DIV_MOMENT_AFTER_REPLACE');

// ------------------------------------- D E F A U L T -- D I A L E C T --------------------------------------//
if (!defined('DIV_TAG_VAR_MEMBER_DELIMITER')) {
    define('DIV_TAG_VAR_MEMBER_DELIMITER', '.');
}

// Variables
if (!defined('DIV_TAG_REPLACEMENT_PREFIX')) {
    define('DIV_TAG_REPLACEMENT_PREFIX', '{');
}
if (!defined('DIV_TAG_REPLACEMENT_SUFFIX')) {
    define('DIV_TAG_REPLACEMENT_SUFFIX', '}');
}
if (!defined('DIV_TAG_MULTI_MODIFIERS_PREFIX')) {
    define('DIV_TAG_MULTI_MODIFIERS_PREFIX', '{$');
}
if (!defined('DIV_TAG_MULTI_MODIFIERS_OPERATOR')) {
    define('DIV_TAG_MULTI_MODIFIERS_OPERATOR', '|');
}
if (!defined('DIV_TAG_MULTI_MODIFIERS_SEPARATOR')) {
    define('DIV_TAG_MULTI_MODIFIERS_SEPARATOR', '|');
}
if (!defined('DIV_TAG_MULTI_MODIFIERS_SUFFIX')) {
    define('DIV_TAG_MULTI_MODIFIERS_SUFFIX', '|}');
}
if (!defined('DIV_TAG_SUBMATCH_SEPARATOR')) {
    define('DIV_TAG_SUBMATCH_SEPARATOR', ':');
}

// Variable's modifiers
if (!defined('DIV_TAG_MODIFIER_SIMPLE')) {
    define('DIV_TAG_MODIFIER_SIMPLE', '$');
}
if (!defined('DIV_TAG_MODIFIER_CAPITALIZE_FIRST')) {
    define('DIV_TAG_MODIFIER_CAPITALIZE_FIRST', '^');
}
if (!defined('DIV_TAG_MODIFIER_CAPITALIZE_WORDS')) {
    define('DIV_TAG_MODIFIER_CAPITALIZE_WORDS', '^^');
}
if (!defined('DIV_TAG_MODIFIER_UPPERCASE')) {
    define('DIV_TAG_MODIFIER_UPPERCASE', '^^^');
}
if (!defined('DIV_TAG_MODIFIER_LOWERCASE')) {
    define('DIV_TAG_MODIFIER_LOWERCASE', '_');
}
if (!defined('DIV_TAG_MODIFIER_LENGTH')) {
    define('DIV_TAG_MODIFIER_LENGTH', '%');
}
if (!defined('DIV_TAG_MODIFIER_COUNT_WORDS')) {
    define('DIV_TAG_MODIFIER_COUNT_WORDS', '%%');
}
if (!defined('DIV_TAG_MODIFIER_COUNT_SENTENCES')) {
    define('DIV_TAG_MODIFIER_COUNT_SENTENCES', '%%%');
}
if (!defined('DIV_TAG_MODIFIER_COUNT_PARAGRAPHS')) {
    define('DIV_TAG_MODIFIER_COUNT_PARAGRAPHS', '%%%%');
}
if (!defined('DIV_TAG_MODIFIER_ENCODE_URL')) {
    define('DIV_TAG_MODIFIER_ENCODE_URL', '&');
}
if (!defined('DIV_TAG_MODIFIER_ENCODE_RAW_URL')) {
    define('DIV_TAG_MODIFIER_ENCODE_RAW_URL', '&&');
}
if (!defined('DIV_TAG_MODIFIER_ENCODE_JSON')) {
    define('DIV_TAG_MODIFIER_ENCODE_JSON', 'json:');
}
if (!defined('DIV_TAG_MODIFIER_HTML_ENTITIES')) {
    define('DIV_TAG_MODIFIER_HTML_ENTITIES', 'html:');
}
if (!defined('DIV_TAG_MODIFIER_NL2BR')) {
    define('DIV_TAG_MODIFIER_NL2BR', 'br:');
}
if (!defined('DIV_TAG_MODIFIER_TRUNCATE')) {
    define('DIV_TAG_MODIFIER_TRUNCATE', '~');
}
if (!defined('DIV_TAG_MODIFIER_WORDWRAP')) {
    define('DIV_TAG_MODIFIER_WORDWRAP', '/');
}
if (!defined('DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR')) {
    define('DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR', ',');
}
if (!defined('DIV_TAG_MODIFIER_SINGLE_QUOTES')) {
    define('DIV_TAG_MODIFIER_SINGLE_QUOTES', "'");
}
if (!defined('DIV_TAG_MODIFIER_JS')) {
    define('DIV_TAG_MODIFIER_JS', 'js:');
}
if (!defined('DIV_TAG_MODIFIER_FORMAT')) {
    define('DIV_TAG_MODIFIER_FORMAT', '');
}

// Data format
if (!defined('DIV_TAG_DATE_FORMAT_PREFIX')) {
    define('DIV_TAG_DATE_FORMAT_PREFIX', '{/');
}
if (!defined('DIV_TAG_DATE_FORMAT_SUFFIX')) {
    define('DIV_TAG_DATE_FORMAT_SUFFIX', '/}');
}
if (!defined('DIV_TAG_DATE_FORMAT_SEPARATOR')) {
    define('DIV_TAG_DATE_FORMAT_SEPARATOR', ':');
}
if (!defined('DIV_TAG_NUMBER_FORMAT_PREFIX')) {
    define('DIV_TAG_NUMBER_FORMAT_PREFIX', '{#');
}
if (!defined('DIV_TAG_NUMBER_FORMAT_SUFFIX')) {
    define('DIV_TAG_NUMBER_FORMAT_SUFFIX', '#}');
}
if (!defined('DIV_TAG_NUMBER_FORMAT_SEPARATOR')) {
    define('DIV_TAG_NUMBER_FORMAT_SEPARATOR', ':');
}

// Formulas
if (!defined('DIV_TAG_FORMULA_BEGIN')) {
    define('DIV_TAG_FORMULA_BEGIN', '(#');
}
if (!defined('DIV_TAG_FORMULA_END')) {
    define('DIV_TAG_FORMULA_END', '#)');
}
if (!defined('DIV_TAG_FORMULA_FORMAT_SEPARATOR')) {
    define('DIV_TAG_FORMULA_FORMAT_SEPARATOR', ':');
}

// Sub-parsers
if (!defined('DIV_TAG_SUBPARSER_BEGIN_PREFIX')) {
    define('DIV_TAG_SUBPARSER_BEGIN_PREFIX', '{');
}
if (!defined('DIV_TAG_SUBPARSER_BEGIN_SUFFIX')) {
    define('DIV_TAG_SUBPARSER_BEGIN_SUFFIX', '}');
}
if (!defined('DIV_TAG_SUBPARSER_END_PREFIX')) {
    define('DIV_TAG_SUBPARSER_END_PREFIX', '{/');
}
if (!defined('DIV_TAG_SUBPARSER_END_SUFFIX')) {
    define('DIV_TAG_SUBPARSER_END_SUFFIX', '}');
}

// Ignored parts
if (!defined('DIV_TAG_IGNORE_BEGIN')) {
    define('DIV_TAG_IGNORE_BEGIN', '{ignore}');
}
if (!defined('DIV_TAG_IGNORE_END')) {
    define('DIV_TAG_IGNORE_END', '{/ignore}');
}

// Comments
if (!defined('DIV_TAG_COMMENT_BEGIN')) {
    define('DIV_TAG_COMMENT_BEGIN', '<!--{');
}
if (!defined('DIV_TAG_COMMENT_END')) {
    define('DIV_TAG_COMMENT_END', '}-->');
}

// HTML to Plain text
if (!defined('DIV_TAG_TXT_BEGIN')) {
    define('DIV_TAG_TXT_BEGIN', '{txt}');
}
if (!defined('DIV_TAG_TXT_END')) {
    define('DIV_TAG_TXT_END', '{/txt}');
}
if (!defined('DIV_TAG_TXT_WIDTH_SEPARATOR')) {
    define('DIV_TAG_TXT_WIDTH_SEPARATOR', '=>');
}

// Strip
if (!defined('DIV_TAG_STRIP_BEGIN')) {
    define('DIV_TAG_STRIP_BEGIN', '{strip}');
}
if (!defined('DIV_TAG_STRIP_END')) {
    define('DIV_TAG_STRIP_END', '{/strip}');
}

// Loops
if (!defined('DIV_TAG_LOOP_BEGIN_PREFIX')) {
    define('DIV_TAG_LOOP_BEGIN_PREFIX', '[$');
}
if (!defined('DIV_TAG_LOOP_BEGIN_SUFFIX')) {
    define('DIV_TAG_LOOP_BEGIN_SUFFIX', ']');
}
if (!defined('DIV_TAG_LOOP_END_PREFIX')) {
    define('DIV_TAG_LOOP_END_PREFIX', '[/$');
}
if (!defined('DIV_TAG_LOOP_END_SUFFIX')) {
    define('DIV_TAG_LOOP_END_SUFFIX', ']');
}
if (!defined('DIV_TAG_EMPTY')) {
    define('DIV_TAG_EMPTY', '@empty@');
}
if (!defined('DIV_TAG_BREAK')) {
    define('DIV_TAG_BREAK', '@break@');
}
if (!defined('DIV_TAG_LOOP_VAR_SEPARATOR')) {
    define('DIV_TAG_LOOP_VAR_SEPARATOR', '=>');
}

// Iterations
if (!defined('DIV_TAG_ITERATION_BEGIN_PREFIX')) {
    define('DIV_TAG_ITERATION_BEGIN_PREFIX', '[:');
}
if (!defined('DIV_TAG_ITERATION_BEGIN_SUFFIX')) {
    define('DIV_TAG_ITERATION_BEGIN_SUFFIX', ':]');
}
if (!defined('DIV_TAG_ITERATION_END')) {
    define('DIV_TAG_ITERATION_END', '[/]');
}
if (!defined('DIV_TAG_ITERATION_PARAM_SEPARATOR')) {
    define('DIV_TAG_ITERATION_PARAM_SEPARATOR', ',');
}

// Conditional parts
if (!defined('DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX')) {
    define('DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX', '?$');
}
if (!defined('DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX')) {
    define('DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX', '');
}
if (!defined('DIV_TAG_CONDITIONAL_TRUE_END_PREFIX')) {
    define('DIV_TAG_CONDITIONAL_TRUE_END_PREFIX', '$');
}
if (!defined('DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX')) {
    define('DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX', '?');
}
if (!defined('DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX')) {
    define('DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX', '!$');
}

if (!defined('DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX')) {
    define('DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX', '');
}

if (!defined('DIV_TAG_CONDITIONAL_FALSE_END_PREFIX')) {
    define('DIV_TAG_CONDITIONAL_FALSE_END_PREFIX', '$');
}
if (!defined('DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX')) {
    define('DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX', '!');
}
if (!defined('DIV_TAG_ELSE')) {
    define('DIV_TAG_ELSE', '@else@');
}

// Conditions
if (!defined('DIV_TAG_CONDITIONS_BEGIN_PREFIX')) {
    define('DIV_TAG_CONDITIONS_BEGIN_PREFIX', '{?(');
}
if (!defined('DIV_TAG_CONDITIONS_BEGIN_SUFFIX')) {
    define('DIV_TAG_CONDITIONS_BEGIN_SUFFIX', ')?}');
}
if (!defined('DIV_TAG_CONDITIONS_END')) {
    define('DIV_TAG_CONDITIONS_END', '{/?}');
}

// Template vars
if (!defined('DIV_TAG_TPLVAR_BEGIN')) {
    define('DIV_TAG_TPLVAR_BEGIN', '{=');
}
if (!defined('DIV_TAG_TPLVAR_END')) {
    define('DIV_TAG_TPLVAR_END', '=}');
}
if (!defined('DIV_TAG_TPLVAR_ASSIGN_OPERATOR')) {
    define('DIV_TAG_TPLVAR_ASSIGN_OPERATOR', ':');
}
if (!defined('DIV_TAG_TPLVAR_PROTECTOR')) {
    define('DIV_TAG_TPLVAR_PROTECTOR', '*');
}

// Default replacement
if (!defined('DIV_TAG_DEFAULT_REPLACEMENT_BEGIN')) {
    define('DIV_TAG_DEFAULT_REPLACEMENT_BEGIN', '{@');
}
if (!defined('DIV_TAG_DEFAULT_REPLACEMENT_END')) {
    define('DIV_TAG_DEFAULT_REPLACEMENT_END', '@}');
}

// Includes
if (!defined('DIV_TAG_INCLUDE_BEGIN')) {
    define('DIV_TAG_INCLUDE_BEGIN', '{% ');
}
if (!defined('DIV_TAG_INCLUDE_END')) {
    define('DIV_TAG_INCLUDE_END', ' %}');
}

// Pre-processed
if (!defined('DIV_TAG_PREPROCESSED_BEGIN')) {
    define('DIV_TAG_PREPROCESSED_BEGIN', '{%% ');
}
if (!defined('DIV_TAG_PREPROCESSED_END')) {
    define('DIV_TAG_PREPROCESSED_END', ' %%}');
}
if (!defined('DIV_TAG_PREPROCESSED_SEPARATOR')) {
    define('DIV_TAG_PREPROCESSED_SEPARATOR', ':');
}

// Capsules
if (!defined('DIV_TAG_CAPSULE_BEGIN_PREFIX')) {
    define('DIV_TAG_CAPSULE_BEGIN_PREFIX', '[[');
}
if (!defined('DIV_TAG_CAPSULE_BEGIN_SUFFIX')) {
    define('DIV_TAG_CAPSULE_BEGIN_SUFFIX', '');
}
if (!defined('DIV_TAG_CAPSULE_END_PREFIX')) {
    define('DIV_TAG_CAPSULE_END_PREFIX', '');
}
if (!defined('DIV_TAG_CAPSULE_END_SUFFIX')) {
    define('DIV_TAG_CAPSULE_END_SUFFIX', ']]');
}

// Multi replacements
if (!defined('DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX')) {
    define('DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX', '{:');
}
if (!defined('DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX')) {
    define('DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX', '}');
}
if (!defined('DIV_TAG_MULTI_REPLACEMENT_END_PREFIX')) {
    define('DIV_TAG_MULTI_REPLACEMENT_END_PREFIX', '{:/');
}
if (!defined('DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX')) {
    define('DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX', '}');
}

// Friendly tags
if (!defined('DIV_TAG_FRIENDLY_BEGIN')) {
    define('DIV_TAG_FRIENDLY_BEGIN', '<!--|');
}
if (!defined('DIV_TAG_FRIENDLY_END')) {
    define('DIV_TAG_FRIENDLY_END', '|-->');
}

// Aggregate functions
if (!defined('DIV_TAG_AGGREGATE_FUNCTION_COUNT')) {
    define('DIV_TAG_AGGREGATE_FUNCTION_COUNT', 'count');
}
if (!defined('DIV_TAG_AGGREGATE_FUNCTION_MAX')) {
    define('DIV_TAG_AGGREGATE_FUNCTION_MAX', 'max');
}
if (!defined('DIV_TAG_AGGREGATE_FUNCTION_MIN')) {
    define('DIV_TAG_AGGREGATE_FUNCTION_MIN', 'min');
}
if (!defined('DIV_TAG_AGGREGATE_FUNCTION_SUM')) {
    define('DIV_TAG_AGGREGATE_FUNCTION_SUM', 'sum');
}
if (!defined('DIV_TAG_AGGREGATE_FUNCTION_AVG')) {
    define('DIV_TAG_AGGREGATE_FUNCTION_AVG', 'avg');
}
if (!defined('DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR')) {
    define('DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR', ':');
}
if (!defined('DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR')) {
    define('DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR', '-');
}

// Locations
if (!defined('DIV_TAG_LOCATION_BEGIN')) {
    define('DIV_TAG_LOCATION_BEGIN', '(( ');
}
if (!defined('DIV_TAG_LOCATION_END')) {
    define('DIV_TAG_LOCATION_END', ' ))');
}
if (!defined('DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX')) {
    define('DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX', '{{');
}
if (!defined('DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX')) {
    define('DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX', '');
}
if (!defined('DIV_TAG_LOCATION_CONTENT_END_PREFIX')) {
    define('DIV_TAG_LOCATION_CONTENT_END_PREFIX', '');
}
if (!defined('DIV_TAG_LOCATION_CONTENT_END_SUFFIX')) {
    define('DIV_TAG_LOCATION_CONTENT_END_SUFFIX', '}}');
}

// Macros
if (!defined('DIV_TAG_MACRO_BEGIN')) {
    define('DIV_TAG_MACRO_BEGIN', '<?');
}
if (!defined('DIV_TAG_MACRO_END')) {
    define('DIV_TAG_MACRO_END', '?>');
}

// Special replacements
if (!defined('DIV_TAG_SPECIAL_REPLACE_NEW_LINE')) {
    define('DIV_TAG_SPECIAL_REPLACE_NEW_LINE', '{\n}');
}
if (!defined('DIV_TAG_SPECIAL_REPLACE_CAR_RETURN')) {
    define('DIV_TAG_SPECIAL_REPLACE_CAR_RETURN', '{\r}');
}
if (!defined('DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB')) {
    define('DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB', '{\t}');
}
if (!defined('DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB')) {
    define('DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB', '{\v}');
}
if (!defined('DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE')) {
    define('DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE', '{\f}');
}
if (!defined('DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL')) {
    define('DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL', '{\$}');
}
if (!defined('DIV_TAG_SPECIAL_REPLACE_SPACE')) {
    define('DIV_TAG_SPECIAL_REPLACE_SPACE', '{\s}');
}
if (!defined('DIV_TAG_TEASER_BREAK')) {
    define('DIV_TAG_TEASER_BREAK', '<!--break-->');
}

define('DIV_DEFAULT_DIALECT', '{
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
		\'DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL\' : \'{\\$}\',		\'DIV_TAG_TEASER_BREAK\' : \'<!--break-->\',
		\'DIV_TAG_SPECIAL_REPLACE_SPACE\' : \'{\\s}\'
		}');

// --------------------------------------------------------------------------------------------------------------------------------------//

define('DIV_TEMPLATE_FOR_DOCS', '@_DIALECT = '.uniqid('', true).base64_decode('PGh0bWw+DQoJPGhlYWQ+DQoJCTx0aXRsZT57JHRpdGxlfTwvdGl0bGU+DQoJCTxzdHlsZSB0eXBl
PSJ0ZXh0L2NzcyI+DQoJCQlib2R5ICAgICAgICAgIHtiYWNrZ3JvdW5kOiAjNjU2NTY1OyBmb250
LWZhbWlseTogVmVyZGFuYTt9DQoJCQlkaXYuc2VjdGlvbiAgIHtiYWNrZ3JvdW5kOiB3aGl0ZTsJ
bWFyZ2luLXRvcDogMjBweDsgcGFkZGluZzogMTBweDsgd2lkdGg6IDc4MHB4O30NCgkJCS5zZWN0
aW9uIGgyICAge2NvbG9yOiB3aGl0ZTsgZm9udC1zaXplOiAyNHB4O2ZvbnQtd2VpZ2h0OiBib2xk
OyBtYXJnaW4tbGVmdDogLTMwcHg7cGFkZGluZy1ib3R0b206IDVweDsgcGFkZGluZy1sZWZ0OiAz
MHB4O3BhZGRpbmctdG9wOiA1cHg7IGJhY2tncm91bmQ6IGJsYWNrOyBib3JkZXItbGVmdDogMTBw
eCBzb2xpZCBncmF5O30JDQoJCQloMSAgICAgICAgICAgIHtjb2xvcjogd2hpdGU7fQ0KCQkJdGFi
bGUuZGF0YSB0ZCB7cGFkZGluZzogNXB4O2JvcmRlci1ib3R0b206IDFweCBzb2xpZCBncmF5OyBi
b3JkZXItcmlnaHQ6IDFweCBzb2xpZCBncmF5O30NCgkJCXRhYmxlLmRhdGEgdGgge3BhZGRpbmc6
IDVweDtjb2xvcjogd2hpdGU7IGJhY2tncm91bmQ6IGJsYWNrO30NCgkJCS5jb2RlICAgICAgICAg
e3BhZGRpbmc6IDBweDsgbWFyZ2luOiAwcHg7IGJhY2tncm91bmQ6ICNlZWVlZWU7IGNvbG9yOiBi
bGFjazsgZm9udC1mYW1pbHk6ICJDb3VyaWVyIE5ldyI7IHRleHQtYWxpZ246IGxlZnQ7IGZvbnQt
c2l6ZTogMTNweDt9CQkJCQkNCgkJCS5jb2RlIC5saW5lICAge3RleHQtYWxpZ246cmlnaHQ7IGJh
Y2tncm91bmQ6IGdyYXk7IGNvbG9yOiB3aGl0ZTsgYm9yZGVyLXJpZ2h0OiAycHggc29saWQgYmxh
Y2s7IHBhZGRpbmctcmlnaHQ6IDVweDt9DQoJCQl0YWJsZS5pbmRleCwgdGFibGUuaW5kZXggYSwg
dGFibGUuaW5kZXggYTp2aXNpdGVkIHtjb2xvcjp3aGl0ZTt9CQkJCQkNCgkJCWRpdi5oZWFkZXIg
ICAge2NvbG9yOiB3aGl0ZTt9DQoJCQkudGVtcGxhdGUtZGVzY3JpcHRpb257YmFja2dyb3VuZDog
I2VlZWVlZTsgcGFkZGluZzogMTBweDt9DQoJCTwvc3R5bGU+DQoJPC9oZWFkPg0KCTxib2R5Pg0K
CQk8bGFiZWw+PGEgaHJlZj0iI2hlYWRlciIgc3R5bGU9InBvc2l0aW9uOiBmaXhlZDsgYm90dG9t
OiA1cHg7IHJpZ2h0OiA1cHg7Y29sb3I6IHdoaXRlOyI+SW5kZXg8L2E+PC9sYWJlbD4NCgkJPHRh
YmxlIHdpZHRoPSI3NTAiIGFsaWduPSJjZW50ZXIiPjx0cj48dGQgdmFsaWduPSJ0b3AiPg0KCQk8
ZGl2IGlkID0gImhlYWRlciIgY2xhc3MgPSAiaGVhZGVyIj4NCgkJPGgxPnskdGl0bGV9PC9oMT4N
CgkJPHA+R2VuZXJhdGVkIGJ5IERpdiBhdCB7L2Rpdi5ub3c6WS1tLWQgSDppOnMvfTwvcD4NCgkJ
PGgyPkluZGV4PC9oMj4NCgkJPHRhYmxlIGNsYXNzPSJpbmRleCBkYXRhIiB3aWR0aD0iMTAwJSI+
DQoJCQk8dHI+PHRoPjwvdGg+PHRoPk5hbWU8L3RoPjx0aD5EZXNjcmlwdGlvbjwvdGg+PHRoPlZl
cnNpb248L3RoPjwvdHI+DQoJCQlbJGRvY3NdDQoJCQkJPHRyPjx0ZD57JF9vcmRlcn08L3RkPg0K
CQkJCTx0ZD48YSBocmVmPSIjeyRfa2V5fSI+PyRuYW1lIHskbmFtZX0gJG5hbWU/PC9hPjwvdGQ+
DQoJCQkJPHRkPj8kZGVzY3JpcHRpb24geyRkZXNjcmlwdGlvbn0gJGRlc2NyaXB0aW9uPzwvdGQ+
DQoJCQkJPHRkPj8kdmVyc2lvbiB7JHZlcnNpb259ICR2ZXJzaW9uPzwvdGQ+PC90cj4NCgkJCVsv
JGRvY3NdDQoJCTwvdGFibGU+DQoJCTwvZGl2Pg0KCQkNCgkJez0gcmVwbDE6IFtbIjwiLCIiXSxb
Ij4iLCIiXV0gPX0NCgkJDQoJCVskZG9jc10NCgkJCTxkaXYgY2xhc3M9InNlY3Rpb24iPg0KCQkJ
CTxoMiBpZCA9ICJ7JF9rZXl9Ij4/JGljb24geyRpY29ufSAkaWNvbj8geyRuYW1lfTwvaDI+DQoJ
CQkJPHRhYmxlIHdpZHRoPSIxMDAlIj4NCgkJCQk8dHI+PHRkIGFsaWduPSJyaWdodCI+UGF0aDo8
L3RkPjx0ZD57JF9rZXl9IDwvdGQ+PC90cj4NCgkJCQk/JHR5cGUgICAgPHRyPjx0ZCBhbGlnbj0i
cmlnaHQiIHdpZHRoPSIxNTAiPlR5cGU6PC90ZD48dGQ+PGI+eyR0eXBlfTwvYj48L3RkPjwvdHI+
JHR5cGU/DQoJCQkJPyRhdXRob3IgIDx0cj48dGQgYWxpZ249InJpZ2h0IiB3aWR0aD0iMTUwIj5B
dXRob3I6PC90ZD48dGQ+PGI+e2h0bWw6YXV0aG9yfTwvYj48L3RkPjwvdHI+ICRhdXRob3I/DQoJ
CQkJPyR2ZXJzaW9uIDx0cj48dGQgYWxpZ249InJpZ2h0IiB3aWR0aD0iMTUwIj5WZXJzaW9uOjwv
dGQ+PHRkPjxiPnskdmVyc2lvbn08L2I+PC90ZD48L3RyPiAkdmVyc2lvbj8NCgkJCQk/JHVwZGF0
ZSAgPHRyPjx0ZCBhbGlnbj0icmlnaHQiIHdpZHRoPSIxNTAiPkxhc3QgdXBkYXRlOjwvdGQ+PHRk
PnskdXBkYXRlfSA8L3RkPjwvdHI+JHVwZGF0ZT8NCgkJCQk8L3RhYmxlPg0KCQkJCTxici8+DQoJ
CQkJPyRkZXNjcmlwdGlvbiA8cCBjbGFzcz0idGVtcGxhdGUtZGVzY3JpcHRpb24iPnskZGVzY3Jp
cHRpb259PC9wPiRkZXNjcmlwdGlvbj8NCgkJCQk/JHZhcnMNCgkJCQkJPGgzPlRlbXBsYXRlJ3Mg
VmFyaWFibGVzICh7JHZhcnN9KTwvaDM+DQoJCQkJCTx0YWJsZSBjbGFzcz0iZGF0YSI+DQoJCQkJ
CTx0cj48dGg+PC90aD48dGg+PC90aD48dGg+VHlwZTwvdGg+PHRoPk5hbWU8L3RoPjx0aD5EZXNj
cmlwdGlvbjwvdGg+PC90cj4NCgkJCQkJWyR2YXJzXQ0KCQkJCQkNCgkJCQkJCXs/KCB0cmltKCJ7
J3ZhbHVlfSIpICE9PSAiIiApP30NCgkJCQkJCTw/DQoJCQkJCQkJJHZhbHVlID0gdHJpbShzdHJf
cmVwbGFjZShhcnJheSgiXHQiLCJcbiIsIlxyIiksIiAiLCAkdmFsdWUpKTsJDQoJCQkJCQkJd2hp
bGUoc3RycG9zKCR2YWx1ZSwgIiAgIikpICR2YWx1ZSA9IHN0cl9yZXBsYWNlKCIgICIsIiAiLCAk
dmFsdWUpOw0KCQkJCQkJCSRwYXJzID0gZXhwbG9kZSgiICIsICR2YWx1ZSwgNCk7DQoJCQkJCQk/
Pg0KCQkJCQkJPHRyPg0KCQkJCQkJCTx0ZD57JF9vcmRlcn08L3RkPg0KCQkJCQkJCVskcGFyc10N
CgkJCQkJCQk8dGQ+ezpyZXBsMX17JHZhbHVlfXs6L3JlcGwxfTwvdGQ+DQoJCQkJCQkJWy8kcGFy
c10NCgkJCQkJCTwvdHI+DQoJCQkJCQl7Lz99DQoJCQkJCVsvJHZhcnNdDQoJCQkJCTwvdGFibGU+
DQoJCQkJJHZhcnM/DQoJCQkJPyRpbmNsdWRlDQoJCQkJCTxoMz5JbmNsdWRlOjwvaDM+DQoJCQkJ
CVskaW5jbHVkZV0NCgkJCQkJCXskX29yZGVyfS4gPGEgaHJlZj0iI3skdmFsdWV9Ij57JHZhbHVl
fTwvYT48YnIvPg0KCQkJCQlbLyRpbmNsdWRlXQ0KCQkJCSRpbmNsdWRlPw0KCQkJCT8kZXhhbXBs
ZQ0KCQkJCQk8aDM+RXhhbXBsZTo8L2gzPg0KCQkJCQk8dGFibGUgd2lkdGggPSAiMTAwJSIgY2xh
c3M9ImNvZGUiIGNlbGxzcGFjaW5nPSIwIiBjZWxscGFkZGluZz0iMCI+DQoJCQkJCVskZXhhbXBs
ZV0NCgkJCQkJCQk8dHI+DQoJCQkJCQkJPHRkIGNsYXNzPSJsaW5lIiB3aWR0aD0iMzAiPnskX29y
ZGVyfTwvdGQ+DQoJCQkJCQkJPHRkPjxwcmUgY2xhc3M9ImNvZGUiPntodG1sX3d5c2l3eWc6YWZ0
ZXJSZXBsYWNlfXskdmFsdWV9ey9odG1sX3d5c2l3eWc6YWZ0ZXJSZXBsYWNlfTwvcHJlPjwvdGQ+
DQoJCQkJCQkJPC90cj4NCgkJCQkJWy8kZXhhbXBsZV0NCgkJCQkJPC90YWJsZT4gDQoJCQkJJGV4
YW1wbGU/DQoJCQk8L2Rpdj4NCgkJWy8kZG9jc10NCgkJPC90ZD48L3RyPjwvdGFibGU+DQoJPC9i
b2R5Pg0KPC9odG1sPg=='));

/**
 * Class div
 */
class div
{

    // Public

    // template source
    public $__src;

    // original template source
    public $__src_original;

    // template variables
    public $__items = [];

    // original template variables
    public $__items_orig = [];

    // to remember the template variables
    public $__memory = [];

    // path to current template file
    public $__path = '';

    // template variables to ignore
    public $__ignore = [];

    // internal and random ignore tag (security)
    public $__ignore_secret_tag;

    // template's parts to restore after parse
    public $__restore = [];

    // path of current templates's root folder
    public $__packages = PACKAGES;

    // properties of the template
    public $__properties = [];

    // ----- Private ------
    // template id
    private $__id;

    // temporal vars
    private $__temp = [];

    // template checksum
    private $__crc;

    // ----- Globals -----

    // custom variable's modifiers
    private static $__custom_modifiers = [];

    // global template's variables
    private static $__globals = [];

    // global template's variables defined in the design
    private static $__globals_design = [];

    // global and protected template variables defined in the design
    private static $__globals_design_protected = [];

    // default value for another value
    private static $__defaults = [];

    // default value for another value by variable
    private static $__defaults_by_var = [];

    // system data
    private static $__system_data;

    private static $__system_data_allowed = [];

    // do not load code from files
    private static $__discard_file_system = false;

    // list of allowed custom functions
    private static $__allowed_functions = [];

    // list of allowed class's methods
    private static $__allowed_methods;

    // list of sub-parsers
    private static $__sub_parsers = [];

    // template's documentation
    private static $__docs = [];

    // on/off documentation
    private static $__docs_on = false;

    // includes's history
    private static $__includes_history = [];

    // ----- Internals -----

    // current version of Div
    private static $__version = '5.1.6';

    // name of the super class
    private static $__super_class;

    // name of parent class's methods
    private static $__parent_method_names = [];

    // duration of parser
    private static $__parse_duration;

    // current level of parser
    private static $__parse_level = 0;

    // auxiliary engine
    private static $__engine;

    // variable's modifiers
    private static $__modifiers = [
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
        DIV_TAG_MODIFIER_JS,
    ];

    // is current dialect checked?
    private static $__dialect_checked = false;

    // allowed PHP functions
    private static $__allowed_php_functions;

    // is log mode?
    private static $__log_mode = false;

    // the log filename
    private static $__log_file;

    // is PHP cli?
    private static $__is_cli;

    // ignored parts
    private static $__ignored_parts = [];

    // last template id
    private static $__last_id = 0;

    // remember previous work
    private static $__remember = [];

    // do not remember this work
    private static $__dont_remember_it = [];

    // historical errors
    private static $__errors = [];

    // include path
    private static $__include_paths;

    // packages by class
    private static $__packages_by_class = [];

    // internal messages
    private static $__internal_messages = [];

    // error reporting to set during execution of macros and expressions
    private static $__error_reporting = E_ALL;

    // for save PHP configuration error reporting
    private static $__error_reporting_php;

    // cached values
    public static $__cached_values = [];

    /**
     * Constructor
     *
     * @param string $src
     * @param mixed  $items
     * @param mixed  $ignore
     *
     * @throws \ReflectionException
     */
    public function __construct($src = null, $items = null, $ignore = [])
    {
        // Enabling system vars
        if (self::$__parse_level < 2) {
            self::enableSystemVar('div'.DIV_TAG_VAR_MEMBER_DELIMITER.'version');
            self::enableSystemVar('div'.DIV_TAG_VAR_MEMBER_DELIMITER.'post');
            self::enableSystemVar('div'.DIV_TAG_VAR_MEMBER_DELIMITER.'get');
            self::enableSystemVar('div'.DIV_TAG_VAR_MEMBER_DELIMITER.'now');
            self::enableSystemVar('div'.DIV_TAG_VAR_MEMBER_DELIMITER.'level');
        }

        // Generate internal and random ignore tag (security reasons)
        $this->__ignore_secret_tag = uniqid('', true);

        // Validate the current dialect
        if (self::$__dialect_checked === false) {
            $r = self::isValidCurrentDialect();
            if ($r !== true) {
                self::error('Current dialect is invalid: '.$r, DIV_ERROR_FATAL);
            }
            self::$__dialect_checked = true;
        }

        $class_name = get_class($this);

        self::$__packages_by_class [$class_name] = $this->__packages;

        if (self::$__super_class === null) {
            self::$__super_class = $this->getSuperParent();
        }

        if (self::$__parent_method_names === null) {
            self::$__parent_method_names = get_class_methods(self::$__super_class);
        }

        $this->__id = ++self::$__last_id;

        if (self::$__log_mode) {
            $this->logger('Building instance #'.$this->__id.' of '.$class_name.'...');
        }

        // Calling the beforeBuild hook
        $this->beforeBuild($src, $items);

        if ($items === null && $this->__items !== null) {
            $items = $this->__items;
        }

        $this->__items_orig = $items;

        $decode = true;

        $discardFileSystem = self::$__discard_file_system;

        if ($src === null) {
            if ($class_name !== self::$__super_class && $this->__src === null) {
                $reflection = new ReflectionClass($class_name);
                $dir = pathinfo($reflection->getFileName(), PATHINFO_DIRNAME);
                $filename = pathinfo($reflection->getFileName(), PATHINFO_BASENAME);
                $ext = pathinfo($reflection->getFileName(), PATHINFO_EXTENSION);
                $src = $dir.'/'.substr($filename,0,0 - strlen($ext) - 1);
            }

            if ($this->__src !== null) {
                $src = $this->__src;
            }
        }

        if ($items === null) {
            $items = $src;
            $items = str_replace('.'.DIV_DEFAULT_TPL_FILE_EXT, '', $items);
            $decode = false;
        }

        if (!$discardFileSystem && self::isString($items)) {
            if (strlen($items.'.'.DIV_DEFAULT_DATA_FILE_EXT) < 255) {
                $exists = false;

                if (self::fileExists($items)) {
                    $items = self::getFileContents($items);
                    $exists = true;
                } elseif (self::fileExists($items.'.'.DIV_DEFAULT_DATA_FILE_EXT)) {
                    $items = self::getFileContents($items.'.'.DIV_DEFAULT_DATA_FILE_EXT);
                    $exists = true;
                }

                if ($exists === true || $decode === true) {
                    $json = $items;
                    $missing_vars = [];
                    $items = self::jsonDecode($json, [], $missing_vars);

                    // TODO: improve this!
                    /* if (isset($missing_vars[0]))
                        $items = self::jsonDecode($json, $items, $missing_vars);
                    */
                }

                /*
                 * if ($exists === true)
                 * break;
                 */
            } else {
                $items = self::jsonDecode($items);
            }
        }

        if (is_object($items)) {
            if (method_exists($items, '__toString')) {
                $item_str = (string)$items;
                if (!property_exists($items, 'value')) {
                    $items->value = $item_str;
                }
                $items->_to_string = $item_str;
            }
            $items = get_object_vars($items);
        }

        if (!$discardFileSystem) {
            $src = $this->loadTemplate($src);
        }

        if (!is_array($items)) {
            $items = [];
        }

        $this->__src = $src;
        $this->__src_original = $src;
        $this->__items = $items;

        if (self::isString($ignore)) {
            $ignore = explode(',', $ignore);
        }

        if (array_key_exists(0, $ignore)) {
            foreach ($ignore as $key => $val) {
                $this->__ignore [$val] = true;
            }
        }

        // Calling the afterBuild hook
        $this->afterBuild();

        // Enabling methods
        if (self::$__allowed_methods === null) {
            $keys = explode(',', DIV_PHP_ALLOWED_METHODS);;
            self::$__allowed_methods = array_combine($keys, $keys);

            if (self::$__super_class !== $class_name) {
                $keys = array_diff(get_class_methods($class_name), get_class_methods(self::$__super_class));
                if (array_key_exists(0, $keys)) {
                    self::$__allowed_methods = array_merge(self::$__allowed_methods, array_combine($keys, $keys));
                }
            }
        }

        // Pre-defined sub-parsers

        self::setSubParser('parse', 'subParse_parse');
        self::setSubParser('html_wysiwyg', 'subParse_html_wysiwyg');
        self::setSubParser('join', 'subParse_join');

        // Name of current class and parent
        if (!array_key_exists('div', $this->__items)) {
            $this->__items['div'] = [];
        }
        if (is_object($this->__items['div'])) {
            $this->__items['div'] = get_object_vars($this->__items['div']);
        }
        $this->__items['div']['class_name'] = $class_name;
        $this->__items['div']['super_class_name'] = self::$__super_class;
    }

    /**
     * Return current version of div
     *
     * @return string
     */
    public static function getVersion() {
        return self::$__version;
    }

    /**
     * Secure call
     *
     * @param       $callable
     * @param array $arguments
     * @param bool  $proceed
     *
     * @return mixed|string
     */
    public function call($callable, $arguments = [], &$proceed = false)
    {
        $callable_result = '';
        $proceed = false;
        if (is_callable($callable)) // functions, static methods & closures
        {
            $callable_result = @call_user_func_array($callable, $arguments);
            $proceed = true;
        } elseif (is_string($callable) && method_exists($this, $callable)) // $this/self methods
        {
            $callable_result = @call_user_func_array([$this, $callable], $arguments);
            $proceed = true;
        }

        return $callable_result;
    }

    /**
     * Add a custom include path
     *
     * @param $path
     */
    final public static function addIncludePath($path)
    {
        self::getIncludePaths();
        self::$__include_paths[] = $path;
    }

    /**
     * Return a list of include_path setting + the PACKAGES
     *
     * @param string $packages
     * @param string $repo
     *
     * @return array
     */
    final public static function getIncludePaths($packages = PACKAGES, $repo = DIV_REPO)
    {
        if (self::$__include_paths === null) {
            $os = self::getOperatingSystem();
            self::$__include_paths = explode(($os === 'win32' ? ';' : ':'), ini_get('include_path'));
            self::$__include_paths [] = $packages;
            if ($packages !== $repo) {
                self::$__include_paths [] = $repo;
            }
        }

        return self::$__include_paths;
    }

    /**
     * Return the current operating system
     *
     * @return string (win32/linux/unix)
     */
    final public static function getOperatingSystem()
    {
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            if (isset($_SERVER['WINDIR']) || strpos($_SERVER['SERVER_SOFTWARE'], 'Win32') !== false) {
                return 'win32';
            }
            if (!isset($_SERVER['WINDIR']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Linux') !== false) {
                return 'linux';
            }
        }

        if (file_exists('C:\Windows')) {
            return 'win32';
        }

        return 'unix';
    }

    /**
     * Return the super parent class name
     *
     * @param string $class_name
     *
     * @return string
     */
    final public function getSuperParent($class_name = null)
    {
        if ($class_name === null) {
            $class_name = get_class($this);
        }
        $parent = get_parent_class($class_name);

        if ($parent === false) {
            return $class_name;
        }

        return $this->getSuperParent($parent);
    }

    /**
     * Return the current template's id
     *
     * @return integer
     */
    final public function getId()
    {
        return $this->__id;
    }

    /**
     * Create an auxiliary instance (as singleton)
     *
     * @param object $from
     */
    final public static function createAuxiliaryEngine(&$from = null)
    {
        if ($from === null) {
            $class_name = self::$__super_class;
        } // auxiliary engine is brother of $__super_class
        elseif (is_string($from)) {
            if (class_exists($from)) {
                $class_name = $from;
            } else {
                self::error("Class $from not found during creation of auxiliary engine");
                $class_name = self::$__super_class;
            }
        } else {
            $class_name = get_class($from);
        } // auxiliary engine is brother of $from

        if ((self::$__engine !== null) && get_class(self::$__engine) !== $class_name) {
            self::$__engine = null;
        }

        if (self::$__engine === null) {
            if (self::$__log_mode) {
                self::log("createAuxiliaryEngine: A new $class_name instance will be created ...");
            }

            $tmp = self::$__discard_file_system;
            self::$__discard_file_system = true;
            self::$__engine = new $class_name ('', []); // auxiliary engine have empty src

            if (is_object($from)) // auxiliary engine have optional items
            {
                self::$__engine->__items = $from->__items;
                self::$__engine->__items_orig = $from->__items_orig;
            }
            self::$__discard_file_system = $tmp;
        }
    }

    /**
     * Create a clone of auxiliary
     *
     * @param mixed  $items
     * @param mixed  $items_orig
     * @param object $from
     * @param string $src
     * @param string $src_orig
     *
     * @return div
     */
    final public static function getAuxiliaryEngineClone(&$items = null, &$items_orig = null, $from = null, $src = null, $src_orig = null)
    {
        $from_class = null;

        if (is_string($from)) {
            $from_class = $from;
        } elseif ($from !== null) {
            $from_class = get_class($from);
        }

        if ($from !== null) {

            if (self::$__engine !== null && $from_class === get_class(self::$__engine)) {
                $obj = clone self::$__engine;
            } else {
                $temp_engine = null;
                if (self::$__engine !== null) {
                    $temp_engine = clone self::$__engine;
                }
                self::createAuxiliaryEngine($from);
                $obj = clone self::$__engine;
                self::$__engine = $temp_engine;
            }
        } else {
            if (self::$__engine === null) {
                self::createAuxiliaryEngine($from);
            }
            $obj = clone self::$__engine;
        }

        if (self::$__log_mode) {
            self::log('getAuxiliaryEngineClone: New auxiliary #'.$obj->getId());
        }

        if ($items !== null) {
            $obj->__items = $items;
        }
        if ($items_orig !== null) {
            $obj->__items_orig = $items_orig;
        }
        if ($src !== null) {
            $obj->__src = $items;
        }
        if ($src_orig !== null) {
            $obj->__src_orig = $items_orig;
        }

        return $obj;
    }

    /**
     * Save parser's operationsd
     *
     * @param array $params
     */
    final public function saveOperation($params = [])
    {
        $id = crc32(serialize($params));
        if (!array_key_exists($this->__crc, self::$__remember)) {
            self::$__remember[$this->__crc] = [];
        }
        if (!array_key_exists($id, self::$__remember[$this->__crc])) {
            self::$__remember [$this->__crc][$id] = $params;
        }
    }

    /**
     * Return the saved operations in $__remember
     *
     * @return array:
     */
    final public static function getMemories()
    {
        return self::$__remember;
    }

    /**
     * Set operations saved previously
     *
     * @param array $memories
     */
    final public static function setMemories($memories)
    {
        foreach ($memories as $k => $v) {
            self::$__remember[$k] = $v;
        }
    }

    /**
     * Add a custom variable's modifier
     *
     * @param string $prefix
     * @param string $function
     */
    final public static function addCustomModifier($prefix, $function)
    {
        self::$__custom_modifiers [$prefix] = [$prefix, $function];
        self::$__modifiers [] = $prefix;
    }

    /**
     * Enable system var for utility
     *
     * @param string $var
     */
    final public static function enableSystemVar($var)
    {
        self::$__system_data_allowed [$var] = true;
    }

    /**
     * Disable system var for performance
     *
     * @param string $var
     */
    final public static function disableSystemVar($var)
    {
        if (array_key_exists($var, self::$__system_data_allowed)) {
            unset (self::$__system_data_allowed[$var]);
        }
    }

    /**
     * Return the loaded data from the system
     *
     * @return array
     */
    final public static function getSystemData()
    {
        $d = DIV_TAG_VAR_MEMBER_DELIMITER;
        if (self::$__system_data === null) {
            self::$__system_data = [];

            if (array_key_exists("div{$d}ascii", self::$__system_data_allowed)) {
                $ascii = [];
                for ($i = 0; $i <= 255; $i++) {
                    $ascii [$i] = chr($i);
                }
                self::$__system_data ["div{$d}ascii"] = $ascii;
            }

            if (array_key_exists("div{$d}now", self::$__system_data_allowed)) {
                self::$__system_data ["div{$d}now"] = time();
            }
            if (array_key_exists("div{$d}post", self::$__system_data_allowed)) {
                self::$__system_data ["div{$d}post"] = $_POST;
            }
            if (array_key_exists("div{$d}get", self::$__system_data_allowed)) {
                self::$__system_data ["div{$d}get"] = $_GET;
            }
            if (array_key_exists("div{$d}server", self::$__system_data_allowed)) {
                self::$__system_data ["div{$d}server"] = $_SERVER;
            }
            if (array_key_exists("div{$d}session", self::$__system_data_allowed)) {
                self::$__system_data ["div{$d}session"] = isset($_SESSION) ? $_SESSION : [];
            }
            if (array_key_exists("div{$d}version", self::$__system_data_allowed)) {
                self::$__system_data ["div{$d}version"] = self::$__version;
            }
            if (array_key_exists("div{$d}script_name", self::$__system_data_allowed)) {
                $script_name = explode('/', $_SERVER ['SCRIPT_NAME']);
                $script_name = $script_name [count($script_name) - 1];
                self::$__system_data ["div{$d}script_name"] = $script_name;
            }

            if (array_key_exists("div{$d}level", self::$__system_data_allowed)) {
                self::$__system_data ["div{$d}level"] = self::$__parse_level;
            }

        }

        return self::$__system_data;
    }

    /**
     * Set allowed function
     *
     * @param string $function_name
     */
    final public static function setAllowedFunction($function_name)
    {
        self::$__allowed_functions [$function_name] = true;
    }

    /**
     * Unset allowed function
     *
     * @param string $function_name
     */
    final public static function unsetAllowedFunction($function_name)
    {
        self::$__allowed_functions [$function_name] = false;
    }

    /**
     * Add or set a global var
     *
     * @param string $var
     * @param mixed  $value
     */
    final public static function setGlobal($var, $value)
    {
        self::$__globals [$var] = $value;
    }

    /**
     * Remove a global var
     *
     * @param string $var
     */
    final public static function delGlobal($var)
    {
        unset (self::$__globals [$var]);
    }

    /**
     * Add or set a default replacement of value
     *
     * @param mixed $search
     * @param mixed $replace
     */
    final public static function setDefault($search, $replace)
    {
        self::$__defaults [serialize($search)] = $replace;
    }

    /**
     * Add or set a default replacement of value for a specific var
     *
     * @param string  $var
     * @param mixed   $search
     * @param mixed   $replace
     * @param boolean $update
     */
    final public static function setDefaultByVar($var, $search, $replace, $update = true)
    {
        $id = serialize($search);
        if (!array_key_exists($var, self::$__defaults_by_var)) {
            self::$__defaults_by_var [$var] = [];
        }
        if ($update === true && !array_key_exists($id, self::$__defaults_by_var [$var])) {
            self::$__defaults_by_var [$var] [$id] = $replace;
        }
    }

    /**
     * Set a sub-parser
     *
     * @param mixed $name
     * @param mixed $function
     */
    final public static function setSubParser($name, $function = null)
    {
        if (is_array($name)) {
            if ($function === null) {
                foreach ($name as $key => $value) {
                    if (is_numeric($key)) {
                        self::$__sub_parsers [$value] = $value;
                    } else {
                        self::$__sub_parsers [$key] = $value;
                    }
                }
            } elseif (is_array($function)) {
                foreach ($name as $key => $value) {
                    self::$__sub_parsers [$value] = $function [$key];
                }
            } else {
                foreach ($name as $key => $value) {
                    self::$__sub_parsers [$value] = $function;
                }
            }
        } else {
            if ($function === null) {
                $function = $name;
            }
            self::$__sub_parsers [$name] = $function;
        }
        self::repairSubParsers();
    }

    /**
     * Repair the sub-parsers and their events
     */
    final public static function repairSubParsers()
    {
        $events = [
            'beforeParse',
            'afterInclude',
            'afterParse',
            'afterReplace',
        ];
        $news = [];

        foreach (self::$__sub_parsers as $parser => $function) {
            $arr = explode(':', $parser);

            if (array_key_exists(1, $arr)) {
                $last = array_pop($arr);
                if (in_array($last, $events, true)) {
                    continue;
                }
            }

            foreach ($events as $event) {
                if (!array_key_exists("$parser:$event", self::$__sub_parsers)) {
                    $news ["$parser:$event"] = $function;
                }
            }
        }

        self::$__sub_parsers = array_merge(self::$__sub_parsers, $news);
    }

    /**
     * Load template from filesystem
     *
     * @param string $template_path
     *
     * @return string
     */
    final public function loadTemplate($template_path)
    {
        if (self::$__log_mode === true) {
            $this->logger("Loading the template: $template_path");
        }

        $src = $template_path;

        if (strlen($template_path) < 255) {
            $paths = [
                $template_path,
                $template_path.'.'.DIV_DEFAULT_TPL_FILE_EXT,
                $template_path,
                $template_path.'.'.DIV_DEFAULT_TPL_FILE_EXT,
            ];

            foreach ($paths as $path) {
                if ((strlen($path) < 255) && self::fileExists($path)) {
                    $src = self::getFileContents($path);
                    $this->__path = str_replace('\\', '/', $path);
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
    final public function changeTemplate($src = null)
    {
        $class_name = get_class($this);
        $discard_fs = self::$__discard_file_system;

        if ($src === null) {
            if ($class_name !== self::$__super_class && $this->__src === null) {
                $src = $class_name;
            }
            if ($this->__src !== null) {
                $src = $this->__src;
            }
        }

        if (!$discard_fs) {
            $src = $this->loadTemplate($src);
        }

        $this->__src = $src;
        $this->__src_original = $src;
    }

    /**
     * Return the code of current template
     *
     * @return string
     */
    final public function getTemplate()
    {
        return $this->__src;
    }

    /**
     * Return the original code of template
     *
     * @return string
     */
    final public function getOriginalTemplate()
    {
        return $this->__src_original;
    }

    /**
     * Remove a default replacement
     *
     * @param mixed $search
     */
    final public static function delDefault($search)
    {
        $id = serialize($search);
        if (array_key_exists($id, self::$__defaults)) {
            unset (self::$__defaults [$id]);
        }
    }

    /**
     * Remove a default replacement by var
     *
     * @param string $var
     * @param mixed  $search
     */
    final public static function delDefaultByVar($var, $search)
    {
        if (array_key_exists($var, self::$__defaults_by_var)) {
            $id = serialize($search);
            if (array_key_exists($id, self::$__defaults_by_var[$var])) {
                unset (self::$__defaults_by_var [$var] [$id]);
            }
        }
    }

    /**
     * Add or Set item of information
     *
     * @param string $var
     * @param mixed  $value
     *
     * @return mixed
     */
    final public function setItem($var, $value = null)
    {
        if (is_array($var)) {
            $r = [];
            foreach ($var as $idx => $val) {
                if (self::issetVar($idx, $this->__items)) {
                    $r [$idx] = self::getVarValue($idx, $this->__items);
                } else {
                    $r [$idx] = null;
                }

                self::setVarValue($idx, $val, $this->__items);
            }

            return $r;
        }

        if (self::issetVar($var, $this->__items)) {
            $item = self::getVarValue($var, $this->__items);
        } else {
            $item = null;
        }

        self::setVarValue($var, $value, $this->__items);

        return $item;
    }

    /**
     * Delete an item of information
     *
     * @param string $var
     *
     * @return boolean
     */
    final public function delItem($var)
    {
        return self::unsetVar($var, $this->__items);
    }

    /**
     * Return an item
     *
     * @param string $var
     * @param mixed  $default
     *
     * @return mixed
     */
    final public function getItem($var, $default = null)
    {
        if (!self::issetVar($var, $this->__items)) {
            return $default;
        }

        return self::getVarValue($var, $this->__items);
    }

    /**
     * Return all items (in memory and context)
     *
     * @param array $items
     *
     * @return array
     */
    final public function getAllItems($items = [])
    {
        $i = $this->__items;
        $m = $this->__memory;
        $g = self::$__globals_design;
        $i = self::cop($i, $m);
        $i = self::cop($i, $g);
        $i = self::cop($i, $items);

        return $i;
    }

    /**
     * Return a list of block's ranges
     *
     * @param string  $tag_ini
     * @param string  $tag_end
     * @param string  $src
     * @param boolean $only_first
     * @param integer $pos
     *
     * @return array
     */
    final public function getRanges($tag_ini, $tag_end, $src = null, $only_first = false, $pos = 0)
    {
        $ranges = [];
        if (($src === null) && isset($this)) {
            $src = $this->__src;
        }

        if (!empty ($src) && ($src !== null) && isset($src [0])) {
            $tag_ini_len = strlen($tag_ini);
            $tag_end_len = strlen($tag_end);
            do {
                $ini = false;

                if (isset($src [$pos])) {
                    $ini = strpos($src, $tag_ini, $pos);
                }

                if (($ini !== false) && isset($src [$ini + $tag_ini_len])) {
                    $fin = strpos($src, $tag_end, $ini + $tag_ini_len);
                    if ($fin !== false) {
                        $l = strlen($src);
                        $last_pos = -1;
                        while (true) {
                            $ini = strpos($src, $tag_ini, $pos);
                            if ($ini === false || ($ini !== false && $pos === $last_pos)) {
                                break;
                            }
                            $end = false;
                            $plus = 1;
                            $tag_pos_right = $ini + $tag_ini_len;
                            $last_tag_pos_right = $tag_pos_right - 1;

                            while (true) {
                                $open = strpos($src, $tag_ini, $tag_pos_right);
                                $close = strpos($src, $tag_end, $tag_pos_right);

                                if ($open === false && $close === false) {
                                    break;
                                } // not open and not close
                                if ($open === false && $close !== false && $tag_pos_right === $last_tag_pos_right) {
                                    break;
                                } // close and not open
                                if ($open !== false && $close === false && $tag_pos_right === $last_tag_pos_right) {
                                    break;
                                } // open and not close

                                if ($open !== false || $close !== false) { // open or close
                                    if (($close < $open || $open === false) && $close !== false) { // close if is closed and before open or not open
                                        $last_tag_pos_right = $tag_pos_right;
                                        $tag_pos_right = $close + $tag_end_len;
                                        $plus--;
                                        // IMPORTANT! Don't separate elseif
                                    } elseif (($open < $close || $close === false) && $open !== false) { // open if is opened and before close or not close
                                        $last_tag_pos_right = $tag_pos_right;
                                        $tag_pos_right = $open + $tag_ini_len;
                                        $plus++;
                                    }
                                }

                                if ($plus === 0) { // all opens are closed
                                    $end = $close;
                                    break;
                                }

                                if ($open >= $l) {
                                    break;
                                }
                            }

                            $last_pos = $pos;

                            if ($end !== false) {
                                $ranges [] = [
                                    $ini,
                                    $end,
                                ];
                                if ($only_first === true) {
                                    break;
                                }
                                $pos = $ini + $tag_ini_len;
                                continue;
                            }
                        }
                    }
                }

                if (!isset($ranges [0]) && $ini !== false) {
                    if (self::$__log_mode && isset($this)) {
                        foreach ($this->__items as $key => $value) {
                            if (strpos($tag_ini, $key) !== false) {
                                $this->logger('Unclosed tag '.$tag_ini.' at '.$ini.' character', DIV_ERROR_WARNING);
                                break;
                            }
                        }
                    }

                    $pos = $ini + 1;

                    continue;
                }

                break;
            } while (true);
        }

        return $ranges;
    }

    /**
     * Return a list of ranges of blocks
     *
     * @param string  $src
     * @param string  $begin_prefix
     * @param string  $begin_suffix
     * @param string  $end_prefix
     * @param string  $end_suffix
     * @param integer $after
     * @param integer $before
     * @param boolean $only_first
     * @param string  $var_member_delimiter
     *
     * @return array
     */
    final public function getBlockRanges($src = null, $begin_prefix = '{', $begin_suffix = '}', $end_prefix = '{/', $end_suffix = '}', $after = 0, $before = null, $only_first = false, $var_member_delimiter = DIV_TAG_VAR_MEMBER_DELIMITER)
    {
        if ($src === null) {
            $src = $this->__src;
        }
        if ($before !== null) {
            $src = substr($src, 0, $before);
        }

        $l = strlen($src);
        $l1 = strlen($begin_prefix);
        $tags_done = [];
        $ranges = [];
        $from = $after;
        $delimiter_len = strlen($var_member_delimiter);
        do {
            $prefix_pos = strpos($src, $begin_prefix, $from);
            if ($prefix_pos !== false) {
                if (isset($src [$prefix_pos + 1])) {

                    if ($begin_suffix !== '' && $begin_suffix !== null) {
                        $suffix_pos = strpos($src, $begin_suffix, $prefix_pos + 1);
                    } else {

                        $stop_chars = [
                            '<',
                            '>',
                            ' ',
                            "\n",
                            "\r",
                            "\t",
                        ];
                        $stop_pos = [];

                        foreach ($stop_chars as $k => $v) {
                            $continue = false;
                            $pp = false;
                            do {

                                $pp = strpos($src, $v, $pp !== false ? $pp + 1 : $prefix_pos);

                                if ($pp === false) {
                                    $continue = true;
                                    break;
                                }
                            } while (substr($src, $pp - $delimiter_len + 1, $delimiter_len) === $var_member_delimiter);

                            if ($continue) {
                                continue;
                            }

                            $stop_pos [] = $pp;
                        }

                        $suffix_pos = false;
                        if (count($stop_pos) > 0) {
                            $suffix_pos = min($stop_pos);
                        }
                    }

                    $key = '';
                    if ($suffix_pos < $l && $suffix_pos !== false) {
                        $key = substr($src, $prefix_pos + $l1, $suffix_pos - $prefix_pos - $l1);
                    }

                    if ($key !== '' && !isset($tags_done [$key])) {
                        $tag_begin = $begin_prefix.$key.$begin_suffix;
                        $tag_end = $end_prefix.$key.$end_suffix;

                        $tag_begin_ignore = empty($begin_suffix) ? $begin_prefix.$key.DIV_TAG_VAR_MEMBER_DELIMITER : false;
                        $tag_end_ignore = empty($end_suffix) ? $end_prefix.$key.DIV_TAG_VAR_MEMBER_DELIMITER : false;
                        $tag_begin_ignore_len = $tag_begin_ignore === false ? 0 : strlen($tag_begin_ignore);
                        $tag_end_ignore_len = $tag_end_ignore === false ? 0 : strlen($tag_end_ignore);
                        $tag_begin_ignore_replace = substr(str_repeat(uniqid('', true), $tag_begin_ignore_len), 0, $tag_begin_ignore_len);
                        $tag_end_ignore_replace = substr(str_repeat(uniqid('', true), $tag_end_ignore_len), 0, $tag_end_ignore_len);
                        $temporal_src = $src;
                        if ($tag_begin_ignore !== false) {
                            $temporal_src = str_replace($tag_begin_ignore, $tag_begin_ignore_replace, $temporal_src);
                        }
                        if ($tag_end_ignore !== false) {
                            $temporal_src = str_replace($tag_end_ignore, $tag_end_ignore_replace, $temporal_src);
                        }

                        $rs = $this->getRanges($tag_begin, $tag_end, $temporal_src, $only_first, $from);
                        $l2 = strlen($tag_begin);
                        foreach ($rs as $k => $v) {
                            $rs [$k] [2] = $key;
                            $rs [$k] [3] = substr($src, $v [0] + $l2, $v [1] - $v [0] - $l2);
                            $rs [$k] [4] = strlen($key);
                        }
                        $ranges = array_merge($ranges, $rs);

                        // Only the first...
                        if ($only_first && isset($ranges [0])) {
                            break;
                        }

                        $tags_done [$key] = true;
                    }
                }
                $from = $prefix_pos + 1;
            }
        } while ($prefix_pos !== false);

        return $ranges;
    }

    /**
     * Return a default replacement of value
     *
     * @param mixed $value
     *
     * @return mixed
     */
    final public static function getDefault($value)
    {
        $id = serialize($value);
        if (array_key_exists($id, self::$__defaults)) {
            return self::$__defaults [$id];
        }

        return $value;
    }

    /**
     * Return a default replacement of value by var
     *
     * @param string $var
     * @param mixed  $value
     *
     * @return mixed
     */
    final public static function getDefaultByVar($var, $value)
    {
        if (array_key_exists($var, self::$__defaults_by_var)) {
            $id = serialize($value);
            if (array_key_exists($id, self::$__defaults_by_var [$var])) {
                return self::$__defaults_by_var [$var] [$id];
            }
        }

        return $value;
    }

    // ------------------------------------------- SEARCHERS ----------------------------------------- //

    /**
     * Search a position in a list of ranges
     *
     * @param array   $ranges
     * @param integer $pos
     * @param boolean $strict
     *
     * @return boolean
     */
    final public function searchInRanges($ranges, $pos, $strict = false)
    {
        foreach ($ranges as $range) {
            if ($strict) {
                if ($pos > $range[0] && $pos < $range[1]) {
                    return true;
                }
            } elseif ($pos >= $range[0] && $pos <= $range[1]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Search $pos in the ranges of lists/loops in current source
     *
     * @param integer $pos
     *
     * @return boolean
     */
    final public function searchInListRanges($pos = 0)
    {
        $ranges = $this->getBlockRanges(null, DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_BEGIN_SUFFIX, DIV_TAG_LOOP_END_PREFIX, DIV_TAG_LOOP_END_SUFFIX);

        foreach ($ranges as $rang) {
            if ($pos > $rang [0] && $pos < $rang [1]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Search $pos before the first range of lists/loops in current source
     *
     * @param integer $pos
     *
     * @return boolean
     */
    final public function searchPreviousLoops($pos = 0)
    {
        $ranges = $this->getBlockRanges(null, DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_BEGIN_SUFFIX, DIV_TAG_LOOP_END_PREFIX, DIV_TAG_LOOP_END_SUFFIX);
        foreach ($ranges as $rang) {
            if ($pos > $rang [0]) {
                return $rang [0];
            }
        }

        return false;
    }

    /**
     * Return true if pos is after first range
     *
     * @param string  $tag_begin
     * @param string  $tag_end
     * @param integer $pos
     *
     * @return boolean
     */
    final public function searchPosAfterRange($tag_begin, $tag_end, $pos)
    {
        $ranges = $this->getRanges($tag_begin, $tag_end, null, true);
        if (isset($ranges [0]) && $ranges [0] [0] < $pos) {
            return true;
        }

        return false;
    }

    /**
     * Return true if pos is in the ranges of capsules of current source
     *
     * @param array   $items
     * @param integer $pos
     *
     * @return boolean
     */
    final public function searchInCapsuleRanges(&$items = null, $pos = 0)
    {
        if ($items === null) {
            $items = &$this->__items;
        }

        foreach ($items as $key => $value) {
            $ranges = $this->getRanges(DIV_TAG_CAPSULE_BEGIN_PREFIX.$key, DIV_TAG_CAPSULE_END_PREFIX.$key);
            foreach ($ranges as $range) {
                if ($pos >= $range[0] && $pos <= $range[1]) {
                    return true;
                }
            }
        }

        return false;
    }

    // -------------------------------------------------------------------------------------------------- //

    /**
     * Return any value as a boolean
     *
     * @param mixed $value
     *
     * @return boolean
     */
    final public static function mixedBool($value)
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_object($value)) {
            return count(get_object_vars($value)) > 0;
        }
        if (is_array($value)) {
            return count($value) > 0;
        }
        if (self::isString($value)) {
            if (strtolower($value) === 'false' || $value === '0') {
                return false;
            }
            if (strtolower($value) === 'true' || $value === '1') {
                return true;
            }

            return strlen(trim($value)) > 0;
        }
        if (is_numeric($value)) {
            return $value > 0;
        }
        if ($value === null) {
            return false;
        }

        return $value;
    }

    /**
     * Return the correct @else@ tag of conditional block
     *
     * @param string $sub_src
     *
     * @return mixed
     */
    final public function getElseTag($sub_src)
    {
        $range_conditions = $this->getRanges(DIV_TAG_CONDITIONS_BEGIN_PREFIX, DIV_TAG_CONDITIONS_END, $sub_src);
        $range_conditionals = $this->getConditionalRanges(true, $sub_src);
        $ranges = array_merge($range_conditions, $range_conditionals);

        $else_pos = 0;
        $ls = strlen($sub_src);
        do {
            $continue = false;
            if ($else_pos < $ls) {
                $else = strpos($sub_src, DIV_TAG_ELSE, $else_pos);
            } else {
                $else = false;
            }

            // checking that the tag doesn't belong to another IF inside this IF
            if ($else !== false) {
                foreach ($ranges as $r) {
                    if ($else >= $r [0] && $else <= $r [1]) {
                        $else_pos = $r [1] + 1;
                        $else = false;
                        $continue = true;
                        break;
                    }
                }
            }
        } while ($continue === true);

        return $else;
    }

    /**
     * Return the correct DIV_TAG_EMPTY tag of list block
     *
     * @param string $sub_src
     *
     * @return mixed
     */
    final public function getEmptyTag($sub_src)
    {
        $ranges = $this->getRanges(DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_END_PREFIX, $sub_src);

        $empty_pos = 0;
        $ls = strlen($sub_src);
        do {
            $continue = false;
            if ($empty_pos < $ls) {
                $empty = strpos($sub_src, DIV_TAG_EMPTY, $empty_pos);
            } else {
                $empty = false;
            }

            // checking that the tag doesn't belong to another list block inside this list block
            if ($empty !== false) {
                foreach ($ranges as $r) {
                    if ($empty >= $r [0] && $empty <= $r [1]) {
                        $empty_pos = $r [1] + 1;
                        $empty = false;
                        $continue = true;
                        break;
                    }
                }
            }
        } while ($continue === true);

        return $empty;
    }

    /**
     * Parse conditional blocks
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return string
     */
    final public function parseConditionalBlock($key, $value)
    {
        if (self::$__log_mode) {
            $this->logger('Parsing conditional block: '.$key);
        }

        if (isset($this->__ignore[$key]) && $this->__ignore [$key] === true) {
            return false;
        }

        $src = &$this->__src;

        if (is_array($value) || is_object($value)) {
            $vars = $value;
            if (is_object($vars)) {
                $vars = get_object_vars($vars);
            }
            foreach ($vars as $k => $val) {
                if (is_numeric($k)) {
                    break;
                }
                $this->parseConditionalBlock($key.DIV_TAG_VAR_MEMBER_DELIMITER.$k, $val);
            }
        }

        $value = self::mixedBool($value);
        $passes = [false, true];
        $pos = 0;

        foreach ($passes as $flag) {

            if ($flag === false) {
                $tag_begin = DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX.$key.DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX;
                $tag_end = DIV_TAG_CONDITIONAL_TRUE_END_PREFIX.$key.DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX;
                $tag_begin_ignore = empty(DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX) ? DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX.$key.DIV_TAG_VAR_MEMBER_DELIMITER : false;
                $tag_end_ignore = empty(DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX) ? DIV_TAG_CONDITIONAL_TRUE_END_PREFIX.$key.DIV_TAG_VAR_MEMBER_DELIMITER : false;
            } else {
                $tag_begin = DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX.$key.DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX;
                $tag_end = DIV_TAG_CONDITIONAL_FALSE_END_PREFIX.$key.DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX;
                $tag_begin_ignore = empty(DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX) ? DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX.$key.DIV_TAG_VAR_MEMBER_DELIMITER : false;
                $tag_end_ignore = empty(DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX) ? DIV_TAG_CONDITIONAL_FALSE_END_PREFIX.$key.DIV_TAG_VAR_MEMBER_DELIMITER : false;
            }

            $tag_begin_len = strlen($tag_begin);
            $tag_end_len = strlen($tag_end);
            $tag_else_len = strlen(DIV_TAG_ELSE);
            $tag_begin_ignore_len = $tag_begin_ignore === false ? 0 : strlen($tag_begin_ignore);
            $tag_end_ignore_len = $tag_end_ignore === false ? 0 : strlen($tag_end_ignore);

            $tag_begin_ignore_replace = substr(str_repeat(uniqid('', true), $tag_begin_ignore_len), 0, $tag_begin_ignore_len);
            $tag_end_ignore_replace = substr(str_repeat(uniqid('', true), $tag_end_ignore_len), 0, $tag_end_ignore_len);

            while (true) {

                if (strpos($src, $tag_begin) === false) {
                    break;
                }

                $temporal_src = $src;
                if ($tag_begin_ignore !== false) {
                    $temporal_src = str_replace($tag_begin_ignore, $tag_begin_ignore_replace, $temporal_src);
                }
                if ($tag_end_ignore !== false) {
                    $temporal_src = str_replace($tag_end_ignore, $tag_end_ignore_replace, $temporal_src);
                }
                $ranges = $this->getRanges($tag_begin, $tag_end, $temporal_src, true, $pos);

                if (count($ranges) > 0) {

                    list($ini, $fin) = $ranges[0];

                    // Controlling injected vars
                    // _is_last _is_first _is_odd _is_even

                    if (in_array($key, ['_is_last', '_is_first', '_is_odd', '_id_even'])
                        && $this->isBlockInsideBlock(DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_END_PREFIX, $ini, $fin)) {
                        $pos = $fin + 1;
                        if (self::$__log_mode) {
                            $this->logger("Ignore the injected var inside another list block: $key..");
                        }
                        continue;
                    }

                    if ($this->searchPosAfterRange(DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END, $ini)) {
                        $pos = $fin + 1;
                        continue;
                    }

                    $sub_src = substr($src, $ini + $tag_begin_len, $fin - ($ini + $tag_begin_len));
                    $else = $this->getElseTag($sub_src);

                    if ($value === $flag) {
                        if ($else !== false) {
                            $con = substr($src, $ini + $tag_begin_len + $else + $tag_else_len, $fin - ($ini + $tag_begin_len + $else + $tag_else_len));
                            if (strpos($con, ' ') === 0) {
                                $con = substr($con, 1);
                            }
                            if (substr($con, -1) === ' ') {
                                $con = substr_replace($con, '', -1);
                            }
                            $src = substr($src, 0, $ini).$con.substr($src, $fin + $tag_end_len);
                        } else {
                            $src = substr($src, 0, $ini).substr($src, $fin + $tag_end_len);
                        }
                    } elseif ($else !== false) {
                        $con = substr($src, $ini + $tag_begin_len, $else);
                        if (strpos($con, ' ') === 0) {
                            $con = substr($con, 1);
                        }
                        if (substr($con, -1) === ' ') {
                            $con = substr_replace($con, '', -1);
                        }
                        $src = substr($src, 0, $ini).$con.substr($src, $fin + $tag_end_len);
                    } else {
                        $con = substr($src, $ini + $tag_begin_len, $fin - ($ini + $tag_begin_len));
                        if (strpos($con, ' ') === 0) {
                            $con = substr($con, 1);
                        }
                        if (substr($con, -1) === ' ') {
                            $con = substr_replace($con, '', -1);
                        }
                        $src = substr($src, 0, $ini).$con.substr($src, $fin + $tag_end_len);
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
     * @param mixed  $value
     *
     * @return boolean
     */
    final public function numberFormat($key, $value)
    {
        if (isset($this->__ignore [$key]) && $this->__ignore [$key] === true) {
            return false;
        }

        $tag_begin = DIV_TAG_NUMBER_FORMAT_PREFIX.$key.DIV_TAG_NUMBER_FORMAT_SEPARATOR;
        $tag_end = DIV_TAG_NUMBER_FORMAT_SUFFIX;
        $l1 = strlen($tag_begin);
        $l2 = strlen($tag_end);
        if (strpos($this->__src, $tag_begin) === false) {
            return false;
        }
        if (strpos($this->__src, $tag_end) === false) {
            return false;
        }

        $p1 = strpos($this->__src, DIV_TAG_TPLVAR_BEGIN);
        $pos = 0;
        while (true) {
            $ranges = $this->getRanges($tag_begin, $tag_end, null, true, $pos);

            if (count($ranges) < 1) {
                break;
            }

            list($ini, $fin) = $ranges[0];

            if (self::$__log_mode) {
                $this->logger("Formatting number $key = $value");
            }

            if ($ini > $p1 && $p1 !== false) {
                return true;
            }

            if ($this->searchPosAfterRange(DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END, $ini)) {
                $pos = $ini + 1;
                continue;
            }

            if ($this->searchInListRanges($ini)) {
                $pos = $ini + 1;
                continue;
            }

            $format = substr($this->__src, $ini + $l1, $fin - ($ini + $l1));
            if (trim($format) === '') {
                $format = '0.';
            }

            if (!is_numeric($value)) {
                $this->__src = substr($this->__src, 0, $ini).DIV_TAG_NUMBER_FORMAT_PREFIX.$value.DIV_TAG_NUMBER_FORMAT_SEPARATOR.$format.DIV_TAG_NUMBER_FORMAT_SUFFIX.substr($this->__src, $fin + $l2);

                return true;
            }

            $separator = '.';
            $miles_sep = '';

            if (!is_numeric(substr($format, strlen($format) - 1))) {
                $separator = substr($format, strlen($format) - 1);
                $format = substr($format, 0, strlen($format) - 1);
            }

            if (!is_numeric(substr($format, strlen($format) - 1))) {
                $miles_sep = $separator;
                $separator = substr($format, strlen($format) - 1);
                $format = substr($format, 0, strlen($format) - 1);
            }

            $decimals = (int)$format;
            $this->__src = substr($this->__src, 0, $ini).number_format($value, $decimals, $separator, $miles_sep).substr($this->__src, $fin + $l2);
        }

        return true;
    }

    /**
     * Parse sub-match
     *
     * @param mixed  $items
     * @param string $key
     * @param mixed  $value
     * @param array  $modifiers
     *
     * @return boolean
     */
    final public function parseSubMatch(&$items, $key, $value, $modifiers = [])
    {
        if (isset($this->__ignore [$key]) && $this->__ignore [$key] === true) {
            return false;
        }

        $literal = $this->isLiteral($key);

        $vpx = '';
        $vsx = '';

        if ($literal === true) {
            $vpx = '{'.$this->__ignore_secret_tag.'}';
            $vsx = '{/'.$this->__ignore_secret_tag.'}';
        }

        if (strpos($this->__src, $key.DIV_TAG_SUBMATCH_SEPARATOR) !== false) {
            foreach ($modifiers as $modifier) {
                while (true) {
                    $tag_begin = DIV_TAG_REPLACEMENT_PREFIX.$modifier.$key.DIV_TAG_SUBMATCH_SEPARATOR;
                    $ranges = $this->getRanges($tag_begin, DIV_TAG_REPLACEMENT_SUFFIX, null, true);
                    $l = strlen($tag_begin);
                    if (count($ranges) < 1) {
                        break;
                    }

                    if (self::$__log_mode) {
                        $this->logger("Parsing sub-match  $tag_begin");
                    }

                    // User wrote
                    $r = substr($this->__src, $ranges [0] [0] + $l, $ranges [0] [1] - ($ranges [0] [0] + $l));

                    // Interpreted by Div
                    $arr = explode(DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR, $r);
                    if (count($arr) < 2) {
                        $arr = [
                            0,
                            $arr [0],
                        ];
                    }
                    $arr [0] = trim($arr [0]);
                    $arr [1] = trim($arr [1]);

                    $new_key = str_replace('.', '', uniqid('submatch', true));

                    self::$__dont_remember_it [$new_key] = true;

                    if (!is_numeric($arr [0]) || !is_numeric($arr [1])) {
                        if (strpos((string)$arr[1], DIV_TAG_MODIFIER_TRUNCATE) === 0) {
                            $items [$new_key] = $vpx.self::teaser((string)$value, (int)substr($arr [1], 1)).$vsx;
                            $this->saveOperation([
                                'o'        => 'replace_sub_match_teaser',
                                'key'      => $key,
                                'modifier' => $modifier,
                                'param'    => $r,
                            ]);
                        } elseif (strpos((string)$arr[1], DIV_TAG_MODIFIER_WORDWRAP) === 0) {
                            $items [$new_key] = $vpx.wordwrap((string)$value, (int)substr($arr [1], 1), "\n", 1).$vsx;
                            $this->saveOperation([
                                'o'        => 'replace_sub_match_wordwrap',
                                'key'      => $key,
                                'modifier' => $modifier,
                                'param'    => $r,
                            ]);
                        } elseif (strpos((string)$arr[1], DIV_TAG_MODIFIER_FORMAT) === 0 || DIV_TAG_MODIFIER_FORMAT === '') {
                            $items [$new_key] = $vpx.sprintf($arr [1], $value).$vsx;
                            $this->saveOperation([
                                'o'        => 'replace_sub_match_sprintf',
                                'key'      => $key,
                                'modifier' => $modifier,
                                'param'    => $r,
                            ]);
                        }
                    } else {
                        $items [$new_key] = $vpx.substr((string)$value, $arr [0], $arr [1]).$vsx;
                        $this->saveOperation([
                            'o'        => 'replace_sub_match_sub_str',
                            'key'      => $key,
                            'modifier' => $modifier,
                            'param'    => $r,
                            'from'     => $arr [0],
                            'for'      => $arr [1],
                        ]);
                    }

                    $right = '';

                    if ($ranges [0] [1] + 1 < strlen($this->__src)) {
                        $right = substr($this->__src, $ranges [0] [1] + strlen(DIV_TAG_REPLACEMENT_SUFFIX));
                    }
                    $this->__src = substr($this->__src, 0, $ranges [0] [0]).DIV_TAG_REPLACEMENT_PREFIX."{$modifier}$new_key".DIV_TAG_REPLACEMENT_SUFFIX.$right;
                }
            }
        }

        if (strpos($this->__src, (string)$key.DIV_TAG_VAR_MEMBER_DELIMITER) !== false) {
            if (is_object($value)) {
                $vars = get_object_vars($value);
                foreach ($vars as $kk => $v) {
                    $this->parseSubMatch($items, $key.DIV_TAG_VAR_MEMBER_DELIMITER.$kk, $v, $modifiers);
                }
            }
            if (is_array($value)) {
                foreach ($value as $kk => $v) {
                    $this->parseSubMatch($items, $key.DIV_TAG_VAR_MEMBER_DELIMITER.$kk, $v, $modifiers);
                }
            }
        }

        return true;
    }

    /**
     * Parsing sub matches
     *
     * @param mixed $items
     */
    final public function parseSubmatches(&$items = null)
    {
        if (self::$__log_mode) {
            $this->logger('Parsing sub-matches...');
        }
        if ($items === null) {
            $items = &$this->__items;
        }

        $modifiers = [];

        foreach (self::$__modifiers as $m) {
            if (strpos($this->__src, DIV_TAG_REPLACEMENT_PREFIX.$m) !== false) {
                $modifiers [] = $m;
            }
        }

        foreach ($items as $key => $value) {
            $this->parseSubMatch($items, $key, $value, $modifiers);
        }
    }

    /**
     * Parse friendly tags
     */
    final public function parseFriendly()
    {
        $tag_ini_len = strlen(DIV_TAG_FRIENDLY_BEGIN);
        $tag_end_len = strlen(DIV_TAG_FRIENDLY_END);
        while (true) {
            $r = $this->getRanges(DIV_TAG_FRIENDLY_BEGIN, DIV_TAG_FRIENDLY_END, null, true);
            if (count($r) < 1) {
                break;
            }
            list($ini, $end) = $r[0];
            $this->__src = substr($this->__src, 0, $ini).substr($this->__src, $ini + $tag_ini_len, $end - ($ini + $tag_ini_len)).substr($this->__src, $end + $tag_end_len);
        }
    }

    /**
     * Set a variable as literal
     *
     * @param string $var
     */
    final public function addLiteral($var)
    {
        if (is_string($var)) {
            $var = explode(' ', str_replace(',', ' ', $var));
        }

        $literals = self::getVarValue('div'.DIV_TAG_VAR_MEMBER_DELIMITER.'literals', self::$__globals_design);

        if ($literals === null || $literals === false) {
            $literals = [];
        }

        if (is_string($literals)) {
            $literals = explode(' ', str_replace(',', ' ', $literals));
        }

        $literals = array_merge($literals, $var);

        self::setVarValue('div'.DIV_TAG_VAR_MEMBER_DELIMITER.'literals', $literals, self::$__globals_design, true);
    }

    /**
     * Get literal vars from dynamic configuration
     */
    final public function getLiterals()
    {
        $val = self::getVarValue('div'.DIV_TAG_VAR_MEMBER_DELIMITER.'literals', $this->__memory);

        if ($val === null || $val === false) {
            return [];
        }

        if (is_string($val)) {
            $val = explode(' ', str_replace(',', ' ', $val));
        }

        $arr = [];
        foreach ($val as $v) {
            $v = trim($v);
            if ($v !== '') {
                $arr [$v] = $v;
            }
        }

        return $arr;
    }

    /**
     * Return if a var is literal or not
     *
     * @param string $key
     *
     * @return boolean
     */
    final public function isLiteral($key)
    {
        if (trim($key) === '') {
            return false;
        }

        $literals = $this->getLiterals();

        return array_key_exists($key, $literals);
    }

    /**
     * Parse matches
     *
     * @param string  $key
     * @param mixed   $value
     * @param object  $engine
     * @param boolean $ignore_logical_order
     *
     * @return boolean
     * @see [1] ignoring conditional tag length, don't worry
     *
     */
    final public function parseMatch($key, $value, &$engine, $ignore_logical_order = false)
    {
        if (isset($this->__ignore [$key]) && $this->__ignore [$key] === true) {
            return false;
        }

        $value = self::getDefault($value);
        $value = self::getDefaultByVar($key, $value);

        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            $value = '';
        }

        $is_string = self::isString($value);

        if ($is_string) {
            $value = (string)$value;
        }

        $literal = $this->isLiteral($key);

        $vpx = '';
        $vsx = '';

        if ($literal === true) {
            $vpx = '{'.$this->__ignore_secret_tag.'}';
            $vsx = '{/'.$this->__ignore_secret_tag.'}';
        }

        $prefix = DIV_TAG_REPLACEMENT_PREFIX;
        $suffix = DIV_TAG_REPLACEMENT_SUFFIX;

        if ($is_string || is_numeric($value)) {
            if (!$ignore_logical_order) {
                $p1 = strpos($this->__src, DIV_TAG_TPLVAR_BEGIN);
                $p2 = strpos($this->__src, DIV_TAG_MACRO_BEGIN);
            } else {
                $p1 = false;
                $p2 = false;
            }

            if ($p1 === false && $p2 === false) {
                $sub_str = $this->__src;
            } elseif ($p1 !== false && $p2 !== false) {
                $min = min($p1, $p2);
                $sub_str = substr($this->__src, 0, $min);
                $p1 = $min;
            } elseif ($p1 !== false && $p2 === false) {
                $sub_str = substr($this->__src, 0, $p1);
            } else {
                $sub_str = substr($this->__src, 0, $p2);
                $p1 = $p2;
            }

            if (strpos($value, $prefix.DIV_TAG_MODIFIER_SIMPLE.$key.$suffix) !== false) {
                $value = str_replace($prefix.DIV_TAG_MODIFIER_SIMPLE.$key.$suffix, '', $value);
                self::error("Was detected an infinite loop in recursive replacement of ${$key}.", DIV_ERROR_WARNING);
            }

            $px = false;
            foreach (self::$__modifiers as $lm) {
                $py = strpos($sub_str, $prefix.$lm.$key.$suffix);
                if ($py !== false) {
                    $px = $py;
                    break;
                }
            }

            if ($px !== false) {

                $replace_count = 0;
                if (self::$__log_mode) {
                    $this->logger("Parsing match: $key in '".substr($sub_str, 0, 50)."'");
                }

                $value = trim((string)$value);

                if (trim($value) !== '' && $literal === false) {
                    $crc = crc32($value);
                    $engine->__src = $value;
                    $engine->__items = $this->__items;
                    $engine->parseInclude($this->__items);
                    $engine->parsePreprocessed($this->__items);
                    $value = $engine->__src;
                    if (self::issetVar($key, $this->__items) && crc32($value) !== $crc && gettype(self::getVarValue($key, $this->__items)) === 'string') {
                        self::setVarValue($key, $value, $this->__items);
                    }
                }

                $mod = DIV_TAG_MODIFIER_SIMPLE;
                $sub_str = str_replace($prefix.$mod.$key.$suffix, $vpx.$value.$vsx, $sub_str, $replace_count); // simple replacement

                if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                    $this->saveOperation([
                        'o'        => 'simple_replacement',
                        'key'      => $key,
                        'modifier' => $mod,
                        'before'   => $p1,
                    ]);
                }

                $mod = DIV_TAG_MODIFIER_CAPITALIZE_FIRST;
                if (strpos($sub_str, $prefix.$mod) !== false) {
                    $sub_str = str_replace(DIV_TAG_REPLACEMENT_PREFIX.$mod.$key.$suffix, $vpx.ucfirst($value).$vsx, $sub_str, $replace_count);
                    if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                        $this->saveOperation([
                            'o'        => 'simple_replacement',
                            'key'      => $key,
                            'modifier' => $mod,
                            'before'   => $p1,
                        ]);
                    }
                }

                $mod = DIV_TAG_MODIFIER_CAPITALIZE_WORDS;
                if (strpos($sub_str, $prefix.$mod) !== false) {
                    $sub_str = str_replace($prefix.$mod.$key.$suffix, $vpx.ucwords($value).$vsx, $sub_str, $replace_count);
                    if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                        $this->saveOperation([
                            'o'        => 'simple_replacement',
                            'key'      => $key,
                            'modifier' => $mod,
                            'before'   => $p1,
                        ]);
                    }
                }

                $mod = DIV_TAG_MODIFIER_UPPERCASE;
                if (strpos($sub_str, $prefix.$mod) !== false) {
                    $sub_str = str_replace($prefix.$mod.$key.$suffix, $vpx.strtoupper($value).$vsx, $sub_str, $replace_count);
                    if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                        $this->saveOperation([
                            'o'        => 'simple_replacement',
                            'key'      => $key,
                            'modifier' => DIV_TAG_MODIFIER_UPPERCASE,
                            'before'   => $p1,
                        ]);
                    }
                }

                $mod = DIV_TAG_MODIFIER_LENGTH;
                if (strpos($sub_str, $prefix.$mod) !== false) {
                    $sub_str = str_replace($prefix.$mod.$key.$suffix, strlen($value), $sub_str, $replace_count);
                    if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                        $this->saveOperation([
                            'o'        => 'simple_replacement',
                            'key'      => $key,
                            'modifier' => $mod,
                            'before'   => $p1,
                        ]);
                    }
                }

                $mod = DIV_TAG_MODIFIER_COUNT_WORDS;
                if (strpos($sub_str, $prefix.$mod) !== false) {
                    $sub_str = str_replace($prefix.$mod.$key.$suffix, self::getCountOfWords($value), $sub_str, $replace_count);
                    if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                        $this->saveOperation([
                            'o'        => 'simple_replacement',
                            'key'      => $key,
                            'modifier' => $mod,
                            'before'   => $p1,
                        ]);
                    }
                }

                $mod = DIV_TAG_MODIFIER_COUNT_SENTENCES;
                if (strpos($sub_str, $prefix.$mod) !== false) {
                    $sub_str = str_replace($prefix.$mod.$key.$suffix, self::getCountOfSentences($value), $sub_str, $replace_count);
                    if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                        $this->saveOperation([
                            'o'        => 'simple_replacement',
                            'key'      => $key,
                            'modifier' => $mod,
                            'before'   => $p1,
                        ]);
                    }
                }

                $mod = DIV_TAG_MODIFIER_COUNT_PARAGRAPHS;
                if (strpos($sub_str, $prefix.$mod) !== false) {
                    $sub_str = str_replace($prefix.$mod.$key.$suffix, self::getCountOfParagraphs($value), $sub_str, $replace_count);
                    if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                        $this->saveOperation([
                            'o'        => 'simple_replacement',
                            'key'      => $key,
                            'modifier' => $mod,
                            'before'   => $p1,
                        ]);
                    }
                }

                $mod = DIV_TAG_MODIFIER_ENCODE_URL;
                if (strpos($sub_str, $prefix.$mod) !== false) {
                    $sub_str = str_replace($prefix.$mod.$key.$suffix, $vpx.urlencode($value).$vsx, $sub_str, $replace_count);
                    if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                        $this->saveOperation([
                            'o'        => 'simple_replacement',
                            'key'      => $key,
                            'modifier' => $mod,
                            'before'   => $p1,
                        ]);
                    }
                }

                $mod = DIV_TAG_MODIFIER_ENCODE_RAW_URL;
                if (strpos($sub_str, $prefix.$mod) !== false) {
                    $sub_str = str_replace($prefix.$mod.$key.$suffix, $vpx.rawurlencode($value).$vsx, $sub_str, $replace_count);
                    if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                        $this->saveOperation([
                            'o'        => 'simple_replacement',
                            'key'      => $key,
                            'modifier' => $mod,
                            'before'   => $p1,
                        ]);
                    }
                }

                $sub_str = str_replace([
                    $prefix.DIV_TAG_MODIFIER_LOWERCASE.$key.$suffix, // lower case
                    $prefix.DIV_TAG_MODIFIER_HTML_ENTITIES.$key.$suffix, // html entities
                    $prefix.DIV_TAG_MODIFIER_NL2BR.$key.$suffix, // convert newlines to <br/>
                    $prefix.DIV_TAG_MODIFIER_SINGLE_QUOTES.$key.$suffix, // escape unescaped single quotes,
                    $prefix.DIV_TAG_MODIFIER_JS.$key.$suffix,
                ], // escape quotes and backslashes, newlines, etc.
                    [
                        $vpx.strtolower($value).$vsx,
                        $vpx.htmlentities($value).$vsx,
                        $vpx.nl2br($value).$vsx,
                        $vpx.preg_replace([
                            "%(?<!\\\\\\\\)'%",
                            "%(?<!\\\\\\\\)\"%",
                        ], [
                            "\\'",
                            "\\\"",
                        ], $value).$vsx,
                        $vpx.strtr($value, [
                            "\\" => "\\\\",
                            "'"  => "\\'",
                            '"'  => "\\\"",
                            "\r" => "\\r",
                            "\n" => "\\n",
                            '</' => "<\/",
                        ]).$vsx,
                    ], $sub_str);

                foreach (self::$__custom_modifiers as $modifier) {
                    if (strpos($sub_str, $modifier[0]) === false) {
                        continue;
                    }

                    $call = $modifier[1];
                    $proceed = false;

                    // new from 5.0: closure functions & $this methods as custom modifiers
                    $callable_result = $this->call($call, [$value], $proceed);

                    if ($proceed) {
                        $sub_str = str_replace($prefix.$modifier [0].$key.$suffix, $vpx.$callable_result.$vsx, $sub_str);
                    }
                }

                if ($p1 !== false) {
                    $this->__src = $sub_str.substr($this->__src, $p1);
                } else {
                    $this->__src = $sub_str;
                }
            }
        }

        $mod = DIV_TAG_MODIFIER_ENCODE_JSON;
        if (strpos($this->__src, $prefix.$mod.$key.$suffix) !== false) {
            $this->__src = str_replace($prefix.$mod.$key.$suffix, $vpx.self::jsonEncode($value).$vsx, $this->__src, $replace_count);
            if ($replace_count > 0 && !isset(self::$__dont_remember_it [$key])) {
                $this->saveOperation([
                    'o'      => 'json_encode',
                    'key'    => $key,
                    'before' => false,
                ]);
            }
        }
    }

    /**
     * Parse iterations
     *
     * @param mixed  $items
     * @param string $src
     */
    final public function parseIterations(&$items, &$src = null)
    {
        $l1 = strlen(DIV_TAG_ITERATION_BEGIN_PREFIX);
        $l2 = strlen(DIV_TAG_ITERATION_END);

        if (self::$__log_mode) {
            $this->logger('Parsing iterations...');
        }

        if ($src === null) {
            $src = &$this->__src;
        }

        $last_ranges = [
            [
                -99,
            ],
        ];

        while (true) {
            $ranges = $this->getRanges(DIV_TAG_ITERATION_BEGIN_PREFIX, DIV_TAG_ITERATION_END, $src, true);

            if (count($ranges) < 1) {
                break;
            }
            if ($ranges[0][0] === $last_ranges[0][0]) {
                break;
            }

            $last_ranges = $ranges;

            list($p, $p2) = $ranges[0];
            $p1 = strpos($src, DIV_TAG_ITERATION_BEGIN_SUFFIX, $p + 1);
            $s = substr($src, $p + $l1, $p1 - ($p + $l1));

            $range = explode(DIV_TAG_ITERATION_PARAM_SEPARATOR, $s);
            $c = count($range);
            if ($c < 2) {
                $range[1] = $range[0];
                $range[0] = 1;
            }
            $iterator_var = 'value';
            $step = 1;

            if ($c === 3) {
                if (is_numeric($range[2])) {
                    $step = trim($range[2]) * 1;
                } else {
                    $iterator_var = trim($range[2]);
                }
            }

            if ($c === 4) {
                $iterator_var = $range[2];
                $step = trim($range[3]) * 1;
            }

            if (is_numeric($range[0]) && is_numeric($range[1])) {

                $range[0] = trim($range[0]) * 1;
                $range[1] = trim($range[1]) * 1;

                $key = uniqid('', true);

                $sub_src = substr($src, $p1 + $l1, $p2 - ($p1 + $l1));
                $iterator_var_code = " $iterator_var ".DIV_TAG_LOOP_VAR_SEPARATOR;

                if (strpos($sub_src, DIV_TAG_LOOP_VAR_SEPARATOR)) {
                    $iterator_var_code = '';
                }

                $src = substr($src, 0, $p).DIV_TAG_LOOP_BEGIN_PREFIX.$key.DIV_TAG_LOOP_BEGIN_SUFFIX.$iterator_var_code.$sub_src.DIV_TAG_LOOP_END_PREFIX.$key.DIV_TAG_LOOP_END_SUFFIX.substr($src, $p2 + $l2);

                $items[$key] = [];
                if ($range[1] >= $range[0]) {
                    for ($i = $range[0]; $i >= $range[0] && $i <= $range[1]; $i = $i + $step) {
                        $items[$key][] = $i;
                    }
                } else {
                    for ($i = $range[0]; $i >= $range[1] && $i <= $range[0]; $i = $i - $step) {
                        $items[$key][] = $i;
                    }
                }
            }
        }
    }

    /**
     * Return true if a block is inside another block
     *
     * @param string  $tag_ini
     * @param string  $tag_end
     * @param integer $pos1
     * @param integer $pos2
     * @param array   $ranges
     *
     * @return boolean
     */
    final public function isBlockInsideBlock($tag_ini, $tag_end, $pos1, $pos2, $ranges = null)
    {
        if ($ranges === null) {
            $ranges = $this->getRanges($tag_ini, $tag_end);
        }

        foreach ($ranges as $rang) {
            if ($rang [0] < $pos1 && $rang [1] > $pos2) {
                return true;
            }
        }

        return false;
    }

    /**
     * Parse list block
     *
     * @param mixed  $value
     * @param string $key
     * @param mixed  $items
     *
     * @return boolean
     */
    final public function parseListBlock($value, $key, $items)
    {
        if (isset($this->__ignore[$key]) && $this->__ignore [$key] === true) {
            return false;
        }

        $tag_begin = DIV_TAG_LOOP_BEGIN_PREFIX.$key.DIV_TAG_LOOP_BEGIN_SUFFIX;
        $tag_end = DIV_TAG_LOOP_END_PREFIX.$key.DIV_TAG_LOOP_END_SUFFIX;
        if (strpos($this->__src, $tag_begin) === false) {
            return false;
        }

        $l1 = strlen($tag_begin);
        $l2 = strlen($tag_end);
        $ranges = [];
        $pos = 0;

        while (true) {

            $lists = $this->getRanges($tag_begin, $tag_end, null, true, $pos);
            if (count($lists) < 1) {
                break;
            }
            if (self::$__log_mode) {
                $this->logger("Parsing the list: $key..");
            }

            $list = $lists[0];
            list($p1, $p2) = $list;

            // Checking logical order ...
            $r = $this->checkLogicalOrder($p1, '', false, false, false, true);
            if ($r !== false) {
                $pos = $p2 + 1;
                continue;
            }

            // Check if list is inside another list block ...
            if ($this->searchInListRanges($p1)) {
                $pos = $p2 + 1;
                continue;
            }

            $ranges[] = $list;

            if ($p2 > $p1) {
                $minihtml = substr($this->__src, $p1 + $l1, $p2 - $p1 - $l1);

                $itemkey = 'value';

                // The itemkey/itervar can't have space or newline chararters

                if (strpos($minihtml, DIV_TAG_LOOP_VAR_SEPARATOR) !== false) {
                    $arr = explode(DIV_TAG_LOOP_VAR_SEPARATOR, $minihtml, 2);
                    if (strpos($arr [0], "\n") === false) {
                        $arr [0] = trim($arr [0]);
                        if (strpos($arr [0], ' ') === false) {
                            if ($itemkey !== '') {
                                $itemkey = $arr [0];
                            }
                            $minihtml = $arr [1];
                        }
                    }
                }

                $ii = 0;
                $randoms = [];
                $count = count($value);

                $ii = 0;

                $keys = array_keys($value);
                $keys_count = count($keys);

                $go_index = strpos($minihtml, '_index') !== false;
                $go_key = strpos($minihtml, '_key') !== false;
                $go_index_random = strpos($minihtml, '_index_random') !== false;
                $go_is_odd = strpos($minihtml, '_is_odd') !== false;
                $go_is_even = strpos($minihtml, '_is_even') !== false;
                $go_is_first = strpos($minihtml, '_is_first') !== false;
                $go_is_last = strpos($minihtml, '_is_last') !== false;
                $go_list = strpos($minihtml, '_list') !== false;
                $go_item = strpos($minihtml, '_item') !== false;
                $go_order = strpos($minihtml, '_order') !== false;
                $go_previous = strpos($minihtml, '_previous') !== false;
                $go_next = strpos($minihtml, '_next') !== false;

                $x_items = [];
                $x_items_orig = [];

                // Preparing xitems data
                $previous = null;
                $next = null;

                $empty = $this->getEmptyTag($minihtml);
                if ($empty !== false) {
                    $body_parts = [
                        substr($minihtml, 0, $empty),
                        substr($minihtml, $empty + strlen(DIV_TAG_EMPTY)),
                    ];
                } else {
                    $body_parts = [
                        $minihtml,
                        '',
                    ];
                }

                $h = $body_parts[1];
                if (!empty($value)) {

                    $minihtml = $body_parts [0];

                    foreach ($value as $kk => $item) {

                        if (isset($keys [$ii + 1])) {
                            $next = $value [$keys [$ii + 1]];
                        } else {
                            $next = null;
                        }

                        $ii++;
                        $another = [];

                        $item_orig = $item;

                        if ($go_index) {
                            $another['_index'] = $ii - 1;
                        }

                        if ($go_key) {
                            $another['_key'] = $kk;
                        }

                        if ($go_index_random) {
                            do {
                                $random = mt_rand(1, $count);
                            } while (isset($randoms[$random]));

                            $randoms [$random] = true;
                            $another ['_index_random'] = $random - 1;
                        }
                        if ($go_is_odd) {
                            $another ['_is_odd'] = ($ii % 2 !== 0);
                        }
                        if ($go_is_even) {
                            $another ['_is_even'] = ($ii % 2 === 0);
                        }
                        if ($go_is_first) {
                            $another ['_is_first'] = ($ii === 1);
                        }
                        if ($go_is_last) {
                            $another ['_is_last'] = ($ii === $keys_count);
                        }
                        if ($go_list) {
                            $another ['_list'] = $key;
                        }

                        if ($go_item) {
                            if (is_object($item)) {
                                $another ['_item'] = clone $item;
                            } else {
                                $another ['_item'] = $item;
                            }
                        }

                        if ($go_order) {
                            $another ['_order'] = $ii;
                        }

                        if ($go_previous) {
                            if (is_object($previous)) {
                                $another ['_previous'] = clone $previous;
                            } else {
                                $another ['_previous'] = $previous;
                            }
                        }

                        if ($go_next) {
                            if (is_object($next)) {
                                $another ['_next'] = clone $next;
                            } else {
                                $another ['_next'] = $next;
                            }
                        }

                        $previous = $item;

                        if (is_object($item) && self::isString($item)) {
                            $itemstr = (string)$item;
                            if (!property_exists($item, 'value')) {
                                $item->value = $itemstr;
                            }
                            if (!property_exists($item, '_to_string')) {
                                $item->_to_string = $itemstr;
                            }
                        }

                        if (is_object($item)) {
                            $item = get_object_vars($item);
                        }

                        if (!is_array($item) || is_scalar($value)) {
                            $item = [
                                $itemkey => $item,
                            ];
                        } elseif ($itemkey !== 'value') {
                            $item [$itemkey] = array_merge($item, $another);
                        }

                        $item = array_merge($item, $another);
                        $x_items[] = $item;
                        $x_items_orig[] = self::cop($item_orig, $another);
                    }

                    // Parsing ...
                    $h = '';
                    $engine = self::getAuxiliaryEngineClone($x_items, $x_items, $this);
                    $engine->__src_original = $minihtml;
                    $engine->__memory = $this->__memory;
                    $engine->__path = $this->__path;

                    $globals_design = self::$__globals_design;

                    foreach ($x_items as $x_key => $item) {
                        // Save similar global design vars

                        $tempglobal = self::$__globals_design;
                        foreach ($item as $kkk => $vvv) {
                            if (array_key_exists($kkk, self::$__globals_design)) {
                                unset (self::$__globals_design [$kkk]);
                            }
                        }

                        if (self::$__log_mode) {
                            $this->logger("Parsing item $x_key of the list '$key'...");
                        }

                        $engine->__items[$x_key] = array_merge($items, $item);
                        $engine->__items_orig = $x_items_orig [$x_key];

                        // Save some vars
                        $memory = $engine->__memory;

                        foreach ($item as $kkk => $vvv) {
                            if (array_key_exists($kkk, $engine->__memory)) {
                                unset ($engine->__memory [$kkk]);
                            }
                        }

                        // Parse minihtml
                        $engine->parse(true, $x_key);

                        // Restore some vars
                        $engine->__memory = $memory;
                        $engine->__items [$x_key] = $item;
                        self::$__globals_design = array_merge($tempglobal, self::$__globals_design);

                        $break = strpos($engine->__src, DIV_TAG_BREAK);

                        foreach ($item as $kkk => $vvv) {
                            if (array_key_exists($kkk, $this->__memory)) {
                                unset ($this->__memory [$kkk]);
                            }
                        }

                        if ($break !== false) {
                            $engine->__src = substr($engine->__src, 0, $break);
                            $h .= $engine->__src;
                            break;
                        }

                        $h .= $engine->__src;
                    }

                    // Restore global design vars
                    self::$__globals_design = $globals_design;
                }

                // Replace
                $this->__src = substr($this->__src, 0, $p1).$h.substr($this->__src, $p2 + $l2);
            }
        }

        return true;
    }

    /**
     * Parse list
     *
     * @param mixed  $items
     * @param string $superkey
     *
     * @return bool
     */
    final public function parseList($items = null, $superkey = '')
    {
        if (self::$__log_mode) {
            $this->logger("Parsing loops, SUPERKEY = '$superkey'...");
        }

        if (isset($this->__ignore[$superkey]) && $this->__ignore[$superkey] === true) {
            return false;
        }

        if ($items === null) {
            $items = $this->__items;
        }

        if (!is_array($items) && is_object($items)) {
            $items = get_object_vars($items);
        }

        if (!is_array($items)) {
            return false;
        }

        if ($superkey !== '') {
            $superkey .= DIV_TAG_VAR_MEMBER_DELIMITER;
        }

        if (strpos($this->__src, DIV_TAG_LOOP_BEGIN_PREFIX.$superkey) !== false) {
            foreach ($items as $key => $value) {
                $key = $superkey.$key;
                if (strpos($this->__src, DIV_TAG_LOOP_BEGIN_PREFIX.$key.DIV_TAG_VAR_MEMBER_DELIMITER) !== false) {
                    if (!is_array($value)) {
                        if (is_object($value)) {
                            $value = get_object_vars($value);
                        } else {
                            continue;
                        }
                    }
                    $this->parseList($value, $key);
                }
            }
        }

        $pos = [];
        foreach ($items as $key => $value) {
            $p = strpos($this->__src, DIV_TAG_LOOP_BEGIN_PREFIX.$superkey.$key.DIV_TAG_LOOP_BEGIN_SUFFIX);
            if ($p !== false) {
                $pos [$key] = $p;
            }
        }

        asort($pos);

        foreach ($pos as $key => $v) {

            $value = $items [$key];

            if (is_scalar($value)) {
                $value = (string)$value;
            }
            if (self::isString($value)) {
                $value = str_split($value);
            }

            if (!is_array($value)) {
                if (is_object($value)) {
                    $value = get_object_vars($value);
                } else {
                    continue;
                }
            }

            $this->parseListBlock($value, $superkey.$key, $items);
        }
    }

    /**
     * Ignore parts of template
     *
     * @return array
     */
    final public function parseIgnore()
    {
        if (self::$__log_mode) {
            $this->logger("Parsing ignore's blocks...");
        }

        // Generate internal and random ignore tag (security reasons)
        if ($this->__ignore_secret_tag === null) {
            $this->__ignore_secret_tag = uniqid('', true);
        }

        for ($i = 0; $i < 2; $i++) {
            $tag_begin = DIV_TAG_IGNORE_BEGIN;
            $tag_end = DIV_TAG_IGNORE_END;

            if ($i === 1) {
                $tag_begin = '{'.$this->__ignore_secret_tag.'}';
                $tag_end = '{/'.$this->__ignore_secret_tag.'}';
            }

            $l1 = strlen($tag_begin);
            $l2 = strlen($tag_end);

            $pos = 0;
            while (true) {
                $ranges = $this->getRanges($tag_begin, $tag_end, null, true, $pos);
                if (count($ranges) < 1) {
                    break;
                }

                if (self::searchInRanges($this->getRanges(DIV_TAG_COMMENT_BEGIN, DIV_TAG_COMMENT_END), $ranges [0] [0]) !== false) {
                    $pos = $ranges [0] [1] + 1;
                    continue;
                }

                $id = uniqid('', true);
                self::$__ignored_parts [$id] = substr($this->__src, $ranges [0] [0] + $l1, $ranges [0] [1] - $ranges [0] [0] - $l1);
                $this->__src = substr($this->__src, 0, $ranges [0] [0]).'{'.$id.'}'.substr($this->__src, $ranges [0] [1] + $l2);
                $pos = $ranges [0] [0] + 1;
            }
        }

        return self::$__ignored_parts;
    }

    /**
     * Save value for the future (reduce calculation)
     *
     * @param $index
     * @param $resolver
     *
     * @return mixed
     */
    final public static function getCachedValue($index, $resolver) {
        if (!array_key_exists($index, self::$__cached_values)){
            self::$__cached_values[$index] = $resolver();
        }
        return self::$__cached_values[$index];
    }
    /**
     * Return the resolved path for include and preprocessed
     *
     * @param string $path
     *
     * @return string
     */
    final public function getTplPath($path)
    {
        $return = null;
        $fileExtLength = self::getCachedValue('length_of_tpl_file_ext_with_dot', static function() {
            return  strlen(DIV_DEFAULT_TPL_FILE_EXT) + 1;
        });

        if (substr($path, 0 - $fileExtLength) !== '.'.DIV_DEFAULT_TPL_FILE_EXT
            && self::fileExists($path.'.'.DIV_DEFAULT_TPL_FILE_EXT)) {
            $path .= '.'.DIV_DEFAULT_TPL_FILE_EXT;
        }

        /*$path = str_replace('.'.DIV_DEFAULT_TPL_FILE_EXT.'.'.DIV_DEFAULT_TPL_FILE_EXT,
            '.'.DIV_DEFAULT_TPL_FILE_EXT, $path);
        */

        // Relative path
        if ($this->__path !== '' && !self::fileExists($path) && self::fileExists($this->__path)) {
            $folder = self::getFolderOf($this->__path);
            if (self::fileExists($folder.'/'.$path.'.'.DIV_DEFAULT_TPL_FILE_EXT)) {
                $path .= '.'.DIV_DEFAULT_TPL_FILE_EXT;
            }
            if (self::fileExists($folder.'/'.$path)) {
                $return = $folder.'/'.$path;
            }
        }

        if ($return === null) {
            // Resolving with the history ...
            $max = 0;
            $return = $path;
            foreach (self::$__includes_history as $ih) {
                $folder = self::getFolderOf($ih);
                $full_path = $folder.'/'.$path;

                if (self::fileExists($full_path.'.'.DIV_DEFAULT_TPL_FILE_EXT)) {
                    $full_path .= '.'.DIV_DEFAULT_TPL_FILE_EXT;
                } elseif (self::fileExists($full_path.'.'.DIV_DEFAULT_TPL_FILE_EXT)) {
                    $full_path .= '.'.DIV_DEFAULT_TPL_FILE_EXT;
                }

                $similar = similar_text($ih, $full_path);
                if ((self::fileExists($full_path) || self::fileExists($full_path)) && $similar >= $max) {
                    $return = $full_path;
                }
            }
        }

        if (!self::fileExists($return)) {
            return null;
        }

        return $return;
    }

    /**
     * Detect recursive inclusion
     *
     * @param array   $exclusion
     * @param string  $path
     * @param integer $ini
     *
     * @return boolean
     */
    final public static function detectRecursiveInclusion($exclusion, $path, $ini)
    {
        if (trim($path) === '') {
            return false;
        }

        foreach ($exclusion as $exc) {
            $p = $exc ['path'];
            $i = $exc ['ini'];
            $f = $exc ['end'];
            if ($p === $path && $ini >= $i && $ini <= $f) {
                return true;
            }
        }

        return false;
    }

    /**
     * Secure is_string
     *
     * @param mixed $value
     *
     * @return boolean
     */
    final public static function isString($value)
    {
        if (is_string($value)) {
            return true;
        }
        if (is_object($value) && method_exists($value, '__toString')) {
            return true;
        }

        return false;
    }

    /**
     * Include others templates
     *
     * @param mixed $items
     */
    final public function parseInclude(&$items)
    {
        if (self::$__log_mode) {
            $this->logger('Parsing includes...');
        }

        $prefix = DIV_TAG_INCLUDE_BEGIN;
        $suffix = DIV_TAG_INCLUDE_END;

        if (is_object($items)) {
            $items = get_object_vars($items);
        }
        if (is_array($items)) {
            foreach ($items as $key => $value) {
                if (array_key_exists($key, $this->__ignore) && $this->__ignore[$key] === true) {
                    continue;
                }

                if (strpos($this->__src, $prefix.$key.$suffix) !== false && self::isString($value)) {
                    if (self::fileExists($value.'.'.DIV_DEFAULT_TPL_FILE_EXT)) {
                        $value .= '.'.DIV_DEFAULT_TPL_FILE_EXT;
                    }
                    $this->__src = str_replace($prefix.$key.$suffix, $prefix.$value.$suffix, $this->__src);
                }
            }
        }

        $restores = [];
        $pos = 0;

        $exclusion = [];

        $l1 = strlen($prefix);
        $l2 = strlen($suffix);

        while (true) {
            $ranges = $this->getRanges($prefix, $suffix, null, true, $pos);
            if (count($ranges) < 1) {
                break;
            }

            $ini = $ranges [0] [0];
            $fin = $ranges [0] [1];

            $ranges_x = $this->getRanges(DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END);
            $proceed = true;
            foreach ($ranges_x as $rx) {
                if ($ini >= $rx [0] && $ini <= $rx [1]) {
                    $pos = $fin + 1;
                    $proceed = false;
                    break;
                }
            }

            if (!$proceed) {
                continue;
            }

            if (self::searchInRanges($this->getRanges(DIV_TAG_COMMENT_BEGIN, DIV_TAG_COMMENT_END), $ranges [0] [0]) !== false) {
                $pos = $fin + 1;
                continue;
            }

            // Div 5.0: no include if conditional & conditions blocks are not resolved yet
            // This check prevent infinite loops

            $cr = $this->getConditionalRanges(true, null, false);
            if ($this->searchInRanges($cr, $ini)) {
                $pos = $ini + 1;
                continue;
            }

            if ($this->searchInRanges($this->getRanges(DIV_TAG_CONDITIONS_BEGIN_PREFIX, DIV_TAG_CONDITIONS_END), $ini)) {
                $pos = $ini + 1;
                continue;
            }

            $path = trim(substr($this->__src, $ini + $l1, $fin - $ini - $l1));

            // New feature in 4.5: advanced params for includes
            $data_path = null;
            if (!self::fileExists($path)) {
                $sep = strpos($path, DIV_TAG_PREPROCESSED_SEPARATOR);

                if ($sep !== false) {
                    $data_path = trim(substr($path, $sep + 1));
                    $path = substr($path, 0, $sep);

                    $all_items = $this->getAllItems($items);

                    // Div 5.0: priority change for items before filesystem.
                    // If is needed to force load data from external file,
                    // then type the path (ex: block.json)

                    if (self::varExists($data_path, $all_items)) {
                        $data_path = self::getVarValue($data_path, $all_items);
                    } elseif (self::fileExists($data_path)) {
                        $data_path = file_get_contents($data_path);
                        $data_path = self::jsonDecode($data_path, $all_items);
                    } elseif (self::fileExists($data_path.'.'.DIV_DEFAULT_DATA_FILE_EXT)) {
                        $data_path = file_get_contents($data_path.'.'.DIV_DEFAULT_DATA_FILE_EXT);
                        $data_path = self::jsonDecode($data_path, $all_items);
                    } else {
                        $data_path = self::jsonDecode($data_path, $all_items);
                    }

                    if (is_object($data_path)) {
                        $data_path = get_object_vars($data_path);
                    }

                    if (is_array($data_path)) {
                        $items = array_merge($items, $data_path);
                    }
                }
            }

            $path = $this->getTplPath($path);

            if (self::$__log_mode) {
                $this->logger("Trying to include $path");
            }

            if ($path !== $this->__path && !self::detectRecursiveInclusion($exclusion, $path, $ini)) {
                if (self::fileExists($path)) {
                    $c = '';
                    if (!is_dir($path)) {

                        self::$__includes_history[] = $path;

                        $c = self::getFileContents($path);

                        // advanced operations before include
                        if ($data_path !== null) {

                            // extract part of template
                            if (isset($data_path ['from'])) {
                                if (isset($data_path ['to'])) {
                                    $from = $data_path ['from'];
                                    $to = $data_path ['to'];

                                    if (!is_numeric($from) && !is_numeric($to)) {
                                        if (!empty ($from) && !empty ($to)) {
                                            // string from/to (ranges)

                                            $extracts = $this->getRanges($from, $to, $c);

                                            $new_c = '';
                                            $from_len = strlen($from);

                                            $i = 0;
                                            foreach ($extracts as $extract) {
                                                $i++;

                                                if (isset($data_path ['offset']) && $i < $data_path ['offset'] * 1) {
                                                    continue;
                                                }

                                                $new_c .= substr($c, $extract [0] + $from_len, $extract [1] - $extract [0] - $from_len);

                                                if (isset($data_path ['limit']) && $i === $data_path ['limit'] * 1) {
                                                    break;
                                                }
                                            }

                                            $c = $new_c;
                                        } else {
                                            // numeric/string from/to
                                            if (!is_numeric($from)) {
                                                if (!empty ($from)) {
                                                    $from = strpos($c, $from);
                                                } else {
                                                    $from = false;
                                                }
                                            }
                                            if (!is_numeric($to)) {
                                                if (!empty ($to)) {
                                                    $to = strpos($c, $to);
                                                } else {
                                                    $to = false;
                                                }
                                            }

                                            if ($from !== false && $to !== false && $from <= $to) {
                                                $c = substr($c, $from, $from + ($to - $from) + 1);
                                            }
                                        }
                                    }
                                } else {
                                    // only from
                                    $from = $data_path ['from'];

                                    if (!is_numeric($from)) {
                                        $from = strpos($c, $from);
                                    }

                                    if ($from !== false) {
                                        $c = substr($c, $from);
                                    }
                                }
                            } elseif (isset($data_path ['to'])) {
                                $to = $data_path ['to'];
                                if (!is_numeric($to)) {
                                    $to = strpos($c, $to);
                                }

                                if ($to !== false) {
                                    $c = substr($c, 0, $to + 1);
                                }
                            }
                        }

                        $tpl_prop = $this->getTemplateProperties($c);
                        $c = $this->prepareDialect($c, $tpl_prop, false);

                        if (self::$__docs_on) {
                            if (self::fileExists($this->__path) || self::fileExists(PACKAGES.$this->__path)) {
                                $section = trim($this->__path);
                                $contained = trim($path);

                                if (strpos($section, './') === 0) {
                                    $section = substr($this->__path, 2);
                                }
                                if (strpos($contained, './') === 0) {
                                    $contained = substr($path, 2);
                                }

                                self::$__docs [$contained] = [];
                                if (!isset(self::$__docs [$section])) {
                                    self::$__docs [$section] = [];
                                }
                                if (!isset(self::$__docs [$section] ['include'])) {
                                    self::$__docs [$section] ['include'] = [];
                                }
                                self::$__docs [$section] ['include'] [$contained] = $contained;

                                $engine = self::getAuxiliaryEngineClone($items, $items, $this);
                                $engine->__src = $c;
                                $engine->parseComments($path);
                                $c = $engine->__src;
                                unset ($engine);
                            }
                        }
                    } else {
                        self::error("Template '$path' not found or is not a template"); // level = DIV_ERROR_WARNING
                    }

                    $c_len = strlen($c);

                    foreach ($exclusion as $idx => $exc) {
                        if ($exc ['ini'] > $ini) {
                            $exclusion [$idx] ['ini'] += $c_len;
                        }
                        if ($exc ['end'] > $ini) {
                            $exclusion [$idx] ['end'] += $c_len;
                        }
                    }

                    $exclusion ['inclusion-'.$this->__path] = [
                        'path' => $path,
                        'ini'  => $ini,
                        'end'  => $ini + $c_len,
                    ];

                    $this->__src = substr($this->__src, 0, $ini).$c.substr($this->__src, $fin + $l2);
                } else {
                    $id = uniqid('', true);
                    $restores [$id] = substr($this->__src, $ini, $fin + $l2 - $ini);
                    $this->__src = substr($this->__src, 0, $ini).$id.substr($this->__src, $fin + $l2);
                }
            } else {
                if (trim($path) !== '') {
                    self::error("Recursive inclusion of template '$path' in '".substr($this->__src, $ini - 20, 20)."'is not allowed", DIV_ERROR_WARNING);
                }

                $pos = $ini + 1;
            }
        }

        foreach ($restores as $id => $restore) {
            $this->__src = str_replace($id, $restore, $this->__src);
        }
    }

    /**
     * Parsing preprocessed templates
     *
     * @param mixed $items
     */
    final public function parsePreprocessed($items)
    {
        if (is_object($items)) {
            $items = get_object_vars($items);
        }

        // Div doesn't know the future!
        $items = array_merge($this->__memory, $items);

        // Tags
        $prefix = DIV_TAG_PREPROCESSED_BEGIN;
        $suffix = DIV_TAG_PREPROCESSED_END;

        $l1 = strlen($prefix);
        $l2 = strlen($suffix);

        if (self::$__log_mode) {
            $this->logger('Parsing preprocessed...');
        }

        // TODO: maybe is not necessary check is_array
        if (is_array($items)) {
            foreach ($items as $key => $value) {
                if (array_key_exists($key, $this->__ignore) && $this->__ignore[$key] === true) {
                    continue;
                }
                if ((strpos($this->__src, $prefix.$key.$suffix) !== false) && self::isString($value)) {
                    if (self::fileExists($value.'.'.DIV_DEFAULT_TPL_FILE_EXT)) {
                        $value .= '.'.DIV_DEFAULT_TPL_FILE_EXT;
                    }
                    $this->__src = str_replace($prefix.$key.$suffix, $prefix.$value.$suffix, $this->__src);
                }
            }
        }

        $restores = [];

        $pos = 0;
        $original_items = $items;
        while (true) {
            $items = $original_items;

            $ranges = $this->getRanges($prefix, $suffix, null, true, $pos);
            if (count($ranges) < 1) {
                break;
            }

            $ini = $ranges [0] [0];
            $fin = $ranges [0] [1];

            $r = $this->checkLogicalOrder($ini, '', false, false, false, true);
            if ($r !== false) {
                $pos = $ini + 1;
                continue;
            }

            // Div 5.0: Do not pre-process anything within the conditional
            // blocks if the conditions have not been resolved.
            // This check prevent infinite loops

            $cr = $this->getConditionalRanges(true, null, false);
            if ($this->searchInRanges($cr, $ini)) {
                $pos = $ini + 1;
                continue;
            }

            if ($this->searchInRanges($this->getRanges(DIV_TAG_CONDITIONS_BEGIN_PREFIX, DIV_TAG_CONDITIONS_END), $ini)) {
                $pos = $ini + 1;
                continue;
            }

            // Div 5.0: Do not pre-process anything within the loops
            // blocks if the loops have not been resolved.
            $lr = $this->getListRanges(); // warning! params should be null, false
            if ($this->searchInRanges($lr, $ini)) {
                $pos = $ini + 1;
                continue;
            }

            $path = trim(substr($this->__src, $ini + $l1, $fin - $ini - $l1));
            $standalone = false;
            // New feature in 4.5: specific data for preprocessed template
            if (!self::fileExists($path)) {
                $sep = strpos($path, DIV_TAG_PREPROCESSED_SEPARATOR);

                if ($sep !== false) {

                    $data_path = trim(substr($path, $sep + 1));
                    $path = substr($path, 0, $sep);
                    $all_items = $this->getAllItems($items);

                    // Div 5.0: priority change for items before filesystem.
                    // If is needed to force load data from external file,
                    // then type the path (ex: block.json)
                    if (self::varExists($data_path, $all_items)) {
                        $data_path = self::getVarValue($data_path, $all_items);
                    } elseif (self::fileExists($data_path)) {
                        $data_path = file_get_contents($data_path);
                        $engine = self::getAuxiliaryEngineClone($all_items, $all_items, $this);
                        $engine->__src = $data_path;
                        $engine->parse(false, null, self::$__parse_level + 1);
                        $data_path = self::jsonDecode($engine->__src, $all_items);
                    } elseif (self::fileExists($data_path.'.'.DIV_DEFAULT_DATA_FILE_EXT)) {
                        $data_path = file_get_contents($data_path.'.'.DIV_DEFAULT_DATA_FILE_EXT);
                        $engine = self::getAuxiliaryEngineClone($all_items, $all_items, $this);
                        $engine->__src = $data_path;
                        $engine->parse(false, null, self::$__parse_level + 1);
                        $data_path = self::jsonDecode($engine->__src, $all_items);
                    } else {
                        $engine = self::getAuxiliaryEngineClone($all_items, $all_items, $this);
                        $engine->__src = $data_path;
                        $engine->parse(false, null, self::$__parse_level + 1);
                        $data_path = self::jsonDecode($engine->__src, $all_items);
                    }

                    if (is_object($data_path)) {
                        $data_path = get_object_vars($data_path);
                    }

                    // Div 5.0: standalone templates (ignoring parent scope)
                    $standalone = self::getVarValue('div.standalone', $data_path);

                    if ($standalone === null) {
                        $standalone = false;
                    }
                    if ($standalone) {
                        $items = $data_path;
                    } elseif (is_array($data_path)) {
                        $items = array_merge($items, $data_path);
                    }
                }
            }

            $ranges_x = $this->getRanges(DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END);
            $proceed = true;
            foreach ($ranges_x as $rx) {
                if ($ini >= $rx [0] && $ini <= $rx [1]) {
                    $pos = $ini + 1;
                    $proceed = false;
                    break;
                }
            }

            if (!$proceed) {
                continue;
            }

            $path = $this->getTplPath($path);

            if (($path === $this->__path) && self::$__log_mode /*|| self::detectRecursiveInclusion($exclusion, $path, $ini)*/) {
                self::log("Recursive inclusion of template '$path' in '".substr($this->__src, $ini - 20, 20)."' during pre-process.", DIV_ERROR_WARNING);
            }

            if (self::fileExists($path)) {
                $c = self::getFileContents($path);

                // Div 5.0: new feature!: custom engine
                $custom_engine = null;
                if (isset($data_path)) {
                    $custom_engine = self::getVarValue('div.engine', $data_path);
                }

                if ($custom_engine !== null) {
                    $engine = self::getAuxiliaryEngineClone($items, $items, $custom_engine);
                } else {
                    $engine = self::getAuxiliaryEngineClone($items, $items, $this);
                }

                $engine->__src = $c;

                if (self::$__docs_on && self::fileExists($this->__path)) {
                    $section = trim($this->__path);
                    $contained = trim($path);

                    if (strpos($section, './') === 0) {
                        $section = substr($this->__path, 2);
                    }
                    if (strpos($contained, './') === 0) {
                        $contained = substr($path, 2);
                    }

                    self::$__docs [$contained] = [];
                    if (!isset(self::$__docs [$section])) {
                        self::$__docs [$section] = [];
                    }
                    if (!isset(self::$__docs [$section] ['preprocess'])) {
                        self::$__docs [$section] ['include'] = [];
                    }
                    self::$__docs [$section] ['preprocess'] [] = $contained;
                }

                $engine->__path = $path;
                self::$__includes_history [] = $path;

                $originals = self::$__globals_design;
                if ($standalone) {
                    self::$__globals_design = $items;
                } else {
                    self::$__globals_design = array_merge(self::$__globals_design, $items);
                }

                $engine->__items = $items;
                $engine->parse(false, null, self::$__parse_level + 1);
                self::$__globals_design = $originals;

                $pre = $engine->__src;

                $this->__src = substr($this->__src, 0, $ini).$pre.substr($this->__src, $fin + $l2);
            } else {
                $id = uniqid('', true);
                $restores [$id] = substr($this->__src, $ini, $fin + $l2 - $ini);
                $this->__src = substr($this->__src, 0, $ini).$id.substr($this->__src, $fin + $l2);
            }
        }

        foreach ($restores as $id => $restore) {
            $this->__src = str_replace($id, $restore, $this->__src);
        }
    }

    /**
     * Parse comments
     *
     * @param string $section
     */
    final public function parseComments($section = null)
    {
        if ($section === null) {
            $section = trim($this->__path);
        }
        if ($section === '') {
            $section = uniqid('', true);
        }
        if (strpos($section, './') === 0) {
            $section = substr($section, 2);
        }

        if (self::$__log_mode) {
            $this->logger('Parsing comments...');
        }

        $begin_len = strlen(DIV_TAG_COMMENT_BEGIN);

        $pos = 0;
        while (true) {
            $ranges = $this->getRanges(DIV_TAG_COMMENT_BEGIN, DIV_TAG_COMMENT_END, null, true, $pos);

            if (count($ranges) < 1) {
                break;
            }

            if (self::searchInRanges($this->getRanges(DIV_TAG_INCLUDE_BEGIN, DIV_TAG_INCLUDE_END), $ranges [0] [0]) !== false) {
                $pos = $ranges [0] [1] + 1;
                continue;
            }

            // Parse template's docs
            if (self::$__docs_on) {
                $sub_src = substr($this->__src, $ranges [0] [0] + $begin_len, $ranges [0] [1] - $ranges [0] [0] - $begin_len);
                $arr = explode("\n", $sub_src);

                $last_prop = '';
                $last_tab = 0;
                foreach ($arr as $line) {
                    $orig = $line;
                    $line = str_replace("\r\n", "\n", $line);
                    $line = trim($line);
                    $line = str_replace("\t", ' ', $line);

                    if (($last_prop !== '') && isset($orig [0]) && $line === '') {
                        $line = ' ';
                    }

                    if (isset($line [0])) {
                        if (($last_prop !== '') && $line [0] !== '@') {
                            $line = '@'.$last_prop.': '.substr($orig, $last_tab);
                        }
                        if (strpos($line, '@') === 0) {

                            $multi_line = false;

                            $p = strpos($line, ' ');
                            if ($p !== false) {
                                $prop = substr($line, 1, $p - 1);
                                $value = substr($line, $p);
                            } else {
                                $prop = substr($line, 1);
                                $value = '';
                            }
                            $l = strlen($prop);

                            if ($prop [$l - 1] === ':') {
                                $multi_line = true;
                                $prop = substr($prop, 0, $l - 1);
                            }

                            if (!isset(self::$__docs [$section])) {
                                self::$__docs [$section] = [];
                            }
                            if (isset(self::$__docs [$section] [$prop])) {

                                if (!is_array(self::$__docs [$section] [$prop])) {
                                    if (trim(self::$__docs [$section] [$prop]) !== '') {
                                        self::$__docs [$section] [$prop] = [
                                            self::$__docs [$section] [$prop],
                                        ];
                                    } else {
                                        self::$__docs [$section] [$prop] = [];
                                    }
                                }

                                if (isset(self::$__docs [$section] [$prop] [0]) || (!isset(self::$__docs [$section] [$prop] [0]) && trim($value) !== '')) {
                                    self::$__docs [$section] [$prop] [] = $value;
                                }

                            } else {
                                self::$__docs [$section] [$prop] = $value;
                            }

                            if ($multi_line) {
                                $last_prop = $prop;
                            } else {
                                $last_prop = '';
                            }

                            $ppp = strpos($orig, '@'.$prop);
                            if ($ppp !== false) {
                                $last_tab = $ppp;
                            }
                        }
                    }
                }
            }

            // Extract
            $this->__src = substr($this->__src, 0, $ranges [0] [0]).substr($this->__src, $ranges [0] [1] + strlen(DIV_TAG_COMMENT_END));
        }
    }

    /**
     * Isset template var in items?
     *
     * @param string $var
     * @param mixed  $items
     *
     * @return boolean
     */
    final public static function issetVar($var, $items)
    {
        if (array_key_exists($var, $items)) {
            return true;
        }

        $var = trim($var);
        $parts = explode(DIV_TAG_VAR_MEMBER_DELIMITER, $var);
        $current = $items;

        foreach ($parts as $part) {

            if (trim($part) === '') {
                return false;
            }

            if (is_array($current)) {
                if (array_key_exists($part, $current)) {
                    $current = $current [$part];
                } else {
                    return false;
                }
            } elseif (is_object($current)) {
                if (property_exists($current, $part)) {
                    $current = $current->$part;
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Return a list of all template vars
     *
     * @param mixed  $items
     * @param string $super_key
     *
     * @return array
     */
    final public function getVars($items = null, $super_key = '')
    {
        if ($items === null) {
            $items = $this->__memory;
        }

        $vars = [];
        $items_x = [];

        if (is_object($items)) {
            $items_x = get_object_vars($items);
        } elseif (is_array($items)) {
            $items_x = $items;
        } else {
            return [];
        }

        foreach ($items_x as $key => $value) {
            $x_key = $super_key.$key;

            if ($x_key !== '') {
                if ($x_key === 'vars'.DIV_TAG_VAR_MEMBER_DELIMITER.'this' || $x_key === 'this') {
                    if (is_object($value)) {
                        if ($this === $value) {
                            continue;
                        }

                        $sp = $this->getSuperParent(get_class($value));

                        if ($sp === 'div') {
                            continue;
                        }
                    }
                }

                $vars [] = $x_key;

                if (!is_scalar($value) && $value !== null) {
                    $vars = array_merge($vars, $this->getVars($items_x [$key], $x_key.DIV_TAG_VAR_MEMBER_DELIMITER));
                }
            }
        }

        return $vars;
    }

    /**
     * Unset template var
     *
     * @param string $var
     * @param mixed  $items
     *
     * @return boolean
     */
    final public static function unsetVar($var, &$items)
    {
        $unset = false;
        if (array_key_exists($var, $items)) {
            $unset = true;
            unset ($items [$var]);
        }

        $var = trim($var);

        $parts = explode(DIV_TAG_VAR_MEMBER_DELIMITER, $var);
        if (!isset($parts [0])) {
            return $unset;
        }

        $current = $items;
        $code = '$items';

        foreach ($parts as $part) {

            if (trim($part) === '') {
                return $unset;
            }

            if (is_array($current)) {
                if (array_key_exists($part, $current)) {
                    $current = $current [$part];
                    $code .= '["'.$part.'"]';
                } else {
                    return $unset;
                }
            } elseif (is_object($current)) {
                if (property_exists($current, $part)) {
                    $current = $current->$part;
                    $code .= '->'.$part;
                } else {
                    return $unset;
                }
            }
        }

        eval ("unset($code);");

        return true;
    }

    /**
     * Return value of template var
     *
     * @param string $var
     * @param mixed  $items
     *
     * @return mixed
     */
    final public static function getVarValue($var, $items)
    {
        $items_x = [];

        if (is_object($items)) {
            $items_x = get_object_vars($items);
        } elseif (is_array($items)) {
            $items_x = $items;
        }

        if (array_key_exists($var, $items_x)) {
            return $items_x [$var];
        }

        $var = trim($var);
        $parts = explode(DIV_TAG_VAR_MEMBER_DELIMITER, $var);

        $current = $items_x;
        foreach ($parts as $part) {

            if (trim($part) === '') {
                return null;
            }

            if (is_array($current)) {
                if (array_key_exists($part, $current)) {
                    $current = $current [$part];
                } else {
                    $current = null;
                    break;
                }
            } elseif (is_object($current)) {
                if (property_exists($current, $part)) {
                    $current = $current->$part;
                } else {
                    $current = null;
                    break;
                }
            }
        }

        if ($current === null) {
            $s = '';
            foreach ($parts as $part) {
                if (array_key_exists($s.$part, $items_x)) {
                    $current = self::getVarValue(substr($var, strlen($s.$part) + 1), $items_x [$s.$part]);
                    if ($current !== null) {
                        break;
                    }
                }
                $s .= $part.DIV_TAG_VAR_MEMBER_DELIMITER;
            }
        }

        return $current;
    }

    /**
     * Set value of template var
     *
     * @param string  $var
     * @param mixed   $value
     * @param mixed   $items
     * @param boolean $force
     *
     * @return mixed
     */
    final public static function setVarValue($var, $value, &$items, $force = true)
    {
        if (array_key_exists($var, $items)) {
            $items [$var] = $value;
        }

        $var = trim($var);
        $parts = explode(DIV_TAG_VAR_MEMBER_DELIMITER, $var);
        $current = $items;

        $c = count($parts);
        $i = 0;

        $code = '$items';
        foreach ($parts as $part) {
            $i++;

            if (trim($part) === '' && is_object($current)) {
                return null;
            }

            if (is_array($current)) {

                if (array_key_exists($part, $current)) {
                    if ($i < $c) {
                        if ($part === '') {
                            $code .= '[]';
                        } else {
                            $code .= '[\''.addslashes($part).'\']';
                        }
                        $current = $current [$part];
                    } elseif ($part === '') {
                        eval ($code.'[] = $value;');
                    } else {
                        eval ($code.'[$part] = $value;');
                    }
                } elseif ($i < $c) {
                    if ($part === '') {
                        eval ($code.'[] = array();');
                        $current = [];
                        $code .= '[]';
                    } else {
                        eval ($code.'[$part] = array();');
                        $current = [];
                        $code .= '[\''.addslashes($part).'\']';
                    }
                } elseif ($part === '') {
                    eval ($code.'[] = $value;');
                } else {
                    eval ($code.'[$part] = $value;');
                }
            } elseif (is_object($current)) {
                if (self::isValidVarName($part)) {
                    $part = str_replace('$', '', $part);
                    if (property_exists($current, $part)) {
                        if ($i < $c) {
                            $code .= '->'.$part;
                            $current = $current->$part;
                        } else {
                            eval ($code.'->$part = $value;');
                        }
                    } elseif ($i < $c) {
                        eval ($code.'->$part = new stdClass();');
                        $code .= '->'.$part;
                        $current = new stdClass ();
                    } else {
                        eval ($code.'->$part = $value;');
                    }
                } else {
                    break;
                }
            }
        }

        return $items;
    }

    /**
     * Parse data
     *
     * @param array $items
     */
    final public function parseData(&$items)
    {
        if (self::$__log_mode) {
            $this->logger('Parsing data in templates...');
        }

        $pos = 0;

        $tag_begin = DIV_TAG_TPLVAR_BEGIN;
        $tag_end = DIV_TAG_TPLVAR_END;

        $l1 = strlen($tag_begin);
        $l2 = strlen($tag_end);

        while (true) {
            $value = null;
            $ranges = $this->getRanges($tag_begin, $tag_end, null, true, $pos);
            if (count($ranges) < 1) {
                break;
            }

            $ini = $ranges [0] [0];
            $fin = $ranges [0] [1];

            if ($this->searchInListRanges($ini)) {
                $pos = $ini + 1;
                continue;
            }
            if ($this->searchPosAfterRange(DIV_TAG_FORMULA_BEGIN, DIV_TAG_FORMULA_END, $ini)) {
                $pos = $ini + 1;
                continue;
            }

            // Div 4.5: checking also orphan parts
            // TODO: see "last chance" algorithm in ->parse(); and improve this solution (2 solutions was found)
            $cr = $this->getConditionalRanges(true, null, false);
            if ($this->searchInRanges($cr, $ini)) {
                $pos = $ini + 1;
                continue;
            }

            if ($this->searchInCapsuleRanges($items, $ini)) {
                $pos = $ini + 1;
                continue;
            }

            if ($this->searchInRanges($this->getRanges(DIV_TAG_CONDITIONS_BEGIN_PREFIX, DIV_TAG_CONDITIONS_END), $ini)) {
                $pos = $ini + 1;
                continue;
            }

            $body = substr($this->__src, $ini + $l1, $fin - $ini - $l1);
            $arr = explode(':', $body, 2);
            $var = $arr [0];

            if (isset($arr [1])) {
                $exp = $arr [1];
            } else {
                $exp = '';
            }

            $var = trim($var);

            $setup_design = true;

            // Check for append of array items
            $ni = strpos($var, '[]');
            if ($ni !== false) {
                $temp_value = self::getVarValue(trim(substr($var, 0, $ni)), $items);

                if (is_array($temp_value)) {
                    if (!array_key_exists($var, self::$__globals_design)) {
                        $setup_design = false;
                    }
                    $var = str_replace('[]', '['.count($temp_value).']', $var);
                }
            }

            // Normalize var name syntax (to dots/DIV_TAG_VAR_MEMBER_DELIMITER)
            $var = str_replace([
                '[',
                ']',
            ], [
                DIV_TAG_VAR_MEMBER_DELIMITER,
                '',
            ], $var);

            $var = str_replace('->', DIV_TAG_VAR_MEMBER_DELIMITER, $var);

            if ($setup_design) {
                // Search if var is design var or not
                $ni = -1;
                do {
                    $ni = strpos($var, DIV_TAG_VAR_MEMBER_DELIMITER, $ni + 1);

                    if ($ni !== false) {
                        $nv = trim(substr($var, 0, $ni));
                        if (!array_key_exists($nv, self::$__globals_design) && self::issetVar($nv, $items)) {
                            $setup_design = false;
                            break;
                        }
                    }
                } while ($ni !== false);
            }

            // Protect the variable
            if (strpos($var, DIV_TAG_TPLVAR_PROTECTOR) === 0) {
                $var = substr($var, 1);
                self::$__globals_design_protected [$var] = true;
            }

            if ($this->searchPosAfterRange(DIV_TAG_MACRO_BEGIN, DIV_TAG_MACRO_END, $ini)) {
                $pos = $ini + 1;
                continue;
            }

            // Previous use
            if (self::issetVar($var, $items)) {
                $r = $this->checkLogicalOrder($ini, $var, true, true, true, false);
                if ($r !== false) {
                    $pos = $r;
                    continue;
                }
            }

            $setup = false;

            // Check protection
            $ke = array_key_exists($var, self::$__globals_design);
            if (!$ke || ($ke && !array_key_exists($var, self::$__globals_design_protected))) {
                $exp = trim($exp);

                if (strpos($exp, '->') === 0) { // parsing a method
                    $exp = substr($exp, 2);
                    $value = $this->getMethodResult($exp, $items);
                    $setup = $value !== DIV_METHOD_NOT_EXISTS && (!self::issetVar($var, $items) || self::issetVar($var, self::$__globals_design));
                } elseif (strpos($exp, '$') === 0) {
                    $var_x = substr($exp, 1);
                    if (self::issetVar($var_x, $items)) {
                        $value = self::getVarValue($var_x, $items);
                        $setup = !self::issetVar($var, $items) || self::issetVar($var, self::$__globals_design);
                    }
                } else { // parsing a JSON code
                    $all_items = $this->getAllItems($items);
                    $json = self::jsonDecode($exp, $all_items);

                    if (self::compact($json) === null) {
                        $temp = uniqid('', true);
                        $temp1 = uniqid('', true);
                        $exp = str_replace(DIV_TAG_INCLUDE_BEGIN, $temp, $exp);
                        $exp = str_replace(DIV_TAG_PREPROCESSED_BEGIN, $temp1, $exp);
                        $engine = self::getAuxiliaryEngineClone($all_items, $all_items, $this);
                        $engine->__src = $exp;

                        $engine->parse(false);
                        $exp = $engine->__src;
                        $exp = str_replace($temp, DIV_TAG_INCLUDE_BEGIN, $exp);
                        $exp = str_replace($temp1, DIV_TAG_PREPROCESSED_BEGIN, $exp);
                    }

                    if (!self::issetVar($var, $items) || self::issetVar($var, self::$__globals_design)) {
                        $exp_path = $this->getTplPath($exp);

                        if (self::fileExists($exp_path) && !self::isDir($exp_path)) {
                            $fgc = self::getFileContents($exp_path);
                            if ($fgc !== '') {
                                $exp = $fgc;
                            }
                        } elseif (self::fileExists($exp_path.'.'.DIV_DEFAULT_DATA_FILE_EXT) && !self::isDir($exp_path.'.'.DIV_DEFAULT_DATA_FILE_EXT)) {
                            $fgc = self::getFileContents($exp_path.'.'.DIV_DEFAULT_DATA_FILE_EXT);
                            if ($fgc !== '') {
                                $exp = $fgc;
                            }
                        } elseif (self::fileExists($exp_path.'.'.DIV_DEFAULT_TPL_FILE_EXT) && !self::isDir($exp_path.'.'.DIV_DEFAULT_TPL_FILE_EXT)) {
                            $fgc = self::getFileContents($exp_path.'.'.DIV_DEFAULT_TPL_FILE_EXT);
                            if ($fgc !== '') {
                                $exp = $fgc;
                            }
                        }

                        if (isset($exp [0])) {
                            $_exp = $exp [0];
                        } else {
                            $_exp = '';
                        }

                        if (($_exp !== '{' && $_exp !== '[' && !is_numeric($_exp) && $_exp !== '"' && $_exp !== "'")
                            || (strpos($exp, DIV_TAG_INCLUDE_BEGIN) === 0
                                && substr($exp, 0 - strlen(DIV_TAG_INCLUDE_END)) === DIV_TAG_INCLUDE_END)
                            || (strpos($exp, DIV_TAG_PREPROCESSED_BEGIN) === 0 && substr($exp, 0 - strlen(DIV_TAG_PREPROCESSED_END)) === DIV_TAG_PREPROCESSED_END)) {
                            $exp = '"'.str_replace('"', '\"', $exp).'"';
                        }

                        $value = self::jsonDecode($exp, $all_items);

                        $vars = $value;
                        if (is_object($vars)) {
                            $vars = get_object_vars($vars);
                        }

                        if (is_array($vars)) {
                            foreach ($vars as $kkk => $vvv) {
                                if (self::isString($vvv)) {
                                    $vvv = trim($vvv);
                                    if (isset($vvv [0]) && strpos($vvv, '$') === 0) {
                                        $var_x = substr($vvv, 1);
                                        if (self::issetVar($var_x, $items)) {
                                            if (is_array($value)) {
                                                $value [$kkk] = self::getVarValue($var_x, $items);
                                            }
                                            if (is_object($value)) {
                                                $value->$kkk = self::getVarValue($var_x, $items);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $setup = true;
                    }
                }
            }
            if ($setup === true) {
                self::setVarValue($var, $value, $items);
                if ($setup_design) {
                    self::setVarValue($var, $value, self::$__globals_design);
                }
            }

            $this->__src = substr($this->__src, 0, $ini).substr($this->__src, $fin + $l2);
        }
    }

    /**
     * Parse defaults replacements
     *
     * @param array $items
     */
    final public function parseDefaults(&$items)
    {
        if (self::$__log_mode) {
            $this->logger('Parsing default replacements...');
        }

        $prefix = DIV_TAG_DEFAULT_REPLACEMENT_BEGIN;
        $suffix = DIV_TAG_DEFAULT_REPLACEMENT_END;

        $l1 = strlen($prefix);

        while (true) {
            $ranges = $this->getRanges($prefix, $suffix, null, true);
            if (count($ranges) < 1) {
                break;
            }

            $ini = $ranges [0] [0];
            $fin = $ranges [0] [1];

            $body = substr($this->__src, $ini + $l1, $fin - $ini - $l1);

            $arr = self::jsonDecode($body, $this->getAllItems($items));

            if (!isset($arr[0]) || !isset($arr[1])) {
                self::error('Was detected an invalid JSON in default values: '.substr($body, 0, 80).'...', DIV_ERROR_FATAL);
            }

            if (isset($arr [2])) {
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

            if (self::fileExists($replace) && !self::isDir($search)) {
                $replace = self::jsonDecode(self::getFileContents($replace), $this->getAllItems($items));
            }
            if (self::fileExists($replace.'.'.DIV_DEFAULT_DATA_FILE_EXT) && !self::isDir($search.'.'.DIV_DEFAULT_DATA_FILE_EXT)) {
                $replace = self::jsonDecode(self::getFileContents($replace.'.'.DIV_DEFAULT_DATA_FILE_EXT), self::cop($this->__memory, $items));
            }
            if (self::fileExists($search) && !self::isDir($search)) {
                $search = self::jsonDecode(self::getFileContents($search), $this->getAllItems($items));
            }
            if (self::fileExists($search.'.'.DIV_DEFAULT_DATA_FILE_EXT) && !self::isDir($search.'.'.DIV_DEFAULT_DATA_FILE_EXT)) {
                $search = self::jsonDecode(self::getFileContents($search.'.'.DIV_DEFAULT_DATA_FILE_EXT), $this->getAllItems($items));
            }

            if ($var === null) {
                self::setDefault($search, $replace);
            } else {
                self::setDefaultByVar($var, $search, $replace, false);
            }

            $this->__src = substr($this->__src, 0, $ini).substr($this->__src, $fin + 2);
        }
    }

    /**
     * Parse number formats
     *
     * @param array $items
     */
    final public function parseNumberFormat(&$items = [])
    {
        if (self::$__log_mode) {
            $this->logger("Parsing number's formats...");
        }

        $prefix = DIV_TAG_NUMBER_FORMAT_PREFIX;
        $suffix = DIV_TAG_NUMBER_FORMAT_SUFFIX;
        $ranges = $this->getRanges($prefix, $suffix);

        $l1 = strlen($prefix);

        // check all number format occurrences
        foreach ($ranges as $range) {
            $s = substr($this->__src, $range[0] + $l1, $range[1] - $range[0] - $l1);
            $arr = explode(DIV_TAG_NUMBER_FORMAT_SEPARATOR, $s);
            if (!array_key_exists($arr[0], $items) && is_numeric($arr[0])) {
                $items [$arr[0]] = ( float )$arr [0];
            }
        }

        if (is_array($items)) {
            foreach ($items as $key => $value) {
                if (is_numeric($value)) {
                    $this->numberFormat($key, (string)$value);
                }
            }
        }
    }

    /**
     * Scan matches and call parseMatch
     *
     * @param string  $key
     * @param mixed   $value
     * @param object  $engine
     * @param mixed   $items
     * @param boolean $ignore_logical_order
     */
    final public function scanMatch($key, $value, $engine = null, &$items = null, $ignore_logical_order = false)
    {
        if ($items === null) {
            $items = &$this->__items;
        }
        if ($engine === null) {
            $engine = self::getAuxiliaryEngineClone($items, $items, $this);
        }

        // Scan child properties
        if (strpos($this->__src, $key.DIV_TAG_VAR_MEMBER_DELIMITER) !== false) {
            $vvv = $value;
            if (is_scalar($vvv)) {
                $vvv = (string)$value;
            }

            if (self::isString($vvv)) {
                $vars = str_split($vvv);
            } elseif (is_object($vvv)) {
                $vars = get_object_vars($vvv);
            } else {
                $vars = $vvv;
            }

            if (is_array($vars)) {
                foreach ($vars as $kk => $v) {
                    $this->scanMatch($key.DIV_TAG_VAR_MEMBER_DELIMITER.$kk, $v, $engine, $items, $ignore_logical_order);
                }
            }
        }

        // Match this key
        $this->parseMatch($key, $value, $engine, $ignore_logical_order);

        // Match aggregate functions
        if (is_object($value) && !method_exists($value, '__toString')) {
            $value = get_object_vars($value);
        }

        if (is_array($value)) {

            $cant_values = count($value);

            $this->parseMatch($key, $cant_values, $engine, $ignore_logical_order);

            $sep = DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR;
            $function_max = DIV_TAG_AGGREGATE_FUNCTION_MAX;
            $function_min = DIV_TAG_AGGREGATE_FUNCTION_MIN;

            if ($cant_values > 0) {
                if ((strpos($this->__src, $key) !== false) && self::isNumericList($value) === true) {
                    $sum = array_sum($value);
                    $keys = array_keys($value);

                    if ($cant_values > 1) {
                        $this->parseMatch($function_max.$sep.$key, max($value), $engine, $ignore_logical_order);
                        $this->parseMatch($function_min.$sep.$key, min($value), $engine, $ignore_logical_order);
                    } else {

                        $this->parseMatch($function_max.$sep.$key, $value [$keys [0]], $engine, $ignore_logical_order);
                        $this->parseMatch($function_min.$sep.$key, $value [$keys [0]], $engine, $ignore_logical_order);
                    }

                    $this->parseMatch(DIV_TAG_AGGREGATE_FUNCTION_SUM.$sep.$key, $sum, $engine, $ignore_logical_order);
                    $this->parseMatch(DIV_TAG_AGGREGATE_FUNCTION_AVG.$sep.$key, $sum / $cant_values, $engine, $ignore_logical_order);
                }

                if (strpos($this->__src, $key.DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR) !== false) {

                    $functions = [
                        '',
                        DIV_TAG_AGGREGATE_FUNCTION_COUNT,
                        DIV_TAG_AGGREGATE_FUNCTION_MAX,
                        DIV_TAG_AGGREGATE_FUNCTION_MIN,
                        DIV_TAG_AGGREGATE_FUNCTION_SUM,
                        DIV_TAG_AGGREGATE_FUNCTION_AVG,
                    ];

                    foreach ($functions as $function) {
                        if ($function === '') {
                            $ff = '';
                        } else {
                            $ff = $function.DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR;
                        }

                        $tag_begin = $ff.$key.DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR;
                        $tag_end = DIV_TAG_REPLACEMENT_SUFFIX;

                        $l = strlen($tag_begin);
                        $result = [];

                        $p = 0;
                        while (true) {
                            $ranges = $this->getRanges($tag_begin, $tag_end, $this->__src, true, $p);
                            if (count($ranges) < 1) {
                                break;
                            }

                            $range = $ranges [0];

                            if ($this->searchInRanges($this->getRanges(DIV_TAG_MACRO_BEGIN, DIV_TAG_MACRO_END), $range[0])) {
                                $p = $range[0] + 1;
                                continue;
                            }

                            $var = substr($this->__src, $range[0] + $l, $range[1] - ($range[0] + $l));

                            if (strpos($var, DIV_TAG_SUBMATCH_SEPARATOR) !== false) {
                                $var = explode(DIV_TAG_SUBMATCH_SEPARATOR, $var);
                                $var = $var [0];
                            } elseif (strpos($var, DIV_TAG_NUMBER_FORMAT_SEPARATOR) !== false) {
                                $var = explode(DIV_TAG_NUMBER_FORMAT_SEPARATOR, $var);
                                $var = $var [0];
                            }

                            if (!array_key_exists($var, $result)) {
                                $c = 0;
                                $result [$var] = [];
                                $max = null;
                                $min = null;
                                $sum = 0;
                                $avg = 0;

                                foreach ($value as $v) {
                                    if (is_object($v)) {
                                        $v = get_object_vars($v);
                                    }
                                    if (isset($var[$v])) {
                                        $cant = 0;
                                        if (is_bool($v [$var]) || self::isString($v [$var])) {
                                            $cant = 1;
                                        }
                                        if (is_numeric($v [$var])) {
                                            $cant = $v [$var];
                                        }

                                        switch ($function) {
                                            case DIV_TAG_AGGREGATE_FUNCTION_MIN :
                                                if ($min === null || $min * 1 > $v [$var] * 1) {
                                                    $min = $v [$var] * 1;
                                                }
                                                break;
                                            case DIV_TAG_AGGREGATE_FUNCTION_MAX :
                                                if ($max === null || $max * 1 < $v [$var] * 1) {
                                                    $max = $v [$var] * 1;
                                                }
                                                break;
                                            case DIV_TAG_AGGREGATE_FUNCTION_SUM :
                                                $sum += $cant;
                                                break;
                                            case DIV_TAG_AGGREGATE_FUNCTION_AVG :
                                                $avg += $cant;
                                                break;
                                            default :
                                                if (self::mixedBool($v [$var]) === true) {
                                                    $c++;
                                                }
                                        }
                                    }
                                }

                                $result [$var] [DIV_TAG_AGGREGATE_FUNCTION_MIN] = $min;
                                $result [$var] [DIV_TAG_AGGREGATE_FUNCTION_MAX] = $max;
                                $result [$var] [DIV_TAG_AGGREGATE_FUNCTION_COUNT] = $c;
                                $result [$var] [DIV_TAG_AGGREGATE_FUNCTION_SUM] = $sum;

                                if ($cant_values > 0) {
                                    $result [$var] [DIV_TAG_AGGREGATE_FUNCTION_AVG] = $avg / $cant_values;
                                } else {
                                    $result [$var] [DIV_TAG_AGGREGATE_FUNCTION_AVG] = 0;
                                }
                            }

                            $res = $result [$var] [$function === '' ? DIV_TAG_AGGREGATE_FUNCTION_COUNT : $function];
                            $var = $ff.$key.DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR.$var;
                            $items [$var] = $res;

                            $this->parseMatch($var, $res, $engine, $ignore_logical_order);
                            $p = $range[0] + 1;
                        }
                    }
                }
            }
        }
    }

    /**
     * Parsing multiple variable's modifiers
     */
    final public function parseMultipleModifiers()
    {
        $prefix = DIV_TAG_MULTI_MODIFIERS_PREFIX;
        $suffix = DIV_TAG_MULTI_MODIFIERS_SUFFIX;
        $l1 = strlen($prefix);
        $l2 = strlen($suffix);
        $p = 0;
        while (true) {
            $ranges = $this->getRanges($prefix, $suffix, null, true, $p);
            if (count($ranges) > 0) {
                list($ini, $fin) = $ranges[0];
                $s = substr($this->__src, $ini + $l1, $fin - $ini - $l1);

                $pp = strpos($s, DIV_TAG_MULTI_MODIFIERS_OPERATOR);
                if ($pp === false) {
                    $p = $ini + 1;
                    continue;
                }

                $var_name = trim(substr($s, 0, $pp));
                $s = substr($s, $pp + 1);
                $parts = explode(DIV_TAG_MULTI_MODIFIERS_SEPARATOR, $s);

                $new_code = '';
                if (count($parts) > 0) {

                    $new_var = 'var'.uniqid('', true);
                    self::$__dont_remember_it [$new_var] = true;

                    $new_code = DIV_TAG_STRIP_BEGIN.' '.DIV_TAG_TPLVAR_BEGIN.' '.$new_var.DIV_TAG_TPLVAR_ASSIGN_OPERATOR.' $'.$var_name.' '.DIV_TAG_TPLVAR_END."\n";
                    $ignore = false;

                    $separators = [];
                    $separators[DIV_TAG_MULTI_MODIFIERS_SEPARATOR] = true;
                    $separators[DIV_TAG_MODIFIER_SUBSTRING_SEPARATOR] = true;
                    $separators[DIV_TAG_DATE_FORMAT_SEPARATOR] = true;
                    $separators[DIV_TAG_NUMBER_FORMAT_SEPARATOR] = true;
                    $separators[DIV_TAG_FORMULA_FORMAT_SEPARATOR] = true;
                    $separators[DIV_TAG_TXT_WIDTH_SEPARATOR] = true;
                    $separators[DIV_TAG_LOOP_VAR_SEPARATOR] = true;
                    $separators[DIV_TAG_ITERATION_PARAM_SEPARATOR] = true;
                    $separators[DIV_TAG_PREPROCESSED_SEPARATOR] = true;
                    $separators[DIV_TAG_AGGREGATE_FUNCTION_SEPARATOR] = true;
                    $separators[DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR] = true;

                    foreach ($parts as $part) {
                        if (trim($part) !== '') {

                            $break = false;
                            // by default, a modifier is a prefix ...
                            if (in_array($part, self::$__modifiers, true)) {
                                $new_code .= DIV_TAG_TPLVAR_BEGIN.' '.$new_var.DIV_TAG_TPLVAR_ASSIGN_OPERATOR.' '.DIV_TAG_REPLACEMENT_PREFIX.$part.$new_var.DIV_TAG_REPLACEMENT_SUFFIX.' '.DIV_TAG_TPLVAR_END."\n";
                            } // .. else the sub process
                            elseif (strpos($part, DIV_TAG_MODIFIER_TRUNCATE) === 0 || strpos($part, DIV_TAG_MODIFIER_WORDWRAP) === 0 || strpos($part, ',') !== false) {
                                $new_code .= DIV_TAG_TPLVAR_BEGIN.' '.$new_var.DIV_TAG_TPLVAR_ASSIGN_OPERATOR.' '.DIV_TAG_REPLACEMENT_PREFIX.DIV_TAG_MODIFIER_SIMPLE.$new_var.DIV_TAG_SUBMATCH_SEPARATOR.$part.DIV_TAG_REPLACEMENT_SUFFIX.' '
                                    .DIV_TAG_TPLVAR_END."\n";
                            } else {
                                $break = true;
                            }

                            // .. else take MODIFIER + SEPARATORS
                            foreach ($separators as $sep => $v) {
                                if (in_array($part.$sep, self::$__modifiers, true)) {
                                    $new_code .= DIV_TAG_TPLVAR_BEGIN.' '.$new_var.DIV_TAG_TPLVAR_ASSIGN_OPERATOR.' '.DIV_TAG_REPLACEMENT_PREFIX.$part.$sep.$new_var.DIV_TAG_REPLACEMENT_SUFFIX.' '.DIV_TAG_TPLVAR_END."\n";
                                    $break = false;
                                    break;
                                }
                            }

                            if ($break) {
                                $p = $ini + 1;
                                $ignore = true;
                                break;
                            }
                        }
                    }

                    if ($ignore) {
                        continue;
                    }

                    $new_code .= DIV_TAG_REPLACEMENT_PREFIX.DIV_TAG_MODIFIER_SIMPLE.$new_var.DIV_TAG_REPLACEMENT_SUFFIX.' '.DIV_TAG_STRIP_END."\n";
                }

                $this->__src = substr($this->__src, 0, $ini).$new_code.substr($this->__src, $fin + $l2);
                $p = $ini + 1;
            } else {
                break;
            }
        }
    }

    /**
     * Parse all matches
     *
     * @param array   $items
     * @param boolean $ignore_logical_order
     */
    final public function parseMatches(&$items = null, $ignore_logical_order = false)
    {
        if (self::$__log_mode) {
            $this->logger('Parsing matches...');
        }
        if ($items === null) {
            $items = &$this->__items;
        }

        $restore = [];
        $last_pos = 0;

        if (strpos($this->__src, DIV_TAG_LOOP_BEGIN_PREFIX) !== false) {

            $prefix_len = strlen(DIV_TAG_LOOP_BEGIN_PREFIX);
            $suffix_len = strlen(DIV_TAG_LOOP_BEGIN_SUFFIX);

            while (true) {
                if ($last_pos > strlen($this->__src) - 1) {
                    break;
                }

                $ranges = $this->getBlockRanges(null, DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_BEGIN_SUFFIX, DIV_TAG_LOOP_END_PREFIX, DIV_TAG_LOOP_END_SUFFIX, $last_pos, null, true);

                if (!isset($ranges [0])) {
                    break;
                }

                $ini = $ranges [0] [0];
                $last_pos = $ini + 1;
                $unique_key = uniqid('', true);
                $restore [$unique_key] = $ranges [0] [3];
                $this->__src = substr($this->__src, 0, $ini + $prefix_len + strlen($ranges [0] [2]) + $suffix_len).$unique_key.substr($this->__src, $ranges [0] [1]);
            }
        }

        $engine = self::getAuxiliaryEngineClone($items, $items, $this);
        if (is_array($items)) {
            foreach ($items as $key => $value) {
                $this->scanMatch($key, $value, $engine, $items, $ignore_logical_order);
            }
        }

        foreach ($restore as $unique_key => $part) {
            $this->__src = str_replace($unique_key, $part, $this->__src);
        }
    }

    /**
     * Parse formulas
     *
     * @param array $items
     *
     * @return boolean
     */
    final public function parseFormulas(&$items = [])
    {
        if (self::$__log_mode) {
            $this->logger('Parsing formulas...');
        }

        $p1 = strpos($this->__src, DIV_TAG_TPLVAR_BEGIN);

        $engine = self::getAuxiliaryEngineClone($items, $items, $this);
        $prefix_len = strlen(DIV_TAG_FORMULA_BEGIN);
        $suffix_len = strlen(DIV_TAG_FORMULA_END);

        while (true) {
            $ranges = $this->getRanges(DIV_TAG_FORMULA_BEGIN, DIV_TAG_FORMULA_END, null, true);
            if (count($ranges) > 0) {
                list($ini, $fin) = $ranges[0];

                if ($ini > $p1 && $p1 !== false) {
                    return true;
                }

                $formula = substr($this->__src, $ini + $prefix_len, $fin - ($ini + $prefix_len));
                $formula_orig = $formula;

                if (self::$__log_mode) {
                    $this->logger("Parsing the formula (from {$ini} to {$fin}): $formula");
                }

                $engine->__src = $formula;

                $engine->parse(false);

                $formula = $engine->__src;

                // Get the number format
                $pos = strrpos($formula, DIV_TAG_FORMULA_FORMAT_SEPARATOR);
                $format = '';

                if ($pos !== false && isset($formula [$pos + 1])) {
                    $format = trim(substr($formula, $pos + 1));
                    $formula = substr($formula, 0, $pos);
                }

                $r = null;
                $random_var = uniqid('', true);

                if (self::isValidExpression($formula) && !self::haveVarsThisCode($formula)) {
                    self::changeErrorReporting();
                    eval ('$r = '.$formula.';');
                    self::restoreErrorReporting();
                }

                if ($r === null) {
                    $restore_id = uniqid('', true);
                    $this->__restore [$restore_id] = DIV_TAG_FORMULA_BEGIN.' '.$formula_orig.DIV_TAG_FORMULA_END;
                    $this->__src = substr($this->__src, 0, $ini).'{'.(string)$restore_id.'}'.substr($this->__src, $fin + $suffix_len);
                    continue;
                }

                if ($format !== '' && is_numeric($r)) {
                    $this->__src = substr($this->__src, 0, $ini).DIV_TAG_NUMBER_FORMAT_PREFIX.$random_var.DIV_TAG_NUMBER_FORMAT_SEPARATOR.$format.DIV_TAG_NUMBER_FORMAT_SUFFIX.substr($this->__src, $fin + $suffix_len);
                    $this->numberFormat($random_var, $r);
                } else {
                    $this->__src = substr($this->__src, 0, $ini).$r.substr($this->__src, $fin + $suffix_len);
                }
            } else {
                break;
            }
        }
    }

    /**
     * Parse conditions
     *
     * @param array $items
     * @param bool  $clean_orphan
     */
    final public function parseConditions(&$items, $clean_orphan = false)
    {
        if (self::$__log_mode) {
            $this->logger('Parsing conditions...');
        }

        $pos = 0;

        // TODO: optimize length of constants
        $length_prefix = strlen(DIV_TAG_CONDITIONS_BEGIN_PREFIX);
        $length_suffix = strlen(DIV_TAG_CONDITIONS_BEGIN_SUFFIX);
        $length_end = strlen(DIV_TAG_CONDITIONS_END);
        $length_else = strlen(DIV_TAG_ELSE);

        while (true) {
            $ranges = $this->getRanges(DIV_TAG_CONDITIONS_BEGIN_PREFIX, DIV_TAG_CONDITIONS_END, null, true, $pos);

            if (count($ranges) > 0) {
                list($ini, $fin) = $ranges[0];

                if ($this->searchInRanges($this->getConditionalRanges(true), $pos, true)) {
                    $pos = $ini + 1;
                    continue;
                }

                if ($this->searchInListRanges($ini)) {
                    $pos = $ini + 1;
                    continue;
                }

                if ($this->searchInRanges($this->getRanges(DIV_TAG_ITERATION_BEGIN_PREFIX, DIV_TAG_ITERATION_END), $ini)) {
                    $pos = $ini + 1;
                    continue;
                }

                $body = substr($this->__src, $ini + $length_prefix, $fin - $ini - $length_prefix);

                $p = strpos($body, DIV_TAG_CONDITIONS_BEGIN_SUFFIX);

                $condition = '';

                if ($p !== false) {
                    $condition = substr($body, 0, $p);

                    $body = substr($body, $p + $length_suffix);
                    $else = $this->getElseTag($body);
                    if ($else !== false) {
                        $body_parts = [
                            substr($body, 0, $else),
                            substr($body, $else + $length_else),
                        ];
                    } else {
                        $body_parts = [
                            $body,
                            '',
                        ];
                    }

                    if ($body_parts [0] !== '') {
                        if ($body_parts [0] [0] === ' ') {
                            $body_parts [0] = substr($body_parts [0], 1);
                        }
                        if (substr($body_parts [0], -1) === ' ') {
                            $body_parts [0] = substr_replace($body_parts [0], '', -1);
                        }
                    }

                    if ($body_parts [1] !== '') {
                        if ($body_parts [1] [0] === ' ') {
                            $body_parts [1] = substr($body_parts [1], 1);
                        }
                        if (substr($body_parts [1], -1) === ' ') {
                            $body_parts [1] = substr_replace($body_parts [1], '', -1);
                        }
                    }

                    $r = false;

                    if (self::$__log_mode) {
                        $this->logger("Parsing condition (from $ini to $fin): $condition");
                    }

                    $engine = self::getAuxiliaryEngineClone($items, $items, $this);
                    $engine->__src = $condition;
                    $engine->parse(false);
                    $condition = $engine->__src;

                    if (self::isValidExpression($condition)) {

                        // Div 5.0: Forced/Convert all vars as template vars
                        // get list of vars and sort array by their char len in reverse order
                        $vars = self::getVarsFromCode($condition);
                        $vars_sorted = [];
                        foreach ($vars as $var) {
                            $vars_sorted[$var] = strlen($var);
                        }
                        arsort($vars_sorted);
                        $vars = array_keys($vars_sorted);

                        //replace each var with $items['varname']
                        foreach ($vars as $var) {
                            $condition = str_replace('$'.$var, "\$items['$var']", $condition);
                        }

                        // Div 5.0: allow only $items var
                        if (!self::haveVarsThisCode($condition, ['items'])) {
                            self::changeErrorReporting();
                            eval ('$r = '.$condition.';');
                            $r = self::mixedBool($r);
                            self::restoreErrorReporting();
                        } elseif ($clean_orphan === false) {
                            $pos = $ini + $length_prefix;
                            continue;
                        }
                    } else {
                        if (self::$__log_mode) {
                            $this->logger("The condition $condition is not valid");
                        }

                        if ($clean_orphan === false) {
                            $pos = $ini + $length_prefix;
                            continue;
                        }
                    }

                    if ($r === true) {
                        $body = $body_parts [0];
                        if (self::$__log_mode) {
                            $this->logger("The condition $condition is true");
                        }
                    } else {
                        $body = $body_parts [1];
                        if (self::$__log_mode) {
                            $this->logger("The condition $condition is false");
                        }
                    }

                    $this->__src = substr($this->__src, 0, $ini).$body.substr($this->__src, $fin + $length_end);
                } else {
                    self::error('Parse error on <b>conditions</b>: '.substr($condition, 0, 50).'...', DIV_ERROR_FATAL);
                }
            } else {
                break;
            }
        }
    }

    /**
     * Parse conditional parts
     *
     * @param array $items
     */
    final public function parseConditional(&$items = [])
    {
        if (self::$__log_mode) {
            $this->logger('Parsing conditional parts...');
        }

        $r = array_merge($this->getBlockRanges(null, DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX, DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX, DIV_TAG_CONDITIONAL_TRUE_END_PREFIX, DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX),
            $this->getBlockRanges(null, DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX, DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX, DIV_TAG_CONDITIONAL_FALSE_END_PREFIX, DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX));

        $vars = [];
        $ii = $this->getAllItems($items);
        foreach ($r as $tag) {

            $original_tag = $tag;
            $tag [2] = self::div($tag [2], $ii, [], 1, true);

            if (self::issetVar($tag[2], $items)) {
                $vars[$original_tag[2]] = self::getVarValue($tag [2], $items);
            } else {
                $arr = explode('-', $tag [2]);
                // count($arr) === 2
                if (isset($arr [1]) && !isset($arr [2]) && self::issetVar($arr [0], $items)) {
                    $v = self::getVarValue($arr [0], $items);
                    if (is_object($v)) {
                        $v = get_object_vars($v);
                    }
                    if (is_array($v)) {
                        foreach ($v as $kk => $iv) {
                            $vv = self::getVarValue($arr [1], $iv);
                            if (array_key_exists($vv, $vars)) {
                                $vars [$original_tag [2]] = self::mixedBool($vars [$original_tag [2]]) || self::mixedBool($vv);
                            } else {
                                $vars [$original_tag [2]] = self::mixedBool($vv);
                            }
                        }
                    }
                }
            }
        }

        $varsx = $this->getActiveVars($items);
        foreach ($varsx as $var) {
            $vars [$var] = self::mixedBool(self::getVarValue($var, $items));
        }

        if (!empty ($vars)) {
            $keys = array_keys($vars);
            $nkeys = [];
            foreach ($keys as $k => $v) {
                $nkeys [$v] = strlen($v);
            }
            arsort($nkeys);
            foreach ($nkeys as $var => $l) {
                $this->parseConditionalBlock($var, $vars [$var]);
            }
        }
    }

    /**
     * Parse orphan parts
     */
    final public function parseOrphanParts()
    {
        if (self::$__log_mode) {
            $this->logger('Parsing orphan parts...');
        }

        $keys = $this->getConditionalKeys();
        $items = $this->getAllItems();
        foreach ($keys as $key) {
            if (!self::varExists($key, $items))
                $this->parseConditionalBlock($key, false);
        }
    }

    /**
     * Return a list of conditional parts's tags
     *
     * @return array
     */
    final public function getConditionalKeys()
    {
        $ranges = $this->getConditionalRanges();

        $keys = [];
        foreach ($ranges as $rang) {
            $keys [] = $rang [2];
        }

        return $keys;
    }

    /**
     * Return true if $src have a div code
     */
    final public static function haveDivCode($src)
    {
        $all_tags = [
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
            DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL,
            DIV_TAG_SPECIAL_REPLACE_SPACE,
        ];

        foreach ($all_tags as $tag) {
            if (($tag !== '') && strpos($src, $tag) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return a list of conditional parts ranges
     *
     * @param boolean $orphans
     *
     * @return array
     */
    final public function getConditionalRanges($orphans = true, $src = null, $strict = false)
    {
        $ranges = $this->getBlockRanges($src, DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX, DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX, DIV_TAG_CONDITIONAL_FALSE_END_PREFIX, DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX);
        $ranges = array_merge($ranges, $this->getBlockRanges($src, DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX, DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX, DIV_TAG_CONDITIONAL_TRUE_END_PREFIX, DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX));

        if (!$orphans) {
            $nranges = [];
            foreach ($ranges as $rang) {
                if (self::varExists($rang [2], $this->__items)) {
                    $nranges [] = $rang;
                }
            }
            $ranges = $nranges;
        }

        if ($strict !== false) {
            $nranges = [];
            foreach ($ranges as $rang) {
                if (!self::haveDivCode($rang [2])) {
                    $nranges [] = $rang;
                }
            }
            $ranges = $nranges;
        }

        return $ranges;
    }

    /**
     * Return a list of conditional parts ranges
     *
     * @param null $src
     * @param bool $strict
     *
     * @return array
     */
    final public function getListRanges($src = null, $strict = false)
    {
        $ranges = $this->getBlockRanges($src,
            DIV_TAG_LOOP_BEGIN_PREFIX,
            DIV_TAG_LOOP_BEGIN_SUFFIX,
            DIV_TAG_LOOP_END_PREFIX,
            DIV_TAG_LOOP_END_SUFFIX);

        if ($strict !== false) {
            $nranges = [];
            foreach ($ranges as $rang) {
                if (!self::haveDivCode($rang [2])) {
                    $nranges [] = $rang;
                }
            }
            $ranges = $nranges;
        }

        return $ranges;
    }

    /**
     * Parse date formats
     *
     * @param array $items
     */
    final public function parseDateFormat(&$items = [])
    {
        if (self::$__log_mode) {
            $this->logger("Parsing date's formats...");
        }

        $lprefix = strlen(DIV_TAG_DATE_FORMAT_PREFIX);
        $lsuffix = strlen(DIV_TAG_DATE_FORMAT_SUFFIX);
        $ranges = $this->getRanges(DIV_TAG_DATE_FORMAT_PREFIX, DIV_TAG_DATE_FORMAT_SUFFIX);
        $vars = [];

        $temp = '{'.uniqid('', true).'}';

        foreach ($ranges as $range) {
            $s = substr($this->__src, $ranges [0] [0] + $lprefix, $ranges [0] [1] - $ranges [0] [0] - $lprefix);
            $s = str_replace('\\'.DIV_TAG_DATE_FORMAT_SEPARATOR, $temp, $s);

            $p = strpos($s, DIV_TAG_DATE_FORMAT_SEPARATOR);
            if ($p !== false) {
                $var = substr($s, 0, $p);
                if (!array_key_exists($var, $items)) {
                    $items [$var] = $var;
                }
                $vars [] = $var;
            }
        }

        foreach ($vars as $var) {
            $value = $items [$var];
            if (is_scalar($value)) {
                $this->dateFormat($var, $value);
            }
        }
    }

    /**
     * Giving formats to the dates
     *
     * @param string  $key
     * @param integer $value
     *
     * @return boolean
     */
    final public function dateFormat($key, $value)
    {
        $tag_begin = DIV_TAG_DATE_FORMAT_PREFIX.$key.DIV_TAG_DATE_FORMAT_SEPARATOR;
        $tag_end = DIV_TAG_DATE_FORMAT_SUFFIX;
        $l1 = strlen($tag_begin);
        $l2 = strlen($tag_end);

        if (strpos($this->__src, $tag_begin) === false) {
            return false;
        }
        if (strpos($this->__src, $tag_end) === false) {
            return false;
        }

        while (true) {
            $ranges = $this->getRanges($tag_begin, $tag_end, null, true);
            if (count($ranges) < 1) {
                break;
            }

            $ini = $ranges [0] [0];
            $fin = $ranges [0] [1];
            $format = substr($this->__src, $ini + $l1, $fin - ($ini + $l1));

            if (trim($format) === '') {
                $format = 'Y-m-d';
            }
            if (!is_numeric($value)) {
                $value = @strtotime((string)$value);
            }
            $this->__src = substr($this->__src, 0, $ini).date($format, $value).substr($this->__src, $fin + $l2);
        }

        return true;
    }

    /**
     * Parsing capsules
     *
     * @param array $items
     */
    final public function parseCapsules(&$items = [])
    {
        if (self::$__log_mode) {
            $this->logger('Parsing capsules...');
        }

        $pos = 0;
        while (true) {
            $ranges = $this->getBlockRanges(null, DIV_TAG_CAPSULE_BEGIN_PREFIX, DIV_TAG_CAPSULE_BEGIN_SUFFIX, DIV_TAG_CAPSULE_END_PREFIX, DIV_TAG_CAPSULE_END_SUFFIX, $pos, null, true);

            if (count($ranges) < 1) {
                break;
            }

            $key = $ranges [0] [2];
            if (!array_key_exists($key, $items)) {
                $pos = $ranges [0] [0] + 1;
                continue;
            }
            $value = $items [$key];

            $ini = $ranges [0] [0];
            $fin = $ranges [0] [1];
            $sub_src = $ranges [0] [3];

            if (is_object($value)) {
                if (method_exists($value, '__toString')) {
                    $item_str = (string)$value;
                    if (!property_exists($value, 'value')) {
                        $value->value = $item_str;
                    }
                    $value->_to_string = $item_str;
                }
                $value = get_object_vars($value);
            }

            if (is_scalar($value)) {
                $value = [
                    'value' => $value,
                ];
            }

            $value = array_merge($items, $value);

            $temp_global = self::$__globals_design; // priority to item's properties
            // Save similar global design vars

            if (is_array($value)) {
                foreach ($value as $kkk => $vvv) {
                    if (array_key_exists($kkk, self::$__globals_design)) {
                        unset (self::$__globals_design [$kkk]);
                    }
                }
            }

            $engine = self::getAuxiliaryEngineClone($items, $items, $this);
            $engine->__src = $sub_src;
            $engine->__items = $value;

            if (is_array($this->__items_orig)) {
                if (array_key_exists($key, $this->__items_orig)) {
                    $engine->__items_orig = $this->__items_orig [$key];
                }
            } elseif (is_object($this->__items_orig)) {
                if (property_exists($this->__items_orig, $key)) {
                    $engine->__items_orig = $this->__items_orig->$key;
                }
            }

            $engine->parse(false);
            $hh = $engine->__src;

            // Restore global design vars
            self::$__globals_design = $temp_global;

            $this->__src = substr($this->__src, 0, $ini).$hh.substr($this->__src, $fin + strlen(DIV_TAG_CAPSULE_END_PREFIX.$key.DIV_TAG_CAPSULE_END_SUFFIX));

            $pos = $ini;
        }
    }

    /**
     * Parsing a code if method call and invoke the method
     *
     * @param string $code
     * @param array  $items
     *
     * @return mixed
     */
    final public function getMethodResult($code, &$items = null)
    {
        if ($items === null) {
            $items = &$this->__items;
        }

        // Creating auxiliary engine
        $engine = self::getAuxiliaryEngineClone($items, $items, $this);

        // Detect method name
        $p = strpos($code, '(');
        $method = substr($code, 0, $p);
        $engine->__src = $method;
        $engine->parse(false);
        $method = trim($engine->__src);
        $objects = [];

        if (strpos($method, DIV_TAG_VAR_MEMBER_DELIMITER)) {
            $temp = explode(DIV_TAG_VAR_MEMBER_DELIMITER, $method);
            $method = $temp [count($temp) - 1];
            unset ($temp [count($temp) - 1]);
            $path = implode(DIV_TAG_VAR_MEMBER_DELIMITER, $temp);
            $it = $this->getItem($path);

            if (is_object($it)) {
                $objects = [
                    $it,
                ];
            }
        } elseif (is_object($this->__items_orig)) {
            $objects = [
                $this->__items_orig,
                $this,
            ];
        } else {
            $objects = [
                $this,
            ];
        }

        foreach ($objects as $obj) {
            if (!is_object($obj)) {
                continue;
            }

            $class_name = get_class($obj);

            $methods = get_class_methods($class_name);
            $ms = [];
            foreach ($methods as $m) {
                if (!in_array($m, self::$__parent_method_names, true)) {
                    $ms [] = $m;
                }
            }

            if (in_array($method, $ms, true)) {

                $params = substr($code, $p + 1);
                $params = substr($params, 0, strlen($params) - 1);

                if (self::isValidMacro($params)) {

                    $engine->__src = $params;
                    $engine->parse(false);
                    $params = trim($engine->__src);

                    if (strpos($params, '{') !== 0 && strpos($params, '[') !== 0) {
                        $r = null;

                        self::changeErrorReporting();
                        eval ('$r = $obj->'.$method.'('.$params.');');
                        self::restoreErrorReporting();

                        return $r;
                    }

                    $params = self::jsonDecode($params, $this->getAllItems($items));

                    return $obj->$method ($params);
                } else {
                    self::error("Wrong params or obtrusive code in method call: $method", DIV_ERROR_FATAL);
                }
            }
        }

        return DIV_METHOD_NOT_EXISTS;
    }

    /**
     * Return a list of vars that are active in template
     *
     * @param mixed  $items
     * @param string $super_key
     * @param string $src
     *
     * @return array
     */
    final public function getActiveVars($items, $super_key = '', $src = null)
    {
        if ($src === null) {
            $src = &$this->__src;
        }

        if ($super_key !== '' && strpos($src, $super_key) === false) {
            return [];
        }

        if (is_object($items)) {
            $items_x = get_object_vars($items);
        } elseif (is_array($items)) {
            $items_x = $items;
        } else {
            return [];
        }

        $vars = [];

        foreach ($items_x as $key => $value) {
            if (($super_key.$key !== '') && strpos($src, $super_key.$key) !== false) {
                $vars [] = $super_key.$key;
                if (!is_scalar($items_x [$key])) {
                    $vars = array_merge($vars, $this->getActiveVars($items_x [$key], $super_key.$key.DIV_TAG_VAR_MEMBER_DELIMITER, $src));
                }
            }
        }

        return $vars;
    }

    /**
     * Remember the inactive items
     *
     * @param array $items
     */
    private function memory(&$items)
    {
        $vars = $this->getActiveVars($items);

        foreach ($vars as $var) {
            if (!array_key_exists($var, $items) || strpos($var, DIV_TAG_VAR_MEMBER_DELIMITER) !== false) {
                $items[$var] = self::getVarValue($var, $items);
            }
        }

        $this->__memory = array_merge($this->__memory, $items);

        $items = [];

        $vars = $this->getActiveVars($this->__memory);

        foreach ($vars as $var) {
            $items[$var] = self::getVarValue($var, $this->__memory);
        }
    }

    /**
     * Search the locations in the template
     *
     * @return array
     */
    final public function getLocations()
    {
        $r = $this->getRanges(DIV_TAG_LOCATION_BEGIN, DIV_TAG_LOCATION_END);
        $len_prefix = strlen(DIV_TAG_LOCATION_BEGIN);
        $locations = [];
        $tags = [];
        foreach ($r as $item) {
            $tag_name = substr($this->__src, $item [0] + $len_prefix, $item [1] - $item [0] - $len_prefix);
            if (!isset($locations [$tag_name])) {
                $locations [$tag_name] = [];
            }
            $locations [$tag_name] [] = $item [0];
            $tags [$tag_name] = strlen($tag_name);
        }

        arsort($tags);
        $new_tags = [];

        foreach ($tags as $tag_name => $v) {
            $new_tags [$tag_name] = $locations [$tag_name];
        }

        return $new_tags;
    }

    /**
     * Parse the locations in the template
     */
    final public function parseLocations()
    {
        $locations = $this->getLocations();

        foreach ($locations as $location => $positions) {
            $content = '';
            $pos = 0;
            while (true) {
                $tag_begin = DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX.$location.DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX;
                $tag_end = DIV_TAG_LOCATION_CONTENT_END_PREFIX.$location.DIV_TAG_LOCATION_CONTENT_END_SUFFIX;
                $l1 = strlen($tag_begin);
                $l2 = strlen($tag_end);

                $r = $this->getRanges($tag_begin, $tag_end, null, true, $pos);

                if (count($r) === 0) {
                    break;
                }

                $ini = $r [0] [0];
                $end = $r [0] [1];

                // Get the content
                $new_content = substr($this->__src, $ini + $l1, $end - $ini - $l1);
                if ($new_content !== '') {
                    if (strpos($new_content, ' ') === 0) {
                        $new_content = substr($new_content, 1);
                    }
                    if (substr($new_content, -1) === ' ') {
                        $new_content = substr_replace($new_content, '', -1);
                    }
                }

                $content .= $new_content;

                // Remove declaration
                $this->__src = substr($this->__src, 0, $ini).substr($this->__src, $end + $l2);

                // Update the locations (to left)
                foreach ($locations as $k => $v) {
                    foreach ($v as $kk => $p) {
                        if ($p > $end) {
                            $locations [$k] [$kk] -= $l1 + $l2 + ($end - $ini - $l1);
                        }
                    }
                }
            }

            // Inject the content in the locations
            $tag = DIV_TAG_LOCATION_BEGIN.$location.DIV_TAG_LOCATION_END;
            $this->__src = str_replace($tag, $content.$tag, $this->__src);
        }
    }

    /**
     * Clear location's tags
     */
    private function clearLocations()
    {
        $locations = $this->getLocations();
        foreach ($locations as $location => $positions) {
            $this->__src = str_replace(DIV_TAG_LOCATION_BEGIN.$location.DIV_TAG_LOCATION_END, '', $this->__src);
        }
    }

    /**
     * Parse the sub-parsers
     *
     * @param mixed $items
     * @param array $flags
     */
    final public function parseSubParsers(&$items = null, $flags = [])
    {
        if ($items === null) {
            $items = &$this->__items;
        }

        $items_x = array_merge($this->__memory, $items);
        if (!isset($flags ['level'])) {
            $flags ['level'] = self::$__parse_level;
        }

        foreach (self::$__sub_parsers as $parser => $function) {

            // Checking the moment/event
            if (isset($flags ['moment'])) {
                $arr = explode(':', $parser);
                if (isset($arr [1])) {
                    $last = array_pop($arr);
                    if ($last === 'beforeParse' && $flags ['moment'] !== DIV_MOMENT_BEFORE_PARSE) {
                        continue;
                    }
                    if ($last === 'afterInclude' && $flags ['moment'] !== DIV_MOMENT_AFTER_INCLUDE) {
                        continue;
                    }
                    if ($last === 'afterParse' && $flags ['moment'] !== DIV_MOMENT_AFTER_PARSE) {
                        continue;
                    }
                    if ($last === 'afterReplace' && $flags ['moment'] !== DIV_MOMENT_AFTER_REPLACE) {
                        continue;
                    }
                }
            }

            $parser_tag_ini = DIV_TAG_SUBPARSER_BEGIN_PREFIX.$parser.DIV_TAG_SUBPARSER_BEGIN_SUFFIX;
            $parser_tag_end = DIV_TAG_SUBPARSER_END_PREFIX.$parser.DIV_TAG_SUBPARSER_END_SUFFIX;
            $parser_tag_ini_len = strlen($parser_tag_ini);

            $ignore = false;
            $p = 0;
            while (true) {
                $ranges = $this->getRanges($parser_tag_ini, $parser_tag_end, null, true, $p);

                if (count($ranges) < 1) {
                    break;
                }

                if (self::$__log_mode && $p === 0) {
                    $this->logger("Parsing the sub-parser $parser ...");
                }

                $ini = $ranges [0] [0];
                $fin = $ranges [0] [1];

                if ($this->searchInListRanges($ini)) {
                    $p = $ini + 1;
                    $ignore = true;
                    continue;
                }

                if (DIV_TAG_SUBPARSER_BEGIN_SUFFIX === '' && strpos("\n\t <>", substr($this->__src, $ini + 1, 1)) === false) {
                    $p = $ini + 1;
                    continue;
                }
                if (DIV_TAG_SUBPARSER_END_PREFIX === '' && strpos("\n\t <>", substr($this->__src, $fin - 1, 1)) === false) {
                    $p = $ini + 1;
                    continue;
                }

                $sub_src = substr($this->__src, $ini + $parser_tag_ini_len, $fin - $ini - $parser_tag_ini_len);
                $proceed = false;
                $r = $this->call($function, [$sub_src, $items_x, $flags], $proceed);

                if ($proceed) {
                    $this->__src = substr($this->__src, 0, $ini).$r.substr($this->__src, $fin + $parser_tag_ini_len + 1);
                    $p = $ini + 1;
                } else {
                    $p = $ini + 1;
                    $ignore = true;
                }
            }

            if (strpos($this->__src, $parser_tag_ini) !== false && !$ignore) {
                $proceed = false;
                $r = $this->call($function, [false, $items_x, $flags], $proceed);

                // TODO: do not retrieve $replace_count ??
                if ($proceed) {
                    $this->__src = str_replace($parser_tag_ini, $r, $this->__src, $replace_count);
                }
            }
        }

        $this->memory($items_x);
    }

    /**
     * Checking logical order
     *
     * @param integer $ini
     * @param string  $var
     * @param boolean $check_loops
     * @param boolean $check_matches
     * @param boolean $check_formats
     * @param boolean $check_data
     *
     * @return mixed
     */
    final public function checkLogicalOrder($ini = 0, $var = '', $check_loops = false, $check_matches = false, $check_formats = false, $check_data = false)
    {
        if (self::$__log_mode) {
            $this->logger("Checking logical order at $ini...");
        }

        if ($check_data) {
            $rang = $this->getRanges(DIV_TAG_TPLVAR_BEGIN, DIV_TAG_TPLVAR_END, null, true);
            if ((count($rang) > 0) && $rang [0] [0] < $ini) {
                return $ini + 1;
            }
        }

        if ($check_loops) {
            if ($var !== '') {
                $prev_use = strpos($this->__src, DIV_TAG_LOOP_BEGIN_PREFIX.$var.DIV_TAG_LOOP_BEGIN_SUFFIX);
            } else {
                $prev_use = $this->searchPreviousLoops($ini);
            }

            if ($prev_use !== false && $prev_use < $ini) {
                return $ini + 1;
            }
        }

        if ($check_matches) {
            foreach (self::$__modifiers as $m) {
                $prev_use = strpos($this->__src, DIV_TAG_REPLACEMENT_PREFIX.$m.$var);
                if ($prev_use !== false && $prev_use < $ini) {
                    return $ini + 1;
                }
            }
        }

        if ($check_formats) {
            $prev_use = strpos($this->__src, DIV_TAG_NUMBER_FORMAT_PREFIX.$var);
            if ($prev_use !== false && $prev_use < $ini) {
                return $ini + 1;
            }
            $prev_use = strpos($this->__src, DIV_TAG_DATE_FORMAT_PREFIX.$var);
            if ($prev_use !== false && $prev_use < $ini) {
                return $ini + 1;
            }
        }

        return false;
    }

    /**
     * Parsing the macros
     *
     * @param mixed   $items
     * @param boolean $ignore_previous_match
     *
     * @return mixed
     */
    final public function parseMacros(&$items = null, $ignore_previous_match = false)
    {
        if (self::$__log_mode) {
            $this->logger('Parsing macros...');
        }

        if ($items === null) {
            $items = &$this->__items;
        }

        // Free the macro's scope and protect the scope of this method
        $this->__temp = [];
        $this->__temp ['p'] = 0;

        $l1 = strlen(DIV_TAG_MACRO_BEGIN);
        $l2 = strlen(DIV_TAG_MACRO_END);

        while (true) {
            $class_name = get_class($this);

            $this->__temp ['ranges'] = $this->getRanges(DIV_TAG_MACRO_BEGIN, DIV_TAG_MACRO_END, null, true, $this->__temp ['p']);

            if (count($this->__temp ['ranges']) < 1) {
                break;
            }

            $this->__temp ['ini'] = $this->__temp ['ranges'] [0] [0];
            $this->__temp ['fin'] = $this->__temp ['ranges'] [0] [1];

            if (!$ignore_previous_match) {
                $this->__temp ['r'] = $this->checkLogicalOrder($this->__temp ['ini'], '', true, !$ignore_previous_match, true, false);

                if ($this->searchInListRanges($this->__temp ['ini'])) {
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
            $this->__temp ['code'] = trim(substr($this->__src, $this->__temp ['ini'] + $l1, $this->__temp ['fin'] - $this->__temp ['ini'] - $l1));
            $this->__temp ['temp'] = uniqid('', true);

            $this->__src = substr($this->__src, 0, $this->__temp ['ini']).'{'.$this->__temp ['temp'].'}'.substr($this->__src, $this->__temp ['fin'] + $l2);

            if (strpos($this->__temp ['code'], 'php') === 0) {
                $__code = substr($this->__temp ['code'], 3);
            }

            ob_start();

            $this->__temp ['invalid_macro'] = false;

            if (self::isValidMacro($this->__temp ['code'])) {

                // Preparing methods
                $this->__temp ['validmethods'] = implode(',', self::$__allowed_methods);
                $this->__temp ['validmethods'] = str_replace(',', '(,'.$class_name.'::', $class_name.'::'.$this->__temp ['validmethods']).'(';
                $this->__temp ['methods'] = explode(',', str_replace($class_name.'::', '', $this->__temp ['validmethods']));
                $this->__temp ['methodsx'] = explode(',', $this->__temp ['validmethods']);
                $this->__temp ['code'] = str_replace($this->__temp ['methods'], $this->__temp ['methodsx'], $this->__temp ['code']);

                // Preparing variables
                foreach ($items as $key => $value) {
                    if (strpos($key, DIV_TAG_VAR_MEMBER_DELIMITER) !== false) {
                        self::setVarValue($key, $value, $items);
                        unset ($items [$key]);
                    }
                }

                $this->__temp ['codevars'] = '';

                foreach ($items as $key => $value) {
                    if (self::isValidVarName($key)) {
                        $this->__temp ['codevars'] .= '$'.$key.' = $items["'.$key.'"];';
                    }
                }

                $this->__temp ['items'] = $items;

                unset($key, $value, $class_name);

                if ($this->__temp ['codevars'] !== '') {
                    eval ($this->__temp ['codevars']);
                }

                unset ($items);

                // Executing the macro

                eval ($this->__temp['code']);

                // Div 4.5: change $vars with temporal var, ...important!
                // because get_defined_vars return also 'vars'

                $this->__temp['vars'] = get_defined_vars();

                $items = $this->__temp['items'];

                foreach ($this->__temp['vars'] as $var => $value) {
                    if ($var === 'this') {
                        continue;
                    } // Very very important!!

                    if (!array_key_exists($var, $items) || array_key_exists($var, self::$__globals_design)) {
                        self::$__globals_design[$var] = $value;
                    }

                    $items[$var] = $value;
                }
            } else {
                $this->__temp['invalid_macro'] = true;
            }

            $this->__src = str_replace('{'.$this->__temp['temp'].'}', ob_get_contents(), $this->__src);

            ob_end_clean();

            if ($this->__temp['invalid_macro']) {
                $this->__temp['msgs'] = self::getInternalMsg('php_validations');
                $this->__temp['details'] = "<ul>\n";
                foreach ($this->__temp['msgs'] as $msg) {
                    $this->__temp['details'] .= '<li>'.$msg['msg']."</li>\n";
                }
                $this->__temp['details'] .= '</ul>';

                self::error("Invalid macro: \n\n <br/> <pre width=\"80\">".substr($this->__temp['code'], 0, 300).'(...)</pre><br/>'.$this->__temp['details']);
            }

            $this->__temp['p'] = $this->__temp['ini'] + 1;
        }

        // Free all temporal vars
        $this->__temp = [];

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
     *
     * @return array
     */
    final public function saveSections($begin_prefix, $begin_suffix, $end_prefix, $end_suffix, $src = null)
    {
        if ($src === null) {
            $src = $this->__src;
        }

        $pos = 0;

        $saved_sections = [];

        while (true) {
            $r = $this->getBlockRanges($src, $begin_prefix, $begin_suffix, $end_prefix, $end_suffix, $pos, null, true);

            if (count($r) < 1) {
                break;
            }

            $ini = $r[0][0] + strlen($begin_prefix) + strlen($r[0][2]) + strlen($begin_suffix);
            $length = $r[0][1] - $ini;

            $uid = '{'.uniqid('', true).'}';
            $section = substr($src, $ini, $length);
            $saved_sections[$uid] = $section;

            $src = substr($src, 0, $ini).$uid.substr($src, $ini + $length);

            $pos = $ini + 1;
        }

        return [
            'src'      => $src,
            'sections' => $saved_sections,
        ];
    }

    /**
     * Restoring saved sections
     *
     * @param string $src
     * @param array  $sections
     *
     * @return string
     */
    final public function restoreSavedSections($src, $sections)
    {
        foreach ($sections as $uid => $section) {
            $src = str_replace($uid, $section, $src);
        }

        return $src;
    }

    /**
     * Making that remembered
     *
     * @param integer $checksum
     * @param mixed   $items
     */
    private function makeItAgain($checksum, &$items)
    {
        if (self::$__log_mode === true) {
            $this->logger('Making again some remembered tasks...');
        }

        $simple = DIV_TAG_REPLACEMENT_PREFIX.DIV_TAG_MODIFIER_SIMPLE;

        // Save some sections (to ignore)

        // ... saving loops
        $r = $this->saveSections(DIV_TAG_LOOP_BEGIN_PREFIX, DIV_TAG_LOOP_BEGIN_SUFFIX, DIV_TAG_LOOP_END_PREFIX, DIV_TAG_LOOP_END_SUFFIX, $this->__src);
        $this->__src = $r['src'];
        $saved_sections = $r['sections'];

        // ... saving capsules
        $r = $this->saveSections(DIV_TAG_CAPSULE_BEGIN_PREFIX, DIV_TAG_CAPSULE_BEGIN_SUFFIX, DIV_TAG_CAPSULE_END_PREFIX, DIV_TAG_CAPSULE_END_SUFFIX, $this->__src);
        $this->__src = $r['src'];
        $saved_sections = array_merge($saved_sections, $r['sections']);

        foreach (self::$__remember[$checksum] as $params) {

            $literal = $this->isLiteral($params['key']);

            $vpx = '';
            $vsx = '';

            if ($literal === true) {
                $vpx = '{'.$this->__ignore_secret_tag.'}';
                $vsx = '{/'.$this->__ignore_secret_tag.'}';
            }

            switch ($params['o']) {
                case 'replace_sub_match_teaser' :
                    $value = self::getVarValue($params['key'], $items);
                    $value = self::anyToStr($value);
                    if ($value === null) {
                        continue 2;
                    }
                    $value = self::teaser((string)$value, (int)$params['param']);

                    $search = DIV_TAG_REPLACEMENT_PREFIX.$params['modifier'].$params['key'].DIV_TAG_SUBMATCH_SEPARATOR.$params['param'].DIV_TAG_REPLACEMENT_SUFFIX;
                    $this->__src = str_replace($search, $vpx.$value.$vsx, $this->__src);
                    break;

                case 'replace_sub_match_sub_str' :
                    $value = self::getVarValue($params['key'], $items);
                    if ($value === null) {
                        continue 2;
                    }
                    $value = self::anyToStr($value);
                    $this->__src = str_replace($simple.$params['key'].DIV_TAG_SUBMATCH_SEPARATOR.$params['param'].DIV_TAG_REPLACEMENT_SUFFIX, $vpx.substr($value, $params['from'], $params['for']).$vsx, $this->__src);
                    break;

                case 'replace_sub_match_wordwrap' :
                    $value = self::getVarValue($params['key'], $items);
                    if ($value === null) {
                        continue 2;
                    }
                    $value = self::anyToStr($value);
                    $this->__src =
                        str_replace($simple.$params['key'].DIV_TAG_SUBMATCH_SEPARATOR.$params['param'].DIV_TAG_REPLACEMENT_SUFFIX, $vpx.wordwrap((string)$value, (int)substr($params['param'], strlen(DIV_TAG_MODIFIER_WORDWRAP)), "\n", 1).$vsx,
                            $this->__src);
                    break;

                case 'replace_sub_match_sprintf' :
                    $value = self::getVarValue($params['key'], $items);
                    if ($value === null) {
                        continue 2;
                    }
                    $value = self::anyToStr($value);
                    $this->__src = str_replace($simple.$params['key'].DIV_TAG_SUBMATCH_SEPARATOR.$params['param'].DIV_TAG_REPLACEMENT_SUFFIX, $vpx.sprintf($params['param'], $value).$vsx, $this->__src);
                    break;

                case 'json_encode' :
                    $value = self::getVarValue($params['key'], $items);
                    if ($value === null) {
                        continue 2;
                    }
                    $this->__src = str_replace(DIV_TAG_REPLACEMENT_PREFIX.DIV_TAG_MODIFIER_ENCODE_JSON.$params['key'].DIV_TAG_REPLACEMENT_SUFFIX, $vpx.self::jsonEncode($value).$vsx, $this->__src);
                    break;

                case 'simple_replacement' :
                    $value = self::getVarValue($params['key'], $items);

                    if ($value === null) {
                        continue 2;
                    }

                    $value = self::anyToStr($value);

                    switch ($params['modifier']) {
                        case DIV_TAG_MODIFIER_CAPITALIZE_FIRST :
                            $value = ucfirst($value);
                            break;
                        case DIV_TAG_MODIFIER_CAPITALIZE_WORDS :
                            $value = ucwords($value);
                            break;
                        case DIV_TAG_MODIFIER_UPPERCASE :
                            $value = strtoupper($value);
                            break;
                        case DIV_TAG_MODIFIER_LENGTH :
                            $value = strlen($value);
                            break;
                        case DIV_TAG_MODIFIER_COUNT_WORDS :
                            $value = self::getCountOfWords($value);
                            break;
                        case DIV_TAG_MODIFIER_COUNT_SENTENCES :
                            $value = self::getCountOfSentences($value);
                            break;
                        case DIV_TAG_MODIFIER_COUNT_PARAGRAPHS :
                            $value = self::getCountOfParagraphs($value);
                            break;
                        case DIV_TAG_MODIFIER_ENCODE_URL :
                            $value = urlencode($value);
                            break;
                        case DIV_TAG_MODIFIER_ENCODE_RAW_URL :
                            $value = rawurlencode($value);
                            break;
                    }

                    if ($params['before'] === false) {
                        $this->__src = str_replace(DIV_TAG_REPLACEMENT_PREFIX.$params['modifier'].$params['key'].DIV_TAG_REPLACEMENT_SUFFIX, $vpx.$value.$vsx, $this->__src);
                    } else {
                        $sub_str = substr($this->__src, 0, $params['before']);
                        $sub_str = str_replace(DIV_TAG_REPLACEMENT_PREFIX.$params['modifier'].$params['key'].DIV_TAG_REPLACEMENT_SUFFIX, $vpx.$value.$vsx, $sub_str);
                        $this->__src = $sub_str.substr($this->__src, $params ['before']);
                    }
                    break;
            }
        }

        // Restoring saved sections
        $this->__src = $this->restoreSavedSections($this->__src, $saved_sections);
    }

    /**
     * Return the template's properties
     *
     * @param string $src
     *
     * @return array
     */
    final public function getTemplateProperties(&$src = null)
    {
        $update = false;

        if ($src === null) {
            $src = &$this->__src;
            $update = true;
        }

        $properties = [];

        if (strpos($src, '@_') !== false) {
            $src = str_replace("\n\r", "\n", $src);
            $lines = explode("\n", $src);
            $new_src = '';
            $engine = self::getAuxiliaryEngineClone($this->__memory, $this->__memory, $this);

            foreach ($lines as $line_number => $line) {
                $s = trim($line);
                if (strpos($s, '@_') === 0) {
                    $s = substr($s, 2);
                    if ($s !== '') {
                        $arr = explode('=', $s);
                        if (count($arr) > 1) {
                            $var = strtoupper(trim($arr [0]));
                            if (!array_key_exists($var, $properties)) {
                                array_shift($arr);
                                $value = implode('=', $arr);
                                $engine->__src = $value;
                                $engine->parse(false);
                                $value = $engine->__src;
                                $value_json = self::jsonDecode($value, $this->getAllItems());
                                if ($value_json !== null) {
                                    $value = $value_json;
                                } else {
                                    $value = trim($value);
                                }
                                $properties [$var] = $value;
                                continue;
                            }
                        }
                    }
                }
                $new_src .= $line.(isset($lines[$line_number + 1]) ? "\n" : '');
            }
            $src = $new_src;
        }

        if ($update) {
            $this->__src = $src;
        }

        if (self::$__docs_on) {
            $section = trim($this->__path);
            if ($section !== '') {
                if (strpos($section, './') === 0) {
                    $section = substr($this->__path, 2);
                }
                if ($section !== '') {
                    self::$__docs [$section] ['properties'] = $properties;
                }
            }
        }

        return $properties;
    }

    /**
     * Load properties from template code
     */
    final public function loadTemplateProperties()
    {
        $this->__properties = $this->getTemplateProperties();
    }

    /**
     * Preparing template's dialect
     *
     * @param string  $src
     * @param array   $properties
     * @param boolean $update By default $this->__src will be updated after translation if $src is null
     *                        Set this param to false if you want to protect the template source code
     *
     * @return mixed
     */
    final public function prepareDialect($src = null, $properties = null, $update = true)
    {

        if ($src === null) {
            $src = $this->__src;
            $update = $update && true;
        }

        if ($properties === null) {
            $properties = $this->__properties;
        }

        if (array_key_exists('DIALECT', $properties)) {
            $f = trim($properties ['DIALECT']);

            if (self::$__log_mode === true) {
                $this->logger('Preparing the dialect...');
            }

            $json = DIV_DEFAULT_DIALECT;

            if ($f !== '') {
                $f = $this->getTplPath($f);
                $json = self::getFileContents($f);
            }

            if ($json !== null && $json !== DIV_DEFAULT_DIALECT) {
                $src = $this->translateFrom($json, $src);
                if ($update) {
                    $this->__src = $src;
                }
            } elseif (self::$__log_mode) {
                self::log('The dialect '.$f.' is corrupt or invalid');
            }
        }

        return $src;
    }

    /**
     * Parse the template
     *
     * @param boolean $from_original
     * @param mixed   $item_index
     * @param integer $min_level
     *
     * @return string
     */
    final public function parse($from_original = true, $item_index = null, $min_level = 1)
    {

        // Generate internal and random ignore tag (security reasons)
        if ($this->__ignore_secret_tag === null) {
            $this->__ignore_secret_tag = uniqid('', true);
        }

        self::createAuxiliaryEngine($this);
        self::$__parse_level++;

        if (self::$__log_mode) {
            $this->logger('Parsing all...');
        }

        $time_start = microtime(true);

        self::repairSubParsers();

        // Calling the beforeParse hook
        $this->beforeParse();

        if ($from_original === true) {
            if (self::$__log_mode) {
                $this->logger('Parsing from the original source');
            }
            if ($this->__src_original === null) {
                $this->__src_original = $this->__src;
            } else {
                $this->__src = $this->__src_original;
            }
        }

        $sub_parsers_restore = [];

        if (trim($this->__src) !== '') {
            if ($item_index !== null) {
                if (self::$__log_mode) {
                    $this->logger("Parsing with '$item_index' index of __items");
                }
                $items = $this->__items [$item_index];
            } else {
                $items = $this->__items;
            }

            if ($items === null) {
                $items = [];
            }

            // Reserved vars
            $items['_empty'] = [];
            $items['_'] = [];
            $items['_vars'] = array_keys($items);

            // Add global vars (self::$globals)
            foreach (self::$__globals as $var => $value) {
                if (!array_key_exists($var, $items)) {
                    $items [$var] = $value;
                }
            }

            $items = array_merge($items, self::$__globals_design);
            $items = array_merge($items, self::getSystemData());

            // Add properties
            $props = get_object_vars($this);
            foreach ($props as $prop => $value) {
                if (strpos($prop, '__') !== 0) {
                    $items [$prop] = $value;
                }
            }

            if (strpos($this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX) !== false) {
                $this->parseSubParsers($items, [
                    'moment' => DIV_MOMENT_BEFORE_PARSE,
                ]);
            }

            // Template's properties
            $this->loadTemplateProperties();

            // Preparing dialect
            $this->__src = $this->prepareDialect();

            if (strpos($this->__src, DIV_TAG_IGNORE_BEGIN) !== false || strpos($this->__src, '{'.$this->__ignore_secret_tag.'}') !== false) {
                $this->parseIgnore();
            }

            if (strpos($this->__src, DIV_TAG_COMMENT_BEGIN) !== false) {
                $this->parseComments();
            }

            if (strpos($this->__src, DIV_TAG_FRIENDLY_BEGIN) !== false) {
                $this->parseFriendly();
            }

            $cycles2 = 0;

            $this->memory($items);

            $msg_infinite_cycle = 'Too many iterations of the parser: possible infinite cycle. Review your template code.';

            $last_action = false;

            do {

                $cycles1 = 0;
                $cycles2++;

                if ($cycles2 > DIV_MAX_PARSE_CYCLES) {
                    self::error($msg_infinite_cycle, 'FATAL');
                }

                do {

                    $checksum = crc32($this->__src);
                    $this->__crc = $checksum;

                    if (self::$__log_mode === true) {
                        $this->logger('Template | size: '.strlen($this->__src));
                        if (isset($this->__src [100])) {
                            $this->logger('Template [checksum='.$checksum.']:'.htmlentities(str_replace("\n", ' ', substr($this->__src, 0, 100)).'...'.substr($this->__src, strlen($this->__src) - 100)));
                        } else {
                            $this->logger('Template [checksum='.$checksum.']: '.htmlentities($this->__src));
                        }
                    }

                    $cycles1++;

                    if ($cycles1 > DIV_MAX_PARSE_CYCLES) {
                        $this->error($msg_infinite_cycle, 'FATAL');
                    }
                    $this->memory($items);

                    // Conditional
                    if (strpos($this->__src, DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX) !== false || strpos($this->__src, DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX) !== false) {
                        $this->parseConditional($items);
                    }

                    // Conditions
                    if (strpos($this->__src, DIV_TAG_CONDITIONS_BEGIN_PREFIX) !== false) {
                        $this->parseConditions($items);
                    }

                    // Include
                    if (strpos($this->__src, DIV_TAG_INCLUDE_BEGIN) !== false) {
                        $this->parseInclude($items);

                        if (strpos($this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX) !== false) {
                            $this->parseSubParsers($items, [
                                'moment' => DIV_MOMENT_AFTER_INCLUDE,
                            ]);
                        }

                        if (strpos($this->__src, DIV_TAG_IGNORE_BEGIN) !== false || strpos($this->__src, '{'.$this->__ignore_secret_tag.'}') !== false) {
                            $this->parseIgnore();
                        }
                        if (strpos($this->__src, DIV_TAG_COMMENT_BEGIN) !== false) {
                            $this->parseComments();
                        }
                        if (strpos($this->__src, DIV_TAG_FRIENDLY_BEGIN) !== false) {
                            $this->parseFriendly();
                        }

                        $this->memory($items);
                    }

                    // Multiple variable's modifiers
                    if (strpos($this->__src, DIV_TAG_MULTI_MODIFIERS_PREFIX) !== false && strpos($this->__src, DIV_TAG_MULTI_MODIFIERS_SUFFIX) !== false) {
                        $this->parseMultipleModifiers();
                    }

                    // Data in templates
                    if ((strpos($this->__src, DIV_TAG_TPLVAR_BEGIN) !== false) && strpos($this->__src, DIV_TAG_TPLVAR_END) !== false) {
                        $items = array_merge($this->__memory, $items);
                        $this->parseData($items);
                        $this->memory($items);
                    }

                    // Number format
                    if (strpos($this->__src, DIV_TAG_NUMBER_FORMAT_PREFIX) !== false) {
                        $this->parseNumberFormat($items);
                    }

                    // Preprocessed
                    if (strpos($this->__src, DIV_TAG_PREPROCESSED_BEGIN) !== false) {
                        $this->parsePreprocessed($items);
                        $this->memory($items);
                    }

                    $items = array_merge($items, self::$__globals_design);

                    // Default values in templates
                    if (strpos($this->__src, DIV_TAG_DEFAULT_REPLACEMENT_BEGIN) !== false) {
                        $this->parseDefaults($items);
                    }

                    // Macros
                    if (strpos($this->__src, DIV_TAG_MACRO_BEGIN) !== false) {
                        $items = array_merge($this->__memory, $items);
                        $items = $this->parseMacros($items, $last_action);
                        $this->memory($items);
                    }

                    // Lists
                    if ((strpos($this->__src, DIV_TAG_LOOP_BEGIN_PREFIX) !== false) && (strpos($this->__src, DIV_TAG_LOOP_END_PREFIX) !== false) && strpos($this->__src, DIV_TAG_LOOP_END_SUFFIX) !== false) {
                        $this->parseList($items);
                    }

                    $items = array_merge($items, self::$__globals_design);

                    // Capsules
                    if ((strpos($this->__src, DIV_TAG_CAPSULE_BEGIN_PREFIX) !== false) && strpos($this->__src, DIV_TAG_CAPSULE_END_SUFFIX) !== false) {
                        $this->parseCapsules($items);
                    }

                    $items = array_merge($items, self::$__globals_design);

                    // Make it again
                    if (isset(self::$__remember [$checksum])) {

                        $this->makeItAgain($checksum, $items);

                        if (strpos($this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX) !== false) {
                            $this->parseSubParsers($items, [
                                'moment' => DIV_MOMENT_AFTER_REPLACE,
                            ]);
                        }
                    }

                    // Sub-Matches
                    if (self::atLeastOneString($this->__src, self::$__modifiers)) {
                        $this->parseSubmatches($items);
                    }

                    // Matches
                    if (self::atLeastOneString($this->__src, self::$__modifiers) || (strpos($this->__src, DIV_TAG_NUMBER_FORMAT_PREFIX) !== false && strpos($this->__src, DIV_TAG_NUMBER_FORMAT_SUFFIX) !== false)) {
                        $this->parseMatches($items, $last_action);
                    }

                    // Discard literal vars
                    if (strpos($this->__src, DIV_TAG_IGNORE_BEGIN) !== false || strpos($this->__src, '{'.$this->__ignore_secret_tag.'}') !== false) {
                        $this->parseIgnore();
                    }

                    // Sub-parse: after replace
                    if (strpos($this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX) !== false) {
                        $this->parseSubParsers($items, [
                            'moment' => DIV_MOMENT_AFTER_REPLACE,
                        ]);
                    }

                    // Iterations
                    if ((strpos($this->__src, DIV_TAG_ITERATION_BEGIN_PREFIX) !== false) && strpos($this->__src, DIV_TAG_ITERATION_END) !== false) {
                        $this->parseIterations($items);
                    }

                    $crc_now = crc32($this->__src);

                    if ($checksum !== $crc_now) {
                        $last_action = false;
                    }
                } while ($checksum !== $crc_now);

                // Computing
                if ((strpos($this->__src, DIV_TAG_FORMULA_BEGIN) !== false) && strpos($this->__src, DIV_TAG_FORMULA_END) !== false) {
                    $this->parseFormulas($items);
                }

                // Date format
                if ((strpos($this->__src, DIV_TAG_DATE_FORMAT_PREFIX) !== false) && strpos($this->__src, DIV_TAG_DATE_FORMAT_SUFFIX) !== false) {
                    $this->parseDateFormat($items);
                }

                // Multiple replacements
                if ((strpos($this->__src, DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX) !== false) && (strpos($this->__src, DIV_TAG_MULTI_REPLACEMENT_END_PREFIX) !== false) && strpos($this->__src, DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX) !== false) {
                    $this->parseMultiReplace($items);
                }

                // Searching orphan parts (conditions)
                if (strpos($this->__src, DIV_TAG_CONDITIONS_BEGIN_PREFIX) !== false) {
                    $this->parseConditions($items, true);
                }

                // Div 4.5: One more time? Parsing orphans's parts while checksum not change.
                // (do it because the orphan's parts stop the parser and the results are ugly)
                // TODO: research best solution for this! (this is the second solution found)

                if ($checksum === crc32($this->__src) && self::$__parse_level <= $min_level) {
                    $this->parseOrphanParts();
                }

                $crc_now = crc32($this->__src);

                // Last action?

                $last_action = ($last_action === false && $crc_now === $checksum);
            } while ($checksum !== $crc_now || $last_action === true);

            // Searching orphan parts (conditionals)
            if (strpos($this->__src, DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX) !== false || strpos($this->__src, DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX) !== false) {
                $this->parseOrphanParts();
            }

            if (strpos($this->__src, DIV_TAG_IGNORE_BEGIN) !== false || strpos($this->__src, '{'.$this->__ignore_secret_tag.'}') !== false) {
                $this->parseIgnore();
            }
            if (strpos($this->__src, DIV_TAG_COMMENT_BEGIN) !== false) {
                $this->parseComments();
            }
            if (strpos($this->__src, DIV_TAG_FRIENDLY_BEGIN) !== false) {
                $this->parseFriendly();
            }

            // Locations
            if ((strpos($this->__src, DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX) !== false) && strpos($this->__src, DIV_TAG_LOCATION_BEGIN) !== false) {
                $this->parseLocations();
            }

            // Clear location's tags
            $all_items = null;

            if (strpos($this->__src, DIV_TAG_LOCATION_BEGIN) !== false) {
                $all_items = $this->getAllItems($items);
                $clear = self::getVarValue('div'.DIV_TAG_VAR_MEMBER_DELIMITER.'clear_locations', $all_items);

                if ($clear === null) {
                    $clear = true;
                }

                if ($clear) {
                    $this->clearLocations();
                }
            }

            // Restoring parsers requests
            foreach ($this->__restore as $restore_id => $rest) {
                $this->__src = str_replace('{'.$restore_id.'}', $rest, $this->__src);
            }

            $this->clean();

            // The last action
            if (self::$__parse_level <= 1) {

                // Clear location's tags
                if (strpos($this->__src, DIV_TAG_LOCATION_BEGIN) !== false) {
                    $this->clearLocations();
                }

                $this->parseSpecialChars();

                // Restoring ignored parts
                foreach (self::$__ignored_parts as $id => $ignore) {

                    foreach (self::$__sub_parsers as $sub_parser => $function) {
                        $temp_unique = uniqid('', true);

                        $replaces_count = 0;

                        $tag_search = DIV_TAG_SUBPARSER_BEGIN_PREFIX.$sub_parser.DIV_TAG_SUBPARSER_BEGIN_SUFFIX;
                        $tag_replace = DIV_TAG_SUBPARSER_BEGIN_PREFIX.$temp_unique.DIV_TAG_SUBPARSER_BEGIN_SUFFIX;

                        $ignore = str_replace($tag_search, $tag_replace, $ignore, $replaces_count);

                        if ($replaces_count > 0) {
                            $sub_parsers_restore [$tag_search] = $tag_replace;
                        }
                    }

                    $this->__src = str_replace('{'.$id.'}', $ignore, $this->__src);
                }

                // Restoring ignored parts inside values

                $items = $this->__memory;
                $vars = $this->getVars($items);
                foreach ($vars as $var) {
                    $exp = self::getVarValue($var, $items);
                    if (is_string($exp)) {
                        foreach (self::$__ignored_parts as $id => $ignore) {
                            $exp = str_replace('{'.$id.'}', $ignore, $exp);
                        }
                        self::setVarValue($var, $exp, $items);
                    }
                }
                $this->__memory = $items;
            }

            $this->txt();

            if (strpos($this->__src, DIV_TAG_SUBPARSER_BEGIN_PREFIX) !== false) {
                $items = array_merge($this->__memory, $items);
                $this->parseSubParsers($items, [
                    'moment' => DIV_MOMENT_AFTER_PARSE,
                    'level'  => self::$__parse_level,
                ]);
                $this->memory($items);
            }

            // Restore ignored sub-parsers
            foreach ($sub_parsers_restore as $tag_search => $tag_replace) {
                $this->__src = str_replace($tag_replace, $tag_search, $this->__src);
            }
        }

        $time_end = microtime(true);

        if (self::$__log_mode) {
            $this->logger('Parser duration: '.number_format($time_end - $time_start, 5).' secs');
        }

        self::$__parse_duration = $time_end - $time_start;
        self::$__parse_level--;

        if (self::$__parse_level === 0) {
            $this->__items = array_merge($this->__items, $this->__memory);
            $this->__items = array_merge($this->__items, self::$__globals_design);
            self::$__globals_design = [];
            self::$__globals_design_protected = [];
        }

        // Calling the afterParse hook
        $this->afterParse();
    }

    static function getParseLevel() {
        return self::$__parse_level;
    }

    /**
     * Parsing SpecialChars
     */
    final public function parseSpecialChars()
    {
        $this->__src = str_replace(DIV_TAG_SPECIAL_REPLACE_NEW_LINE."\n\n", DIV_TAG_SPECIAL_REPLACE_NEW_LINE."\n", $this->__src);
        $this->__src = str_replace(DIV_TAG_SPECIAL_REPLACE_NEW_LINE, "\n", $this->__src);
        $this->__src = str_replace(DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB, "\t", $this->__src);
        $this->__src = str_replace(DIV_TAG_SPECIAL_REPLACE_CAR_RETURN, "\r", $this->__src);
        $this->__src = str_replace(DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB, "\v", $this->__src);
        $this->__src = str_replace(DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE, "\f", $this->__src);
        $this->__src = str_replace(DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL, '$', $this->__src);
        $this->__src = str_replace(DIV_TAG_SPECIAL_REPLACE_SPACE, ' ', $this->__src);

    }

    /**
     * Multiple replacement
     *
     * @param array $items
     */
    final public function parseMultiReplace(&$items = null)
    {
        if ($items === null) {
            $items = $this->__items;
        }
        if (is_array($items)) {
            foreach ($items as $key => $value) {
                if (self::isArrayOfArray($value)) {
                    $pos = 0;
                    while (true) {
                        $ranges = $this->getRanges("{:$key}", "{:/$key}", null, true, $pos);

                        if (count($ranges) < 1) {
                            break;
                        }

                        $l = strlen($key) + 4;
                        $ini = $ranges [0] [0];
                        $fin = $ranges [0] [1];

                        $sub_src = substr($this->__src, $ini + $l - 1, $fin - $ini - $l + 1);
                        $engine = self::getAuxiliaryEngineClone($items, $items, $this);
                        $engine->__src = $sub_src;
                        $engine->parse(false);
                        $sub_src = $engine->__src;

                        foreach ($value as $vv) {
                            if (isset($vv [0]) && isset($vv [1])) {
                                $regexp = false;
                                if (isset($vv [2]) && $vv [2] === true) {
                                    $regexp = true;
                                }
                                if ($regexp) {
                                    $sub_src = preg_replace($vv [0], $vv [1], $sub_src);
                                } else {
                                    $sub_src = str_replace($vv [0], $vv [1], $sub_src);
                                }
                            }
                        }

                        $this->__src = substr($this->__src, 0, $ini).$sub_src.substr($this->__src, $fin + $l);
                    }
                }
            }
        }
    }

    /**
     * Clean the output: parsing the strip tags
     */
    final public function clean()
    {
        $this->__src = preg_replace("/\015\012|\015|\012/", "\n", $this->__src);
        $l1 = strlen(DIV_TAG_STRIP_BEGIN);
        $l2 = strlen(DIV_TAG_STRIP_END);

        while (true) {
            $ranges = $this->getRanges(DIV_TAG_STRIP_BEGIN, DIV_TAG_STRIP_END, null, true);

            if (count($ranges) < 1) {
                break;
            }

            $ini = $ranges [0] [0];
            $fin = $ranges [0] [1];
            $sub_src = substr($this->__src, $ini + $l1, $fin - $ini - $l1);

            while (strpos($sub_src, "\n\n") !== false) {
                $sub_src = str_replace("\n\n", "\n", $sub_src);
            }

            $lines = explode("\n", $sub_src);

            $sub_src = '';
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $sub_src .= $line."\n";
            }
            $sub_src = trim($sub_src);
            $this->__src = substr($this->__src, 0, $ini).$sub_src.substr($this->__src, $fin + $l2);
        }
    }

    /**
     * Parse txt tags and convert HTML to readable text
     */
    final public function txt()
    {
        $begin_tag_len = strlen(DIV_TAG_TXT_BEGIN);
        $end_tag_len = strlen(DIV_TAG_TXT_END);
        $width_separator_len = strlen(DIV_TAG_TXT_WIDTH_SEPARATOR);

        while (true) {
            $ranges = $this->getRanges(DIV_TAG_TXT_BEGIN, DIV_TAG_TXT_END, null, true);
            if (count($ranges) < 1) {
                break;
            }

            $begin_tag_pos = $ranges [0] [0];
            $end_tag_pos = $ranges [0] [1];

            $sub_src = substr($this->__src, $begin_tag_pos + $begin_tag_len, $end_tag_pos - $begin_tag_pos - $begin_tag_len);

            $width = 100;
            $width_separator_pos = strpos($sub_src, DIV_TAG_TXT_WIDTH_SEPARATOR);
            if ($width_separator_pos !== false) {
                $width = (int)trim(substr($sub_src, 0, $width_separator_pos));
                $sub_src = substr($sub_src, $width_separator_pos + $width_separator_len);
            }

            $sub_src = self::htmlToText($sub_src, $width);
            $this->__src = substr($this->__src, 0, $begin_tag_pos).$sub_src.substr($this->__src, $end_tag_pos + $end_tag_len);
        }
    }

    /**
     * Translate and change de original template
     *
     * @param mixed  $dialectFrom
     * @param string $src
     * @param mixed  $items
     *
     * @return string
     */
    final public function translateAndChange($dialectFrom, $src = null, $items = null)
    {
        $translation = $this->translateFrom($dialectFrom, $src, $items);
        $this->changeTemplate($translation);

        return $translation;
    }

    /**
     * Translate simple blocks
     *
     * @param string  $src
     * @param string  $begin_tag
     * @param string  $end_tag
     * @param string  $translated_begin_tag
     * @param string  $translated_end_tag
     * @param string  $separator_tag
     * @param string  $translated_separator_tag
     * @param boolean $first
     *
     * @return string
     */
    private function translateSimpleBlocks($src, $begin_tag, $end_tag, $translated_begin_tag, $translated_end_tag, $separator_tag = '', $translated_separator_tag = '', $first = true)
    {
        $begin_tag_len = strlen($begin_tag);
        $end_tag_len = strlen($end_tag);
        $separator_tag_len = strlen(trim((string)$separator_tag));
        $p = 0;
        while (true) {
            $r = $this->getRanges($begin_tag, $end_tag, $src, true, $p);

            if (count($r) < 1) {
                break;
            }

            list($ini, $end_tag) = $r[0]; // TODO: review $end_tag substitution
            $sub_src = substr($src, $ini + $begin_tag_len, $end_tag - $ini - $begin_tag_len);

            if ($separator_tag_len > 0) {
                if ($first) {
                    $po = strpos($sub_src, $separator_tag);
                } else {
                    $po = strrpos($sub_src, $separator_tag);
                }

                if ($po !== false) {
                    $sub_src = substr($sub_src, 0, $po).$translated_separator_tag.substr($sub_src, $po + $separator_tag_len);
                }
            }

            $src = substr($src, 0, $ini).$translated_begin_tag.$sub_src.$translated_end_tag.substr($src, $end_tag + $end_tag_len);

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
     * @param string $translated_begin_prefix
     * @param string $translated_begin_suffix
     * @param string $translated_end_prefix
     * @param string $translated_end_suffix
     * @param string $var_member_delimiter
     * @param string $translated_var_member_delimiter
     *
     * @return string
     */
    private function translateKeyBlocks(
        $src,
        $begin_prefix,
        $begin_suffix,
        $end_prefix,
        $end_suffix,
        $translated_begin_prefix,
        $translated_begin_suffix,
        $translated_end_prefix,
        $translated_end_suffix,
        $var_member_delimiter,
        $translated_var_member_delimiter
    ) {
        $p = 0;
        while (true) {

            $r = $this->getBlockRanges($src, $begin_prefix, $begin_suffix, $end_prefix, $end_suffix, $p, null, true, $var_member_delimiter);

            if (count($r) < 1) {
                break;
            }

            $ini = $r [0] [0];
            $end = $r [0] [1];
            $key = $r [0] [2];
            $sub_src = $r [0] [3];
            $prefix = $begin_prefix.$key.$begin_suffix;
            $suffix = $end_prefix.$key.$end_suffix;
            $prefix_len = strlen($prefix);
            $suffix_len = strlen($suffix);

            $key = str_replace($var_member_delimiter, $translated_var_member_delimiter, $key);
            $src = substr($src, 0, $ini).$translated_begin_prefix.$key.$translated_begin_suffix.$sub_src.$translated_end_prefix.$key.$translated_end_suffix.substr($src, $end + $suffix_len);

            $p = $ini + 1;
        }

        return $src;
    }

    /**
     * Translate templates
     *
     * @param string $src
     * @param mixed  $dialectFrom
     * @param mixed  $dialectTo
     * @param mixed  $items
     *
     * @return string
     */
    final public function translate($dialectFrom = [], $dialectTo = [], $src = null, $items = null)
    {
        // initialize variables

        if (self::$__log_mode === true) {
            $this->logger('Translating template...');
        }

        $update = false;
        if ($src === null) {
            $src = &$this->__src;
            $update = true;
        }

        if ($items === null) {
            $items = &$this->__items;
        }

        $constants = get_defined_constants(true);
        $constants = $constants ['user'];
        $new_constants = [];

        foreach ($constants as $c => $v) {
            if (strpos($c, 'DIV_TAG_') === 0) {
                $new_constants [$c] = $v;
            }
        }

        $constants = $new_constants;

        // Preparing dialect from ...
        if (is_string($dialectFrom)) {
            $dialectFrom = self::jsonDecode($dialectFrom);
        }
        if (is_object($dialectFrom)) {
            $dialectFrom = get_object_vars($dialectFrom);
        }
        if (!is_array($dialectFrom)) {
            return false;
        }

        // Preparing dialect to ...
        if (is_string($dialectTo)) {
            $dialectTo = self::jsonDecode($dialectTo);
        }
        if (is_object($dialectTo)) {
            $dialectTo = get_object_vars($dialectTo);
        }
        if (!is_array($dialectTo)) {
            return false;
        }

        foreach ($constants as $c => $v) {
            if (!isset($dialectFrom [$c])) {
                $dialectFrom [$c] = $v;
            }
            if (!isset($dialectTo [$c])) {
                $dialectTo [$c] = $v;
            }
        }

        // Searching differences
        $different = false;

        foreach ($dialectFrom as $c => $v) {
            if ($v !== $dialectTo[$c]) {
                $different = true;
                break;
            }
        }

        if (!$different) {
            return $src;
        } // The dialects are equals

        $order = [
            'replacement'      => strlen($dialectFrom['DIV_TAG_REPLACEMENT_PREFIX']),
            'multi_modifiers'  => strlen($dialectFrom['DIV_TAG_MULTI_MODIFIERS_PREFIX']),
            'date_format'      => strlen($dialectFrom['DIV_TAG_DATE_FORMAT_PREFIX']),
            'number_format'    => strlen($dialectFrom['DIV_TAG_NUMBER_FORMAT_PREFIX']),
            'formulas'         => strlen($dialectFrom['DIV_TAG_FORMULA_BEGIN']),
            'sub_parsers'      => strlen($dialectFrom['DIV_TAG_SUBPARSER_BEGIN_PREFIX']),
            'ignore'           => strlen($dialectFrom['DIV_TAG_IGNORE_BEGIN']),
            'comment'          => strlen($dialectFrom['DIV_TAG_COMMENT_BEGIN']),
            'html2txt'         => strlen($dialectFrom['DIV_TAG_TXT_BEGIN']),
            'strip'            => strlen($dialectFrom['DIV_TAG_STRIP_BEGIN']),
            'loops'            => strlen($dialectFrom['DIV_TAG_LOOP_BEGIN_PREFIX']),
            'iterations'       => strlen($dialectFrom['DIV_TAG_ITERATION_BEGIN_PREFIX']),
            'conditionals'     => strlen($dialectFrom['DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX']),
            'conditions'       => strlen($dialectFrom['DIV_TAG_CONDITIONS_BEGIN_PREFIX']),
            'tpl_vars'         => strlen($dialectFrom['DIV_TAG_TPLVAR_BEGIN']),
            'default_replace'  => strlen($dialectFrom['DIV_TAG_DEFAULT_REPLACEMENT_BEGIN']),
            'include'          => strlen($dialectFrom['DIV_TAG_IGNORE_BEGIN']),
            'preprocessed'     => strlen($dialectFrom['DIV_TAG_PREPROCESSED_BEGIN']),
            'capsules'         => strlen($dialectFrom['DIV_TAG_CAPSULE_BEGIN_PREFIX']),
            'multi_replace'    => strlen($dialectFrom['DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX']),
            'friendly_tags'    => strlen($dialectFrom['DIV_TAG_FRIENDLY_BEGIN']),
            'macros'           => strlen($dialectFrom['DIV_TAG_MACRO_BEGIN']),
            'location'         => strlen($dialectFrom['DIV_TAG_LOCATION_BEGIN']),
            'location_content' => strlen($dialectFrom['DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX']),
        ];

        arsort($order);

        $x_modifier = [];
        $y_modifier = [];
        foreach ($constants as $modifier_name => $v) {
            if (strpos($modifier_name, 'DIV_TAG_MODIFIER_') === 0) {
                $x_modifier[$modifier_name] = [];
                $x_modifier[$modifier_name][0] = $dialectFrom[$modifier_name];
                $x_modifier[$modifier_name][1] = $dialectTo[$modifier_name];
                $y_modifier[$x_modifier[$modifier_name][0]] = $x_modifier[$modifier_name][1];
            }
        }

        $aggregate_functions = [];
        foreach ($constants as $c => $v) {
            if (strpos($c, 'DIV_TAG_AGGREGATE_FUNCTION_') === 0) {
                $aggregate_functions[$dialectTo[$c]] = $dialectFrom[$c];
            }
        }

        asort($aggregate_functions);
        $aggregate_functions_keys = array_keys($aggregate_functions);

        foreach ($order as $o => $priority) {
            switch ($o) {
                case 'replacement' :
                    foreach ($x_modifier as $modifier => $values) {
                        $prefix_len = strlen($dialectFrom['DIV_TAG_REPLACEMENT_PREFIX'].$values [0]);
                        $suffix_len = strlen($dialectFrom['DIV_TAG_REPLACEMENT_SUFFIX']);
                        $p = 0;
                        while (true) {

                            $r = $this->getRanges($dialectFrom['DIV_TAG_REPLACEMENT_PREFIX'].$values [0], $dialectFrom['DIV_TAG_REPLACEMENT_SUFFIX'], $src, true, $p);
                            if (count($r) < 1) {
                                break;
                            }

                            list($ini, $end) = $r[0];
                            $sub_src = substr($src, $ini + $prefix_len, $end - $ini - $prefix_len);

                            if (strpos($sub_src, "\n") !== false) {
                                $p = $ini + 1;
                                continue;
                            }
                            if (strpos($sub_src, "\t") !== false) {
                                $p = $ini + 1;
                                continue;
                            }
                            if (strpos($sub_src, "\r") !== false) {
                                $p = $ini + 1;
                                continue;
                            }
                            if (strpos($sub_src, ' ') !== false) {
                                $p = $ini + 1;
                                continue;
                            }

                            // Aggregate functions
                            $sub_src = str_replace($dialectFrom['DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR'], $dialectTo['DIV_TAG_AGGREGATE_FUNCTION_PROPERTY_SEPARATOR'], $sub_src);
                            $sub_src = str_replace($aggregate_functions, $aggregate_functions_keys, $sub_src);

                            // Teaser or truncate
                            $sub_src = str_replace($dialectFrom['DIV_TAG_SUBMATCH_SEPARATOR'].$dialectFrom['DIV_TAG_MODIFIER_TRUNCATE'], $dialectTo['DIV_TAG_SUBMATCH_SEPARATOR'].$dialectTo['DIV_TAG_MODIFIER_TRUNCATE'], $sub_src);

                            // Word wrap
                            $sub_src = str_replace($dialectFrom['DIV_TAG_SUBMATCH_SEPARATOR'].$dialectFrom['DIV_TAG_MODIFIER_WORDWRAP'], $dialectTo['DIV_TAG_SUBMATCH_SEPARATOR'].$dialectTo['DIV_TAG_MODIFIER_WORDWRAP'], $sub_src);

                            // Format (sprintf)
                            $sub_src = str_replace($dialectFrom['DIV_TAG_SUBMATCH_SEPARATOR'].$dialectFrom['DIV_TAG_MODIFIER_FORMAT'], $dialectTo['DIV_TAG_SUBMATCH_SEPARATOR'].$dialectTo['DIV_TAG_MODIFIER_FORMAT'], $sub_src);

                            // Substring
                            $sub_src = str_replace($dialectFrom['DIV_TAG_SUBMATCH_SEPARATOR'], $dialectTo['DIV_TAG_SUBMATCH_SEPARATOR'], $sub_src);

                            // Member delimiters
                            $sub_src = str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], $sub_src);

                            $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_REPLACEMENT_PREFIX'].$values [1].$sub_src.$dialectTo['DIV_TAG_REPLACEMENT_SUFFIX'].substr($src, $end + $suffix_len);

                            $p = $ini + 1; // IMPORTANT!
                        }
                    }
                    break;
                case 'multi_modifiers' :
                    $prefix_len = strlen($dialectFrom['DIV_TAG_MULTI_MODIFIERS_PREFIX']);
                    $suffix_len = strlen($dialectFrom['DIV_TAG_MULTI_MODIFIERS_SUFFIX']);
                    $p = 0;
                    while (true) {
                        $r = $this->getRanges($dialectFrom['DIV_TAG_MULTI_MODIFIERS_PREFIX'], $dialectFrom['DIV_TAG_MULTI_MODIFIERS_SUFFIX'], $src, true, $p);
                        if (count($r) < 1) {
                            break;
                        }

                        $ini = $r [0] [0];
                        $end = $r [0] [1];
                        $sub_src = substr($src, $ini + $prefix_len, $end - $ini - $prefix_len);

                        if (strpos($sub_src, "\n") !== false) {
                            $p = $ini + 1;
                            continue;
                        }
                        if (strpos($sub_src, "\t") !== false) {
                            $p = $ini + 1;
                            continue;
                        }
                        if (strpos($sub_src, "\r") !== false) {
                            $p = $ini + 1;
                            continue;
                        }
                        if (strpos($sub_src, ' ') !== false) {
                            $p = $ini + 1;
                            continue;
                        }

                        $po = strpos($sub_src, $dialectFrom['DIV_TAG_MULTI_MODIFIERS_OPERATOR']);

                        if ($po === false) {
                            $p = $ini + 1;
                            continue;
                        }

                        $temp = substr($sub_src, $po + 1);
                        $parts = explode($dialectFrom['DIV_TAG_MULTI_MODIFIERS_SEPARATOR'], $temp);

                        foreach ($parts as $k => $v) {
                            if (isset($y_modifier [$v])) {
                                $parts [$k] = $y_modifier [$v];
                            }
                            if (isset($y_modifier [$v.':'])) {
                                $parts [$k] = $y_modifier [$v.':'];
                            }
                        }

                        $temp = implode($dialectTo['DIV_TAG_MULTI_MODIFIERS_SEPARATOR'], $parts);
                        $sub_src = str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], substr($sub_src, 0, $po)).$dialectTo['DIV_TAG_MULTI_MODIFIERS_OPERATOR'].$temp;

                        // Member delimiters
                        $sub_src = str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], $sub_src);

                        $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_MULTI_MODIFIERS_PREFIX'].$sub_src.$dialectTo['DIV_TAG_MULTI_MODIFIERS_SUFFIX'].substr($src, $end + $suffix_len);
                        $p = $ini + 1; // IMPORTANT!
                    }
                    break;
                case 'date_format' :
                    $prefix_len = strlen($dialectFrom['DIV_TAG_DATE_FORMAT_PREFIX']);
                    $suffix_len = strlen($dialectFrom['DIV_TAG_DATE_FORMAT_SUFFIX']);
                    $separator_len = strlen($dialectFrom['DIV_TAG_DATE_FORMAT_SEPARATOR']);
                    $p = 0;
                    while (true) {
                        $r = $this->getRanges($dialectFrom['DIV_TAG_DATE_FORMAT_PREFIX'], $dialectFrom['DIV_TAG_DATE_FORMAT_SUFFIX'], $src, true, $p);
                        if (count($r) < 1) {
                            break;
                        }

                        list($ini, $end) = $r[0];
                        $sub_src = substr($src, $ini + $prefix_len, $end - $ini - $prefix_len);

                        $po = strpos($sub_src, $dialectFrom['DIV_TAG_DATE_FORMAT_SEPARATOR']);
                        if ($po !== false) {
                            $sub_src = str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], substr($sub_src, 0, $po)).$dialectTo['DIV_TAG_DATE_FORMAT_SEPARATOR'].substr($sub_src, $po + $separator_len);
                        }

                        $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_DATE_FORMAT_PREFIX'].$sub_src.$dialectTo['DIV_TAG_DATE_FORMAT_SUFFIX'].substr($src, $end + $suffix_len);
                        $p = $ini + 1; // IMPORTANT!
                    }
                    break;
                case 'number_format' :
                    $prefix_len = strlen($dialectFrom['DIV_TAG_NUMBER_FORMAT_PREFIX']);
                    $suffix_len = strlen($dialectFrom['DIV_TAG_NUMBER_FORMAT_SUFFIX']);
                    $separator_len = strlen($dialectFrom['DIV_TAG_NUMBER_FORMAT_SEPARATOR']);
                    $p = 0;
                    while (true) {
                        $r = $this->getRanges($dialectFrom['DIV_TAG_NUMBER_FORMAT_PREFIX'], $dialectFrom['DIV_TAG_NUMBER_FORMAT_SUFFIX'], $src, true, $p);
                        if (count($r) < 1) {
                            break;
                        }

                        $ini = $r [0] [0];
                        $end = $r [0] [1];
                        $sub_src = substr($src, $ini + $prefix_len, $end - $ini - $prefix_len);

                        $po = strpos($sub_src, $dialectFrom['DIV_TAG_NUMBER_FORMAT_SEPARATOR']);
                        if ($po !== false) {
                            $sub_src = str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], substr($sub_src, 0, $po)).$dialectTo['DIV_TAG_NUMBER_FORMAT_SEPARATOR'].substr($sub_src, $po + $separator_len);
                        }

                        $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_NUMBER_FORMAT_PREFIX'].$sub_src.$dialectTo['DIV_TAG_NUMBER_FORMAT_SUFFIX'].substr($src, $end + $suffix_len);
                        $p = $ini + 1; // IMPORTANT!
                    }

                    break;
                case 'formulas' :
                    $src = $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_FORMULA_BEGIN'], $dialectFrom['DIV_TAG_FORMULA_END'], $dialectTo['DIV_TAG_FORMULA_BEGIN'], $dialectTo['DIV_TAG_FORMULA_END'],
                        $dialectFrom['DIV_TAG_FORMULA_FORMAT_SEPARATOR'],
                        $dialectTo['DIV_TAG_FORMULA_FORMAT_SEPARATOR'], false);
                    break;
                case 'sub_parsers' :

                    foreach (self::$__sub_parsers as $sub_parser => $function) {
                        $src = str_replace($dialectFrom['DIV_TAG_SUBPARSER_BEGIN_PREFIX'].$sub_parser.$dialectFrom['DIV_TAG_SUBPARSER_BEGIN_SUFFIX'], $dialectTo['DIV_TAG_SUBPARSER_BEGIN_PREFIX'].$sub_parser.$dialectTo['DIV_TAG_SUBPARSER_BEGIN_SUFFIX'],
                            $src);
                        $src = str_replace($dialectFrom['DIV_TAG_SUBPARSER_END_PREFIX'].$sub_parser.$dialectFrom['DIV_TAG_SUBPARSER_END_SUFFIX'], $dialectTo['DIV_TAG_SUBPARSER_END_PREFIX'].$sub_parser.$dialectTo['DIV_TAG_SUBPARSER_END_SUFFIX'], $src);
                    }

                    break;
                case 'ignore' :
                    $src = $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_IGNORE_BEGIN'], $dialectFrom['DIV_TAG_IGNORE_END'], $dialectTo['DIV_TAG_IGNORE_BEGIN'], $dialectTo['DIV_TAG_IGNORE_END']);
                    break;

                case 'comment' :
                    $src = $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_COMMENT_BEGIN'], $dialectFrom['DIV_TAG_COMMENT_END'], $dialectTo['DIV_TAG_COMMENT_BEGIN'], $dialectTo['DIV_TAG_COMMENT_END']);
                    break;

                case 'html2txt' :
                    $prefix_len = strlen($dialectFrom['DIV_TAG_TXT_BEGIN']);
                    $suffix_len = strlen($dialectFrom['DIV_TAG_TXT_END']);
                    $separator_len = strlen($dialectFrom['DIV_TAG_TXT_WIDTH_SEPARATOR']);
                    $p = 0;
                    while (true) {
                        $r = $this->getRanges($dialectFrom['DIV_TAG_TXT_BEGIN'], $dialectFrom['DIV_TAG_TXT_END'], $src, true, $p);
                        if (count($r) < 1) {
                            break;
                        }

                        $ini = $r [0] [0];
                        $end = $r [0] [1];

                        $sub_src = substr($src, $ini + $prefix_len, $end - $ini - $prefix_len);

                        $po = strpos($sub_src, $dialectFrom['DIV_TAG_TXT_WIDTH_SEPARATOR']);
                        if ($po !== false) {
                            $sub_src = substr($sub_src, 0, $po).$dialectTo['DIV_TAG_TXT_WIDTH_SEPARATOR'].substr($sub_src, $po + $separator_len);
                        }

                        $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_TXT_BEGIN'].$sub_src.$dialectTo['DIV_TAG_TXT_END'].substr($src, $end + $suffix_len);

                        $p = $ini + 1;
                    }
                    break;
                case 'strip' :
                    $src = $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_STRIP_BEGIN'], $dialectFrom['DIV_TAG_STRIP_END'], $dialectTo['DIV_TAG_STRIP_BEGIN'], $dialectTo['DIV_TAG_STRIP_END']);
                    break;

                case 'loops' :
                    $separator_len = strlen($dialectFrom['DIV_TAG_LOOP_VAR_SEPARATOR']);
                    $p = 0;
                    while (true) {

                        $r = $this->getBlockRanges($src, $dialectFrom['DIV_TAG_LOOP_BEGIN_PREFIX'], $dialectFrom['DIV_TAG_LOOP_BEGIN_SUFFIX'], $dialectFrom['DIV_TAG_LOOP_END_PREFIX'], $dialectFrom['DIV_TAG_LOOP_END_SUFFIX'], $p, null, true,
                            $dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER']);

                        if (count($r) < 1) {
                            break;
                        }

                        $ini = $r [0] [0];
                        $end = $r [0] [1];
                        $key = $r [0] [2];
                        $sub_src = $r [0] [3];
                        $prefix = $dialectFrom['DIV_TAG_LOOP_BEGIN_PREFIX'].$key.$dialectFrom['DIV_TAG_LOOP_BEGIN_SUFFIX'];
                        $suffix = $dialectFrom['DIV_TAG_LOOP_END_PREFIX'].$key.$dialectFrom['DIV_TAG_LOOP_END_SUFFIX'];
                        $suffix_len = strlen($suffix);

                        $po = strpos($sub_src, $dialectFrom['DIV_TAG_LOOP_VAR_SEPARATOR']);
                        if ($po !== false) {
                            $sub_src = substr($sub_src, 0, $po).$dialectTo['DIV_TAG_LOOP_VAR_SEPARATOR'].substr($sub_src, $po + $separator_len);
                        }

                        $sub_src = str_replace([
                            $dialectFrom['DIV_TAG_EMPTY'],
                            $dialectFrom['DIV_TAG_BREAK'],
                        ], [
                            $dialectTo['DIV_TAG_EMPTY'],
                            $dialectTo['DIV_TAG_BREAK'],
                        ], $sub_src);

                        $key = str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], $key);
                        $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_LOOP_BEGIN_PREFIX'].$key.$dialectTo['DIV_TAG_LOOP_BEGIN_SUFFIX'].$sub_src.$dialectTo['DIV_TAG_LOOP_END_PREFIX'].$key.$dialectTo['DIV_TAG_LOOP_END_SUFFIX'].substr($src,
                                $end + $suffix_len);

                        $p = $ini + 1;
                    }
                    break;
                case 'iterations' :
                    $prefix_len = strlen($dialectFrom['DIV_TAG_ITERATION_BEGIN_PREFIX']);
                    $suffix_len = strlen($dialectFrom['DIV_TAG_ITERATION_BEGIN_SUFFIX']);
                    $end_tag_len = strlen($dialectFrom['DIV_TAG_ITERATION_END']);
                    $separator_len = strlen($dialectFrom['DIV_TAG_LOOP_VAR_SEPARATOR']);
                    $p = 0;

                    while (true) {
                        $ranges = $this->getRanges($dialectFrom['DIV_TAG_ITERATION_BEGIN_PREFIX'], $dialectFrom['DIV_TAG_ITERATION_END'], $src, true, $p);
                        if (count($ranges) < 1) {
                            break;
                        }

                        $ini = $ranges [0] [0];
                        $end = $ranges [0] [1];
                        $p1 = strpos($src, $dialectFrom['DIV_TAG_ITERATION_BEGIN_SUFFIX'], $ini + 1);

                        $s = substr($src, $ini + $prefix_len, $p1 - ($ini + $prefix_len));

                        $parts = explode($dialectFrom['DIV_TAG_ITERATION_PARAM_SEPARATOR'], $s);

                        $s = implode($dialectTo['DIV_TAG_ITERATION_PARAM_SEPARATOR'], $parts);

                        $sub_src = substr($src, $p1 + $suffix_len, $end - ($p1 + $suffix_len));

                        $po = strpos($sub_src, $dialectFrom['DIV_TAG_LOOP_VAR_SEPARATOR']);
                        if ($po !== false) {
                            $sub_src = substr($sub_src, 0, $po).$dialectTo['DIV_TAG_LOOP_VAR_SEPARATOR'].substr($sub_src, $po + $separator_len);
                        }

                        $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_ITERATION_BEGIN_PREFIX'].$s.$dialectTo['DIV_TAG_ITERATION_BEGIN_SUFFIX'].$sub_src.$dialectTo['DIV_TAG_ITERATION_END'].substr($src, $end + $end_tag_len);

                        $p = $ini + 1;
                    }
                    break;
                case 'conditionals' :

                    // true

                    $p = 0;
                    while (true) {

                        $r = $this->getBlockRanges($src, $dialectFrom['DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX'], $dialectFrom['DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX'], $dialectFrom['DIV_TAG_CONDITIONAL_TRUE_END_PREFIX'],
                            $dialectFrom['DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX'], $p, null, true, $dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER']);

                        if (count($r) < 1) {
                            break;
                        }

                        $ini = $r[0][0];
                        $end = $r[0][1];
                        $key = $r[0][2];
                        $sub_src = $r[0][3];
                        $suffix = $dialectFrom['DIV_TAG_CONDITIONAL_TRUE_END_PREFIX'].$key.$dialectFrom['DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX'];
                        $suffix_len = strlen($suffix);
                        $sub_src = str_replace($dialectFrom['DIV_TAG_ELSE'], $dialectTo['DIV_TAG_ELSE'], $sub_src);
                        $key = str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], $key);
                        $src =
                            substr($src, 0, $ini).$dialectTo['DIV_TAG_CONDITIONAL_TRUE_BEGIN_PREFIX'].str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], $key)
                            .$dialectTo['DIV_TAG_CONDITIONAL_TRUE_BEGIN_SUFFIX']
                            .$sub_src.$dialectTo['DIV_TAG_CONDITIONAL_TRUE_END_PREFIX'].$key.$dialectTo['DIV_TAG_CONDITIONAL_TRUE_END_SUFFIX'].substr($src, $end + $suffix_len);
                        $p = $ini + 1;
                    }

                    // false

                    $p = 0;
                    while (true) {

                        $r = $this->getBlockRanges($src, $dialectFrom['DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX'], $dialectFrom['DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX'], $dialectFrom['DIV_TAG_CONDITIONAL_FALSE_END_PREFIX'],
                            $dialectFrom['DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX'], $p, null, true, $dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER']);

                        if (count($r) < 1) {
                            break;
                        }

                        $ini = $r[0][0];
                        $end = $r[0][1];
                        $key = $r[0][2];
                        $sub_src = $r[0][3];
                        $suffix = $dialectFrom['DIV_TAG_CONDITIONAL_FALSE_END_PREFIX'].$key.$dialectFrom['DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX'];
                        $suffix_len = strlen($suffix);
                        $sub_src = str_replace($dialectFrom['DIV_TAG_ELSE'], $dialectTo['DIV_TAG_ELSE'], $sub_src);
                        $key = str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], $key);
                        $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_CONDITIONAL_FALSE_BEGIN_PREFIX'].$key.$dialectTo['DIV_TAG_CONDITIONAL_FALSE_BEGIN_SUFFIX'].$sub_src.$dialectTo['DIV_TAG_CONDITIONAL_FALSE_END_PREFIX'].$key
                            .$dialectTo['DIV_TAG_CONDITIONAL_FALSE_END_SUFFIX'].substr($src, $end + $suffix_len);
                        $p = $ini + 1;
                    }
                    break;

                case 'conditions' :
                    $prefix_len = strlen($dialectFrom['DIV_TAG_CONDITIONS_BEGIN_PREFIX']);
                    $suffix_len = strlen($dialectFrom['DIV_TAG_CONDITIONS_BEGIN_SUFFIX']);
                    $end_tag_len = strlen($dialectFrom['DIV_TAG_CONDITIONS_END']);
                    $else_tag_len = strlen($dialectFrom['DIV_TAG_ELSE']);
                    $p = 0;
                    while (true) {
                        $r = $this->getRanges($dialectFrom['DIV_TAG_CONDITIONS_BEGIN_PREFIX'], $dialectFrom['DIV_TAG_CONDITIONS_END'], $src, true, $p);

                        if (count($r) < 1) {
                            break;
                        }

                        $ini = $r [0] [0];
                        $end = $r [0] [1];
                        $p1 = strpos($src, $dialectFrom['DIV_TAG_CONDITIONS_BEGIN_SUFFIX'], $ini + 1);
                        $s = substr($src, $ini + $prefix_len, $p1 - ($ini + $prefix_len));
                        $sub_src = substr($src, $p1 + $suffix_len, $end - ($p1 + $suffix_len));
                        $po = strpos($sub_src, $dialectFrom['DIV_TAG_ELSE']);
                        if ($po !== false) {
                            $sub_src = substr($sub_src, 0, $po).$dialectTo['DIV_TAG_ELSE'].substr($sub_src, $po + $else_tag_len);
                        }
                        $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_CONDITIONS_BEGIN_PREFIX'].$s.$dialectTo['DIV_TAG_CONDITIONS_BEGIN_SUFFIX'].$sub_src.$dialectTo['DIV_TAG_CONDITIONS_END'].substr($src, $end + $end_tag_len);
                        $p = $ini + 1;
                    }
                    break;

                case 'tpl_vars' :
                    $begin_tag_len = strlen($dialectFrom['DIV_TAG_TPLVAR_BEGIN']);
                    $end_tag_len = strlen($dialectFrom['DIV_TAG_TPLVAR_END']);
                    $operator_tag_len = strlen($dialectFrom['DIV_TAG_TPLVAR_ASSIGN_OPERATOR']);

                    $p = 0;
                    while (true) {
                        $r = $this->getRanges($dialectFrom['DIV_TAG_TPLVAR_BEGIN'], $dialectFrom['DIV_TAG_TPLVAR_END'], $src, true, $p);

                        if (count($r) < 1) {
                            break;
                        }

                        $ini = $r [0] [0];
                        $end = $r [0] [1];
                        $p1 = strpos($src, $dialectFrom['DIV_TAG_TPLVAR_ASSIGN_OPERATOR'], $ini + $begin_tag_len);

                        if ($p1 === false) {
                            $p = $ini + 1;
                            continue;
                        }

                        $tpl_var_value = substr($src, $p1 + $operator_tag_len, $end - ($p1 + $operator_tag_len));

                        $missing_vars = [];
                        $this->jsonDecode($tpl_var_value, [], $missing_vars);

                        foreach ($missing_vars as $missing) {
                            $tpl_var_value = str_replace('$'.$missing, '$'.str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], $missing), $tpl_var_value);
                        }

                        $src = substr($src, 0, $ini).$dialectTo['DIV_TAG_TPLVAR_BEGIN'].str_replace($dialectFrom['DIV_TAG_TPLVAR_PROTECTOR'], $dialectTo['DIV_TAG_TPLVAR_PROTECTOR'],
                                str_replace($dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER'], substr($src, $ini + $begin_tag_len, $p1 - ($ini + $begin_tag_len)))).$dialectTo['DIV_TAG_TPLVAR_ASSIGN_OPERATOR']
                            .$tpl_var_value
                            .$dialectTo['DIV_TAG_TPLVAR_END'].substr($src, $end + $end_tag_len);

                        $p = $ini + 1;
                    }
                    break;

                case 'default_replace' :
                    $src =
                        $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_DEFAULT_REPLACEMENT_BEGIN'], $dialectFrom['DIV_TAG_DEFAULT_REPLACEMENT_END'], $dialectTo['DIV_TAG_DEFAULT_REPLACEMENT_BEGIN'], $dialectTo['DIV_TAG_DEFAULT_REPLACEMENT_END']);
                    break;

                case 'include' :
                    $src = $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_INCLUDE_BEGIN'], $dialectFrom['DIV_TAG_INCLUDE_END'], $dialectTo['DIV_TAG_INCLUDE_BEGIN'], $dialectTo['DIV_TAG_INCLUDE_END']);
                    break;

                case 'preprocessed' :
                    $src = $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_PREPROCESSED_BEGIN'], $dialectFrom['DIV_TAG_PREPROCESSED_END'], $dialectTo['DIV_TAG_PREPROCESSED_BEGIN'], $dialectTo['DIV_TAG_PREPROCESSED_END'],
                        $dialectTo['DIV_TAG_PREPROCESSED_SEPARATOR']);
                    break;

                case 'capsules' :
                    $src = $this->translateKeyBlocks($src, $dialectFrom['DIV_TAG_CAPSULE_BEGIN_PREFIX'], $dialectFrom['DIV_TAG_CAPSULE_BEGIN_SUFFIX'], $dialectFrom['DIV_TAG_CAPSULE_END_PREFIX'], $dialectFrom['DIV_TAG_CAPSULE_END_SUFFIX'],
                        $dialectTo['DIV_TAG_CAPSULE_BEGIN_PREFIX'], $dialectTo['DIV_TAG_CAPSULE_BEGIN_SUFFIX'], $dialectTo['DIV_TAG_CAPSULE_END_PREFIX'], $dialectTo['DIV_TAG_CAPSULE_END_SUFFIX'], $dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'],
                        $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER']);
                    break;

                case 'multi_replace' :
                    $src = $this->translateKeyBlocks($src, $dialectFrom['DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX'], $dialectFrom['DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX'], $dialectFrom['DIV_TAG_MULTI_REPLACEMENT_END_PREFIX'],
                        $dialectFrom['DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX'], $dialectTo['DIV_TAG_MULTI_REPLACEMENT_BEGIN_PREFIX'], $dialectTo['DIV_TAG_MULTI_REPLACEMENT_BEGIN_SUFFIX'], $dialectTo['DIV_TAG_MULTI_REPLACEMENT_END_PREFIX'],
                        $dialectTo['DIV_TAG_MULTI_REPLACEMENT_END_SUFFIX'], $dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER']);
                    break;

                case 'friendly_tags' :
                    $src = $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_FRIENDLY_BEGIN'], $dialectFrom['DIV_TAG_FRIENDLY_END'], $dialectTo['DIV_TAG_FRIENDLY_BEGIN'], $dialectTo['DIV_TAG_FRIENDLY_END']);
                    break;

                case 'macros' :
                    $src = $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_MACRO_BEGIN'], $dialectFrom['DIV_TAG_MACRO_END'], $dialectTo['DIV_TAG_MACRO_BEGIN'], $dialectTo['DIV_TAG_MACRO_END']);
                    break;

                case 'location' :
                    $src = $this->translateSimpleBlocks($src, $dialectFrom['DIV_TAG_LOCATION_BEGIN'], $dialectFrom['DIV_TAG_LOCATION_END'], $dialectTo['DIV_TAG_LOCATION_BEGIN'], $dialectTo['DIV_TAG_LOCATION_END']);
                    break;

                case 'location_content' :
                    $src = $this->translateKeyBlocks($src, $dialectFrom['DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX'], $dialectFrom['DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX'], $dialectFrom['DIV_TAG_LOCATION_CONTENT_END_PREFIX'],
                        $dialectFrom['DIV_TAG_LOCATION_CONTENT_END_SUFFIX'], $dialectTo['DIV_TAG_LOCATION_CONTENT_BEGIN_PREFIX'], $dialectTo['DIV_TAG_LOCATION_CONTENT_BEGIN_SUFFIX'], $dialectTo['DIV_TAG_LOCATION_CONTENT_END_PREFIX'],
                        $dialectTo['DIV_TAG_LOCATION_CONTENT_END_SUFFIX'], $dialectFrom['DIV_TAG_VAR_MEMBER_DELIMITER'], $dialectTo['DIV_TAG_VAR_MEMBER_DELIMITER']);
                    break;
            }
        }

        $order = [
            $dialectTo['DIV_TAG_SPECIAL_REPLACE_NEW_LINE']       => $dialectFrom['DIV_TAG_SPECIAL_REPLACE_NEW_LINE'],
            $dialectTo['DIV_TAG_SPECIAL_REPLACE_CAR_RETURN']     => $dialectFrom['DIV_TAG_SPECIAL_REPLACE_CAR_RETURN'],
            $dialectTo['DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB'] => $dialectFrom['DIV_TAG_SPECIAL_REPLACE_HORIZONTAL_TAB'],
            $dialectTo['DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB']   => $dialectFrom['DIV_TAG_SPECIAL_REPLACE_VERTICAL_TAB'],
            $dialectTo['DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE']      => $dialectFrom['DIV_TAG_SPECIAL_REPLACE_NEXT_PAGE'],
            $dialectTo['DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL']  => $dialectFrom['DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL'],
            $dialectTo['DIV_TAG_SPECIAL_REPLACE_SPACE']          => $dialectFrom['DIV_TAG_SPECIAL_REPLACE_SPACE'],
            $dialectTo['DIV_TAG_TEASER_BREAK']                   => $dialectFrom['DIV_TAG_TEASER_BREAK'],
        ];
        asort($order);
        $src = str_replace($order, array_keys($order), $src);

        if ($update) {
            $this->__src = $src;
        }

        return $src;
    }

    /**
     * Translate dialects from some dialect to current dialect
     *
     * @param mixed  $dialectFrom
     * @param string $src
     * @param mixed  $items
     *
     * @return string
     */
    final public function translateFrom($dialectFrom, $src = null, $items = null)
    {
        if (self::$__log_mode === true) {
            $this->logger('Translating from some dialect to current dialect...');
        }

        return $this->translate($dialectFrom, [], $src, $items);
    }

    /**
     * Translate dialects to some dialect from current dialect
     *
     * @param mixed  $dialectTo
     * @param string $src
     * @param mixed  $items
     *
     * @return string
     */
    final public function translateTo($dialectTo, $src = null, $items = null)
    {
        if (self::$__log_mode === true) {
            $this->logger('Translating from current dialect to some dialect...');
        }

        return $this->translate([], $dialectTo, $src, $items);
    }

    /**
     * Convert div to string | Return the parsed template
     *
     * @return string
     */
    final public function __toString()
    {
        // __toString can not throw an exception !!!
        try {
            $this->parse();
        } catch (Exception $e) {
            $this->logger('PARSE EXCEPTION: '.$e->getFile().':'.$e->getLine().': '.$e->getMessage());
        }

        return $this->__src.'';
    }

    // ------------------------ PREDEFINED SUBPARSERS ---------------------------- //

    /**
     * Parse this
     *
     * @param string $src
     * @param mixed  $items
     *
     * @return string
     */
    private function subParse_parse($src, $items = null)
    {
        if ($items === null) {
            $items = $this->__items;
        }

        $tpl = self::getAuxiliaryEngineClone($items, $items, $this);
        $tpl->__src = $src;
        $tpl->__src_original = $src;
        $tpl->parse();

        return $tpl->__src;
    }

    /**
     * Convert all chars to HTML entities/codes.
     *
     * @param string $src
     *
     * @return string
     */
    private function subParse_html_wysiwyg($src)
    {
        $l = strlen($src);
        $new_code = '';
        for ($i = 0; $i < $l; $i++) {
            if ($src [$i] !== "\n" && $src [$i] !== "\t" && $src [$i] !== "\r") {
                $new_code .= '&#'.ord($src [$i]).';';
            } else {
                $new_code .= $src [$i];
            }
        }

        return $new_code;
    }

    /**
     * Implode array
     *
     * @param string $src
     * @param mixed  $items
     *
     * @return string
     */
    private function subParse_join($src, $items)
    {
        $arr = explode('|', $src);
        $var_name = trim($arr[0]);
        $delimiter = '';
        if (isset($arr[1])) {
            $delimiter = $arr[1];
        }
        $collection = self::getVarValue($var_name, $items);

        return implode($delimiter, $collection);
    }

    // -------------------------------- HOOKS ------------------------------------- //

    /**
     * The hooks
     *
     * @param string $src
     * @param mixed  $items
     */
    public function beforeBuild(&$src = null, &$items = null)
    {
    }

    public function afterBuild()
    {
    }

    public function beforeParse()
    {
    }

    public function afterParse()
    {
    }

    /**
     * Output the parsed template
     *
     * @param string $template
     */
    public function show($template = null)
    {
        if ($template !== null) {
            $this->changeTemplate($template);
        }

        $this->parse();
        echo $this->__src;
    }

    // -------------------------------- Functions ------------------------------------- //

    /**
     * Parse a template
     *
     * @param string  $src
     * @param string  $items
     * @param array   $ignore
     * @param integer $min_level
     * @param boolean $discard_file_system
     *
     * @return string
     */
    final public static function div($src = null, $items = null, $ignore = [], $min_level = 1, $discard_file_system = false)
    {
        $class = get_class();

        // some time $src is not a template, then discard file system can avoid infinite loops

        $discard_fs = self::$__discard_file_system; // save current value
        self::$__discard_file_system = $discard_file_system;

        $engine = new $class($src, $items, $ignore);
        $engine->parse(false, null, $min_level);

        self::$__discard_file_system = $discard_fs; // restore original value

        return $engine->__src;
    }

    /**
     * Enable documentation
     */
    final public static function docsOn()
    {
        self::$__docs_on = true;
    }

    /**
     * Disable documentation
     */
    final public static function docsOff()
    {
        self::$__docs_on = false;
    }

    /**
     * Disable documentation
     */
    final public static function docsReset()
    {
        self::$__docs = [];
    }

    /**
     * Get documentation's data
     *
     * @return array
     */
    final public static function getDocs()
    {
        return self::$__docs;
    }

    /**
     * Get a redeable documentation
     *
     * @param string $tpl
     * @param mixed  $items
     *
     * @return string
     */
    final public static function getDocsReadable($tpl = null, $items = null)
    {
        $docs = self::$__docs;
        $keys = array_keys($docs);

        asort($keys);
        $docs_x = [];

        foreach ($keys as $key) {
            $docs_x[$key] = $docs[$key];
        }

        if ($items === null) {
            $items = [
                'title' => "Templates's documentation",
            ];
        } elseif (is_object($items)) {
            $items = get_object_vars($items);
        }

        $items = array_merge($items, ['docs' => $docs_x]);

        if ($tpl === null) {
            $tpl = DIV_TEMPLATE_FOR_DOCS;
        }

        $obj = self::getAuxiliaryEngineClone($items);
        $obj->__src = $tpl;
        $obj->parse(false);

        return $obj->__src;
    }

    /**
     * Clear all empty/null values and return a compact mixed
     *
     * @param mixed $mixed
     *
     * @return mixed
     */
    final public static function compact($mixed)
    {
        if (empty ($mixed)) {
            return null;
        }
        if ($mixed === null) {
            return null;
        }
        if (is_scalar($mixed) && (string)$mixed === '') {
            return null;
        }

        if (is_object($mixed)) {
            $vars = get_object_vars($mixed);
            foreach ($vars as $var => $value) {
                $value = self::compact($value);
                if ($value === null) {
                    unset ($mixed->$var);
                }
            }
            $vars = get_object_vars($mixed);
            if (count($vars) < 1) {
                return null;
            }
        }

        if (is_array($mixed)) {
            $arr = [];
            foreach ($mixed as $var => $value) {
                $value = self::compact($value);
                if ($value !== null) {
                    $arr [$var] = $value;
                }
            }
            if (count($arr) < 1) {
                return null;
            }
            $mixed = $arr;
        }

        return $mixed;
    }

    /**
     * Convert any value to string (with Div method)
     *
     * @param mixed $value
     *
     * @return string
     */
    final public static function anyToStr($value)
    {
        if (self::isString($value)) {
            return (string)$value;
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_numeric($value)) {
            return (string)$value;
        }
        if (is_object($value)) {
            return ''.count(get_object_vars($value));
        }
        if (is_array($value)) {
            return ''.count($value);
        }
        if ($value === null) {
            return '';
        }

        return (string)$value;
    }

    /**
     * Complete object/array properties
     *
     * @param mixed   $source
     * @param mixed   $complement
     * @param integer $level
     *
     * @return mixed
     */
    final public static function cop(&$source, $complement, $level = 0)
    {
        $null = null;

        if (is_null($source)) {
            return $complement;
        }

        if (is_null($complement)) {
            return $source;
        }

        if (is_scalar($source) && is_scalar($complement)) {
            return $complement;
        }

        if (is_scalar($complement) || is_scalar($source)) {
            return $source;
        }

        if ($level < 100) { // prevent infinite loop
            if (is_object($complement)) {
                $complement = get_object_vars($complement);
            }

            foreach ($complement as $key => $value) {
                if (is_object($source)) {
                    if (property_exists($source, $key)) {
                        $source->$key = self::cop($source->$key, $value, $level + 1);
                    } else {
                        $source->$key = self::cop($null, $value, $level + 1);
                    }
                }
                if (is_array($source)) {
                    if (array_key_exists($key, $source)) {
                        $source [$key] = self::cop($source[$key], $value, $level + 1);
                    } else {
                        $source[$key] = self::cop($null, $value, $level + 1);
                    }
                }
            }
        }

        return $source;
    }

    /**
     * Safe substring
     *
     * @param string  $string
     * @param integer $max_length
     *
     * @return string
     */
    final public static function substr($string, $max_length)
    {
        $max_length = max($max_length, 0);
        if (strlen($string) <= $max_length) {
            return $string;
        }
        $string = substr($string, 0, $max_length);

        return $string;
    }

    /**
     * Return the teaser of a text
     *
     * @param string  $text
     * @param integer $max_length
     *
     * @return string
     */
    final public static function teaser($text, $max_length = 600)
    {
        $delimiter = strpos($text, DIV_TAG_TEASER_BREAK);
        if ($max_length === 0 && $delimiter === false) {
            return $text;
        }
        if ($delimiter !== false) {
            return substr($text, 0, $delimiter);
        }
        if (strlen($text) <= $max_length) {
            return $text;
        }

        $summary = self::substr($text, $max_length);

        $max_reversed_pos = strlen($summary);
        $min_reversed_pos = $max_reversed_pos;
        $reversed = strrev($summary);
        $break_points = [];
        $break_points [] = [
            '</p>' => 0,
        ];
        $line_breaks = [
            '<br />' => 6,
            '<br>'   => 4,
        ];

        //if(isset($filters ['filter_autop'])) $line_breaks ["\n"] = 1;
        $break_points [] = $line_breaks;
        $break_points [] = [
            '. ' => 1,
            '! ' => 1,
            '? ' => 1,
            '?'  => 0,
            '? ' => 1,
        ];

        foreach ($break_points as $points) {
            foreach ($points as $point => $offset) {
                $reversed_pos = strpos($reversed, strrev($point));
                if ($reversed_pos !== false) {
                    $min_reversed_pos = min($reversed_pos + $offset, $min_reversed_pos);
                }
            }
            if ($min_reversed_pos !== $max_reversed_pos) {
                $summary = ($min_reversed_pos === 0) ? $summary : substr($summary, 0, 0 - $min_reversed_pos);
                break;
            }
        }

        return $summary;
    }

    /**
     * UTF utility
     *
     * @param string $utf16
     *
     * @return string
     */
    final public static function utf162utf8($utf16)
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
        }
        $bytes = (ord($utf16[0]) << 8) | ord($utf16[1]);

        if ((0x7F & $bytes) === $bytes) {
            return chr(0x7F & $bytes);
        }
        if ((0x07FF & $bytes) === $bytes) {
            return chr(0xC0 | (($bytes >> 6) & 0x1F)).chr(0x80 | ($bytes & 0x3F));
        }
        if ((0xFFFF & $bytes) === $bytes) {
            return chr(0xE0 | (($bytes >> 12) & 0x0F)).chr(0x80 | (($bytes >> 6) & 0x3F)).chr(0x80 | ($bytes & 0x3F));
        }

        return '';
    }

    /**
     * JSON Decode
     *
     * @param string $str
     * @param mixed  $items
     * @param mixed  $missing_vars
     *
     * @return mixed
     */
    final public static function jsonDecode($str, $items = [], &$missing_vars = [])
    {
        $str = trim(preg_replace([
            '#^\s*//(.+)$#m',
            '#^\s*/\*(.+)\*/#Us',
            '#/\*(.+)\*/\s*$#Us',
        ], '', $str));

        // Syntax specific for div
        if (isset($str [0]) && strpos($str, '$') === 0) {
            $str = substr($str, 1);

            $r = null;

            if (self::issetVar($str, $items)) {
                $r = self::getVarValue($str, $items);
            } else {
                $missing_vars [] = $str;
            }

            return $r;
        }

        switch (strtolower($str)) {
            case 'true' :
                return true;
            case 'false' :
                return false;
            case 'null' :
                return null;
            default :
                $m = [];

                if (is_numeric($str)) {
                    return (( float )$str === ( integer )$str) ? ( integer )$str : ( float )$str;
                }

                if (preg_match('/^("|\').*(\1)$/s', $str, $m) && $m [1] === $m [2]) {
                    $delimiter = substr($str, 0, 1);
                    $chars = substr($str, 1, -1);
                    $utf8 = '';
                    $str_len_chars = strlen($chars);

                    for ($c = 0; $c < $str_len_chars; ++$c) {

                        $sub_str_chars_c_2 = substr($chars, $c, 2);
                        $ord_chars_c = ord($chars[$c]);

                        switch (true) {
                            case $sub_str_chars_c_2 === '\b' :
                                $utf8 .= chr(0x08);
                                ++$c;
                                break;
                            case $sub_str_chars_c_2 === '\t' :
                                $utf8 .= chr(0x09);
                                ++$c;
                                break;
                            case $sub_str_chars_c_2 === '\n' :
                                $utf8 .= chr(0x0A);
                                ++$c;
                                break;
                            case $sub_str_chars_c_2 === '\f' :
                                $utf8 .= chr(0x0C);
                                ++$c;
                                break;
                            case $sub_str_chars_c_2 === '\r' :
                                $utf8 .= chr(0x0D);
                                ++$c;
                                break;
                            case $sub_str_chars_c_2 === '\\"' :
                            case $sub_str_chars_c_2 === '\\\'' :
                            case $sub_str_chars_c_2 === '\\\\' :
                            case $sub_str_chars_c_2 === '\\/' :
                                if (($delimiter === '"' && $sub_str_chars_c_2 !== '\\\'') || ($delimiter === "'" && $sub_str_chars_c_2 !== '\\"')) {
                                    $utf8 .= $chars[++$c];
                                }
                                break;
                            case preg_match('/\\\u[0-9A-F]{4}/i', substr($chars, $c, 6)) :
                                $utf16 = chr(hexdec(substr($chars, $c + 2, 2))).chr(hexdec(substr($chars, $c + 4, 2)));
                                $utf8 .= self::utf162utf8($utf16);
                                $c += 5;
                                break;
                            case ($ord_chars_c >= 0x20) && ($ord_chars_c <= 0x7F) :
                                $utf8 .= $chars[$c];
                                break;
                            case ($ord_chars_c & 0xE0) === 0xC0 :
                                $utf8 .= substr($chars, $c, 2);
                                ++$c;
                                break;
                            case ($ord_chars_c & 0xF0) === 0xE0 :
                                $utf8 .= substr($chars, $c, 3);
                                $c += 2;
                                break;
                            case ($ord_chars_c & 0xF8) === 0xF0 :
                                $utf8 .= substr($chars, $c, 4);
                                $c += 3;
                                break;
                            case ($ord_chars_c & 0xFC) === 0xF8 :
                                $utf8 .= substr($chars, $c, 5);
                                $c += 4;
                                break;
                            case ($ord_chars_c & 0xFE) === 0xFC :
                                $utf8 .= substr($chars, $c, 6);
                                $c += 5;
                                break;
                        }
                    }

                    return $utf8;
                } elseif (preg_match('/^\[.*\]$/s', $str) || preg_match('/^\{.*\}$/s', $str)) {
                    if (strpos($str, '[') === 0) {
                        $stk = [
                            3,
                        ];
                        $arr = [];
                    } elseif (true & 16) {
                        $stk = [
                            4,
                        ];
                        $obj = [];
                    } else {
                        $stk = [
                            4,
                        ];
                        $obj = new stdClass ();
                    }

                    $stk[] = [
                        'what'      => 1,
                        'where'     => 0,
                        'delimiter' => false,
                    ];

                    $chars = substr($str, 1, -1);
                    $chars = trim(preg_replace([
                        '#^\s*//(.+)$#m',
                        '#^\s*/\*(.+)\*/#Us',
                        '#/\*(.+)\*/\s*$#Us',
                    ], '', $chars));

                    if ($chars === '') {
                        if (reset($stk) === 3) {
                            return $arr;
                        }

                        return $obj;
                    }

                    $str_len_chars = strlen($chars);

                    for ($c = 0; $c <= $str_len_chars; ++$c) {
                        $top = end($stk);
                        $sub_str_chars_c_2 = substr($chars, $c, 2);

                        if (($c === $str_len_chars) || (($chars[$c] === ',') && ($top ['what'] === 1))) {
                            $slice = substr($chars, $top ['where'], $c - $top ['where']);
                            $stk[] = [
                                'what'      => 1,
                                'where'     => $c + 1,
                                'delimiter' => false,
                            ];

                            if (reset($stk) === 3) {
                                $arr[] = self::jsonDecode($slice, $items);
                            } elseif (reset($stk) === 4) {
                                $parts = [];
                                if (preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                    $key = self::jsonDecode($parts [1], $items);
                                    $val = self::jsonDecode($parts [2], $items);

                                    if (true & 16) {
                                        $obj [$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                } elseif (preg_match('/^\s*(\w+)\s*:\s*(\S.*),?$/Uis', $slice, $parts)) {
                                    $key = $parts [1];
                                    $val = self::jsonDecode($parts [2], $items);

                                    if (true & 16) {
                                        $obj [$key] = $val;
                                    } else {
                                        $obj->$key = $val;
                                    }
                                }
                            }
                        } elseif ((($chars[$c] === '"') || ($chars[$c] === "'")) && ($top ['what'] !== 2)) {
                            $stk[] = [
                                'what'      => 2,
                                'where'     => $c,
                                'delimiter' => $chars[$c],
                            ];
                        } elseif (($chars[$c] === $top ['delimiter']) && ($top ['what'] === 2) && ((strlen(substr($chars, 0, $c)) - strlen(rtrim(substr($chars, 0, $c), '\\'))) % 2 !== 1)) {
                            array_pop($stk);
                        } elseif (($chars[$c] === '[')
                            && in_array($top ['what'], [
                                1,
                                3,
                                4,
                            ], true)) {
                            $stk[] = [
                                'what'      => 3,
                                'where'     => $c,
                                'delimiter' => false,
                            ];
                        } elseif (($chars[$c] === ']') && ($top ['what'] === 3)) {
                            array_pop($stk);
                        } elseif (($chars[$c] === '{')
                            && in_array($top ['what'], [1, 3, 4], true)) {
                            $stk[] = [
                                'what'      => 4,
                                'where'     => $c,
                                'delimiter' => false,
                            ];
                        } elseif (($chars[$c] === '}') && ($top ['what'] === 4)) {
                            array_pop($stk);
                        } elseif (($sub_str_chars_c_2 === '/*')
                            && in_array($top ['what'], [1, 3, 4], true)) {
                            $stk[] = [
                                'what'      => 5,
                                'where'     => $c,
                                'delimiter' => false,
                            ];
                            $c++;
                        } elseif (($sub_str_chars_c_2 === '*/') && ($top ['what'] === 5)) {
                            array_pop($stk);
                            $c++;
                            for ($i = $top ['where']; $i <= $c; ++$i) {
                                $chars = substr_replace($chars, ' ', $i, 1);
                            }
                        }
                    }

                    if (reset($stk) === 3) {
                        return $arr;
                    }

                    if (reset($stk) === 4) {
                        return $obj;
                    }
                }
        }
    }

    /**
     * JSON Encode
     *
     * @param mixed $data
     *
     * @return string
     */
    final public static function jsonEncode($data)
    {
        if (is_array($data) || is_object($data)) {
            $is_list = is_array($data) && (empty ($data) || array_keys($data) === range(0, count($data) - 1));

            if ($is_list) {
                $json = '['.implode(',', array_map('divengine\div::jsonEncode', $data)).']';
            } else {
                $items = [];
                foreach ($data as $key => $value) {
                    $items[] = self::jsonEncode((string)$key).':'.self::jsonEncode($value);
                }
                $json = '{'.implode(',', $items).'}';
            }
        } elseif (self::isString($data)) {
            $string = '"'.addcslashes($data, "\\\"\n\r\t/".chr(8).chr(12)).'"';
            $json = '';
            $len = strlen($string);
            for ($i = 0; $i < $len; $i++) {
                $char = $string [$i];
                $c1 = ord($char);
                if ($c1 < 128) {
                    $json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
                    continue;
                }
                $c2 = ord($string [++$i]);
                if (($c1 & 32) === 0) {
                    $json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
                    continue;
                }
                $c3 = ord($string [++$i]);
                if (($c1 & 16) === 0) {
                    $json .= sprintf("\\u%04x", (($c1 - 224) << 12) + (($c2 - 128) << 6) + ($c3 - 128));
                    continue;
                }
                $c4 = ord($string [++$i]);
                if (($c1 & 8) === 0) {
                    $u = (($c1 & 15) << 2) + (($c2 >> 4) & 3) - 1;

                    $w1 = (54 << 10) + ($u << 6) + (($c2 & 15) << 2) + (($c3 >> 4) & 3);
                    $w2 = (55 << 10) + (($c3 & 15) << 6) + ($c4 - 128);
                    $json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
                }
            }
        } else {
            $json = strtolower(var_export($data, true));
        }

        return $json;
    }

    /**
     * Convert HTML to plain and formatted text
     *
     * @param string  $html
     * @param integer $width
     *
     * @return string
     */
    public static function htmlToText($html, $width = 50)
    {

        // Special strip tags
        $new_html = '';
        do {
            $p1 = strpos($html, '<style');
            $p2 = strpos($html, '</style>');

            if ($p1 !== false && $p2 !== false) {
                if ($p2 > $p1) {
                    $new_html .= substr($html, 0, $p1);
                    $new_html .= substr($html, $p2 + 8);
                    $html = substr($html, $p2 + 8);
                } else {
                    break;
                }
            }
        } while ($p1 !== false && $p2 !== false);

        if ($new_html !== '') {
            $html = $new_html;
        }

        // Other stuffs

        $html = str_replace('<br>', "\n", $html);
        $html = str_replace('<br/>', "\n", $html);
        $html = str_replace('<br />', "\n", $html);
        $html = str_replace('</tr>', "\n", $html);
        $html = str_replace('<td', "\t</td", $html);
        $html = str_replace('<th', "\t</th", $html);
        $html = str_replace('</table>', "\n", $html);
        $hr = str_repeat('-', $width)."\n";
        $html = str_replace('<hr>', $hr, $html);
        $html = str_replace('<hr/>', $hr, $html);
        $html = str_replace('</p>', "\n", $html);
        $html = str_replace('<h1', '- <h1'.$hr, $html);
        $html = str_replace('<h2', '-- <h2'.$hr, $html);
        $html = str_replace('<h3', '--- <h3'.$hr, $html);
        $html = str_replace('<li', '* <li'.$hr, $html);

        for ($i = 1; $i < 5; $i++) {
            $html = str_replace("</h$i>\n", "</h$i>".$hr, $html);
            $html = str_replace("</h$i>", "</h$i>\n\n".$hr, $html);
        }

        $html = html_entity_decode($html);
        $html = preg_replace('!<[^>]*?>!', ' ', $html);
        $html = str_replace("\t", ' ', $html);

        // Strip tags
        $html = preg_replace("/\015\012|\015|\012/", "\n", $html);
        $html = strip_tags($html);

        while (strpos($html, '  ') !== false) {
            $html = str_replace('  ', ' ', $html);
        }
        $html = str_replace(' '."\n", "\n", $html);
        $html = str_replace("\n ", "\n", $html);

        while (strpos($html, '  ') !== false) {
            $html = str_replace('  ', ' ', $html);
        }
        $html = trim($html);
        if ($width !== null && $width !== 0) {
            $html = wordwrap($html, $width, "\n");
        }

        return $html;
    }

    /**
     * Return true if at least one needle is contained in the haystack
     *
     * @param string $haystack
     * @param array  $needles
     *
     * @return boolean
     */
    final public static function atLeastOneString($haystack, $needles = [])
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return the last key of array or null if not exists
     *
     * @param array $arr
     *
     * @return mixed
     */
    final public static function getLastKeyOfArray($arr)
    {
        if (is_array($arr) && count($arr) > 0) {
            $keys = array_keys($arr);
            $keys = array_reverse($keys);

            return $keys [0];
        }
    }

    /**
     * Return true if var exists in the template's items recursively
     *
     * @param string $var
     * @param mixed  $items
     *
     * @return boolean
     */
    final public static function varExists($var, &$items = null)
    {
        if ($items === null) {
            return false;
        }

        $sub_vars = explode(DIV_TAG_VAR_MEMBER_DELIMITER, $var);

        if (count($sub_vars) === 1) {
            if (is_array($items)) {
                return array_key_exists($var, $items);
            }
            if (is_object($items)) {
                return property_exists($items, $var);
            }
        } else {
            $temp_sub_var = $sub_vars[0];
            $l = strlen($temp_sub_var);
            if ($l + 1 < strlen($var)) {
                if (is_array($items)) {
                    return self::varExists(substr($var, $l + 1), $items[$temp_sub_var]);
                }
                if (is_object($items)) {
                    return self::varExists(substr($var, $l + 1), $items->$temp_sub_var);
                }
            }
        }

        return false;
    }

    /**
     * Return the first instance of $this->__packages
     */
    public static function getPackagesPath()
    {
        $class = get_class();
        if (isset(self::$__packages_by_class [$class])) {
            return self::$__packages_by_class [$class];
        }

        return PACKAGES;
    }

    /**
     * Secure 'file exists' method
     *
     * @param string $filename
     *
     * @return boolean
     */
    final public static function fileExists($filename)
    {
        if (strpos(strtolower($filename), 'http://') === 0 || strpos(strtolower($filename), 'https://') === 0 || strpos(strtolower($filename), 'ftp://') === 0) {
            return false;
        }

        if (strlen($filename) > DIV_MAX_FILENAME_SIZE) {
            return false;
        }

        if (file_exists($filename) && is_file($filename)) {
            return true;
        }

        $include_paths = self::getIncludePaths(self::getPackagesPath());

        foreach ($include_paths as $include_path) {
            $path = str_replace("\\", '/', $include_path.'/'.$filename);
            while (strpos($path, '//') !== false) {
                $path = str_replace('//', '/', $path);
            }
            $path = str_replace('/./', '/', $path);
            if (strpos($path, './') === 0) {
                $path = substr($path, 2);
            }
            if (@file_exists($path) && @is_file($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Secure 'is dir' method
     *
     * @param string $dir_name
     *
     * @return boolean
     */
    final public static function isDir($dir_name)
    {
        if (strpos(strtolower($dir_name), 'http://') === 0 || strpos(strtolower($dir_name), 'https://') === 0 || strpos(strtolower($dir_name), 'ftp://') === 0) {
            return false;
        }

        if (strlen($dir_name) > DIV_MAX_FILENAME_SIZE) {
            return false;
        }

        return is_dir($dir_name);
    }

    /**
     * Secure 'file get contents' method
     *
     * @param string $filename
     *
     * @return string
     */
    final public static function getFileContents($filename)
    {
        if (strpos(strtolower($filename), 'http://') === 0 || strpos(strtolower($filename), 'https://') === 0 || strpos(strtolower($filename), 'ftp://') === 0) {
            return $filename;
        }

        if (file_exists($filename)) {
            return file_get_contents($filename);
        }

        $include_paths = self::getIncludePaths(self::getPackagesPath());

        foreach ($include_paths as $include_path) {
            $path = str_replace("\\", '/', $include_path.'/'.$filename);
            while (strpos($path, '//') !== false) {
                $path = str_replace('//', '/', $path);
            }
            $path = str_replace('/./', '/', $path);
            if (strpos($path, './') === 0) {
                $path = substr($path, 2);
            }

            if (file_exists($path) && is_file($path)) {
                return file_get_contents($path);
            }
        }

        return null;
    }

    /**
     * Get folder of path/file
     *
     * @param string $filename
     *
     * @return string
     */
    final public static function getFolderOf($filename)
    {
        // $filename = str_replace('\\', '/', $filename);

        if (is_dir($filename)) {
            return $filename;
        }
        $p = strrpos($filename, '/');
        if ($p === false) {
            return './';
        }
        $folder = substr($filename, 0, $p);

        return $folder;
    }

    /**
     * Return mixed value as HTML format, (util for debug and fast presentation)
     *
     * @param mixed $mixed
     *
     * @return string
     */
    final public static function asThis($mixed)
    {
        if (is_array($mixed)) {
            if (self::isArrayOfArray($mixed) === true) {
                $html = '<table>';

                // header
                foreach ($mixed as $key_row => $row) {
                    $html .= '<tr>';
                    foreach ($row as $key_col => $col) {
                        $html .= "<th>$key_col</th>";
                    }
                    $html .= '</tr>';
                    break;
                }

                // rows
                foreach ($mixed as $key_row => $row) {
                    $html .= '<tr>';
                    foreach ($row as $key_col => $col) {
                        $html .= '<td>'.self::asThis($col).'</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</table>';
            } elseif (self::isArrayOfObjects($mixed)) {

                $html = '<table>';

                // header
                foreach ($mixed as $key_row => $row) {
                    $html .= '<tr>';
                    $vars = get_object_vars($row);

                    foreach ($vars as $key_col => $col) {
                        $html .= "<th>$key_col</th>";
                    }
                    $html .= '</tr>';
                    break;
                }

                // rows
                foreach ($mixed as $key_row => $row) {
                    $vars = get_object_vars($row);
                    $html .= '<tr>';
                    foreach ($vars as $key_col => $col) {
                        $html .= '<td>'.self::asThis($col).'</td>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</table>';
            } elseif (self::isNumericList($mixed)) {
                $html = '<table class "numeric-list">';
                foreach ($mixed as $key => $v) {
                    $html .= "<td>$v</td>";
                }
                $html .= '</table>';
            } else {
                $html = '<ul class = "array">';
                foreach ($mixed as $key => $value) {
                    $t = '';
                    if (!is_numeric($key) && trim((string)$key) !== '' && $key !== null) {
                        $t = "$key: <br>";
                    }
                    $html .= '<li> '.self::asThis($value).'</li>';
                }
                $html .= '</ul>';
            }
        } elseif (is_object($mixed)) {
            $html = get_class($mixed).': <table>';
            $vars = get_object_vars($mixed);

            foreach ($vars as $var => $value) {
                $html .= '<li>'.self::asThis($mixed->$var).'</li>';
            }
            $html .= '</ul>';
        } elseif (is_bool($mixed)) {
            $html = ($mixed === true ? 'TRUE' : 'FALSE');
        } else {
            $html = "<label>$mixed</label>";
        }

        return $html;
    }

    /**
     * Count a number of paragraphs in a text
     *
     * @param string $text
     *
     * @return integer
     */
    public static function getCountOfParagraphs($text)
    {
        return count(preg_split('/[\r\n]+/', $text));
    }

    /**
     * Count a number of sentences in a text
     *
     * @param string $text
     *
     * @return integer
     */
    public static function getCountOfSentences($text)
    {
        return preg_match_all('/[^\s]\.(?!\w)/', $text, $match);
    }

    /**
     * Count a number of words in a text
     *
     * @param string $text
     *
     * @return integer
     */
    public static function getCountOfWords($text)
    {
        $split_array = preg_split('/\s+/', $text);
        $word_count = preg_grep('/[a-zA-Z0-9\\x80-\\xff]/', $split_array);

        return count($word_count);
    }

    /**
     * Return true if $arr is array of array
     *
     * @param array $arr
     *
     * @return boolean
     */
    final public static function isArrayOfArray($arr)
    {
        $is = false;
        if (is_array($arr)) {
            $is = true;
            foreach ($arr as $v) {
                if (!is_array($v)) {
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
     *
     * @return boolean
     */
    final public static function isArrayOfObjects($arr)
    {
        $is = false;
        if (is_array($arr)) {
            $is = true;
            foreach ($arr as $v) {
                if (!is_object($v)) {
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
     *
     * @return boolean
     */
    final public static function isNumericList($arr)
    {
        $is = false;
        if (is_array($arr)) {
            $is = true;
            foreach ($arr as $v) {
                if (!is_numeric($v)) {
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
     *
     * @return array
     */
    final public static function getVarsFromCode($code)
    {
        $t = token_get_all("<?php $code ?>");
        $vars = [];
        foreach ($t as $key => $value) {
            if (is_array($value) && $value [0] === T_VARIABLE) {
                $vars [] = substr($value [1], 1);
            }
        }

        return $vars;
    }

    /**
     * Return true if the PHP code have any var
     *
     * @param string $code
     *
     * @return bool
     */
    final public static function haveVarsThisCode($code, $ignore = [])
    {
        $vars = self::getVarsFromCode($code);
        if (count($vars) > 0) {
            $diff = array_diff($vars, $ignore);
            if (count($diff) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if current dialect is valid
     *
     * @return mixed
     */
    final public static function isValidCurrentDialect()
    {

        // TODO: Improve this syntax checker
        $all_tags = [
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
            DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL,
            DIV_TAG_SPECIAL_REPLACE_SPACE,
        ];

        // Required tags
        $names = [
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
            'DIV_TAG_SPECIAL_REPLACE_SPACE',
            'DIV_TAG_TEASER_BREAK',
        ];

        $r = [
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
            DIV_TAG_SPECIAL_REPLACE_SPACE,
            DIV_TAG_TEASER_BREAK,
        ];

        $p = array_search('', $r, true);
        if ($p !== false) {
            return $names [$p].' is required';
        }
        $p = array_search(null, $r, true);
        if ($p !== false) {
            return $names [$p].' is required';
        }

        // Unique tags
        $names = [
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
            'DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL',
            'DIV_TAG_SPECIAL_REPLACE_SPACE',
        ];

        $r = [
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
            DIV_TAG_SPECIAL_REPLACE_DOLLAR_SYMBOL,
            DIV_TAG_SPECIAL_REPLACE_SPACE,
        ];

        foreach ($r as $k => $t) {
            $p = array_search($t, $r, true);
            if ($p !== false && $p !== $k) {
                return $names [$k].' must be unique and not equal to '.$names [$p];
            }
        }

        // Teaser break must be unique
        if (in_array(DIV_TAG_TEASER_BREAK, $all_tags, true)) {
            return 'DIV_TAG_TEASER_BREAK must be unique';
        }

        return true;
    }

    /**
     * Return true if $var_name is valid PHP var name
     *
     * @param string $var_name
     *
     * @return boolean
     */
    final public static function isValidVarName($var_name)
    {
        $r = preg_replace('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/', '', $var_name);

        return $r === '$' || $r === '';
    }

    /**
     * Return true if code is not obtrusive
     *
     * @param string  $code
     * @param boolean $multi_lines
     * @param mixed   $valid_tokens
     * @param array   $invalid_vars
     * @param boolean $allow_classes
     * @param boolean $allow_functions
     *
     * @return boolean
     */
    final public static function isValidPHPCode(
        $code,
        $multi_lines = true,
        $valid_tokens = null,
        $invalid_vars = [
            '$this',
            '$_SESSION',
            '$GLOBALS',
            '$_POST',
            '$_GET',
            '$_FILES',
            '$_COOKIE',
            '$_ENV',
            '$_REQUEST',
        ],
        $allow_classes = false,
        $allow_functions = false
    ) {
        if ($valid_tokens === null) {
            $valid_tokens = DIV_PHP_VALID_TOKENS_FOR_EXPRESSIONS.','.DIV_PHP_VALID_TOKENS_FOR_MACROS;
        }

        $t = token_get_all("<?php $code ?>");

        foreach ($t as $key => $value) {
            if (is_array($value)) {
                $t [$key] [0] = token_name($value [0]);
            }
        }

        $count = count($t);

        if (is_string($valid_tokens)) {
            $valid_tokens = explode(',', $valid_tokens);
        }

        foreach ($valid_tokens as $kk => $tk) {
            $tk = strtoupper(trim($tk));
            $valid_tokens [$tk] = $tk;
        }

        if (self::$__allowed_php_functions === null) {
            $keys = explode(',', DIV_PHP_ALLOWED_FUNCTIONS);
            self::$__allowed_php_functions = array_combine($keys, $keys);
        }

        $last_token = null;
        $last_token_object = [];
        $previous_last_token = null;
        $previous_last_token_object = null;
        foreach ($t as $idx => $token) {

            if ($token === ';' && $multi_lines === false) {
                self::internalMsg('Multi-lines not allowed', 'php_validations');

                return false;
            }

            if (is_array($token)) {
                $n = $token [0];

                switch ($n) {
                    case 'T_VARIABLE' :
                        if (in_array($token[1], $invalid_vars, true)) {
                            self::internalMsg("Access denied to {$token[1]}", 'php_validations');

                            return false;
                        }
                        break;

                    case 'T_OPEN_TAG' :
                        if ($idx > 0) {
                            self::internalMsg('Invalid token T_OPEN_TAG', 'php_validations');

                            return false;
                        }
                        break;

                    case 'T_CLOSE_TAG' :
                        if ($idx < $count - 1) {
                            self::internalMsg('Invalid token T_CLOSE_TAG', 'php_validations');

                            return false;
                        }
                        break;

                    case 'T_STRING' :

                        $class_name = false;
                        $function_name = false;

                        $f = $token [1];

                        if ($last_token === 'T_CLASS') {
                            $class_name = true;
                        }
                        if ($last_token === 'T_FUNCTION') {
                            $function_name = true;
                        }

                        $lw = strtolower($f);

                        if (!isset(self::$__allowed_methods [$f]) && $lw !== 'true' && $lw !== 'false' && $lw !== 'null') {
                            if (is_callable($f)) {
                                if (!isset(self::$__allowed_php_functions [$f])) {

                                    if (!isset(self::$__allowed_functions [$f])) {
                                        self::internalMsg("Invalid function $f", 'php_validations');

                                        return false;
                                    }

                                    if (self::$__allowed_functions [$f] === false) {
                                        self::internalMsg("Invalid function $f", 'php_validations');

                                        return false;
                                    }
                                }
                            } else {
                                // allow access to object members in macro, the object can not be a invalid var
                                if ($last_token === 'T_OBJECT_OPERATOR' && $previous_last_token === 'T_VARIABLE' && !in_array($previous_last_token_object[1], $invalid_vars, true)) {
                                    continue 2;
                                }

                                if ((($class_name && $allow_classes) || ($function_name && $allow_functions)) === false) {
                                    self::internalMsg("$f is not callable", 'php_validations');

                                    return false;
                                }
                            }
                        }
                        break;

                    default :
                        if (!isset($valid_tokens [$n])) {
                            self::internalMsg("Invalid token $n", 'php_validations');

                            return false;
                        }
                }

                if ($n !== 'T_WHITESPACE') {
                    $previous_last_token_object = $last_token_object;
                    $previous_last_token = $last_token;
                    $last_token_object = $token;
                    $last_token = $n;
                }
            }
        }

        return true;
    }

    /**
     * Check if code is a valid expression
     *
     * @param string $code
     *
     * @return boolean
     */
    final public static function isValidExpression($code)
    {
        return self::isValidPHPCode($code, false, DIV_PHP_VALID_TOKENS_FOR_EXPRESSIONS);
    }

    /**
     * Check if code is a valid macro
     *
     * @param string $code
     *
     * @return boolean
     */
    final public static function isValidMacro($code)
    {
        return self::isValidPHPCode($code, true, DIV_PHP_VALID_TOKENS_FOR_EXPRESSIONS.','.DIV_PHP_VALID_TOKENS_FOR_MACROS);
    }

    /**
     * Return true if the script was executed in the CLI enviroment
     *
     * @return boolean
     */
    final public static function isCli()
    {
        if (self::$__is_cli === null) {
            self::$__is_cli = (!isset($_SERVER ['SERVER_SOFTWARE']) && (PHP_SAPI === 'cli' || (is_numeric($_SERVER ['argc']) && $_SERVER ['argc'] > 0)));
        }

        return self::$__is_cli;
    }

    /**
     * Save internal message
     *
     * @param string $msg
     * @param string $category
     *
     */
    public static function internalMsg($msg, $category = 'global')
    {
        $d = debug_backtrace();
        $caller = $d [0] ['function'];

        if (isset($d ['class'])) {
            $caller = $d ['class'].'::'.$caller;
        }

        self::$__internal_messages [$category] [] = [
            'msg'    => $msg,
            'date'   => date('Y-m-d h:i:s'),
            'caller' => $caller,
        ];
    }

    /**
     * Return a list of internal messages
     *
     * @param string $category
     *
     * @return array
     */
    public static function getInternalMsg($category)
    {
        return self::$__internal_messages [$category];
    }

    /**
     * Get error reporting level during execution of macros and expressions
     *
     * @return mixed
     */
    public static function getErrorReporting()
    {
        if (self::$__error_reporting === null) {
            self::$__error_reporting = E_ALL;
        }

        return self::$__error_reporting;
    }

    /**
     * Set error reporting level during execution of macros and expressions
     *
     * @param mixed $code
     */
    public static function setErrorReporting($code = E_ALL)
    {
        self::$__error_reporting = $code;
    }

    /**
     * Save current PHP error reporting level and change to
     * current engine configuration
     */
    public static function changeErrorReporting()
    {
        self::$__error_reporting_php = ini_get('error_reporting');
        ini_set('error_reporting', self::getErrorReporting());
    }

    /**
     * Restore saved error reporting level of PHP
     */
    public static function restoreErrorReporting()
    {
        ini_set('error_reporting', self::$__error_reporting_php);
    }

    /**
     * Show error and die
     *
     * @param string $err_msg
     * @param string $level
     */
    public static function error($err_msg, $level = DIV_ERROR_WARNING)
    {
        self::$__errors [] = [
            $err_msg,
            $level,
        ];

        $is_cli = self::isCli();

        ob_start();
        if ($is_cli) {
            $err_msg = self::htmlToText($err_msg, null);
        }

        if ($is_cli === false) {
            echo '<div style = "z-index:9999; position: absolute; top: '.((count(self::$__errors) - 1) * 50 + 10).'px; right: 20px; width: 600px;max-height: 600px; overflow:auto;; font-family: courier; padding: 10px;';
        }

        switch ($level) {
            case DIV_ERROR_WARNING :
                if (!$is_cli) {
                    echo "background: yellow; border: 1px solid black; color: black;\">[$level] $err_msg</div>";
                } else {
                    echo "$level: $err_msg\n";
                }
                break;
            case DIV_ERROR_FATAL :
                if (!$is_cli) {
                    echo "background: red; border: 1px solid black; color: white;\">[$level] $err_msg</div>";
                } else {
                    echo "$level: $err_msg\n";
                }
                break;
        }

        $msg = ob_get_clean();

        if ($is_cli) {
            echo '[[]]'.self::htmlToText($msg, null)."\n";
        } else {
            echo $msg;
        }

        self::log($msg, $level);

        if ($level === DIV_ERROR_FATAL) {
            die ();
        }
    }

    /**
     * Switch ON the debug mode
     *
     * @param string $log_file
     */
    final public static function logOn($log_file = 'div.log')
    {
        self::$__log_mode = true;
        self::$__log_file = $log_file;
        self::log('Starting div with logs...');
    }

    /**
     * Global logger
     *
     * @param string $msg
     * @param string $level
     */
    public static function log($msg, $level = 'INFO')
    {
        $msg = self::htmlToText($msg, null);

        $msg = str_replace(["\n", "\r"], ["\\n", "\\r"], $msg);
        $msg = '['.$level.'] '.date('Y-m-d h:i:s').' - '.$msg."\n";

        if (self::$__log_mode) {
            $f = fopen(self::$__log_file, 'a');
            fwrite($f, $msg);
            fclose($f);
        }

        $func = 'log';

        if ($level === DIV_ERROR_WARNING) {
            $func = 'warn';
        } elseif ($level === DIV_ERROR_FATAL) {
            $func = 'error';
        }

        if (!self::isCli()) {
            $msg = str_replace("\n\r", ' ', $msg);
            $msg = str_replace(["\r", '"', "\n"], [' ', '', ''], $msg);
            echo "<script type=\"text/javascript\"> if (typeof console !== 'undefined') console.{$func}(\"[[]] $msg \");</script>\n";
        }
    }

    /**
     * Logger of instance
     *
     * @param string $msg
     * @param string $level
     */
    public function logger($msg, $level = 'INFO')
    {
        $msg = 'TPL-ID: '.$this->getId()." > $msg";
        self::log($msg, $level);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (self::$__log_mode) {
            $this->logger('Destroying the instance #'.$this->getId());
        }
    }
}
