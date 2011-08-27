<?php
	include 'layout.php';
	check_admin();
	include 'config.php';
	include 'connect.php';
	include 'queries.php';
	
	//fetch and clean group name
	$group = $_POST['group_name'];
	$group = stripslashes($group);
	$group = mysql_real_escape_string($group);

	//set csv headers
	$csv = 'Date,Day,Day Count,Week Count,ID,Surname,First,DOB,Gender,Athletic,Sport,Level,Activity Type,Duration,METs,Cardio,Muscle,Flex,Body,Heading,Description,Intensity' . "\n";

	//fetch data and fill csv
	$data = getPARaw($group);
	foreach($data as &$val) {
		foreach($val as &$val2) {
			$csv .= $val2;
		}	
	}

	//export csv data
	$filename = 'PARaw_'.date("Y-m-d").'_'. $group;
	header("Content-type: text/csv");
	header("Content-disposition: csv" . date("Y-m-d") . ".csv");
	header("Content-disposition: filename=".$filename.".csv");
	print $csv;
	exit;
?>
