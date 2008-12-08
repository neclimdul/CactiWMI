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


// arguments
$host = $argv[1]; // hostname in form xxx.xxx.xxx.xxx
$user = escapeshellarg($argv[2]); // username in form domain/user.name
$pass = escapeshellarg($argv[3]); // password with characters escaped when passed to cmd line
$countertype = $argv[4]; // integer to reference the type of counter calc required, 0 = instant, 1 = fraction, 2 = average
$wmiclass = $argv[5]; // what wmic class to query in form Win32_ClassName
$column = $argv[6]; // what column to retrieve
$condition_key = $argv[7]; // key to filter on
$condition_val = escapeshellarg($argv[8]); // value to filter by if this is none then processing is adjusted

// globals
$wmiexe = 'wmic'; // executable for the wmic command
$output = null; // by default the output is null

// code
if ($countertype == 1) {
	$column = $column.','.$column.'_Base';
};

$wmiquery = 'SELECT '.$column.' FROM '.$wmiclass; // basic query built
if ($condition_val != 'none') {
	$wmiquery = $wmiquery.' WHERE '.$condition_key.'='.$condition_val; // if the query has a filter argument add it in
};
$wmiquery = '"'.$wmiquery.'"'; // encapsulate the query in " "

$wmiexec = $wmiexe.' -U '.$user.'%'.$pass.' //'.$host.' '.$wmiquery; // setup the query to be run

//echo "\n\n\nDebug:\n".$wmiexec."\n\n\n";

exec($wmiexec,$wmicout);

//$data = explode('|',$wmicout[2]);
$names = explode('|',$wmicout[1]);

if ( $countertype == 0 ) {
	for($j=2;$j<count($wmicout);$j++) {
		$data = explode('|',$wmicout[$j]);
		//print_r($data);
		for($i=0;$i<count($names)-1;$i++){
			echo $names[$i].':'.$data[$i]." ";
		};
	};
};


//echo $output;

?>
