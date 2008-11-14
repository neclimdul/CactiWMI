#!/usr/bin/php -q
<?php

/**
 * @author Ross Fawcett
 * @copyright 2008
 */

include('wmi-includes.php');

// Query to run
$query = "wmic -U ".$argv[2]."%".$argv[3]." //".$argv[1]." \"select DiskReadBytesPersec,DiskReadsPersec,DiskWriteBytesPersec,DiskWritesPersec,Frequency_PerfTime,Timestamp_PerfTime from Win32_PerfRawData_PerfDisk_LogicalDisk where Name='".$argv[4]."'\"";


// Filename for tmp storage
$filename = '/tmp/wmi_new_'.$argv[1].'_'.$argv[4];


// Preset output variable
$output = null;

// Run the query
exec($query,$query_output);
$data1 = explode("|",$query_output[2]);
$names = explode("|",$query_output[1]);

// Check to see if the file exists, if it does open it
if (file_exists($filename)) {
$data2 = unserialize(file_get_contents($filename));

$arr = array();
for($i=0;$i<4;$i++) {
$arr[$names[$i]] = (int) counter_counter($data1[$i],$data2[$i],$data1[6],$data2[6],$data2[4]);
};
$output = process_output($arr);


/*$output =
"DiskReadBPS:" . (int) counter_counter($data1[0],$data2[0],$data1[6],$data2[6],$data2[4]) . " " .
"DiskReadsPS:" . (int) counter_counter($data1[1],$data2[1],$data1[6],$data2[6],$data2[4]) . " " .
"DiskWriteBPS:". (int) counter_counter($data1[2],$data2[2],$data1[6],$data2[6],$data2[4]) . " " .
"DiskWritesPS:". (int) counter_counter($data1[3],$data2[3],$data1[6],$data2[6],$data2[4]);*/
} else {

$arr = array();
for($i=0;$i<4;$i++) {
$arr[$names[$i]] = 0;
};
$output = process_output($arr);
}




// Write new data to the tmp file and then close it
$fp = fopen($filename,'w');
fwrite($fp, serialize($data1));
fclose($fp);

unset($data1);

// Write out some logging of the output variable should there be any data in it
//$fp = fopen('/tmp/testing/wmi.log','a');
//fwrite($fp, $argv[4]." ".$output."\n");
//fclose($fp);


echo $output;

// Skanky function to handle the math calc
function counter_counter($valueA, $valueB, $perfA, $perfB, $perfFreq) {

return ($valueB-$valueA)/(($perfB-$perfA)/($perfFreq));

}


?>
