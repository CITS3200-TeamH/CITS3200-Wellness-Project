<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	include 'rating_calc.php';
	check_student();
	$class = $_SESSION['group_name'];
	$id = $_SESSION['username'];	
	draw_headerin();
	draw_navin();
	include 'validations.php';	
	
	//GET RELEVANT DATES
	$res2 = mysql_query('SELECT start,finish FROM class WHERE name="'.$class.'"');
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
	
	//check the sent data and update, or set error reporting vars
	$none = false;	
	if(isset($_POST['update'])) {
	
		//get age and gender when updating
		$res3 = mysql_query('SELECT age,gender from student where id='.$id.'');
		if($res3) {
			$row3 = mysql_fetch_array($res3);
			$age = $row3['age'];
			$gender = $row3['gender'];
		}
	
		$hangval = $_POST['hang'];
		$chinupval = $_POST['chinup'];
		//check date validity
		if(!isset($_POST['daydate']) || $_POST['daydate'] == "") {
			$daydate = false;
		}else {
			if(strtotime($_POST['daydate']) > strtotime($date) || strtotime($_POST['daydate']) < strtotime($start)) {
				$daydate = false;
			}else {
				//valid date
				$daydate = true;
			}
		}
		
		if(isset($_POST['pushup']) && $_POST['pushup'] != "" && (!is_int_val($_POST['pushup']) || $_POST['pushup'] < 0)) {
			$pushup = false;
		}
		if(isset($_POST['situp']) && $_POST['situp'] != "" && (!is_int_val($_POST['situp']) || $_POST['situp'] < 0)) {
			$situp = false;
		}
		if(isset($_POST['chinup']) && $_POST['chinup'] != "" && (!is_int_val($_POST['chinup']) || $_POST['chinup'] < 0) ) {
			$chinup = false;
		}
		if(isset($_POST['hang']) && $_POST['hang'] != "" && (!is_numeric($_POST['hang']) || $_POST['hang'] < 0) ) {
			$hang = false;
		}
		if(isset($_POST['sitreach1']) && $_POST['sitreach1'] != "" && !is_numeric($_POST['sitreach1'])) {
			$sr1 = false;
		}
		if(isset($_POST['sitreach2']) && $_POST['sitreach2'] != "" && !is_numeric($_POST['sitreach2'])) {
			$sr2 = false;
		}
		if(isset($_POST['height']) && $_POST['height'] != ""  && (!is_numeric($_POST['height']) || $_POST['height'] < 0)) {
			$height = false;
		}
		if(isset($_POST['mass']) &&  $_POST['mass'] != "" && (!is_numeric($_POST['mass']) || $_POST['mass'] < 0)) {
			$mass = false;
		}
		if(isset($_POST['waist']) && $_POST['waist'] != "" && (!is_numeric($_POST['waist']) || $_POST['waist'] < 0)) {
			$waist = false;
		}
		if(isset($_POST['hip']) && $_POST['hip'] != "" && (!is_numeric($_POST['hip']) || $_POST['hip'] < 0)) {
			$hip = false;
		}
		//if(!isset($_POST['bmi_rating'])) {
			//$rating = false;
		//}
		if($_POST['pushup'] == "" && $_POST['situp'] == "" && $_POST['chinup'] == "" && $_POST['hang'] == "" && $_POST['sitreach1'] == "" && $_POST['sitreach2'] == "" && $_POST['height'] == "" && $_POST['mass'] == "" && $_POST['waist'] == "" && $_POST['hip'] == "") {
			$none = true; 
		}else {
			//one field at least was correctly entered, so insert NULL's whereever there was an empty fields
			if($_POST['pushup'] == "") {
				$pushupval = "NULL";
			}else {
				$pushupval = $_POST['pushup'];
			}
			if($_POST['situp'] == "") {
				$situpval = "NULL";
			}else {
				$situpval = $_POST['situp'];
			}
			if($_POST['chinup'] == "") {
				$chinupval = "NULL";
			}else {
				$chinupval = $_POST['chinup'];
			}
			if($_POST['hang'] == "") {
				$hangval = "NULL";
			}else {
				$hangval = $_POST['hang'];
			}
			if($_POST['sitreach1'] == "") {
				$sr1val = "NULL";
			}else {
				$sr1val = $_POST['sitreach1'];
			}
			if($_POST['sitreach2'] == "") {
				$sr2val = "NULL";
			}else {
				$sr2val = $_POST['sitreach2'];
			}
			if($_POST['height'] == "") {
				$heightval = "NULL";
			}else {
				$heightval = $_POST['height'];
			}
			if($_POST['mass'] == "") {
				$massval = "NULL";
			}else {
				$massval = $_POST['mass'];
			}
			if($_POST['waist'] == "") {
				$waistval = "NULL";
			}else {
				$waistval = $_POST['waist'];
			}	
			if($_POST['hip'] == "") {
				$hipval = "NULL";
			}else {
				$hipval = $_POST['hip'];
			}		
		}
		
		if($_POST['mass'] != "" && $_POST['height'] != "") {
			$bmi = round($_POST['mass'] / pow(($_POST['height']/100),2),3);
		}else {
			$bmi = 'NULL';
		}
		if($_POST['waist'] != "" && $_POST['hip'] != "") {
			$ratio = round($_POST['waist'] / ($_POST['hip']),3);
		}else {
			$ratio = 'NULL';
		}
		$wh_rating = getWHRating($age,$gender,$ratio);
		$bmi_rating = getBMIRating($bmi);
		
		//validate the data and insert/update
		if($pushup!==false && $situp!==false && $chinup!==false && $hang!==false && $sr1!==false && $sr2!==false && $height!==false && $mass!==false && $none!==true && $daydate!==false && $waist!==false && $hip!==false) {
			//UPDATING 
			if($_POST['exists'] == true) {
				$res = mysql_query('UPDATE fitness_test SET pushup='.$pushupval.', situp='.$situpval.', chinup='.$chinupval.', hang='.$hangval.', sitreach1='.$sr1val.', sitreach2='.$sr2val.', height='.$heightval.', mass='.$massval.',bmi='.$bmi.',bmi_rating="'.$bmi_rating.'",daydate="'.$_POST['daydate'].'",waist='.$waistval.',hip='.$hipval.',ratio='.$ratio.',wh_rating="'.$wh_rating.'" WHERE subject_id='.$_SESSION['username'].' AND group_id="'.$_SESSION['group_name'].'" AND test_num='.$_POST['testnum'].'');
			}else {
				$res = mysql_query('INSERT INTO fitness_test(subject_id,group_id,daydate,test_num,pushup,situp,chinup,hang,sitreach1,sitreach2,height,mass,bmi,bmi_rating,waist,hip,ratio,wh_rating) VALUES('.$_SESSION['username'].',"'.$_SESSION['group_name'].'","'.$_POST['daydate'].'",'.$_POST['testnum'].', '.$pushupval.','.$situpval.','.$chinupval.','.$hangval.','.$sr1val.','.$sr2val.','.$heightval.','.$massval.','.$bmi.',"'.$bmi_rating.'",'.$waistval.','.$hipval.','.$ratio.',"'.$wh_rating.'" )');
			}
			if($res) {
				$updated = true;
			}else {
				//echo mysql_error();
			}
		}
		
	}
	
	//check end date to validate post-diary page
	$res = mysql_query('SELECT finish FROM class WHERE name="'.$_SESSION['group_name'].'"');
	if($res) {
		$row = mysql_fetch_array($res);
		$finish = $row['finish'];
		if(strtotime($finish) <= strtotime(date('Y-m-d'))) {
			$ended = true;
		} 
	}else {
		echo mysql_error();
	}	
	
	//used to view previous tests
	$result = mysql_query('SELECT * FROM fitness_test WHERE subject_id='.$id.' AND group_id="'. $class .'" AND test_num='.$_GET['testnum'].'');
?>

<! Initialise the jquery datepicker fields>
<script type="text/javascript"> 
	function unlockDate() {
		$('#daydate').datepicker({ dateFormat: 'yy-mm-dd' });
	}
	
	function showBMI() {
		document.getElementById("bmi_div").style.display = "block";
		document.getElementById("ratio_div").style.display = "block";
		document.getElementById("bmi_rating_div").style.display = "block";
		document.getElementById("wh_rating_div").style.display = "block";				
	}
	
	function unlock() {	
		document.getElementById("daydate").readOnly = !document.getElementById("daydate").readOnly;
		document.getElementById("pushup").readOnly = !document.getElementById("pushup").readOnly;
		document.getElementById("situp").readOnly = !document.getElementById("situp").readOnly;
		document.getElementById("chinup").readOnly = !document.getElementById("chinup").readOnly;
		document.getElementById("hang").readOnly = !document.getElementById("hang").readOnly;
		document.getElementById("sitreach1").readOnly = !document.getElementById("sitreach1").readOnly;
		document.getElementById("sitreach2").readOnly = !document.getElementById("sitreach2").readOnly;
		document.getElementById("height").readOnly = !document.getElementById("height").readOnly;
		document.getElementById("mass").readOnly = !document.getElementById("mass").readOnly;
		document.getElementById("waist").readOnly = !document.getElementById("waist").readOnly;
		document.getElementById("hip").readOnly = !document.getElementById("hip").readOnly;		
		if(document.getElementById("unlocker").childNodes[0].nodeValue == "Edit Test") {
			document.getElementById("unlocker").childNodes[0].nodeValue = 'Cancel';
			$('#daydate').datepicker({ dateFormat: 'yy-mm-dd' });	
			document.getElementById("edit_update").style.display = "block";			
		}else {
			document.getElementById("unlocker").childNodes[0].nodeValue = 'Edit Test';
			$('#daydate').datepicker('destroy');
			document.getElementById("edit_update").style.display = "none";						
		}
	}
</script>

<div id="main">
	<div id="dialog" style="display:none; font-size:10pt;"></div>
	<span class="reporting_breadcrumbs" id="breadcrumbs" style="margin:-5px 0px 10px 0px; font-size:8pt; font-family:Arial,Helvetica,sans-serif; float:left; color:black;">
		<!--span>Fitness Tests</span-->
	</span>	
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">Fitness Tests <?php if(isset($_GET['testnum'])) {echo ' : Test ' . $_GET['testnum'];} ?></p>
	</div>
	<div id="content" style="padding-top:0px;">
		<div style="background-color:rgb(245,245,245); border:solid 1px rgb(210,210,210); padding:20px; min-height:300px;">
		<?php
			//GET EXISTING TESTS
			$res = mysql_query('SELECT daydate,test_num FROM fitness_test WHERE subject_id = '.$id.' AND group_id = "'.$class.'" order by daydate');
			if($res) {
				$cnt = mysql_num_rows($res) + 1;
			}else {
				echo mysql_error();
			}
			
			if( (isset($_POST['testnum']) && $_POST['testnum'] > 0) || (isset($_GET['testnum']) && $_GET['testnum'] < $cnt && $_GET['testnum'] != "")) {
					//SUBMISSION SUCCESS NOTIFICATION
					if($updated) {
						echo '
						<script type="text/javascript">
							$("#dialog").dialog({ hide: \'slide\',position:[510,290], close: function(event,ui) { $("#dialog").dialog("destroy");}, 
								buttons: { "Ok": function() { $(this).dialog("close"); } }, title: \'Test Completed\', width: 300, height: 150 });
							document.getElementById("dialog").innerHTML = "Fitness test submitted successfully";
						</script>';
					}
					
					if(isset($_POST['testnum'])) {$num = $_POST['testnum'];}
					if(isset($_GET['testnum'])) {$num = $_GET['testnum'];}					
					//if the data is already there (used to decide whether to update or insert after the form is submitted without the need for further query)
					if(mysql_num_rows($result) > 0) {
						$exists = true;
					}else {
						$exists = false;
					}
					$row = mysql_fetch_array($result);
					echo '<div class="blue_form" style="margin-left:auto; margin-right:auto; background-color:white; border:solid 1px rgb(210,210,210);">
					<form action="fitness_test.php?testnum='.$num.'" id="test_form" method="post" onsubmit="return validate_fitness_test(this)">';

						//test date
						echo '<span id="daydatelabel">Test Date (required)</span> <br/> 
						<input type="text" name="daydate" id="daydate" value="'.$row['daydate'].'"';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo ' style="margin-top:5px; width:150px;"/>';
						if($daydate === false) {
							echo '<b style="margin-left:30px; color:red;">Please enter the test date.</b>';							
						}
											
						//push up test
						echo '<br/><br/><span id="pushuplabel">Push up\'s in 30 seconds</span> <br/> 
						<input type="text" name="pushup" id="pushup" value="'.$row['pushup'].'"';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo ' style="margin-top:5px; width:150px;"/>';
						if($pushup === false) {
							echo '<b style="margin-left:30px; color:red;">Number of push up\'s must be an integer value.</b>';
						}
						if($none == true) { 
							echo '<b style="margin-left:30px; color:red;">At least one field must be completed.</b>';
						}
						if($updated) {
							//echo '<b style="margin-left:30px; color:red;">Data successfully updated.</b>';										
						}	
						
						//sit up test
						echo '<br/><br/><span id="situplabel">Sit Up Cadence Test</span><br/> 
						<input type="text" name="situp" id="situp"';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo 'value="'.$row['situp'].'" style="margin-top:5px; width:150px;"/>';
						if($situp === false) {
							echo '<b style="margin-left:30px; color:red;">Number of sit up\'s must be an integer value.</b>';
						}
					
						//chin up test
						echo '<br/><br/><span id="chinuplabel">Number of Chin Up\'s</span><br/>
						<input type="text" name="chinup" id="chinup" value="'.$row['chinup'].'" ';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo ' style="margin-top:5px; width:150px;"/>';
						if($chinup === false) {
							echo '<b style="margin-left:30px; color:red;">Number of chin up\'s must be an integer value.</b>';
						}
						
						//hanging test
						echo '<br/><br/><span id="hanglabel">Hang Duration (sec)</span><br/>
						<input type="text" name="hang" id="hang" value="'.$row['hang'].'" '; 
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo' style="margin-top:5px; width:150px;"/>';
						if($both === false) {
							echo '<b style="margin-left:30px; color:red;">You need to specify number of chin ups and/or hang duration.</b>';							
						}else if($hang === false) {
							echo '<b style="margin-left:30px; color:red;">You need to specify a hang duration.</b>';
						}
						
					
						//sit and reach tests
						echo '<br/><br/>
						<span id="sitreach1label">Sit & Reach Trial 1 (cm)</span><br/> 
						<input type="text" name="sitreach1" id="sitreach1" value="'.$row['sitreach1'].'"';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo' style="margin-top:5px; width:150px;"/>';
						if($sr1 === false) {
							echo '<b style="margin-left:30px; color:red;">Sit and reach must be a numeric value.</b>';
						}
						echo'<br/><br/>
						<span id="sitreach2label">Sit & Reach Trial 2 (cm)</span><br/>
						<input type="text" name="sitreach2" id="sitreach2" value="'.$row['sitreach2'].'"';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo 'style="margin-top:5px; width:150px;"/>';
						if($sr2 === false) {
							echo '<b style="margin-left:30px; color:red;">Sit and reach must be a numeric value.</b>';
						}
					
						//height
						echo'<br/><br/>
						<span id="heightlabel">Height (cm)</span><br/>
						<input type="text" name="height" id="height" value="'.$row['height'].'"';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo' style="margin-top:5px; width:150px;"/>';
						if($height === false) {
							echo '<b style="margin-left:30px; color:red;">Height must be a numeric value.</b>';
						}
					
						//mass
						echo'<br/><br/>
						<span id="masslabel">Mass (kg)</span><br/>
						<input type="text" name="mass" id="mass" value="'.$row['mass'].'"';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo ' style="margin-top:5px; width:150px;"/>';
						if($mass === false) {
							echo '<b style="margin-left:30px; color:red;">Mass must be a numeric value.</b>';
						}
						
						//BMI
						echo '<div id="bmi_div" style="display:none; margin:0px; padding:0px">
						<br/>
						<span id="bmilabel">Body Mass Index</span><br/>
						<input type="text" name="bmi" disabled="disabled" id="bmi" value="'.$row['bmi'].'" style="margin-top:5px; width:150px;"/>
						</div>';
						
						//BMI RATING
						echo'<div id="bmi_rating_div" style="display:none; margin:0px; padding:0px">
						<br/><span id="bmi_rating_label">BMI Risk Rating</span><br/>
						<input type="text" name="bmi_rating" id="bmi_rating" value="'.$row['bmi_rating'].'" disabled="disabled" style="margin-top:5px; width:150px;"/>
						</div>';
						
						//WAIST
						echo'<div style="margin:0px; padding:0px;">
						<br/>
						<span id="waistlabel">Waist Measurement (cm)</span><br/>
						<input type="text" name="waist" id="waist" value="'.$row['waist'].'"';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo ' style="margin-top:5px; width:150px;"/>';
						if($waist === false) {
							echo '<b style="margin-left:30px; color:red;">Waist measurement must be a positive numeric value.</b>';
						}
						echo '</div>';
						
						//HIP
						echo'<div style="margin:0px; padding:0px;"><br/>
						<span id="hiplabel">Hip Measurement (cm)</span><br/>
						<input type="text" name="hip" id="hip" value="'.$row['hip'].'"';
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {echo 'readonly="readonly"';}
						echo ' style="margin-top:5px; width:150px;"/>';
						if($hip === false) {
							echo '<b style="margin-left:30px; color:red;">Hip measurement must be a positive numeric value.</b>';
						}
						echo '</div>';
						
						//WH RATIO
						echo'<div id="ratio_div" style="display:none; margin:0px; padding:0px">
						<br/><span id="ratiolabel">Waist-to-Hip Ratio</span><br/>
						<input type="text" name="ratio" id="ratio" value="'.$row['ratio'].'" disabled="disabled" style="margin-top:5px; width:150px;"/>
						</div>';
						
						//WH RATING
						echo'<div id="wh_rating_div" style="display:none; margin:0px; padding:0px">
						<br/><span id="wh_rating_label">Waist-to-Hip Risk Rating</span><br/>
						<input type="text" name="ratio" id="ratio" value="'.$row['wh_rating'].'" disabled="disabled" style="margin-top:5px; width:150px;"/>
						</div>';
						
						//hidden date fields for current_date and start_date (used in js validation)
						echo '
						<input type="hidden" name="start_date" value="'.$start.'"/>
						<input type="hidden" name="current_date" value="'.$date.'"/>
						<input type="hidden" name="exists" value="'.$exists.'"/>';

						//bmi rating here
						if(isset($_GET['testnum']) && $_GET['testnum'] < $cnt) {
							echo '
							<script type="text/javascript">
								document.getElementById("main").getElementsByTagName(\'span\')[0].innerHTML = "<a href=\"fitness_test.php\">Fitness Tests</a>";
								document.getElementById("welcome-box").getElementsByTagName(\'p\')[0].innerHTML += " : '.date("F j, Y",strtotime($row['daydate'])).'";
								document.getElementById("main").getElementsByTagName(\'span\')[0].innerHTML += " : Test '.$_GET['testnum'].'" ;
							</script>
							<input type="hidden" name="testnum" value="'.$_GET['testnum'].'">
							<br/><br/><input type="submit" name="update" id="edit_update" style="display:none;" value="Update Data"/>';
						}else {
							echo '
							<script type="text/javascript">
								document.getElementById("main").getElementsByTagName(\'span\')[0].innerHTML = "<a href=\"fitness_test.php\">Fitness Tests</a>";							
								document.getElementById("welcome-box").getElementsByTagName(\'p\')[0].innerHTML += " : Test '.$_POST['testnum'].'";
								document.getElementById("main").getElementsByTagName(\'span\')[0].innerHTML += " : Test '.$_POST['testnum'].'";								
							</script>
							<input type="hidden" name="testnum" value="'.$_POST['testnum'].'">
							<br/><br/><input type="submit" name="update" value="Enter Data"/>';
						}
					echo '</form>';
					
					//adjust the form according to whether it is a new test or a test in editing mode
					if(isset($_POST['testnum']) && $_POST['testnum'] > 0) { echo '<script type="text/javascript">unlockDate();</script>'; }					
					if(isset($_GET['testnum'])) {
						echo '<script type="text/javascript">showBMI();</script>';
						echo '<br/><button onClick="unlock();" id="unlocker" value="locked">Edit Test</button>';						
					}
					echo '</div><br/><br/>
					<a href="fitness_test.php" class="links" style="margin-right:20px;">Back to Fitness Tests</a><a href="home.php" class="links">Home</a>';
				
			}else {
			//lists all old tests for viewing and allows new tests to be entered.
				if($class != "") {
					echo '<div class="blue_form" style="height:auto; margin-left:auto; margin-right:auto; text-align:left; font-size:10pt; background-color:white; border:solid 1px rgb(210,210,210);">';
					if($cnt > 0) { echo $cnt-1 . ' Tests Completed <span id="expand">[ + ]</span><br/><br/>';}
					echo '<div id="tests" style="display:none; width:100%; height:auto; margin-bottom:10px; float:left;">';
						while($row = mysql_fetch_array($res)) {
							echo '
							<div style="width:200px;background-color:white;">
							<a style="font-weight:normal; text-decoration:none; color:black; float:left;" href="fitness_test.php?testnum='.$row['test_num'].'"> 
								Test ' . $row['test_num'] . ': ' . date("F j, Y",strtotime($row['daydate'])) . '
							</a>
							<hr style="width:150px; margin:0px 0px 5px 0px; border:none; float:left; background-color:rgb(200,200,200); height:1px;"/>
							</div>';
						}
					echo '</div>';?>
				
					<! EXPANDING DROP MENU CONTAINING PRIOR TESTS->
					<script>
						$('#expand').click(function() {
						  if($('#expand').text() == "[ + ]") {
		  					 $('#tests').slideDown('slow');
						 	 $('#expand').text("[ - ]");
					 	  }else {
		  					 $('#tests').slideUp('slow');				 	  
						 	 $('#expand').text("[ + ]");				 	  
					 	  }
						});
					</script>
				
					<?php
				
					echo '
					<form action="fitness_test.php" method="POST">
						<input type="hidden" name="testnum" value="'.$cnt.'">
						<input type="submit" name="new" value="New Test">	
					</form>
					</div><br/>
					<a href="home.php" class="links">Back to Home</a>';
				}else {
					echo '<div style="background-color:white; border:1px solid rgb(210,210,210); padding:20px; 	-moz-border-radius:10px; -webkit-border-radius:10px;	">
					<span style="color:rgb(60,60,60); font-size:10pt;">Your group has not been set, please first select your group <a href="query_group.php" class="links">here</a>.</span>
					</div>';	
				}
			}
		?>
		</div>
	</div>
	<div id="right-nav" style="width:250px; margin-top:20px; height:320px; font-size:10pt; line-height:1.5;">
		<b>Fitness Tests</b><hr/>This is where you can enter data for fitness tests carried out during the eDiary period.<br/><br/>
		Any amount of data may be entered and there is no limit to the number of tests that may be carried out.
	</div>
</div>
<?php draw_footer(); ?>
