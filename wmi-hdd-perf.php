#!/usr/bin/php -q
<?php

/**
 * @author Ross Fawcett
 * @copyright 2008
 */
 
$query = "wmic -U ".$argv[2]."%".$argv[3]." //".$argv[1]." \"select PercentFreeSpace,PercentFreeSpace_Base from Win32_PerfRawData_PerfDisk_LogicalDisk where Name='".$argv[4]."'\"";

exec($query,$query_output);

$data = explode("|",$query_output[2]);

$output = "Total:".$data[2]." "."Used:".($data[2]-$data[1])."\n";

echo $output;

?>