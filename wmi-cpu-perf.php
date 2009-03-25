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

$query = "wmic -U ".$argv[2]."%".$argv[3]." //".$argv[1]." \"select LoadPercentage from Win32_Processor where DeviceID='CPU".$argv[4]."'\"";

//echo $query."\n";
//exit;

exec($query,$query_output);
$data = explode("|",$query_output[2]);

$output = "LoadPercentage:".$data[1];


echo $output;

?>
