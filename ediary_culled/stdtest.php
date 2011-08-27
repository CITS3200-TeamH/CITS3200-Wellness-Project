<?php
	include 'config.php';
	include 'connect.php';
	include 'layout.php';
	include 'stats.php';	
	check_student();
	draw_headerin();
	draw_navin();
	$id = $_SESSION['username'];
	$group = $_SESSION['group_name'];

	//get the ratings from training records 2
	$res = mysql_query('SELECT daydate,ratings FROM training_records2 WHERE class="'.$group.'" AND student_id='.$id.' ORDER BY daydate');

	//get the relevant rating categories from the rating item map
	$res2 = mysql_query('SELECT r1.id,description FROM rating_item_map r1,rating_item r2 WHERE r1.groupname="'.$group.'" AND r1.id=r2.id ORDER BY r2.id ');
	//$items = mysql_fetch_array($res2);
	$nitems = mysql_num_rows($res2);
	
	$i = 0;
	while($row = mysql_fetch_array($res2)){
		$descs[$i] = $row['description'];
		$i++;
	}

	if($res) {
		$j = 0;
		$pos = 0;
		while($row = mysql_fetch_array($res)) {
			//for each row, split the ratings and adjust the running totals
			$values = preg_split("[,]",$row['ratings']);
			
			//counter representing the highest week with rating values
			$week  = (int)date('W',strtotime($row['daydate']));
			
			//counter representing the day of the week
			$day = date('N',strtotime($row['daydate']));
			
			if($j == 0) {$lastweek = $week;}
			
			//if the week changes reset the weekly position counter
			if($lastweek < $week) { $pos = 0; }
			
			//add ratings to the respective arrays
			for($i=0; $i<$nitems; $i++) {
				//add items to total items array
				$totals[$i][$j] += $values[$i];
				//add items to weekly arrays [item][week][value]
				$weekly[$i][$week][$pos] = $values[$i];
				//add items to day of the week arrays
				$daily[$i][$day][] = $values[$i];
			}
			if($j != 0) { $lastweek = $week; }
			$pos++;
			$j++;
		}
		//echo $totals[0][0].$totals[0][1].'<br/>';
		//echo 'Highest week with rating values: '.$week.'<br/>';
		//echo 'Standard Deviation for first rating item: '. stddev($totals[0]);
		//echo 'weekly ratings: <br/>';
		
		//table headings
		$width = (count($descs) *80) + 80 + 100;
		echo '<div style="width:'.$width.'px; height:auto; float:left; margin:30px;">
		<table style="border:solid 1px grey; float:left; font-size:10pt; margin-bottom:10px;">
		<tr>
			<td style="width:80px; border:solid 1px grey;">Week</td>';
			for($i=0; $i<count($descs); $i++) {
				echo '<td style="width:80px; border:solid 1px grey;">'.$descs[$i].' (std)</td>';
			}
		echo '
		</tr>
		</table>';
		
		
		echo '<table style="border:solid 1px grey; border-width:0px 1px 1px 1px; font-size:10pt; margin:0px; float:left;">';
		for($i=1;$i<=$week;$i++) {
			echo '<tr style="background-color:rgb(200,200,200);">
			<td style="width:80px; border:solid 1px grey;">'.$i.'</td>';
			for($j=0;$j<$nitems;$j++) {
				if(count($weekly[$j][$i]) > 0) {
					echo '<td style="width:80px; border:solid 1px grey;">'.round(stddev($weekly[$j][$i]),3).'</td>';
				}else {
					echo '<td style="width:80px; border:solid 1px grey;"> - </td>';
				}
			}
			echo '</tr>';
		}
		echo '
		</table>';
		
		/*
		$days[0] = 'Monday';
		$days[1] = 'Tuesday';
		$days[2] = 'Wednesday';
		$days[3] = 'Thursday';
		$days[4] = 'Friday';
		$days[5] = 'Saturday';
		$days[6] = 'Sunday';
		echo '<table style="border:solid 1px grey; border-width:0px 1px 1px 1px; font-size:10pt; margin:20px 0px 0px 0px; float:left;">';
		for($i=1;$i<=7;$i++) {
			echo '<tr style="background-color:rgb(200,200,200);">
			<td style="width:80px; border:solid 1px grey;">'.$days[$i-1].'</td>';
			for($j=0;$j<$nitems;$j++) {
				if(count($daily[$j][$i]) > 0) {
					echo '<td style="width:80px; border:solid 1px grey;">'.round(stddev($daily[$j][$i]),3).'</td>';
				}else {
					echo '<td style="width:80px; border:solid 1px grey;"> - </td>';
				}
			}
			echo '</tr>';
		}
		echo '
		</table>*/
		echo '<table style="border:solid 1px grey; border-width:0px 1px 1px 1px; font-size:10pt; margin:20px 0px 0px 0px; float:left;">';
		for($i=1;$i<=$week;$i++) {
			echo '<tr style="background-color:rgb(200,200,200);">
			<td style="width:80px; border:solid 1px grey;">'.$i.'</td>';
			for($j=0;$j<$nitems;$j++) {
				if(count($weekly[$j][$i]) > 0) {
					echo '<td style="width:80px; border:solid 1px grey;">'.round(mean($weekly[$j][$i]),3).'</td>';
				}else {
					echo '<td style="width:80px; border:solid 1px grey;"> - </td>';
				}
			}
			echo '</tr>';
		}
		echo '
		</table>';
		echo '</div>';
	}else {
		echo mysql_error();
	}
	draw_footer();
?>

