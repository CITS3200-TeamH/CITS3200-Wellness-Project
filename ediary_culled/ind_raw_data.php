<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_student();
	$id = $_SESSION['username'];
	$name = $_SESSION['group_name'];
	//$name = stripslashes($name);
	//$name = mysql_real_escape_string($name);

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
	$diff_qry = mysql_query('SELECT datediff("'.$date.'","'.$start.'")+1 as days');
	if($diff_qry) { $diff_row = mysql_fetch_array($diff_qry); $ndays = $diff_row['days']; }
	

	$sql2= "select tr1.daydate as date,tr1.student_id as id, compcodes.type as type, tr1.duration as duration, tr1.duration*compcodes.mets as mets,compcodes.cardio as cardio,compcodes.muscle as muscle,compcodes.flex as flex,compcodes.body as body, compcodes.heading as heading ,compcodes.description as description,compcodes.intensity as intensity
	FROM training_records1 tr1,compcodes
	where compcodes.compcode = tr1.compcode  and tr1.class = '$name' and tr1.student_id='$id' and tr1.daydate<='$date' 
	order by daydate";
	$result = mysql_query($sql2);
	
	$res3 = mysql_query('select tr2.daydate as date, tr2.ratings as ratings FROM training_records2 tr2 where tr2.class = "'.$name.'" AND tr2.ratings!="" AND tr2.daydate<="'.$date.'" AND tr2.student_id='.$id.' order by date');

	//get rating headers
	$res4 = mysql_query('SELECT r1.description FROM rating_item r1, rating_item_map r2 WHERE r2.id = r1.id AND r2.groupname="'.$name.'" ORDER BY r2.id');
	
	$res5 = mysql_query('SELECT daydate as date,heart_rate as hr,sleep FROM training_records2 WHERE class="'.$name.'" AND student_id='.$id.' ORDER BY date');
	//CSV HEADINGS
	$csv .= 'Date,ID,Fitness Category,Duration,METs,Cardio,Muscle,Flex,Body,Activity,Description,Intensity';
	$j = 0;
	if($res4) {
		while($row = mysql_fetch_array($res4)) {
			$csv .= ','.$row[0];
			$j++;
		}
	}
	$csv .= ',Sleep Hours,Heart Rate' ."\n";
	$nitems = mysql_num_rows($res4);
	$row = mysql_fetch_array($result);
	$row2 = mysql_fetch_array($res3);
	$row3 = mysql_fetch_array($res5);
	$ratings = false;
	$sleep_hr = false;
	for($i=0;$i<$ndays;$i++) {
		$daydate = date('Y-m-d',strtotime('+ '.$i.' day',strtotime($start)) );
		if($row['date'] == $daydate) {
			//loop through records and add them to csv
			while($row['date'] == $daydate) {
				$csv .= $daydate.','.$id.',"'.$row['type'].'",'.$row['duration'].','.$row['mets'].','.$row['cardio'].','.$row['muscle'].','.$row['flex'].','.$row['body'].',"'.$row['heading'].'","'.$row['description'].'","'.$row['intensity'] . '",';
				if($row2['date'] == $daydate) {
					$csv .= $row2['ratings'];
					$ratings = true;
				}else {
					//insert commas for number of rating items
					for($j=0;$j<$nitems-1;$j++) {
						$csv .= ',';
					}
				}
				if($row3['date'] == $daydate) {
					$csv .= ',' .$row3['sleep'] . ',' .$row3['hr'] . "\n";
					$sleep_hr = true;
				}else {
					$csv .= ',,' . "\n";
				}
				$row = mysql_fetch_array($result);
			}
			if($ratings) {
				$row2 = mysql_fetch_array($res3); 
				$ratings = false; 
			}
			if($sleep_hr) {
				$row3 = mysql_fetch_array($res5);
				$sleep_hr = false;
			}
		}else {
			$csv .= $daydate.','.$id.',"n/a",0,0,0,0,0,0,"n/a","n/a","n/a",';
			if($row2['date'] == $daydate) {
				$csv .= $row2['ratings'];
				$row2 = mysql_fetch_array($res3);
			}else {
				//insert commas for number of rating items
				for($j=0;$j<$nitems-1;$j++) {
					$csv .= ',';
				}
			}
			if($row3['date'] == $daydate) {
				$csv .= ',' .$row3['sleep'] . ',' .$row3['hr'];
				$row3 = mysql_fetch_array($res5);
			}else {
				$csv .= ',,';
			}
			$csv .= "\n";
		}
	}


	$filename = 'export_raw_'.date('Y-m-d');
	header("Content-type: text/csv");
	header("Content-disposition: csv" . date("Y-m-d") . ".csv");
	header( "Content-disposition: filename=".$filename.".csv");
	print $csv;
	exit;
?>
