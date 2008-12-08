#!/usr/bin/php -q
<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2008 Ross Fawcett                                         |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 3          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
*/

include('wmi-includes.php');

$user = escapeshellarg($argv[2]);
$pass = escapeshellarg($argv[3]);
$host = $argv[1];
$value = escapeshellarg($argv[4]);

// Query to run - could be worth splitting this down into cleaner variables for ease of reading and also generating help.
$query = "wmic -U ".$user."%".$pass." //".$argv[1].' "select DiskReadBytesPersec,DiskReadsPersec,DiskWriteBytesPersec,DiskWritesPersec,Frequency_PerfTime,Timestamp_PerfTime from Win32_PerfRawData_PerfDisk_LogicalDisk where Name='.$value.'"';

// Filename for tmp storage - consider saving this in the includes file maybe?
$filename = '/tmp/wmi_new_'.$argv[1].'_'.$argv[4];

// Preset output variable
$output = null;

// Run the query
exec($query,$query_output);

// Pull the data we need out of it
$data1 = explode("|",$query_output[2]);
$names = explode("|",$query_output[1]);

/*
Check to see if the file exists, if it does open it.
If the file does not exist use the current data which will result in 0's on the output to make things clean.
*/
if (file_exists($filename)) {
$data2 = unserialize(file_get_contents($filename));
} else {
$data2 = $data1;
};

$arr = array();


// Use the names pulled from wmic rather than hand code. Could make this dynamic at some stage.
for($i=0;$i<4;$i++) {
$arr[$names[$i]] = (int) counter_counter($data1[$i],$data2[$i],$data1[6],$data2[6],$data2[4]);
};

// Output using the function to make things easy.
$output = process_output($arr);

// Serialize and then write the data to the tmp file and then close it.
$fp = fopen($filename,'w');
fwrite($fp, serialize($data1));
fclose($fp);

// Display the output.
echo $output;

?>
