#!/usr/bin/php -q
<?php

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
