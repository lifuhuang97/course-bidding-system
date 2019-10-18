<?php
class Sort {
	function title($a, $b)
	{
	    return strcmp($a->title,$b->title);
	}


	function bootstrap($a, $b)
	{
		return strcmp(end($a),end($b));
    }
    
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