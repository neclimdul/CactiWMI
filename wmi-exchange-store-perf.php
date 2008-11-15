#!/usr/bin/php -q
<?php

/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2008 Ross Fawcett	                                       |
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
