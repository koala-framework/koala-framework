<?p

function smarty_modifier_file_size($FileSiz

	if(!is_int($FileSize) && file_exists($FileSize))
		$FileSize = filesize($FileSize


	$ShortCuts = array("Bytes", "KB", "MB", "GB", "TB", "PB"

	$i = 
	while($FileSize > 1024 && isset($ShortCuts[$i+1]))
		$FileSize = $FileSize / 102
		$i+


	if($FileSize < 10
		$ret = number_format($FileSize, 1, ",", "."
	el
		$ret = number_format($FileSize, 0, ",", "."

	$ret .= " ".$ShortCuts[$i
	return $re


