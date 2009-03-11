#!/usr/bin/php -q
<?php

/*

Exchange Msgs/Min Stats Grabber 0.1

This script uses the console WMI client to query the selected exchange server to gather stats on the messages delivered, sent and submitted per minute on a per database basis.

This code may be freely modified. Licensed under GNU/GPL 3.

- Ross Fawcett (claymen@parkingdenied.com)

*/

$query = "/usr/local/bin/wmic -U ".$argv[2]."%".$argv[3]." //".$argv[1]." \"select Name,MessagesSentPermin,MessagesSubmittedPermin,MessagesDeliveredPermin,SendQueueSize,ReceiveQueueSize from Win32_PerfRawData_MSExchangeIS_MSExchangeISMailbox where Name='".$argv[4]."'\"";

echo "\n\n".$query."\n\n";

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
