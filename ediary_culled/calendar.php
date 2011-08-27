<?php
	function draw_cal() {
	include 'config.php';
	include 'connect.php';
	
	//if the month has been changed from default (default is the current month)
	if(isset($_GET['month']) ) {//new month
		$diff = $_GET['month'] - date('m');
		$newDate = strtotime('+'.$diff.' month', strtotime(date('Y-m-d')));
		$year = date('Y',$newDate);
		$month = date('m',$newDate);
		$monthStr = date('F',$newDate);
		$newDate = date('Y-m-d',$newDate);
		$currMonth = $_GET['month'];
		$numDays = date('t',strtotime($newDate));
		$currDate = date('Y-m-d');
	}else {	//default
		$currDate = date('Y-m-d');
		$currMonth = date('m');
		$numDays = date('t');
		$monthStr = date('F');
		$year = date('Y');
		$month = date('m');
	}
	$weeks = ceil( ($numDays / 7) ); 	//the number of weeks to cycle through when drawing the calendar (depending on current month)
	
	//CHECK DATABASE FOR ANY CURRENT ENTRIES IN THE MONTH BEING SHOWN
	//RED FOR NO DATA, GREEN FOR DATA LOCKED IN, BLUE FOR VALID ENTRY DAY
	$id = $_SESSION['username'];
	$group = $_SESSION['group_name'];
	$query = 'SELECT * FROM training_records2 WHERE student_id='.$id.' AND class="'.$group.'" AND daydate >="'.$year.'-'.$month.'-01" AND daydate <="' .$year.'-'.$month.'-'.$numDays.'" GROUP BY daydate ORDER BY daydate';
	$result = mysql_query($query);
	if($result) { 
		$row = mysql_fetch_array($result);
	}
	
	$query4 = 'SELECT daydate FROM training_records1 WHERE student_id='.$id.' AND class="'.$group.'" AND daydate >="'.$year.'-'.$month.'-01" AND daydate <="' .$year.'-'.$month.'-'.$numDays.'" GROUP BY daydate ORDER BY daydate';
	$result4 = mysql_query($query4);
	if($result4) { 
		$row4 = mysql_fetch_array($result4);
	}
	
	//CHECK EXCEPTIONS
	$dt = $year .'-'. $month .'-01';
	$query3 = 'SELECT daydate FROM exception WHERE subject_id='.$id.' AND group_id="'.$group.'" AND daydate >="'.$dt.'"  GROUP BY daydate ORDER BY daydate';
	$result3 = mysql_query($query3);
	if($result3) { 
		$row3 = mysql_fetch_array($result3);
	}
	
	//check the class window
	$query = 'SELECT start, finish, window FROM class WHERE name = "'.$_SESSION['group_name'].'"';
	$result2 = mysql_query($query);
	if($result2) {
		$row2= mysql_fetch_array($result2);
		$window = $row2['window'];
		$start = $row2['start'];
		$finish = $row2['finish'];
	}
		$lastDay = date('d/m',strtotime('-'.($window-1).' day', strtotime($currDate)));
		$last = date('Y-m-d',strtotime('-'.($window-1).' day', strtotime($currDate)));
	
		$profile_qry = mysql_query('SELECT * FROM student WHERE id='.$id);
		$noprof = false;
		if($profile_qry) { 
			$profile_row = mysql_fetch_array($profile_qry);
			if($profile_row['gender'] == NULL) {
				$noprof = true;
			}
		}
	?>
	
	<script type="text/javascript">
		function fillInfo(run,var2) {
			if(run == 1) {
				document.getElementById('cell_info_box').innerHTML = 
				'<div class="cell_info_div"><img src="images/blue_running_man_sml.jpg" style="float:left;"/><span class="cell_info_span">Exercise Data Completed</span></div>\
				<div class="cell_info_div"><img src="images/blue_heart_rate_sml.jpg" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Wellness Data Completed</span></div>\
				<div class="cell_info_div"><img src="images/blue_rating_item_sml.png" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Rating Items Completed</span></div>';
			}else if(run == 2){
				document.getElementById('cell_info_box').innerHTML = 
				'<div class="cell_info_div"><img src="images/blue_running_man_sml.jpg" style="float:left;"/><span class="cell_info_span">Exercise Data Completed</span></div>\
				<div class="cell_info_div"><img src="images/yellow_heart_rate_sml.jpg" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Wellness Data NOT Completed</span></div>\
				<div class="cell_info_div"><img src="images/blue_rating_item_sml.png" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Rating Items Completed</span></div>';
			}else if(run == 3) {
				document.getElementById('cell_info_box').innerHTML = 
				'<div class="cell_info_div"><img src="images/blue_running_man_sml.jpg" style="float:left;"/><span class="cell_info_span">Exercise Data Completed</span></div>\
				<div class="cell_info_div"><img src="images/blue_heart_rate_sml.jpg" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Wellness Data Completed</span></div>\
				<div class="cell_info_div"><img src="images/yellow_rating_item_sml.png" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Rating Items NOT Completed</span></div>';			
			}else if(run == 4) {
				document.getElementById('cell_info_box').innerHTML = 
				'<div class="cell_info_div"><img src="images/blue_running_man_sml.jpg" style="float:left;"/><span class="cell_info_span">Exercise Data Completed</span></div>\
				<div class="cell_info_div"><img src="images/yellow_heart_rate_sml.jpg" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Wellness Data NOT Completed</span></div>\
				<div class="cell_info_div"><img src="images/yellow_rating_item_sml.png" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Rating Items NOT Completed</span></div>';
			}else if(run == 5) {
				document.getElementById('cell_info_box').innerHTML = 
				'<div class="cell_info_div"><img src="images/yellow_running_man_sml.jpg" style="float:left;"/><span class="cell_info_span">Exercise Data NOT Completed</span></div>\
				<div class="cell_info_div"><img src="images/blue_heart_rate_sml.jpg" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Wellness Data Completed</span></div>\
				<div class="cell_info_div"><img src="images/blue_rating_item_sml.png" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Rating Items Completed</span></div>';
			}else if(run == 6) {
				document.getElementById('cell_info_box').innerHTML = 
				'<div class="cell_info_div"><img src="images/yellow_running_man_sml.jpg" style="float:left;"/><span class="cell_info_span">Exercise Data NOT Completed</span></div>\
				<div class="cell_info_div"><img src="images/yellow_heart_rate_sml.jpg" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Wellness Data NOT Completed</span></div>\
				<div class="cell_info_div"><img src="images/blue_rating_item_sml.png" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Rating Items Completed</span></div>';
			}else if(run == 7) {
				document.getElementById('cell_info_box').innerHTML = 
				'<div class="cell_info_div"><img src="images/yellow_running_man_sml.jpg" style="float:left;"/><span class="cell_info_span">Exercise Data NOT Completed</span></div>\
				<div class="cell_info_div"><img src="images/blue_heart_rate_sml.jpg" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Wellness Data Completed</span></div>\
				<div class="cell_info_div"><img src="images/yellow_rating_item_sml.png" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Rating Items NOT Completed</span></div>';
			}else if(run == 8) {
				document.getElementById('cell_info_box').innerHTML = '\
				<div class="cell_info_div"><img src="images/yellow_running_man_sml.jpg" style="float:left;"/><span class="cell_info_span">Exercise Data NOT Completed</span></div>\
				<div class="cell_info_div"><img src="images/yellow_heart_rate_sml.jpg" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Wellness Data NOT Completed</span></div>\
				<div class="cell_info_div"><img src="images/yellow_rating_item_sml.png" style="height:42px; width:42px; float:left;"/><span class="cell_info_span">Rating Items NOT Completed</span></div>';
			}else {
				document.getElementById('cell_info_box').innerHTML = '';			
			}
		}
	</script>
	<?php
	//NOTIFICATION AREA
	echo '
	<div style="width:240px; height:410px; background-color:white; float:left;">
	<div id="notification">
		<p style="font-weight:bold; text-decoration:underline; font-size:10pt;">'.$monthStr.', '.$year.'</p>';
		if(strtotime($currDate) < strtotime($start)) { 
			echo '<p style="color:red; font-size:10pt;">> Program commences '.$start.'</p>';
		}
		if(strtotime($currDate) > strtotime($finish)) {
			echo '<p style="color:red; font-size:10pt;">> Program ended '.$finish.'</p>';	
		}
		if(strtotime($currDate) == strtotime($start)) { 
			echo '<p style="color:red; font-size:10pt;">> Program starts today</p>';	
		}
		if(strtotime($currDate) == strtotime($finish)) { 
			echo '<p style="color:red; font-size:10pt;">> Program ends today</p>';	
		}
		if(strtotime($last) >= strtotime($start) && strtotime($last) <= strtotime($finish)) {
			echo '<p style="color:red; font-size:10pt;">> Last day to enter data for '.$lastDay.'</p>';
		}
		if(mysql_num_rows($result3) > 0) {
			echo '<p style="color:red; font-size:10pt;">> Some days have been unlocked </p>';			
		}
		if($noprof) {
			echo '<p style="color:red; font-size:10pt;">> You need to complete your <a href="edit_profile.php" class="cal_profile_link" style="color:#0864A5;">profile</a></p>';						
		}
	echo '
	</div>
	<div id="cell_info_box" style="height:180px; width:220px; background-color:white; margin:0px; padding:20px 0px 10px 0px; border:dashed 0px grey; float:left; "></div>
	</div>';


	//CALENDAR
	$days = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
	for($i=1;$i<=7;$i++) {
		echo '<span style="width:30px; height:15px; padding:10px; border-color:white; border-style:solid;
			border-width:1px; margin:2px; float:left; color:black; font-weight:bold; font-size:10pt; background-color:white;">'.$days[$i-1].'</span>';
	}
	
	$day = 1;
	$offset = date("N",mktime(0,0,12,$month,$day,$year));
	echo '<div style="width:400px; float:left; height:auto; background-color:white;">';
	for($i=1;$i<$offset;$i++) {
		echo '<span class="calendar_cell" style="width:30px; height:30px; padding:10px; border-style:solid;
			border-width:1px; margin:2px; float:left; color:white; font-size:10pt; background-color:rgb(230,230,230);"></span>';
	}
	$tooltips = '<script type="text/javascript">';
	for($i=1;$i<=$weeks;$i++) {
		//print the links a week at a time
		for($j=1;$j<=7;++$j) {
			$pa = false; $ri = false; $shr = false; $both = false; $unused = false; //physical activity, rating item, sleep/heart rate status flags
			$ijdate = $year.'-'.$month.'-'.$day;
			echo 	'<a href="record.php?date='.$year.'-'.$month.'-'.$day.'"';
			
			//check for incomplete lifestyle information and add tooltips accordingly
			if(strtotime($row['daydate']) != strtotime($ijdate) && strtotime($row4['daydate']) == strtotime($ijdate)) { 
				echo 'title=" ! Missing wellness & rating item information" ';
			}else if(strtotime($row['daydate']) == strtotime($ijdate) && ($row['ratings'] == "" || !isset($row['sleep']) ) ){
				//echo 'title=" ! Missing some required information" ';
				if($row['ratings'] != "") { $ri = true; echo 'title=" ! Missing wellness information" '; }
				if(isset($row['sleep'])) { $shr = true; echo 'title=" ! Missing rating item information" ';}
			}else if(strtotime($row['daydate']) == strtotime($ijdate) && ($row['ratings'] != "" && isset($row['sleep'])) ){
				$both = true;
			}
			
			echo 'style="text-decoration:none">
			<span class="calendar_cell" style="width:30px; height:30px; padding:10px; border-style:solid; border-width:1px; margin:2px; float:left; color:black; font-size:10pt; text-decoration:none;';
			
			//calendar colouring
			//$ijdate = $year.'-'.$month.'-'.$day;
			if(strtotime($row['daydate']) == strtotime($ijdate) ) {
				echo 'background-color:rgb(200,255,200); border-color:gray;';
				$row = mysql_fetch_array($result);
				//make sure the activity record gets moved along if it is the same date as a lifestyle record
				if(strtotime($row4['daydate']) == strtotime($ijdate)) {
					$row4 = mysql_fetch_array($result4);
					$pa = true;
				}
				//check that the entry is not also an exception (in the case that it was just filled
				//and the user hasn't logged out and in again to remove the exception).
				if(strtotime($row3['daydate']) == strtotime($ijdate) ) {
					echo 'background-color:rgb(255,221,100); border-color:gray;';
					$row3 = mysql_fetch_array($result3);
				}
			}else if(strtotime($row4['daydate']) == strtotime($ijdate) ) {
				echo 'background-color:rgb(200,255,200); border-color:gray;';			
				$row4 = mysql_fetch_array($result4);
				$pa = true;
				
				//check that the entry is not also an exception (in the case that it was just filled
				//and the user hasn't logged out and in again to remove the exception).
				if(strtotime($row3['daydate']) == strtotime($ijdate) ) {
					echo 'background-color:rgb(255,221,100); border-color:gray;';
					$row3 = mysql_fetch_array($result3);
				}								
			}else if(strtotime($row3['daydate']) == strtotime($ijdate) ) {
				echo 'background-color:rgb(255,221,100); border-color:gray;';
				$row3 = mysql_fetch_array($result3);
			}else if(strtotime($ijdate) <= strtotime($currDate) && strtotime($ijdate) >= strtotime('-'.($window-1).' day', strtotime($currDate)) 
			&& strtotime($ijdate) >= strtotime($start) && strtotime($ijdate) <= strtotime($finish) ) {
				echo 'background-color:rgb(180,180,240); border-color:gray;';
			}else {
				echo 'background-color:rgb(240,240,240); border-color:gray; ';
				$unused = true;
			}
			
			if(strtotime($ijdate) <= strtotime($currDate) && strtotime($ijdate) >= strtotime('-'.($window-1).' day', strtotime($currDate)) 
			&& strtotime($ijdate) >= strtotime($start) && strtotime($ijdate) <= strtotime($finish) ) {
				echo 'font-size:16pt;';
			}
			
			//echo '">'. date("jS",strtotime($ijdate)) .'</span></a>';
			echo '" ';
			
			//cell status indicator
			if(!$unused) {
				if($pa) {
					if($both) {
						echo 'onMouseOver="fillInfo(1);"';		//all items completed
					}else if($ri) {
						echo 'onMouseOver="fillInfo(2);"';		//rating items and exercise data completed		
					}else if($shr) {
						echo 'onMouseOver="fillInfo(3);"';		//wellness data and exercise data completed			
					}else {
						echo 'onMouseOver="fillInfo(4);"';		//wellness data only		
					}
				}else {
					if($both) {
						echo 'onMouseOver="fillInfo(5);"'; 		//rating items and wellness data
					}else if($ri) {
						echo 'onMouseOver="fillInfo(6);"';		//rating items only	
					}else if($shr) {
						echo 'onMouseOver="fillInfo(7);"';		//wellness data only	
					}else {
						echo 'onMouseOver="fillInfo(8);"';		//no data!					
					}
				}
			}else {
				echo 'onMouseOver="fillInfo(9);"';	//blank cell				
			}
			
			echo '>'. $day.'</span></a>';
			//'<br/>'.date("D",strtotime($ijdate)).
			$day++;
			if($day > $numDays) {
				for($x=$day;$x<=35-$offset+1;$x++) {
					echo '<span class="calendar_cell" style="width:30px; height:30px; padding:10px; border-style:solid; border-width:1px;
						margin:2px;float:left; color:white; font-size:10pt; background-color:rgb(230,230,230);"></span>';
				}
				break;
			}
		}
	}

	//popup div for the calendar legend
	echo '
	<div id="legend" style="height:450px; width:300px; display:none; background-color:white;">
		<div style="width:300px; height:50px; float:left;">
			<div style="height:28px; width:25px; float:left; margin-right:20px; background-color:rgb(200,255,200); border:solid 1px gray; padding: 2px 0px 0px 5px; font-size:8pt;">1</div>
			Day with an entry in at least one field(s)
		</div>
		<div style="width:300px; height:50px; float:left;">		
			<div style="height:28px; width:25px; float:left; margin-right:20px; background-color:rgb(180,180,240); border:solid 1px gray; padding: 2px 0px 0px 5px; font-size:8pt;">1</div>
			Empty day within valid entry window
		</div>
		<div style="width:300px; height:50px; float:left;">		
<div style="height:28px; width:25px; float:left; margin-right:20px; background-color:rgb(255,221,100); border:solid 1px gray; padding: 2px 0px 0px 5px; font-size:8pt;">1</div>
			Exceptional day
		</div>
		<div style="width:300px; height:50px; float:left;">		
<div style="height:28px; width:25px; float:left; margin-right:20px; background-color:rgb(240,240,240); border:solid 1px gray; padding: 2px 0px 0px 5px; font-size:8pt;">1</div>
			Empty day outside of the entry window
		</div>
		<div style="width:300px; height:50px; float:left;">		
			<div style="height:30px; width:27px; padding:0px 0px 0px 3px; float:left; margin-right:20px; border:solid 1px gray; font-size:15pt;">1</div>
			Any day within the entry window
		</div>	
		Additionally you may mouse over any individual cell for a more detailed summary of entries for that day.
	</div>';
	
	?>
	<script type="text/javascript">
		//necessary js for the popup
		function showLegend() {
			$("#legend").dialog({ hide: 'slide', position:[900,130], close: function(event,ui) { $("#legend").dialog("destroy"); }, 
				buttons: { "Ok": function() { $(this).dialog("close"); } }, title: 'Calendar Guide', width: 350, height: 450 });
			//document.getElementById("dialog").innerHTML = msg;
		}
	</script>
	
	<?php
	//draw the next/previous links
	echo 	'<div style="width:400px; height:40px; background-color:white; float:left;"><br/>
				<a href="home.php?month='.($currMonth-1).'" style="float:left; color:black; font-size:10pt;">Previous</a>
				<a href="home.php?month='.($currMonth+1).'" style="float:right; color:black; font-size:10pt;">Next</a>
				<br/>
				<span style="width:400px; height:40px; margin-top:10px; font-size:8pt; float:left; text-align:center;">
				<a id="calendar_help" href="#help"><span id="calendar_help" onClick="showLegend();">Need help with the calendar?</span></a>
				</span>
			</div>';
	echo '</div>';
	}
?>
