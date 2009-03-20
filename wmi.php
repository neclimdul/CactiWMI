#!/usr/bin/php -q
<?php
/*
This file is the main application which interfaces the wmic binary with the
input and output from Cacti. The idea of this is to move the configuration
into Cacti rather than creating a new script for each item that you wish to
monitor via WMI.

There should be no reason to edit this file unless you know what you are
doing as changes here could potentially affect all your graphs if something
breaks.
*/

// globals
$output = null; // by default the output is null
$inc = null; // by default needs to be null
$sep = " "; // character to use between results
$dbug_levels = array(0,1,2); // valid debug levels
$version = '0.6-SVN'; // version

// include the user configuration
include('wmi-config.php');

// check for debug environment variable
$env_wmi = (int) getenv('wmi_debug');

if ( in_array($env_wmi,$dbug_levels) ) {
	        $dbug = $env_wmi;
};

// grab arguments
$args = getopt("h:u:w:c:k:v:n:");

if (count($args) > 0) {
	$host = $args['h']; // hostname in form xxx.xxx.xxx.xxx
	$credential = $args['u']; // credential from wmi-logins to use for the query
	$wmiclass = $args['w']; // what wmic class to query in form Win32_ClassName
	if (isset($args['c'])) {
		$columns = $args['c']; // what columns to retrieve
	} else {
		$columns = '*';
	};
	
	if (isset($args['n'])) { // test to check if namespace was passed
		$namespace = escapeshellarg($args['n']);
	} else { // if no namespace set default. the wmi client can do this but cuts down further tests to include it now
		$namespace = escapeshellarg('root\CIMV2');
	};

	if (isset($args['k'])&& $args['k'] != 'none') { // check to see if a filter is being used, also check to see if it is "none" as required to work around cacti...
		$condition_key = $args['k']; // the condition key we are filtering on
		$condition_val = escapeshellarg($args['v']); // and therfore the value which we assume is passed
	};
} else {
	echo "ERROR NO INPUT ARGUMENTS\n";
	exit;
};

$wmiquery = 'SELECT '.$columns.' FROM '.$wmiclass; // basic query built
if (isset($condition_key)) {
        $wmiquery = $wmiquery.' WHERE '.$condition_key.'='.$condition_val; // if the query has a filter argument add it in
};
$wmiquery = '"'.$wmiquery.'"'; // encapsulate the query in " "

$wmiexec = $wmiexe.' --namespace='.$namespace.' --authentication-file='.$credential.' //'.$host.' '.$wmiquery; // setup the query to be run

exec($wmiexec,$wmiout,$execstatus); // execute the query and store output in $wmiout and return code in $execstatus

if ($execstatus != 0) {
	$dbug = 1;
	echo "\n\nReturn code non-zero, debug mode enabled!\n\n";
};

if ($dbug == 1) { // basic debug, show output in easy to read format and display the exact execution command
	echo "\n\n".$wmiexec."\nExec Status: ".$execstatus."\n\n";
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
