<?


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
