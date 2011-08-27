<?php
	include 'layout.php';
	check_admin();
	include 'queries.php';
	
	//get clean group name
	$group = $_POST['group_name'];
	$group = stripslashes($group);
	$group = mysql_real_escape_string($group);
	
	//set csv headings
	$csv = 'Date,ID,Surname,First,# Pushups,# Situps,# Chinups,Hang Duration,Sit & Reach 1,Sit & Reach 2,Height (cm),Mass (kg),BMI,BMI Rating,Waist (cm),Hip (cm),Waist/Hip Ratio,Waist/Hip Rating' . "\n";
	
	$data = getFitnessTests($group);
	foreach($data as &$val) {
		foreach($val as &$val2) {
			$csv .= $val2 . ',';
		}
		$csv .= "\n";
	}
	
	//export the data
	$filename = 'full_summary_'.$group.'_'.date("Y-m-d");
	header("Content-type: text/csv");
	header("Content-disposition: csv" . date("Y-m-d") . ".csv");
	header("Content-disposition: filename=".$filename.".csv");
	print $csv;
	exit;
?>
