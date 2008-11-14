<?



function process_output($data) {
	$keys = array_keys($data);
	$return = null;
	foreach($keys as $value) {
		$return = $return.$value.":".str_replace(":","",$data[$value])."\n";
		};
	
	return $return;
};



?>
