<?php
	include 'layout.php';
	check_admin();
	include 'config.php';
	include 'connect.php';
	include 'queries.php';
	$data = getGroupDailyMet("big");
	
	foreach($data as &$val) {
		foreach($val as &$val2) {
			$csv .= $val2;
		}
	}
	
	$file = 'export';
	$filename = $file;
	header("Content-type: text/csv");
	header("Content-disposition: csv" . date("Y-m-d") . ".csv");
	header("Content-disposition: filename=".$filename.".csv");
	print $csv;
	exit;
?>
