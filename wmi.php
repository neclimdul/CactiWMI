#!/usr/bin/php -q
<?php
/**
 * CactiWMI
 * Version 0.0.7-SVN
 *
 * Copyright (c) 2008-2010 Ross Fawcett
 *
 * This file is the main application which interfaces the wmic binary with the
 * input and output from Cacti. The idea of this is to move the configuration
 * into Cacti rather than creating a new script for each item that you wish to
 * monitor via WMI.
 *
 * The only configurable options are listed under general configuration and are
 * the debug level, log location and wmic location. Other than that all other
 * configuration is done via the templates.
 */

// general configuration
$wmiexe = '/usr/local/bin/wmic'; // executable for the wmic command
$pw_location = '/etc/cacti/'; // location of the password files, ensure the trailing slash
$log_location = '/var/log/cacti/wmi/'; // location for the log files, ensure trailing slash
$dbug = 0; // debug level 0,1 or 2

// globals
$output = null; // by default the output is null
$inc = null; // by default needs to be null
$sep = " "; // character to use between results
$dbug_levels = array(0,1,2); // valid debug levels
$version = '0.0.7-SVN'; // version
$namespace = escapeshellarg('root\CIMV2'); // default namespace
$columns = '*'; // default to select all columns

// grab arguments
$args = getopt("h:u:w:c:k:v:n:d:");

$opt_count = count($args); // count number of options, saves having to recount later
$arg_count = count($argv); // count number of arguments, again saving recounts further on

function display_help() {
	echo "wmi.php version $GLOBALS[version]\n",
	     "\n",
	     "Usage:\n",
		 "       -h <hostname>         Hostname of the server to query. (required)\n",
		 "       -u <credential path>  Path to the credential file. See format below. (required)\n",
		 "       -w <wmi class>        WMI Class to be used. (required)\n",
		 "       -n <namespace>        What namespace to use. (optional, defaults to root\CIMV2)\n",
		 "       -c <columns>          What columns to select. (optional, defaults to *)\n",
		 "       -k <filter key>       What key to filter on. (optional, default is no filter)\n",
		 "       -v <filter value>     What value for the key. (required, only when using filter key)\n",
		 "       -d <debug level>      Debug level. (optional, default is none, levels are 1 & 2)\n",
		 "\n",
		 "                             All special characters and spaces must be escaped or enclosed in single quotes!\n",
		 "\n",
	     "Example: wmi.php -h 10.0.0.1 -u /etc/wmi.pw -w Win32_ComputerSystem -c PrimaryOwnerName,NumberOfProcessors -n 'root\\CIMV2' \n",
		 "\n",
		 "Password file format: Plain text file with the following 3 lines replaced with your details.\n",
		 "\n",
		 "                      username=<your username>\n",
		 "                      password=<your password>\n",
		 "                      domain=<your domain> (can be WORKGROUP if not using a domain)\n",
		 "\n";
	exit;
}

if ($opt_count > 0) { // test to see if using new style arguments and if so default to use them
	if (empty($args['h'])) {
		display_help();
	} else {
		$host = $args['h']; // hostname in form xxx.xxx.xxx.xxx
	}
	if (empty($args['u'])) {
		display_help();
	} else {
		$credential = $args['u']; // credential from wmi-logins to use for the query
	}
	if (empty($args['w'])) {
		display_help();
	} else {
		$wmiclass = $args['w']; // what wmic class to query in form Win32_ClassName
	}
	// enables debug mode when the argument is passed (and is valid)
	if (isset($args['d']) && in_array($args['d'],$dbug_levels)) {
		$dbug = $args['d'];
	}
	// what columns to retrieve.
	if (!empty($args['c'])) {
		$columns = $args['c'];
	}

	if (isset($args['n']) && $args['n'] != '') { // test to check if namespace was passed
		$namespace = escapeshellarg($args['n']);
	}

	if (isset($args['k'])&& $args['k'] != '') { // check to see if a filter is being used, also check to see if it is "none" as required to work around cacti...
		$condition_key = $args['k']; // the condition key we are filtering on
		$condition_val = str_replace('\\','',escapeshellarg($args['v'])); // the value we are filtering with, and also strip out any slashes (backwards compatibility)
	}
} elseif ($opt_count == 0 && $arg_count > 1) { // if using old style arguments, process them accordingly
	$host = $argv[1]; // hostname in form xxx.xxx.xxx.xxx
	$credential = $argv[2]; // credential from wmi-logins to use for the query
	$wmiclass = $argv[3]; // what wmic class to query in form Win32_ClassName
	$columns = $argv[4]; // what columns to retrieve
	if (isset($argv[5])) { // if the number of arguments isnt above 5 then don't bother with the where = etc
		$condition_key = $argv[5];
		$condition_val = escapeshellarg($argv[6]);
	}
} else {
	display_help();
}

$wmiquery = 'SELECT '.$columns.' FROM '.$wmiclass; // basic query built
if (isset($condition_key)) {
        $wmiquery = $wmiquery.' WHERE '.$condition_key.'='.$condition_val; // if the query has a filter argument add it in
}
$wmiquery = '"'.$wmiquery.'"'; // encapsulate the query in " "

$wmiexec = $wmiexe.' --namespace='.$namespace.' --authentication-file='.$credential.' //'.$host.' '.$wmiquery. ' 2>/dev/null'; // setup the query to be run and hide error messages

exec($wmiexec,$wmiout,$execstatus); // execute the query and store output in $wmiout and return code in $execstatus

if ($execstatus != 0) {
	$dbug = 1;
	echo "\n\nReturn code non-zero, debug mode enabled!\n\n";
}

if ($dbug == 1) { // basic debug, show output in easy to read format and display the exact execution command
	echo "\n\n".$wmiexec."\nExec Status: ".$execstatus."\n\n";
	$sep = "\n";
}
if ($dbug == 2) { // advanced debug, logs everything to file for full debug
	$dbug_log = $log_location.'dbug_'.$host.'.log';
	$fp = fopen($dbug_log,'a+');
	$dbug_time = date('l jS \of F Y h:i:s A');
	fwrite($fp,"Time: $dbug_time\nWMI Class: $wmiclass\nCredential: $credential\nColumns: $columns\nCondition Key: $condition_key\nCondition Val: $condition_val\nQuery: $wmiquery\nExec: $wmiexec\nOutput:\n".$wmiout[0]."\n".$wmiout[1]."\n");
}

$wmi_count = count($wmiout); // count the number of lines returned from wmic, saves recouting later

if ($wmi_count > 0) {

	$names = explode('|',$wmiout[1]); // build the names list to dymanically output it

	for($i=2;$i<$wmi_count;$i++) { // dynamically output the key:value pairs to suit cacti
		$data = explode('|',$wmiout[$i]);
		if ($dbug == 2) {
			fwrite($fp,$wmiout[$i]."\n");
		}
		$j=0;
		foreach($data as $item) {
			if ( $wmi_count > 3 ) { $inc = $i-2; } // if there are multiple rows returned add an incremental number to the returned keyname
			$output = $output.$names[$j++].$inc.':'.str_replace(array(':',' '),array('','_'),$item).$sep;
		}
	}
}

if ($dbug == 2) {
	fwrite($fp,"Output to Cacti: $output\n\n\n");
	fclose($fp);
}

$output = substr($output,0,-1); // strip of the trailing space just in case cacti doesn't like it

echo $output;
