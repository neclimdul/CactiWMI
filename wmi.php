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
$wmiclass = $argv[4]; // what wmic class to query in form Win32_ClassName
$columns = $argv[5]; // what columns to retrieve

if (count($argv) > 6) {
	$condition_key = $argv[6];
	$condition_val = escapeshellarg($argv[7]);
} else {
	$condition_key = null;
};

// globals
$wmiexe = '/usr/local/bin/wmic'; // executable for the wmic command
$output = null; // by default the output is null

$wmiquery = 'SELECT '.$columns.' FROM '.$wmiclass; // basic query built
if ($condition_key != null) {
        $wmiquery = $wmiquery.' WHERE '.$condition_key.'='.$condition_val; // if the query has a filter argument add it in
};
$wmiquery = '"'.$wmiquery.'"'; // encapsulate the query in " "

$wmiexec = $wmiexe.' -U '.$user.'%'.$pass.' //'.$host.' '.$wmiquery; // setup the query to be run

//echo "\n\n".$wmiexec."\n\n";

exec($wmiexec,$wmiout);

$names = explode('|',$wmiout[1]);

for($i=2;$i<count($wmiout);$i++) {
	$data = explode('|',$wmiout[$i]);
	$j=0;
	foreach($data as $item) {
		$output = $output.$names[$j++].':'.str_replace(':','',$item)." ";
	};
};

echo $output;

?>
