<?php

include "div.php";

$tpl = $_SERVER['argv'][1]; // required
$out = @$_SERVER['argv'][2];
$dat = @$_SERVER['argv'][3];

if (empty($out)) $out = $tpl + ".out";
if (empty($dat)) $dat = [];

echo "Proccess tpl = $tpl out = $out dat = $dat\n";
$div = new div($tpl, $dat);

file_put_contents($out, $div."");
