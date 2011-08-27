<?php
	$csv_output = $_POST['data'];
	$filename = 'export';
	header("Content-type: text/csv");
	header("Content-disposition: filename=".$filename.".csv");
	print $csv_output;
	exit;
?>
