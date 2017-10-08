<?php

/**
 * Command Line Interface for Div PHP Template Engine (>=5.0)
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
 * @package com.divengine
 * @author  Rafa Rodriguez [@rafageist] <rafageist86@gmail.com>
 *
 * @link    http://divengine.com
 * @link    http://github.com/divengine/div
 *
 * @version 1.0
 */

// Globals
$config = [];

/**
 * Load configuration
 */
function loadConfig()
{
	global $config;

	$configPath = "div-cli.ini";
	if(file_exists("./.div/config.ini")) $configPath = "./.div/config.ini";

	if( ! file_exists($configPath))
	{
		message("Configuration $configPath not found. Generating default config...");

		$config = [
			"repo" => [
				"origin" => "divengine.com",
				"destination" => "./repo"
			]
		];

	} else
	{
		message("Loading configuration from $configPath");
		$config = parse_ini_file($configPath, INI_SCANNER_RAW);
	}

}

loadConfig();

define('PACKAGES', $config['repo']['destination']);

// Require
include "div.php";

/**
 * Execute a command
 *
 * @param       $command
 * @param       $args
 * @param array $data
 */
function executor($command, $args, &$data = [])
{
	global $commands;

	// Executor
	$doAfter = [['do' => $command, 'args' => $args]];

	while(count($doAfter) > 0)
	{
		$moreDoAfter = [];
		foreach($doAfter as $doa)
		{
			$do     = $commands[ $doa['do'] ]['do'];
			$moreDo = $do($doa['args'], $data);

			if(is_array($moreDo)) $moreDoAfter = array_merge($moreDoAfter, $moreDo);
		}
		$doAfter = $moreDoAfter;
	}
}

// Functions

/**
 * Show message in console
 *
 * @param        $msg
 * @param string $icon
 */
function message($msg, $icon = 'INFO')
{
	echo "[$icon] " . date("h:i:s") . " $msg \n";
}

function input($msg, $default = null, $expected = [])
{
	echo $msg . ": ";
	$f = fopen("php://stdin", "r");
	while(true)
	{
		$s = fgets($f);
		$s = trim($s);
		if(empty($s)) $s = $default;
		elseif(in_array($s, $expected) || count($expected) == 0) break;

		echo "\n Wrong answer. Please type " . implode(",", $expected) . "\n";
	}

	fclose($f);

	return $s;
}

/**
 * Download from url
 *
 * @param $url
 *
 * @return bool|mixed
 */
function wget($url)
{
	message("Download $url");
	$c = curl_init($url);
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($c);
	$info   = curl_getinfo($c);

	//message("HTTP response content type: ". $info['content_type']);
	//message("HTTP response code: ". $info['http_code']);

	if($info['http_code'] == 404) return false;

	return $result;
}

function listFiles($from, $closure = '')
{
	$list = [];

	if(file_exists($from))
	{
		$add = true;
		if( ! is_null($closure) && ! empty($closure) && is_callable($closure)) $add = $closure($from);

		if($add) $list[ $from ] = $from;

		$stack = [];
		if( ! is_file($from)) $stack = [$from => $from];

		while(count($stack) > 0) // avoid recursive calls!!
		{
			$from = array_shift($stack);
			$dir  = scandir($from);

			foreach($dir as $entry)
			{
				$full_path = str_replace("//", "/", "$from/$entry");

				if($entry != '.' && $entry != '..')
				{
					$add = true;

					if( ! is_file($full_path)) $stack[ $full_path ] = $full_path;

					if( ! is_null($closure) && ! empty($closure) && is_callable($closure)) $add = $closure($full_path);

					if($add) $list[ $full_path ] = $full_path;

				}
			}
		}
	}

	return $list;
}



function fixUrl($value, $config)
{
	$purl = parse_url($value);

	if($purl === false) return false;
	if( ! isset($purl['path'])) return false;
	if( ! isset($purl['scheme']))
	{
		$purl['scheme'] = 'repo';
		$args['value']  = "repo://{$value}";
	}

	if($purl['scheme'] == 'repo') $purl = parse_url($config['repo']['origin'] . "/" . substr($value, 7));

	if( ! isset($purl['scheme'])) $purl['scheme'] = 'http';
	if( ! isset($purl['port'])) $purl['port'] = '';
	else $purl['port'] = ":" . $purl['port'];
	if( ! isset($purl['user'])) $purl['user'] = '';
	if( ! isset($purl['pass'])) $purl['pass'] = '';
	elseif($purl['user'] != '') $purl['pass'] = ':' . $purl['pass'];
	else $purl['pass'] = '';
	if( ! isset($purl['query'])) $purl['query'] = '';
	else $purl['query'] = '?' . $purl['query'];

	if( ! isset($purl['host']))
	{
		$p            = strpos($purl['path'], '/');
		$purl['host'] = substr($purl['path'], 0, $p);
		$purl['path'] = substr($purl['path'], $p + 1);
	}

	$basePath = "{$purl['scheme']}://{$purl['user']}{$purl['pass']}" . ($purl['user'] != '' ? "@" : "") . "{$purl['host']}{$purl['port']}/";

	$path = "{$purl['path']}{$purl['query']}";
	if($path[0] == "/") $path = substr($path, 1);

	$url = $basePath . $path;

	return ['url' => $url, 'basePath' => $basePath, 'path' => $path];
}

// Commands implementation
$commands = [
	'init' => [
		'help' => 'Init development with div',
		'type' => 'simple:string',
		'do' => function($args, &$data = [])
		{
			if(file_exists("./.div"))
			{
				message("The folder .div already exists. Exiting without changes.");

				return false;
			}

			mkdir('./.div');
			mkdir('./.div/repo');
			mkdir('./.div/models');

			$defaultConfig = "[repo]\n" . "origin = \"divengine.com\"\n" . "destination = \"./.div/repo\"";

			file_put_contents("./.div/config.ini", $defaultConfig);
		}

	],
	'get' => [
		'help' => 'Get resource from remote repository',
		'type' => 'simple:string',
		'do' => function($args, &$data = [])
		{
			global $config;
			$doAfter  = [];
			$urlParts = fixUrl($args['value'], $config);
			$path     = $urlParts['path'];
			$basePath = $urlParts['basePath'];
			$url      = $urlParts['url'];

			// try 1: original url
			$content = wget($url);

			if($content == false)
			{
				// try 2: url as template
				$content = wget($url . ".tpl");

				if($content == false)
				{
					message("Resource not found", "FATAL");
					if( ! isset($data['not_found'])) $data['not_found'] = [];
					$data['not_found'][] = $args['value'];

					return false;
				}

				$path .= ".tpl";
			}

			$filename = "{$config['repo']['destination']}/{$path}";
			$path     = dirname($filename);

			if( ! file_exists($path)) mkdir($path, 0777, true);

			message("Writing content of $filename ...");

			file_put_contents($filename, $content);

			$tpl = new div($filename, []);
			div::docsReset();
			div::docsOn();
			$tpl->loadTemplateProperties();
			$tpl->prepareDialect();
			$tpl->parseComments("main");
			$docProps = $tpl->getDocs();
			$tplProps = $tpl->getTemplateProperties();

			// hot injection of dialect as dependency
			if(isset($tplProps['DIALECT']))
			{
				$tplProps['DIALECT'] = trim($tplProps['DIALECT']);

				if( ! isset($docProps['main']['dependency'])) $docProps['main']['dependency'] = [];

				// relative first
				$docProps['main']['dependency'][] = dirname($urlParts['path']) . '/' . $tplProps['DIALECT'];

				// absolute second
				$docProps['main']['dependency'][] = $tplProps['DIALECT'];
			}

			// hot injection of custom div-engine as dependency
			if(isset($docProps['main']['engine']))
			{
				$docProps['main']['engine'] = trim($docProps['main']['engine']);

				if( ! isset($docProps['main']['dependency'])) $docProps['main']['dependency'] = [];

				// relative first
				$docProps['main']['dependency'][] = dirname($urlParts['path']) . '/' . $docProps['main']['engine'];

				// absolute second
				$docProps['main']['dependency'][] = $docProps['main']['engine'];
			}

			// retrieve dependencies
			if(isset($docProps['main']['dependency']))
			{
				$dependencies = $docProps['main']['dependency'];

				if( ! is_array($dependencies)) $dependencies = [$dependencies];

				foreach($dependencies as $dep)
				{
					$dep = trim($dep);
					$dep = str_replace(["\t", "\n", "\r"], "", $dep);
					if( ! empty($dep)) $doAfter[] = [
						'do' => 'get',
						'args' => ['value' => $basePath . $dep]
					];
				}
			}

			return $doAfter;
		}
	],
	'build' => [
		'type' => [
			'-t' => 'required:string', // template
			'-d' => 'optional:string', // data
			'-o' => 'optional:string', // output file
			'-g' => 'optional:string',
			'--verbose' => 'optional:null'
		],
		'do' => function($args, &$data = [])
		{
			message("Starting builder...");

			$tpl = $args['-t'];
			$out = '';
			$dat = '';

			if(isset($args['-o'])) $out = $args['-o'];
			if(isset($args['-d'])) $dat = $args['-d'];

			if(empty($out)) $out = $tpl . ".out";
			if(empty($dat)) $dat = [];

			$temp_div = new div($tpl, []);
			$temp_div->loadTemplateProperties();
			$temp_div->prepareDialect();
			div::docsReset();
			div::docsOn();
			$temp_div->parseComments("main");
			$docProps = $temp_div->getDocs();

			// hot injection of custom div-engine as argument -g
			if(isset($docProps['main']['engine']))
			{
				$args['-g'] = trim($docProps['main']['engine']);
			}

			$className = 'div';
			if(isset($args['-g']))
			{
				$path      = $args['-g'];
				$full_path = PACKAGES . $path;

				message("Checking custom engine in $full_path");

				// check if class file exists
				if( ! file_exists($full_path) && is_file($full_path) && pathinfo($full_path, PATHINFO_EXTENSION) == ".php")
				{
					message("Downloading custom engine in $full_path");
					// try to get from remote repository
					executor('get', ['value' => $path], $data);
				}

				if(file_exists($full_path))
				{
					include_once $full_path;
					$className = basename($full_path, '.php');
				}
				else
					message("Custom engine in $full_path not found", "ERROR");
			}

			message("Processing template $tpl" . ((isset($args['-d'])) ? " with data in {$args['-d']} and generator -$className-" : ""));

			$div = new $className($tpl, $dat);
			//$className::logOn();
			$t1   = microtime(true);
			$code = $div . "";
			$t2   = microtime(true);
			message("The template was parsed in " . number_format($t2 - $t1, 2) . " secs");

			message("Writing results to file $out");
			file_put_contents($out, $code);

			message("BUILD SUCCESS!");
		},
		'help' => 'Parse a template and write result to a file or stdout'
	],
	"--help" => [
		"type" => "simple:string",
		"do" => function($args, &$data = [])
		{

			echo "\n";
			echo "================= \n";
			echo "Help for Div CLI  \n";
			echo "================= \n";
			echo "\n";
			echo "usage: div <command> [<args>]\n";
			echo "\n";
			echo "These are common Div commands used in various situations:\n";
			echo "\n";

			global $commands;

			$maxlen = 0;
			foreach($commands as $command => $info)
			{
				$l      = strlen($command);
				$maxlen = $l > $maxlen ? $l : $maxlen;
			}

			foreach($commands as $command => $info)
			{
				echo str_repeat(' ', $maxlen - strlen($command)) . "$command \t " . $info['help'] . "\n";
			}
		},
		'help' => 'Show this help'
	],
	'inject' => [
		'help' => 'Inject file/block inside other file',
		'type' => [
			'-ff' => 'required:string:The source of the code that will be injected',
			// read from file
			'-fb' => 'optional:string:A flag that identify the block that will be read from -ff',
			// read from block (default, entire file)
			'-tf' => 'required:string:The destiny of the code',
			// write to file
			'-tb' => 'optional:string:A flag that identify the block that will be replaced with the source'
			// write inside block (default, append)
		],
		'do' => function($args, &$data = [])
		{

			if(file_exists($args['-ff']))
			{
				$ff = '';
				if(isset($args['-fb']))
				{
					$flag = $args['-fb'];
					$f    = fopen($args['-ff'], 'r');

					$start = false;
					while( ! feof($f))
					{
						$s = fgets($f);

						if(strpos($s, $flag) !== false)
						{
							if($start) break; // only first block

							$start = true;
							continue;
						}

						if($start)
						{
							$ff .= $s;
						}
					}
				}
				else
					$ff = file_get_contents($args['-ff']);

				// if destiny file not exists, then create it
				if( ! file_exists($args['-tf'])) file_put_contents($args['-tf'], "");

				if(isset($args['-tb']))
				{
					$tf           = fopen($args['-tf'], 'r');
					$tempfilename = $args['-tf'] . "." . uniqid();
					$ttf          = fopen($tempfilename, 'w');

					$inject = false;
					$block  = $args['-tb'];
					$start  = false;

					while( ! feof($tf))
					{
						$s = fgets($tf);

						if(strpos($s, $block) !== false && $start == false)
						{
							fputs($ttf, $s);
							$start = true;
							continue;
						}

						if(strpos($s, $block) !== false && $start == true)
						{
							if( ! $inject)
							{
								fputs($ttf, $ff);
								$inject = true;
							}

							fputs($ttf, $s);
							$start = false;
							continue;
						}

						if($start == true) continue;

						fputs($ttf, $s);
					}

					fclose($tf);
					fclose($ttf);
					rename($args['-tf'], $args['-tf'] . ".bak");
					rename($tempfilename, $args['-tf']);
				}
				else
				{
					$tf = fopen($args['-tf'], "a");
					fputs($tf, $ff);
					fclose($tf);
				}
			}

		}
	],
	'translate' => [
		'help' => "Translate template's syntax to specific dialect",
		'type' => [
			'-t' => 'required:string', // template
			'-fd' => 'optional:string', // from dialect
			'-d' => 'required:string', // to dialect
			'-o' => 'optional:string' // output result to
		],
		'do' => function($args, &$data = [])
		{

			$dialectFrom = [];
			$dialectTo   = [];

			if(isset($args['-fd'])) $dialectFrom = file_get_contents($args['-fd']);

			if(isset($args['-d'])) $dialectTo = file_get_contents($args['-d']);

			$src = file_get_contents($args['-t']);

			$tpl = new div($src, []);

			// Template's properties
			$prop = $tpl->getTemplateProperties();

			// Preparing dialect
			$tpl->__src = $tpl->prepareDialect(null, $prop);

			// Translating...
			$src = $tpl->translate($dialectFrom, $dialectTo);

			// Save result
			file_put_contents($args['-o'], $src);
		}
	],
	'--version' => [
		'help' => "Show the version of current installed Div PHP Template Engine",
		'type' => null,
		'do' => function()
		{
			echo new div('{\n}' . '================================================={\n}' . '[[]] Div PHP Template Engine {$div.version} {\n}' . '     General Public License 3.0 (GPL) {\n}' . '     2011 - 2017 Div Software Solutions {\n}' . '     http://divengine.com {\n}' . '================================================={\n}');

		}
	],
	'show-config' => [
		'help' => "Show configuration",
		'type' => "optional:string",
		'do' => function($args, &$data = [])
		{
			global $config;
			global $configPath;
			echo "Configuration file: $configPath\n";

			$show = $config;
			if(isset($args[0]) && ! empty($args[0]) && isset($config[ $args[0] ])) $show = [$args => $config[ $args[0] ]];

			foreach($show as $key => $value)
			{
				if(is_array($value))
				{
					echo "- $key:\n";
					foreach($value as $kk => $vv)
					{
						echo "  - $kk: $vv\n";
					}
				}
				else
				{
					echo "- $key: $value\n";
				}
			}
		}
	],
	'merge-json' => [
		'help' => "Create a JSON file or output it, resulting from merge of others two JSON files",
		'type' => [
			'-j1' => 'required:string',
			'-j2' => 'required:string',
			'-o' => 'optional:string'
		],
		'do' => function($args, &$data = [])
		{

			if(isset($args['-j1']) && isset($args['-j2']))
			{
				$j1 = $args["-j1"];
				$j2 = $args["-j2"];

				if( ! file_exists($j1))
				{
					message("JSON file $j1 not found");

					return false;
				}

				if( ! file_exists($j2))
				{
					message("JSON file $j2 not found");

					return false;
				}

				// TODO: ...
			}
		}
	],
	"check-repo" => [
		'help' => 'Clear local repo files',
		'type' => 'optional:string',
		'do' => function($args, &$data = [])
		{
			global $config;

			$list = listFiles(PACKAGES, function($full_path, &$data = [])
			{
				global $config;

				if(is_dir($full_path)) return false;

				message('Checking local resource ' . $full_path);

				if(is_file($full_path))
				{
					$dest  = $config['repo']['destination'];
					$ldest = strlen($dest);

					if(substr($full_path, 0, $ldest) == $dest) $full_path = substr($full_path, $ldest);

					$uri = 'repo://' . $full_path;
					$uri = str_replace('///', '//', $uri);

					executor('get', ['value' => $uri], $data);

					if(isset($data['not_found'])) if(in_array($uri, $data['not_found']))
					{
						echo "\n";
						message("Resource $uri not found in remote repository", "FATAL");
						echo "\n";

						return true;
					}
				}

				return false;
			});

			if(count($list) > 0)
			{
				echo "\n";
				message("The following local resources not found in the remote repository {$config['repo']['origin']}");
				echo "\n";

				foreach($list as $full_path)
				{
					$dest  = $config['repo']['destination'];
					$ldest = strlen($dest);

					if(substr($full_path, 0, $ldest) == $dest) $full_path = substr($full_path, $ldest);

					echo "- $full_path\n";
				}

				echo "\n";
				$r = input("Do you want to delete this resources (Y/N) [Y]?", 'Y', ['y', 'yes', 'n', 'no']);
				$r = strtolower($r);
				if($r == 'y' || $r == 'yes')
				{
					foreach($list as $item)
					{
						message("Deleting $item");
						unlink($item);
					}
				}
			}

			// TODO: show more stats (count of updated (md5 check), count deleted, count new, ...)
			$data['not_found'] = $list;
		}
	]
];

// Starter

message("Div Software Solutions | Command Line Tool");
message("Getting arguments...");


$prompt    = $_SERVER['argv'];
$prompt[0] = "";
$prompt    = trim(implode(" ", $prompt));
$prompt    = str_replace(["\n", "\r", "\t"], " ", $prompt);
while(strpos($prompt, '  ') !== false) $prompt = str_replace('  ', ' ', $prompt);
$prompt = str_replace(' = ', '=', $prompt);
$prompt = explode(" ", $prompt);

$args       = [];
$args_total = count($prompt);
$command    = $prompt[0];

if( ! isset($commands[ $command ]))
{
	message("Command not found or unknown. Use --help for show available commands.", "FATAL");
	exit();
}

// load configuration
if( ! isset($commands[ $command ]['config-required'])) $commands[ $command ]['config-required'] = true;
if($commands[ $command ]['config-required']) loadConfig();

$cmd = $commands[ $command ]['type'];

if(is_string($cmd))
{
	$arr = explode(":", $cmd);
	$v   = '';
	for($i = 1; $i < $args_total; $i ++) $v .= $prompt[ $i ] . " ";
	$v = trim($v);

	switch($arr[1])
	{
		case 'string':
			break;
		case 'integer':
			$v = intval($v);
			break;
	}

	$args = ['value' => $v];

}
elseif(is_array($cmd))
{
	for($i = 1; $i < $args_total; $i ++)
	{
		$v = $prompt[ $i ];

		if(strpos($v, "="))
		{
			$arr = explode("=", $v);
			$v   = trim($arr[1]);
			if($v[0] == '"' && substr($v, strlen($v) - 1) == '"') $v = substr($v, 0, strlen($v) - 2);
			$args[ trim($arr[0]) ] = $v;
			continue;
		}

		if(isset($prompt[ $i + 1 ])) $args[ $v ] = $prompt[ ++ $i ];
		else
			$args[ $v ] = null;
	}
}

// Executor
executor($command, $args);
