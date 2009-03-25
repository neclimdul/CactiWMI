<?

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

/*
Takes an input array of "Key" => "Value" and returns it in the form suitable for cacti which is Key1:Value1 Key2:Value2
*/
function process_output($data) {
	$keys = array_keys($data);
	$return = null;
	foreach($keys as $value) {
		$return = $return.$value.":".str_replace(":","",$data[$value])."\n";
		};
	
	return $return;
};

/*
Takes the input variables and cooks them as per the following

Element  		Value
Numerator 		CounterData
Denominator 		PerfTime
Time base 		PerfFreq
Calculation 		(N1-N0)/((D1-D0)/TB)
Average function	(Nx-N0)/((Dx-D0)/TB)

 */
function counter_counter($valueA, $valueB, $perfA, $perfB, $perfFreq) {
	if ($perfA != $perfB) {
	$return = ($valueB-$valueA)/(($perfB-$perfA)/($perfFreq));
	} else {
	$return = 0;
	};
	return $return;
};

?>
