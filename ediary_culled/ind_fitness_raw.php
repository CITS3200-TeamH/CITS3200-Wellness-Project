<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_student();
	$id = $_SESSION['username'];
	$class = $_SESSION['group_name'];
	
	//GET RELEVANT DATES
	$res2 = mysql_query('SELECT start,finish FROM class WHERE name="'.$name.'"');
	if(!$res2) {
		echo mysql_error();
	}
	$row2 = mysql_fetch_array($res2);
	$start = $row2['start'];
	$end = $row2['finish'];
	$date = date('Y-m-d');
	if(strtotime($date) > strtotime($end)) {
		$date = $end;
	}
	
	$res = mysql_query('SELECT * FROM fitness_test WHERE group_id="'.$class.'" AND subject_id='.$id.' ORDER BY daydate');
	if($res) {
		//CSV HEADINGS
		$csv = 'Date,ID,Group,Test Number,Push-ups in 30sec,Situp Cadence,Number of Chinups,Hang Duration (sec),Sit & Reach Trial 1,Sit & Reach Trial 2,Height,Mass,BMI,BMI Rating,Waist (cm),Hip (cm),Waist-to-Hip Ratio,Waist-to-Hip Risk Rating' . "\n";
		
		//CSV DATA
		while($row = mysql_fetch_array($res)) {
			$csv .= $row['daydate'] . ',"' . $row['subject_id'] . '",' . $row['group_id'] . ',' . $row['test_num'] . ',' . $row['pushup'] . ',' . $row['situp'] . ',' . $row['chinup'] . ',' . $row['hang'] . ',' . $row['sitreach1'] . ',' . $row['sitreach2'] . ',' . $row['height'] . ',' . $row['mass'] . ',' . $row['bmi'] . ',"' . $row['bmi_rating'] . '",' . $row['waist'] . ',' . $row['hip'] . ',' . $row['ratio'] . ',"' . $row['wh_rating'] . '"' . "\n"; 
		}
		
	}
	$filename = 'fitness_raw_'.date('Y-m-d');
	header("Content-type: text/csv");
	header("Content-disposition: csv" . date("Y-m-d") . ".csv");
	header( "Content-disposition: filename=".$filename.".csv");
	print $csv;
	exit();
?>
