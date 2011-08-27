<?php
	//	AUTHOR: Jake Dallimore jrhdallimore@gmail.com
	//	DATE:	Oct 14th 2010
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_student();
	draw_headerin();
	draw_navin();
	include 'validations.php';
	$id = $_SESSION['username'];
	$group = $_SESSION['group_name'];
	
	//checks whether the date specified by GET['date'] is valid for data entry
	//must be in the project scope, in the window, or be an exception
	function isValidDate($group,$id,$date) {
		//check exceptions
		$res = mysql_query('SELECT * FROM exception WHERE subject_id='.$id.' AND group_id="'.$group.'" AND daydate="'.$date.'"');
		if($res) {
			$cnt = mysql_num_rows($res); 
		}else {
			echo mysql_error();
		}
		$isException = false;
		if($cnt > 0) {
			$isException = true;
		}
		//get project start and finish dates
		$res = mysql_query('select window, start, finish from class where name="'.$group.'"');
		if($res) {
			$row = mysql_fetch_array($res);
			$start = $row['start'];
			$finish = $row['finish'];
			$window = $row['window'];
			//get the last day of the entry window
			$wlast = date('Y-m-d',strtotime('-'.($window-1).' day', strtotime(date('Y-m-d')) ));
		}else {
			echo mysql_error();
		}
		
		//check date is within project scope
		$inproj = false;
		if(strtotime($date) <= strtotime($finish) && strtotime($date) >= strtotime($start) ){
			$inproj = true;
		} 
		
		//check date is within window
		$inwindow = false;
		if(strtotime($date) >= strtotime($wlast) && strtotime($date) <= strtotime(date('Y-m-d')) ){
			$inwindow = true;
		}
		
		//validate status
		if( ($inproj && $inwindow) || $isException) {
			return true;
		}
		return false;
	}
	function getTOD($start) {
		if(strtotime($start) >= strtotime("21:00")) {
			$tod = "Night";
		}else if(strtotime($start) >= strtotime("18:00")) {
			$tod = "Evening";
		}else if(strtotime($start) >= strtotime("16:00")) {
			$tod = "Late Afternoon";
		}else if(strtotime($start) >= strtotime("14:00")) {
			$tod = "Early Afternoon";						
		}else if(strtotime($start) >= strtotime("11:30")) {
			$tod = "Midday";						
		}else if(strtotime($start) >= strtotime("09:00")) {
			$tod = "Mid Morning";												
		}else if(strtotime($start) >= strtotime("06:00")) {
			$tod = "Morning";												
		}else if(strtotime($start) >= strtotime("04:00")) {
			$tod = "Early Morning";																		
		}else {
			$tod = "Night";																								
		}
		return $tod;
	}
?>

<! Initialise the jquery select menus>
<script type="text/javascript">
	$(function(){
		$('select#typemenu').selectmenu({maxHeight: 150, width:400});				
		$('select#activity').selectmenu({maxHeight: 150, width:400});				
		$('select#compcode').selectmenu({maxHeight: 150, width:500});	
		$('select#start_time').selectmenu({maxHeight:180, width:140});
		$('select#end_time').selectmenu({maxHeight:180, width:140});
		$('select#health').selectmenu({width:140});
		//$( "#draggable" ).draggable();
		//$( "#draggable2" ).draggable();		
	});
	
	
	function rating_info(desc,summary) {
		$("#dialog").dialog({ hide: 'slide', close: function(event,ui) { $("#dialog").dialog("destroy");}, 
		buttons: { "Ok": function() { $(this).dialog("close"); } }, title: 'Rating Item Info.', width: 400, height: 200 });
		if(summary != '') {
			document.getElementById("dialog").innerHTML = summary;
		}else {
			document.getElementById("dialog").innerHTML = 'There is currently no summary available for this item';
		}
		title = 'Rating Item Info: ' + desc;
		$("#dialog").dialog( "option", "title", title );
		document.getElementById(id).style.backgroundColor= "rgb(230,200,200)";
	}
</script>
		
<div id="main"> 
	<!--div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">
			Diary Entry: <?php echo $_GET['date'];?>
		</p>
	</div-->
	
	<div id="content_wide" style="background-color:rgb(245,245,245); border:solid 1px rgb(210,210,210); padding-top:10px; margin-top:0px;">
		<div style="width:987px; height:32px;  float:left; margin-top:3px; margin-bottom:0px; ">
			<a href="record.php?date=<?php echo date('Y-m-d',strtotime('+ 1 day',strtotime($_GET['date'])))?>" class="record_next_prev" style="float:right;">Next >></a>
			<a href="record.php?date=<?php echo date('Y-m-d',strtotime('- 1 day',strtotime($_GET['date'])))?>" class="record_next_prev" style="float:left;"><< Previous</a>	
			<span style="margin-left:auto; margin-right:auto; font-weight:bold; font-size:16pt; float:left; width:813px;  text-align:center; "><?php echo date("l, M jS",strtotime($_GET['date']));?></span>		
		</div>	
		<?php
			echo '<div id="dialog" name="dialog" style="width:400px; height:200px; display:none; font-size:10pt;"></div>';
			if(isset($_GET['date'])) {
				$date = $_GET['date'];
				$valid = isValidDate($group,$id,$date);
				
				echo '<div id="notify" style="width:980px; height:35px; float:left; margin-left:5px;">';
				
				//INSERT/UPDATE SLEEP, HEART RATE, HEALTH DATA
				if(isset($_POST['save_sleep_hr'])) {
					//validate the sent data first though (if javascript disabled)
					if($_POST['resting_hr'] == "" || !is_int_val($_POST['resting_hr']) || $_POST['resting_hr'] < 0) {
						$hr = false;
					}
					if($_POST['sleep_hrs'] == "" || !is_int_val($_POST['sleep_hrs']) || $_POST['sleep_hrs'] < 0 || $_POST['sleep_hrs'] > 24) {
						$slp = false;
					}
					//if the data is valid
					if($hr!==false && $slp!==false) {
						//check if data exists
						$res = mysql_query('SELECT * FROM training_records2 WHERE daydate="'.$date.'" AND student_id='.$id.' AND class="'.$group.'"');
					
						//if exists, update
						if(mysql_num_rows($res) > 0) 
						{
							$res = mysql_query('UPDATE training_records2 SET heart_rate='.$_POST['resting_hr'].',sleep='.$_POST['sleep_hrs'].',health='.$_POST['health'].' WHERE daydate="'.$date.'" AND student_id='.$id.' AND class="'.$group.'"');
						}//else just insert
						else {
							$res = mysql_query('INSERT INTO training_records2 (daydate, student_id, heart_rate, sleep, health, class) VALUES("'.$date.'",'.$id.','.$_POST['resting_hr'].','.$_POST['sleep_hrs'].','.$_POST['health'].',"'.$group.'")');
						}
						if($res) {
							echo '<span style="color:red; font-size:10pt;">General Health & Wellness Data Saved Successfully.</span><br/>';
						}else {
							echo mysql_error();
						}
					
					}else {
						if($slp === false && $hr === false) {
							echo '<span style="color:red; font-size:10pt;">Resting heart rate and amount of sleep not specified, or not positive integer values.</span><br/>';
						}else if($slp === false) {
							echo '<span style="color:red; font-size:10pt;">Amount of sleep not specified, or not a positive integer value.</span><br/>';
						}else if($hr === false) {
							echo '<span style="color:red; font-size:10pt;">Resting heart rate not specified, or not a positive integer value.</span><br/>';
						}
					}
				}//INSERT/UPDATE THE LIFESTYLE DATA
				else if(isset($_POST['save_lifestyle'])) {
					//process the rating items into a string of values separated by commas
					//first one is set outside the loop as we must have at least one rating item to get this far anyway!
					$rating_string = $_POST['rating0'];
					$i = 1;
					while(isset($_POST['rating'.$i])) {
						$rating_string .= ','.$_POST['rating'.$i];
						$i++;
					}
					
					$res = mysql_query('SELECT * FROM training_records2 WHERE daydate="'.$date.'" AND student_id='.$id.' AND class="'.$group.'"');
					
					//if exists, update
					if(mysql_num_rows($res) > 0) 
					{
						$res = mysql_query('UPDATE training_records2 SET ratings="'.$rating_string.'" WHERE daydate="'.$date.'" AND student_id='.$id.' AND class="'.$group.'"');
					}//else just insert
					else {
						$res = mysql_query('INSERT INTO training_records2 (daydate, student_id, class, ratings) VALUES("'.$date.'",'.$id.',"'.$group.'","'.$rating_string.'")');
					}
					if($res) {
						echo '<span style="color:red; font-size:10pt;">Rating Data Saved Successfully.</span><br/>';
					}else {
						echo mysql_error();
					}
				}
				
				
				//INSERT NEW RECORD
				if(isset($_POST['new'])) {
					//validate the data
					if(strtotime($_POST['end_time']) <= strtotime($_POST['start_time']) ) {
						$dur = false;
					}
					if($dur !== false) {
						$duration = (strtotime($_POST['end_time']) - strtotime($_POST['start_time']))/60 ;
						//set the time of day
						$tod = getTOD($_POST['start_time']);
						$res = mysql_query('INSERT INTO training_records1(daydate,compcode,duration,start,end,student_id,class,time_of_day,comments) VALUES("'.$date.'",'.$_POST['compcode'].','.$duration.',"'.$_POST['start_time'].'","'.$_POST['end_time'].'",'.$id.',"'.$group.'","'.$tod.'","'.htmlentities($_POST['comments'],ENT_QUOTES,"UTF-8").'")');
						echo '<span style="color:red; font-size:10pt;">';
						if($res){echo 'Physical Activity Data Saved Successfully.';}else{ echo 'Activity already exists, you cannot add the same activity twice in a single day.';}
						echo '</span>';
					}else {
						echo '<span style="color:red; font-size:10pt;">Activity start time must come prior to end time.</span><br/>';
					}
				}//UPDATE A GIVEN RECORD
				else if(isset($_POST['update'])) {
					//validate the data
					if(strtotime($_POST['end_time']) <= strtotime($_POST['start_time']) ) {
						$dur = false;
					}
					if($dur !== false) {
						$duration = (strtotime($_POST['end_time']) - strtotime($_POST['start_time']))/60 ;
						$tod = getTOD($_POST['start_time']);						
						$res = mysql_query('UPDATE training_records1 SET compcode='.$_POST['compcode'].',duration='.$duration.',start="'.$_POST['start_time'].'",end="'.$_POST['end_time'].'",time_of_day="'.$tod.'",comments="'.htmlentities($_POST['comments'],ENT_QUOTES,"UTF-8").'" WHERE daydate="'.$date.'" AND student_id='.$id.' AND class="'.$group.'" AND compcode='.$_POST['true_compcode']);
						if($res) {echo '<span style="color:red; font-size:10pt;">Physical Activity Data Saved Successfully.</span><br/>'; }else {echo mysql_error(); }
					}else {
						echo '<span style="color:red; font-size:10pt;">Activity start time must come prior to end time.</span><br/>';
					}
				}//DELETE A GIVEN RECORD
				else if(isset($_POST['delete'])) {
					$res = mysql_query('DELETE FROM training_records1 WHERE compcode='.$_POST['true_compcode'].' AND student_id='.$id.' AND daydate="'.$date.'" AND class="'.$group.'"');
					if(mysql_affected_rows() > 0) {
						echo '<span style="color:red; font-size:10pt;">Physical Activity Data Removed Successfully.</span><br/>';
					}
				}
				echo '</div>';
				
				//get new/updated data
				$query = 'SELECT daydate FROM training_records2 WHERE daydate="'.$date.'" AND student_id='.$id.' AND class="'.$group.'"';
				$result = mysql_query($query);
											
				//data exists for this date
				if(mysql_num_rows($result) > 0) {
					$query2 = 'SELECT ratings,heart_rate,sleep,health FROM training_records2 WHERE daydate="'.$date.'" AND student_id='.$id.' AND class="'.$group.'"';
					$result2 = mysql_query($query2);
				
					$row2 = mysql_fetch_array($result2);
				
					//get rating values and split
					$temp = trim($row2['ratings']);
					$values = preg_split("[,]",$temp);
				
					$_POST['resting_hr'] = $row2[1];
					$_POST['sleep_hrs'] = $row2[2];
				}
				
				//GET THE RELEVANT RATING ITEMS
				$res = mysql_query('SELECT r2.id,r2.description,r2.summary from rating_item_map r1,rating_item r2 where groupname="'.$group.'" AND r2.id=r1.id ORDER BY id');
				//$res2 = mysql_query('SELECT ratings FROM training_records2 WHERE class="'.$group.'" AND daydate="'.$date.'" AND student_id='.$id.'');
				if(!$res) { 
					echo mysql_error(); 
				}else {
					if(mysql_num_rows($res) > 0) {
					//TOP DIV
				echo '<div class="blue_form" id="draggable" style="width:945px; height:85px; margin:-8px 0px 0px 0px; padding-top:10px; float:left; background-color:white; border:solid 1px rgb(210,210,210); border-width:1px 1px 1px 1px; -moz-border-radius:10px; -webkit-border-radius:10px;">
					  <h3 style="margin:0px 0px 0px 0px; color:#0864A5;">General Health & Wellness</h3>
					  <form action="record.php?date='.$date.'" method="POST" onsubmit="return validate_sleep_hr(this)">
					  <table border="0" style="width:700px; float:left; font-size:10pt; margin-top:20px; background-color:white;">
					  <tr>
					  <td class="record_td" style="background-color:white; border:none;">Resting HR'.$error['resting_hr'].'</td>
					  <td><input type="text" name="resting_hr" id="resting_hr"';
					  if($row2[1] == 0) {
					  	echo 'value = ""';
					  }else {
					  	echo ' value="'.$row2[1].'" ';
				  	  }
					  echo 'size="3" maxlength="4"></td>
					  <td style="width:40px; height:30px; float:left;">
					  <td class="record_td" style="background-color:white; border:none;">Sleep hours'.$error['sleep_hrs'].'</td>
					  <td><input type="text" name="sleep_hrs" id="sleep_hrs"';
					  if($row2[2] == 0) {
					  	echo 'value=""';
					  }else {
					 	echo 'value="'.$row2[2].'" ';
				 	  }
					  echo 'size="3" maxlength="4"></td>
					  <td style="width:40px; height:30px; float:left;">					  
					  <td class="record_td" style="background-color:white; border:none;">Health</td>
					  <td>
					  <select name="health" id="health">
					  		<option value="5"'; if($row2[3] == '5'){echo 'selected="selected"';} echo '>5 - Excellent</option>
							<option value="4"'; if($row2[3] == '4'){echo 'selected="selected"';} echo '>4 - Good</option>
							<option value="3"'; if($row2[3] == '3'){echo 'selected="selected"';} echo '>3 - Ok</option>					
							<option value="2"'; if($row2[3] == '2'){echo 'selected="selected"';} echo '>2 - Poor</option>
							<option value="1"'; if($row2[3] == '1'){echo 'selected="selected"';} echo '>1 - Awful</option>
						</select>
					  </td>
					  </tr>
					  </table>';
				//if valid present the submit button, else a locked symbol
				if(!$valid) {
					echo '
						<div style="width:50px; margin-top:0px; float:right; height:60px;">
							<img src="images/redcross.gif" alt="cross" style="float:right; margin-right:7px; margin-top:0px;"/>
						  	<span style="float:right; margin-top:3px;">Locked</span>
					  	</div>';
				}else{
				  	echo '
						<div style="width:50px; margin-top:0px; float:right; height:60px;">
							<input type="submit" name="save_sleep_hr" value="" style="float:right; width:45px; height:44px; background:url(images/greentick.jpg) 60% 60% no-repeat; border:none;"/>
				  			<span style="float:right; margin-right:8px; margin-top:3px;">Save</span>
			  			</div>';
				}
				echo '</div>
					  </form>'; 
					  
						echo '<form method="post" action="record.php?date='.$date.'">';
						//LEFT DIV
						echo '
						<div class="blue_form" id="draggable2" style="width:300px; min-height:310px; height:auto; float:left; margin:20px 0px 0px 0px; background-color:white; border:solid 1px rgb(210,210,210); border-width:1px 1px 1px 1px; -moz-border-radius:10px; -webkit-border-radius:10px; padding-top:10px;">
					    <h3 style="margin:0px 0px 20px 0px; color:#0864A5;">Rating Items</h3>
					  	<table border="0" style="font-size:10pt;">';
  						$i = 0;
					  	while($row = mysql_fetch_array($res)) {
					  		$sum = escape_data(nl2br($row['summary']));
					  		echo '
							<tr>
							  	<td class="record_td" style="background-color:white; border:dotted 1px gray; border-width:0px 0px 1px 0px;">'
							  	.$row['description'].
								'<img src="images/question-mark2.png" style="float:right; margin-right:10px;" onClick="rating_info(\''.$row['description'].'\',\''.$sum.'\');"/>
								</td>
							  	<td style="width:100px;">
							  	<select name="rating'.$i.'" id="rating'.$i.'">
									<option value="5"'; if($values[$i] == '5'){echo 'selected="selected"';} echo '>5 - Excellent</option>
									<option value="4"'; if($values[$i] == '4'){echo 'selected="selected"';} echo '>4 - Good</option>
									<option value="3"'; if($values[$i] == '3'){echo 'selected="selected"';} echo '>3 - Ok</option>					
									<option value="2"'; if($values[$i] == '2'){echo 'selected="selected"';} echo '>2 - Poor</option>
									<option value="1"'; if($values[$i] == '1'){echo 'selected="selected"';} echo '>1 - Awful</option>
								</select>
								</td>
						  	</tr>';
						  	$i++;
					  	}
						echo '</table>';
						?>
						<script type="text/javascript">
							$(function(){
								var i = '<?php echo $i; ?>';
								for(var j=0;j<i;j++) {
									
									$('select#rating'+j).selectmenu({width:140});
								}				
							});
						</script>
						<?php
					  	
						//if valid present the submit button, else a locked symbol
						if(!$valid) {
							echo '
								<div style="width:50px; margin-top:20px; float:right; height:60px;">
									<img src="images/redcross.gif" alt="cross" style="float:right; margin-right:7px; margin-top:0px;"/>
								  	<span style="float:right; margin-top:3px;">Locked</span>
							  	</div>';
						}else{
						 	echo '
								<div style="width:50px; margin-top:20px; float:right; height:60px;">
									<input type="submit" name="save_lifestyle" value="" style="float:right; width:45px; height:44px; background:url(images/greentick.jpg) 60% 60% no-repeat; border:none;"/>
						  			<span style="float:right; margin-right:8px; margin-top:3px;">Save</span>
					  			</div>';
						}
						echo '</div>
							  </form>';
			  		}
				}
					  
				 
					  
				//RECORDS DIV
				echo '<div class="blue_form" style="width:583px; height:auto; margin:20px 0px 0px 20px; float:left; overflow-y:auto; background-color:white; border:solid 1px rgb(210,210,210); padding-top:10px;">
			    <h3 style="margin:0px 0px 10px 0px; color:#0864A5;">Physical Activity</h3>';
				//NEW RECORD/UPDATE RECORD FORM
				if(isset($_POST['makenew']) || isset($_POST['edit'])) {
					//get data from compcodes table
					$res = mysql_query("SELECT * FROM compcodes");
					
					echo '<form name="activityform" method="post" action="record.php?date='.$date.'" onsubmit="return validate_diary_form(this)">
						<span style="float:left; margin-bottom:3px; width:300px;">Fitness Category</span><br/>
						<select name="typemenu" id="typemenu" style="width:300px; float:left;">
						<option value="all" selected="selected">All Types</option>';
						$type_res = mysql_query("SELECT type FROM compcodes GROUP BY type");
						while($row2 = mysql_fetch_array($type_res)) {
							echo '<option value="'.$row2['type'].'"';
							//auto select the fitness type when editing the entry
							if(isset($_POST['edit'])) {
								if($_POST['type'] == $row2['type']) {
									echo 'selected="selected"';
								}
							}
							echo '>'.$row2['type'].'</option>';
						}
					
					echo '
					</select><br/>
					<span style="float:left; margin-bottom:3px; width:300px;">Activity</span><br/>
					<select id="activity" name="activity" style="float:left;">';
					
					//set up a js array to hold the activities for use with js onchange. reduces queries.
					$script = '<script type="text/javascript">
								var act = new Array();';
					$script2 = '<span style="float:left; margin-bottom:3px; width:300px;">Description</span><br/>
						  		<select id="compcode" name="compcode" style="width:300px; float:left;">';

					//populate the 'activity' select, whilst filling the javascript structure for descriptions.
					$i = 0;
					$last = "";
					$counter = 0;
					while($row = mysql_fetch_array($res)) {
						$desc = trim($row['description']);
						if($last != $row['heading']) {
							if(isset($_POST['makenew'])) {
								echo '<option value="'.$row['heading'].'">'.$row['heading'].'</option>';
								$last = $row['heading'];
							}else {
								if($row['type'] == $_POST['type']) {
									echo '<option value="'.$row['heading'].'" ';
									if($row['heading'] == $_POST['heading']) {
										echo 'selected="selected"';
									}
									echo '>'.$row['heading'].'</option>';
									$last = $row['heading'];
								}
							}
							$counter++;
						}
						//auto fill and pre-select the compcode select box when editing the entry
						if(isset($_POST['edit'])) {
							if($_POST['heading'] == $row['heading']) {
								$script2 .= '<option value="'.$row['compcode'].'" ';
								if($_POST['compcode'] == $row['compcode']) {
									$script2 .= 'selected="selected"';
								}
								$script2 .= '>'.$desc.' - '.$row['mets'].' METs</option>';
							}
						}
						
						$script .= 'act['.$i.'] = new Array("'.$row['type'].'","'.$row['heading'].'","'.$desc.'","'.$row['compcode'].'","'.$row['mets'].'");';
						if(isset($_POST['makenew']) && $counter == 1) {
							$script2 .= '<option value="'.$row['compcode'].'" style="width:135px;">'.$desc.' - '.$row['mets'].' METs</option>';
						}
						$i++;
					}	
					
					$script .= '</script>';
					$script2 .= '</select>';
					
					echo '
					</select><br/>' .
					$script2 . '<br/>
					<div style="width:400px; height:20px;">
						<span id="start_time_label" style="margin-right:95px;">Start time</span>
						<span id="end_time_label" style="margin-right:95px;">End time</span>						
					</div>';	
						
					$times_script = '<script type="text/javascript">
										var times = new Array();';	
										
					if(isset($_POST['makenew'])) {
					//START AND END TIME CODE BEGINS HERE
						echo '<div style="float:left; width:160px; height:30px;">
						<select name="start_time" id="start_time" style="float:left; margin-right:30px;">';
							$x = 0;
							for($i=0;$i<96;$i++) {
								$times_script .= 'times['.$i.'] = "'.date("H:i",strtotime("+ $x minute",strtotime("00:00"))).'";';
								echo '
								<option value='.date("H:i",strtotime("+ $x minute",strtotime("00:00"))).'>'
									.date("H:i",strtotime("+ $x minute",strtotime("00:00"))).
								'</option>';
								$x += 15;
								$cnt++;
							}
						echo '
						</select></div>
						<div style="float:left; width:160px; height:30px;">
						<select name="end_time" id="end_time" style="float:left; margin-right:30px;">';
							$x = 0;
							for($i=0;$i<96;$i++) {
								if($i == 95) {
									echo '<option value="23:59:59">23:59:59</option>';
								}else {
									echo '
									<option value='.date("H:i",strtotime("+ $x minute",strtotime("00:15"))).'>'
										.date("H:i",strtotime("+ $x minute",strtotime("00:15"))).
									'</option>';
									$x += 15;
								}
							}
							echo'
						</select></div><br/><br/><br/>
						<span style="float:left; font-weight:bold;">Comments</span><br/>
						<div style="width:550px; float:left; margin-bottom:10px;">
						<textarea name="comments" style="width:300px; float:left;" ROWS=5></textarea>
						</div>
						<input type="submit" name="new" value="Create Log" onclick="updateAction=true"/>';
						$times_script .= '</script>';
						echo $times_script;
					} else {
						echo '<div style="float:left; width:160px; height:30px;">
						<select name="start_time" id="start_time" style="float:left; margin-right:30px;">';
							$x = 0;
							for($i=0;$i<96;$i++) {
								$time = date("H:i",strtotime("+ $x minute",strtotime("00:00")));
								$times_script .= 'times['.$i.'] = "'.$time.'";';
								echo '
								<option value="'.$time.'"';
								if ($_POST['start_time'] == $time ) { echo ' selected="selected" ';}
								echo '>'
									.$time.
								'</option>';
								$x += 15;
								$cnt++;
							}
						echo '
						</select></div>
						<div style="float:left; width:160px; height:30px;">
						<select name="end_time" id="end_time" style="float:left; margin-right:30px;">';
							$x = 0;
							$begin = date("H:i",strtotime("+ 15 minute",strtotime($_POST['start_time'])));
							$continue = false;
							for($i=0;$i<96;$i++) {
								$time = date("H:i",strtotime("+ $x minute",strtotime("00:15")));
								if($i == 95) {
									echo '<option value="23:59:59">23:59:59</option>';
								}else {
									//when editing only draw options onwards from the saved value in $_POST['end_time']
									//this prevents the user being able to enter negative time ranges 
									if($begin == $time) {
										echo '<option value="'.$time.'"';
										if($_POST['end_time'] == $time) {
											echo 'selected="selected"';
										}
										echo ' >'.$time.'</option>';
										$continue = true;
									}else if($continue == true) {
										echo '<option value="'.$time.'"';
										if($_POST['end_time'] == $time) {
											echo 'selected="selected"';
										}
										echo ' >'.$time.'</option>';										
									}
									$x += 15;
								}
							}
							echo'
						</select></div><br/><br/><br/>
						<span style="float:left; font-weight:bold;">Comments</span><br/>
						<div style="width:550px; float:left; margin-bottom:10px;">
						<textarea name="comments" style="width:300px; float:left;" ROWS=5>'.$_POST['comments'].'</textarea>
						</div>
						<input type="hidden" name="true_compcode" value="'.$_POST['compcode'].'" />
						<input type="submit" name="update" value="Update Log" onclick="updateAction=true"/>
						<input type="submit" name="delete" value="Remove Log" onclick="updateAction=false"/>';
						$times_script .= '</script>';
						echo $times_script;
					}
					echo '
					<input type="submit" name="cancel" value="Cancel" onclick="updateAction=false;"/>
					</form>';
					echo $script;
					?>
					
					<script type="text/javascript">
					var len = '<?php echo mysql_num_rows($res); ?>';

					var selectmenu=document.getElementById("typemenu");
					selectmenu.onchange = function() {
						var chosenoption = this.options[this.selectedIndex];
						$('select#activity').selectmenu('destroy');
						$('select#compcode').selectmenu('destroy'); 						
						document.activityform.activity.options.length = 0;
		  				document.activityform.compcode.options.length = 0;
					  	var cnt = 0;
					  	var subcnt = 0;
					  	var i = 0;
					  	var last = "";
					  	while(i < len) {
					  		if(chosenoption.value == act[i][0] && last != act[i][1]) {
								document.activityform.activity.options[cnt] = new Option(act[i][1], act[i][1], false, false);
								cnt++;
								last = act[i][1]; //ensures the headings are not double listed
					  		}else if (chosenoption.value == 'all' && last != act[i][1]) {
					  			document.activityform.activity.options[cnt] = new Option(act[i][1], act[i][1], false, false);
								cnt++;
								last = act[i][1]; //ensures the headings are not double listed
					  		}
					  		
					  		//first run condition to update the compcode/descriptions field.
					  		// last = heading used to ensure we only read the relevant descriptions. (ie under the heading we started with)
					  		if(last == act[i][1] && cnt == 1) {
				  				document.activityform.compcode.options[subcnt] = new Option(act[i][2] + " - " + act[i][4] + " METs",act[i][3], false, false);
				  				subcnt++;
					  		}
					  		i++;
					  	}
						$('select#activity').selectmenu({maxHeight: 150, width:400});
						$('select#compcode').selectmenu({maxHeight: 150, width:500}); 						 
				  	}
				  	
				  	var actmenu=document.getElementById("activity");
					actmenu.onchange = function() {
						var chosenoption = this.options[this.selectedIndex];
						$('select#compcode').selectmenu('destroy'); 
						document.activityform.compcode.options.length = 0;
						var i = 0;
						var cnt = 0;
						while(i < len) {
						
							if(chosenoption.value == act[i][1] 
							&&  document.activityform.typemenu.options[document.activityform.typemenu.selectedIndex].value == act[i][0]) {
								document.activityform.compcode.options[cnt] = new Option(act[i][2] + " - " + act[i][4] + " METs", act[i][3], false, false);
								cnt++;
							}else if (chosenoption.value == act[i][1] 
							&& document.activityform.typemenu.options[document.activityform.typemenu.selectedIndex].value == 'all') {
								document.activityform.compcode.options[cnt] = new Option(act[i][2] + " - " + act[i][4] + " METs", act[i][3], false, false);
								cnt++;
							}
							i++;
				  		}
						$('select#compcode').selectmenu({maxHeight: 150, width:500}); 
					}
					
					var start = document.getElementById("start_time");
					start.onchange = function() {
						var chosenoption = this.options[this.selectedIndex];
						$('select#end_time').selectmenu('destroy'); 						
						document.activityform.end_time.options.length = 0;					
						var i = 0;
						var cnt = 0;
						var len = this.length;
						while(i < len) {
							if(times[i] == chosenoption.value) {
								for(var j=i+1;j<len;j++) {
									document.activityform.end_time.options[cnt] = new Option(times[j],times[j],false,false);
									cnt++;
								}
							}
							i++;
						}
						document.activityform.end_time.options[cnt] = new Option("23:59:59","23:59:59",false,false);
						$('select#end_time').selectmenu({maxHeight: 180, width:140}); 											
					}
					</script>

		
					<?php 
				}else{
					//SHOW ALL EXISTING RECORDS
					$result3 = mysql_query('SELECT * FROM training_records1 tr1,compcodes cc WHERE student_id='.$id.' AND daydate="'.$date.'" AND class="'.$group.'" AND cc.compcode=tr1.compcode');
					if($result3) {
						while($row3 = mysql_fetch_array($result3)) {
							echo '<div style="width:580px; height:70px; float:left; font-size:10pt; font-weight:normal; margin-bottom:10px; border:dotted 1px grey; border-width:0px 0px 1px 0px;">
							<b style="color:rgb(60,60,60);"><br/>'.$row3['heading'].'</b> : '.$row3['description'].' , '.$row3['duration'].' mins , '.$row3['time_of_day'].''; 
							if($valid) {
								echo '<form action="record.php?date='.$date.'" method="post" style="float:right; margin-top:-10px;">
								<input type="hidden" name="type" value="'.$row3['type'].'">
								<input type="hidden" name="heading" value="'.$row3['heading'].'">
								<input type="hidden" name="compcode" value="'.$row3['compcode'].'">
								<input type="hidden" name="duration" value="'.$row3['duration'].'">
								<input type="hidden" name="start_time" value="'.$row3['start'].'">
								<input type="hidden" name="end_time" value="'.$row3['end'].'">								
								<input type="hidden" name="tod" value="'.$row3['time_of_day'].'">
								<input type="hidden" name="comments" value="'.trim($row3['comments']).'">						
								<input type="submit" name="edit" value="Edit"/>
								</form>';
							}
							echo '</div>';
						}
					}else {
					 	echo mysql_error();
					}
				
					//RECORD CREATION LINK
					if(!$valid) {
						echo '<span style="float:left; margin-top:15px;">This day is not valid for data entry.</span>';
					}else {
						//if(mysql_num_rows($result) > 0) {
							echo '<form action="record.php?date='.$date.'" method="post" style="margin-top:15px; float:left;">
							<input type="submit" name="makenew" value="New Record"/>
							</form>';
						//}else {
							//echo 'To make a new entry, please enter and save your personal information. This may be changed anytime within the valid entry window.';
						//}
					}
					
				}
				
				echo '</div>';
			}else {
				echo 'A valid date must be set<br/><br/><a href="home.php">Back to Calendar</a>';
			}
		
		?>
	</div>
</div>
<?php  draw_footer(); ?>
