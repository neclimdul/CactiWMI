#!/usr/bin/php -q
<?php

/**
 * @author Ross Fawcett
 * @copyright 2008
 */

$query = "/usr/local/bin/wmic -U ".$argv[2]."%".$argv[3]." //".$argv[1]." \"select Name,MessagesSentPermin,MessagesSubmittedPermin,MessagesDeliveredPermin,SendQueueSize,ReceiveQueueSize from Win32_PerfRawData_MSExchangeIS_MSExchangeISMailbox where Name='".$argv[4]."'\"";

exec($query,$output);

$data = explode("|",$output[2]);

//echo "Total:".$data[2]." "."Used:".($data[2]-$data[1])."\n";

//echo "Delivered:".$data[0]." Sent:".$data[1]." Submitted:".$data[2];
$output = "Delivered:" . $data[0] . " "
         ."Sent:"      . $data[1] . " "
         ."Submitted:" . $data[2] . " "
	 ."SendQueue:" . $data[4] . " "
	 ."RecvQueue:" . $data[5];



echo $output;
?>
