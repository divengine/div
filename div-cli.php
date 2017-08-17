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
 * @author Rafa Rodriguez [@rafageist] <rafageist86@gmail.com>
 *
 * @link http://divengine.com
 * @link http://github.com/divengine/div
 *
 * @version 1.0
 */

include "div.php";

// Functions
function message($msg, $icon = 'INFO')
{
    echo "[$icon] " . date("h:i:s") . " $msg \n";
}

function wget($url)
{
    message("Download $url");
    $c = curl_init($url);
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($c);
    $info = curl_getinfo($c);

    message("HTTP response content type: ". $info['content_type']);
    message("HTTP response code: ". $info['http_code']);

    if ($info['http_code'] == 404)
        return false;

    return $result;
}

$config = parse_ini_file("div-cli.ini", INI_SCANNER_RAW);

// List of available commands
$commands = [
    'get' => [
        'help' => 'Get template from online repository',
        'type' => 'simple:string',
        'do' => function ($args) {

            global $config;

            $doAfter = [];

            $purl = parse_url($args['value']);

            if ($purl === false)
                return false;

            if (!isset($purl['path']))
                return false;

            if (!isset($purl['scheme'])) $purl['scheme'] = 'http';

            if ($purl['scheme'] == 'repo')
            {
                $purl = parse_url($config['repo']['origin'] . "/" . substr($args['value'], 7));
            }

            if ( ! isset($purl['scheme'])) $purl['scheme'] = 'http';

            if (!isset($purl['port'])) $purl['port'] = ''; else $purl['port'] = ":" . $purl['port'];
            if (!isset($purl['user'])) $purl['user'] = '';
            if (!isset($purl['pass'])) $purl['pass'] = ''; else if ($purl['user'] != '') $purl['pass'] = ":" + $purl['pass']; else $purl['pass'] = '';
            if (!isset($purl['query'])) $purl['query'] = ''; else $purl['query'] = '?' . $purl['query'];

            $basePath = "{$purl['scheme']}://{$purl['user']}{$purl['pass']}" . ($purl['user'] != '' ? "@" : "") . "{$purl['host']}{$purl['port']}/";
            $url = $basePath . "{$purl['path']}{$purl['query']}";
            $content = wget($url);

            if ($content == false) {
                message("Resource not found", "FATAL");
                return false;
            }

            $filename = "{$config['repo']['folder']}{$purl['path']}";
            $path = dirname($filename);

            if (!file_exists($path))
                mkdir($path, 0777, true);

            file_put_contents($filename, $content);

            $tpl = new div($filename, []);
            $prop = $tpl->getTemplateProperties();

            if (isset($prop['DEPENDENCY'])) {
                $dependencies = $prop['DEPENDENCY'];

                if (!is_array($dependencies))
                    $dependencies = [$dependencies];

                foreach ($dependencies as $dep) {
                    $doAfter[] = [
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
            '-t' => 'required:string',
            '-d' => 'optional:string',
            '-o' => 'optional:string',
            '--verbose' => 'optional:null'
        ],
        'do' => function ($args) {
            message("Starting builder...");

            $tpl = $args['-t'];
            $out = '';
            $dat = '';

            if (isset($args['-o'])) $out = $args['-o'];
            if (isset($args['-d'])) $dat = $args['-d'];

            if (empty($out)) $out = $tpl + ".out";
            if (empty($dat)) $dat = [];

            message("Processing template $tpl with data $dat");

            $t1 = microtime(true);
            $div = new div($tpl, $dat);
            $t2 = microtime(true);

            message("The template was parsed in " . number_format($t2 - $t1, 2) . " secs");
            message("Write results to file $out");

            file_put_contents($out, $div . "");

            message("BUILD SUCCESS!");
        },
        'help' => 'Parse a template and write result to a file or stdout'
    ],
    "--help" => [
        "type" => "simple:string",
        "do" => function ($args) {

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
            foreach ($commands as $command => $info) {
                $l = strlen($command);
                $maxlen = $l > $maxlen ? $l : $maxlen;
            }

            foreach ($commands as $command => $info) {
                echo str_repeat(' ', $maxlen - strlen($command)) . "$command \t " . $info['help'] . "\n";
            }
        },
        'help' => 'Show this help'
    ],
    'inject' => [
        'help' => 'Inject file/block inside other file',
        'type' => [
            '-ff' => 'required:string:The source of the code that will be injected', // read from file
            '-fb' => 'optional:string:A flag that identify the block that will be read from -ff',// read from block (default, entire file)
            '-tf' => 'required:string:The destiny of the code', // write to file
            '-tb' => 'optional:string:A flag that identify the block that will be replaced with the source' // write inside block (default, append)
        ],
        'do' => function ($args) {

			if (file_exists($args['-ff']))
			{
				$ff = '';
				if (isset($args['-fb'])) {
					$flag = $args['-fb'];
					$f = fopen($args['-ff'], 'r');

					$start = false;
					while (!feof($f)) {
						$s = fgets($f);

						if (strpos($s, $flag) !== false) {
							if ($start)
								break; // only first block

							$start = true;
							continue;
						}

						if ($start) {
							$ff .= $s;
						}
					}
				} else
					$ff = file_get_contents($args['-ff']);

				// if destiny file not exists, then create it
				if (!file_exists($args['-tf']))
					file_put_contents($args['-tf'],"");
				
				if (isset($args['-tb']))
				{
					$tf = fopen($args['-tf'], 'r');
					$tempfilename = $args['-tf'] . "." . uniqid();
					$ttf = fopen($tempfilename, 'w');

				
					$inject = false;
					$block = $args['-tb'];
					$start = false;

					while (!feof($tf)) {
						$s = fgets($tf);

						if (strpos($s, $block) !== false && $start == false) {
							fputs($ttf, $s);
							$start = true;
							continue;
						}

						if (strpos($s, $block) !== false && $start == true) {
							if (!$inject) {
								fputs($ttf, $ff);
								$inject = true;
							}

							fputs($ttf, $s);
							$start = false;
							continue;
						}

						if ($start == true)
							continue;

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
        'do' => function ($args) {

            $dialectFrom = [];
            $dialectTo = [];

            if (isset($args['-fd']))
                $dialectFrom = file_get_contents($args['-fd']);

            if (isset($args['-d']))
                $dialectTo = file_get_contents($args['-d']);

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
        'do' => function () {
            echo new div('{\n}'
                . '================================================={\n}'
                . '[[]] Div PHP Template Engine {$div.version} {\n}'
                . '     General Public License 3.0 (GPL) {\n}'
                . '     2011 - 2017 Div Software Solutions {\n}'
                . '     http://divengine.com {\n}'
                . '================================================={\n}');

        }
    ]
];

// Starter

message("Div Software Solutions | Command Line Tool");
message("Getting arguments...");

$prompt = $_SERVER['argv'];
$prompt[0] = "";
$prompt = trim(implode(" ", $prompt));
$prompt = str_replace(["\n", "\r", "\t"], " ", $prompt);
while (strpos($prompt, '  ') !== false) $prompt = str_replace('  ', ' ', $prompt);
$prompt = str_replace(' = ', '=', $prompt);
$prompt = explode(" ", $prompt);

$args = [];
$args_total = count($prompt);
$command = $prompt[0];

if (!isset($commands[$command])) {
    message("Command not found or unknown. Use --help for show available commands.", "FATAL");
    exit();
}

$cmd = $commands[$command]['type'];

if (is_string($cmd)) {
    $arr = explode(":", $cmd);
    $v = '';
    for ($i = 1; $i < $args_total; $i++) $v .= $prompt[$i] . " ";
    $v = trim($v);

    switch ($arr[1]) {
        case 'string':
            break;
        case 'integer':
            $v = intval($v);
            break;
    }

    $args = ['value' => $v];

} elseif (is_array($cmd)) {
    for ($i = 1; $i < $args_total; $i++) {
        $v = $prompt[$i];

        if (strpos($v, "=")) {
            $arr = explode("=", $v);
            $v = trim($arr[1]);
            if ($v[0] == '"' && substr($v, strlen($v) - 1) == '"')
                $v = substr($v, 0, strlen($v) - 2);
            $args[trim($arr[0])] = $v;
            continue;
        }

        if (isset($prompt[$i + 1]))
            $args[$v] = $prompt[++$i];
        else
            $args[$v] = null;
    }
}

// Executor
$doAfter = [['do' => $command, 'args' => $args]];

while (count($doAfter) > 0) {
    $moreDoAfter = [];
    foreach ($doAfter as $doa) {
        $do = $commands[$doa['do']]['do'];
        $moreDo = $do($doa['args']);

        if (is_array($moreDo))
            $moreDoAfter = array_merge($moreDoAfter, $moreDo);
    }
    $doAfter = $moreDoAfter;
}