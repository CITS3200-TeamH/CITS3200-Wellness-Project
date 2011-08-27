<?php
	include 'layout.php';
	check_admin();

	//prepare query
	include 'config.php';
	include 'connect.php';
	$name = $_POST['group_name'];
	$name = stripslashes($name);
	$name = mysql_real_escape_string($name);

	//count of people who do both wellness and exercise (won't list every date though)
	$res = mysql_query('select tr1.daydate as date,count(distinct(tr1.student_id)) as num from training_records1 tr1,training_records2 tr2 where tr1.student_id=tr2.student_id and tr1.daydate=tr2.daydate and tr1.class=tr2.class and tr1.class="'.$name.'" group by tr1.daydate order by tr1.daydate');

	//count of people who entered wellness data for each given date (won't list every date though)
	$res2 = mysql_query('select daydate as date,count(*) as num from training_records2 where class="'.$name.'" group by daydate order by daydate');
	
	//therefore the 'wellness data only' set will simply be: 'all wellness' - 'wellness + PA'
	//we can do this on the fly later
	
	//count of total members in the group
	$res3 = mysql_query('select count(*) as total from classmap where class_name="'.$name.'"');

	//count of people who entered pa data for each given date
	$res4 = mysql_query('select daydate as date,count(distinct(student_id)) as num from training_records1 where class="'.$name.'" group by daydate order by daydate');

	//therefore the 'PA data only' set will simply be: 'all PA' - 'wellness + PA'
	//we can do this on the fly later
	
	//first set up the first data lines
	$row = mysql_fetch_array($res);
	$row2 = mysql_fetch_array($res2);
	$row3 = mysql_fetch_array($res3);
	$nmembers = $row3['total'];
	$row4 = mysql_fetch_array($res4);

	
	//GET RELEVANT DATES
	$dateres = mysql_query('SELECT start,finish FROM class WHERE name="'.$name.'"');
	if(!$dateres) {
		echo mysql_error();
	}
	$daterow = mysql_fetch_array($dateres);
	$start = $daterow['start'];
	$end = $daterow['finish'];
	$date = date('Y-m-d');
	if(strtotime($date) > strtotime($end)) {
		$date = $end;
	}	
	$diff_qry = mysql_query('SELECT datediff("'.$date.'","'.$start.'")+1 as days');
	if($diff_qry) { $diff_row = mysql_fetch_array($diff_qry); $ndays = $diff_row['days']; }

	//see individual_total_met_d_reports for how to loop through dates and add applicable data
	$csv = 'Date,# Group Members,# Participated,% Participation,# Non-exercise Data,# Exercise Data,# Both,# Non-exercise ONLY,# Exercise ONLY,% Part. w Non-exercise,% Part. w Exercise,% Part. w Both,% Part. w Non-exercise ONLY,% Part. w Exercise ONLY'. "\n";
	for($i=0;$i<$ndays;$i++) {
		//add date to csv
		$daydate = date('Y-m-d',strtotime('+ '.$i.' day',strtotime($start)) );
		$csv .= $daydate . "," . $nmembers .",";

		//first check for non-exercise data
		if($row2['date'] == $daydate) {
			$col3 = $row2['num'];			
			$row2 = mysql_fetch_array($res2);
		}else {
			$col3 = 0;
		}
		//now check for exercise
		if($row4['date'] == $daydate) {
			$col4 = $row4['num'];
			$row4 = mysql_fetch_array($res4);
		}else {
			$col4 = 0;
		}
		//now check for both exercise and non-exercise
		if($row['date'] == $daydate) {
			$col5 = $row['num'];
			$row = mysql_fetch_array($res);
		}else {
			$col5 = 0;
		}
		
		//number of participants
		$col6 = $col3+($col4-$col5);
		
		//now ONLY non-exercise
		$col7 = ($col3 - $col5);
		
		//now ONLY exercise
		$col8 = ($col4 - $col5);
		
		//% Participation (non-exercise OR exercise data)
		$col9 = round( ($col6 / $nmembers * 100) ,3);
		
		//% of participants who did non-exercise
		$col10 = round( ($col3/$col6*100),3);
		
		//% of participants who did exercise
		$col11 = round(($col4/$col6*100),3);
		
		//% of participants did both
		$col12 = round(($col5/ $col6 * 100), 3);
		
		//% of participants who did only non-exercise
		$col13 = round(($col7 / $col6 *100), 3);
		
		//% of participants who did only exercise
		$col14 = round(($col8 / $col6 *100),3);
	
		$csv .= $col6 .','. $col9 .','. $col3 .','. $col4 .','. $col5 .','. $col7 .','. $col8 .','. $col10 .','. 
				$col11 .','. $col12 .','. $col13 .','. $col14 . "\n";
	}

	$filename = "compliance_".$name.'_'.date("Y-m-d");
	header("Content-type: text/csv");
	header("Content-disposition: csv" . date("Y-m-d") . ".csv");
	header( "Content-disposition: filename=".$filename.".csv");
	print $csv;
	exit;
?>
