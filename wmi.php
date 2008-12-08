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

// arguments
$host = $argv[1]; // hostname in form xxx.xxx.xxx.xxx
$user = escapeshellarg($argv[2]); // username in form domain/user.name
$pass = escapeshellarg($argv[3]); // password with characters escaped when passed to cmd line
$countertype = $argv[4]; // integer to reference the type of counter calc required, 0 = instant, 1 = fraction, 2 = average
$wmiclass = $argv[5]; // what wmic class to query in form Win32_ClassName
$columns = $argv[6]; // what columns to retrieve
$condition_key = $argv[7]; // key to filter on
$condition_val = escapeshellarg($argv[8]); // value to filter by if this is none then processing is adjusted

// globals
$wmiexe = 'wmic'; // executable for the wmic command
$output = null; // by default the output is null

// code
if ($countertype == 1) {
	$columns = $columns.',Frequency_PerfTime,Timestamp_PerfTime';
};

$wmiquery = 'SELECT '.$columns.' FROM '.$wmiclass; // basic query built
if ($condition_key != 'none') {
	$wmiquery = $wmiquery.' WHERE '.$condition_key.'='.$condition_val; // if the query has a filter argument add it in
};
$wmiquery = '"'.$wmiquery.'"'; // encapsulate the query in " "

$wmiexec = $wmiexe.' -U '.$user.'%'.$pass.' //'.$host.' '.$wmiquery; // setup the query to be run

//echo "\n\nDebug: ".$wmiexec."\n\n";

exec($wmiexec,$wmicout);

$names = explode('|',$wmicout[1]);

// counter type 0 - instant values
if ($countertype == 0) {
	for($j=2;$j<count($wmicout);$j++) {
		$data = explode('|',$wmicout[$j]);
		for($i=0;$i<count($names);$i++){
			$output = $output.$names[$i].':'.str_replace(':','',$data[$i])."\n";
		};
	};
};

if ($countertype == 1) {
	
	if ($condition_key == 'none') {
		$condition_val = 'none';
	};

	$filename = '/tmp/wmi_'.$host.'_'.$condition_val; // filename for the data source

	//echo $filename; exit;

	if (file_exists($filename)) { // check to ensure file exists and open if it does
		$wmicsaved = unserialize(file_get_contents($filename));
	} else { // use existing data to dump 0's out if the file doesnt exist
		$wmicsaved = $wmicout;
	};
	
	// same loop as above but modified to suit the fact that we don't really want all the values to display
	for($j=2;$j<count($wmicout);$j++) {
		$data1 = explode('|',$wmicout[$j]);
		$data2 = explode('|',$wmicsaved[$j]);
		for($i=0;$i<count($names)-3;$i++){
			$counter = (int) counter_counter($data1[$i],$data2[$i],$data1[6],$data2[6],$data2[4]);
			$output = $output.$names[$i].':'.$counter." ";

		};
		$output = $output.$names[$i+1].':'.$data1[$i+1]."\n\n"; // this assumes that the name value will always be the second last...
	};

	$fp = fopen($filename,'w');
	fwrite($fp, serialize($wmicout));
	fclose($fp);
};

echo $output;

?>
