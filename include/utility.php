<?

// insert decimal dot
// echo insert_dot($test , 3);
function insert_dot($str , $decimals) {
	$str = (string)$str;
	
	$str_len=strlen($str);
	if ($str_len<=$decimals) {
		return "0.".str_pad($str, $decimals , "0" , STR_PAD_LEFT);
	}	
	$str_cut_len=$str_len-$decimals;	
	$before_decimal = substr($str, 0 , $str_cut_len);	
	$after_decimal = substr($str, -$decimals);
	return $before_decimal.".".$after_decimal."<br>";
}

?>