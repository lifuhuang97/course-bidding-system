<?php
class Sort {

	// Sort alphabetically
	function title($a, $b)
	{
	    return strcmp($a,$b);
	}

	function bootstrap($a, $b)
	{
		return strcmp(end($a),end($b));
    }
	
	
	// Sort by the last word
    function field($a, $b)
	{
        $aValue=substr($a,strrpos($a,' '));
        $bValue=substr($b,strrpos($b,' '));
		return strcmp($aValue,$bValue);
	}
	
	function sort_it($list,$sorttype)
	{
		usort($list,array($this,$sorttype));
		return $list;
	}
}

?>