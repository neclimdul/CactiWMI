#!/usr/bin/php -q
<?php

/**
 * @author Ross Fawcett
 * @copyright 2008
 */

$query = "wmic -U ".$argv[2]."%".$argv[3]." //".$argv[1]." \"select LoadPercentage from Win32_Processor where DeviceID='CPU".$argv[4]."'\"";

//echo $query."\n";
//exit;

exec($query,$query_output);
$data = explode("|",$query_output[2]);

$output = "LoadPercentage:".$data[1];


echo $output;

?>
