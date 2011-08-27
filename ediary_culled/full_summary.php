<?php
	include 'layout.php';
	check_admin();
	include 'queries.php';
	
	//get clean group name
	$group = $_POST['group_name'];
	$group = stripslashes($group);
	$group = mysql_real_escape_string($group);
	
	//set csv headings
	$csv = 'Date,Day,Day Count,Week Count,ID,Surname,First,DOB,Gender,Athletic,Sport,Level,Activities,Daily Mets,Daily Duration,Resting HR,Sleep Hrs,Health';
	
		//get rating item descriptions
		$res2 = mysql_query('SELECT r1.description FROM rating_item r1, rating_item_map r2 WHERE r2.id = r1.id AND r2.groupname="'.$group.'" ORDER BY r2.id');
		
		//append to csv heading
		while($row2 = mysql_fetch_array($res2)) {
			$csv .= ','.$row2[0];
		}
		$csv .= "\n";

	//fetch the data and format for CSV
	$data = getFullSummary($group);
	foreach($data as &$val) {
		foreach($val as &$val2) {
			$csv .= $val2;
		}
	}
	
	//export the data
	$filename = 'full_summary_'.$group.'_'.date("Y-m-d");
	header("Content-type: text/csv");
	header("Content-disposition: csv" . date("Y-m-d") . ".csv");
	header("Content-disposition: filename=".$filename.".csv");
	print $csv;
	exit;
?>
