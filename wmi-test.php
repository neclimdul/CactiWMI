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

$query = "wmic -U ".$argv[2]."%".$argv[3]." //".$argv[1]." \"select ".$argv[4]." from ".$argv[5]."\"";

exec($query,$output);

echo $query."\n\n";

$stat = explode("|",$output[1]);


for($j=2;$j<count($output);$j++) {


$val = explode("|",$output[$j]);

for($i=0;$i<count($stat);$i++) {

echo $stat[$i]." => ".$val[$i]."\n";


};

};

?>
