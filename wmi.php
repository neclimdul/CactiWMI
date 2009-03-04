#!/usr/bin/php -q
<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2008-2009 Ross Fawcett                                    |
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

// globals
$output = null; // by default the output is null
$inc = null; // by default needs to be null
$sep = " "; // character to use between results
$dbug_levels = array(0,1,2); // valid debug levels

// include the user configuration
include('wmi-config.php');

// include the logins file which contains the auth credentials
include('wmi-logins.php');

// check for debug environment variable
$env_wmi = (int) getenv('wmi_debug');

if ( in_array($env_wmi,$dbug_levels) ) {
	        $dbug = $env_wmi;
};

// exit if no variables given
if (count($argv) <= 1) { exit; };

// arguments
$host = $argv[1]; // hostname in form xxx.xxx.xxx.xxx
$credential = $argv[2]; // credential from wmi-logins to use for the query
$wmiclass = $argv[3]; // what wmic class to query in form Win32_ClassName
$columns = $argv[4]; // what columns to retrieve

$user = escapeshellarg($logins[$credential][0]); // escape the username
$pass = escapeshellarg($logins[$credential][1]); // escape the password <- very important with highly secure passwords

if (count($argv) > 5) { // if the number of arguments isnt above 5 then don't bother with the where = etc
	$condition_key = $argv[5];
	$condition_val = escapeshellarg($argv[6]);
} else {
	$condition_key = null;
	$condition_val = null;
};

$wmiquery = 'SELECT '.$columns.' FROM '.$wmiclass; // basic query built
if ($condition_key != null) {
        $wmiquery = $wmiquery.' WHERE '.$condition_key.'='.$condition_val; // if the query has a filter argument add it in
};
$wmiquery = '"'.$wmiquery.'"'; // encapsulate the query in " "

$wmiexec = $wmiexe.' -U '.$user.'%'.$pass.' //'.$host.' '.$wmiquery; // setup the query to be run

exec($wmiexec,$wmiout); // execute the query

if ($dbug == 1) { // basic debug, show output in easy to read format and display the exact execution command
	echo "\n\n".$wmiexec."\n\n";
	$sep = "\n";
};
if ($dbug == 2) { // advanced debug, logs everything to file for full debug
	$dbug_log = $log_location.'dbug_'.$host.'.log';
	$fp = fopen($dbug_log,'a+');
	$dbug_time = date('l jS \of F Y h:i:s A');
	fwrite($fp,"Time: $dbug_time\nWMI Class: $wmiclass\nCredential: $credential\nColumns: $columns\nCondition Key: $condition_key\nCondition Val: $condition_val\nQuery: $wmiquery\nExec: $wmiexec\nOutput:\n".$wmiout[0]."\n".$wmiout[1]."\n");
};


if(strstr($wmiout[0],'ERROR') != false) { exit; };

if (count($wmiout) > 0) {

$names = explode('|',$wmiout[1]); // build the names list to dymanically output it

for($i=2;$i<count($wmiout);$i++) { // dynamically output the key:value pairs to suit cacti
	$data = explode('|',$wmiout[$i]);
	if ($dbug == 2) {
		fwrite($fp,$wmiout[$i]."\n");
	};
	$j=0;
	foreach($data as $item) {
		if ( count($wmiout) > 3 ) { $inc = $i-2; }; // if there are multiple rows returned add an incremental number to the returned keyname
		$output = $output.$names[$j++].$inc.':'.str_replace(array(':',' '),array('','_'),$item).$sep;
	};
};

};

if ($dbug == 2) {
	fwrite($fp,"Output to Cacti: $output\n\n\n");
	fclose($fp);
};

echo $output;
?>
