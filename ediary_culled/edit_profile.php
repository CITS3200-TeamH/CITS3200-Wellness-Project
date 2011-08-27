<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_student();
	draw_headerin();
	draw_navin();
	include 'validations.php';
	//check the sent data and update, or report error in the post data
	$incfilled = false;
	$notfilled = false;
	$lvlfilled = false;
	$lvlnotfilled = false;	
	if(isset($_POST['update'])) {
		if(!is_int_val($_POST['age']) || $_POST['age'] == "" || $_POST['age'] <= 0) {
			$age = false;
		}
		if(!isset($_POST['gender'])) {
			$gender = false;
		}
		if(isset($_POST['athletic'])){
			if($_POST['athletic'] == "false") {
				//verify that sport is empty
				if($_POST['sport'] != "") {
					//send error
					$incfilled = true;
				}
				if($_POST['level'] != "") {
					$lvlfilled = true;
				}
			}else {
				//verify that sport is filled out
				if($_POST['sport'] == "") {
					//send error
					$notfilled = true;
				}
				if($_POST['level'] == "") {
					//send error
					$lvlnotfilled = true;
				}
			}
		}else {
			$athletic = false;
		}
		if($age!==false && $gender!==false && $athletic!==false && $incfilled!==true && $notfilled!==true && $lvlfilled!==true && $lvlnotfilled!==true) {
			$res = mysql_query('UPDATE student SET age='.$_POST['age'].', gender=\''.$_POST['gender'].'\', athletic='.$_POST['athletic'].',sport="'.htmlentities($_POST['sport'],ENT_QUOTES,'UTF-8').'", level="'.$_POST['level'].'" WHERE id='.$_SESSION['username'].'');
			if($res) {
				$updated = true;
			}
		}
		
	}
	$result = mysql_query('SELECT * FROM student WHERE id='.$_SESSION['username'].'');
?>
<div id="main">
	<div id="dialog" style="display:none;"></div>
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px; margin-top:20px;">Personal Profile</p>
	</div>
	<div id="content" style="padding-top:0px;">
		<div style="background-color:rgb(245,245,245); border:solid 1px rgb(210,210,210); padding:20px; min-height:300px;">	
		<?php
			if($result) {
			$row = mysql_fetch_array($result);
				echo '
				<form action="edit_profile.php" class="blue_form" method="post" style="margin-left:auto; margin-right:auto; background-color:white; border:solid 1px rgb(210,210,210);" onsubmit="return validateProfile(this);">
					<div style="border:dotted 1px rgb(200,200,200); border-width:0px 0px 1px 0px; margin-left: 0px; padding:0px 0px 20px 0px; width:500px;">
					Age<br/> 
					<input type="text" name="age" id="age" value="'.$row['age'].'" style="margin-top:10px; width:150px;"/>';
					if($age === false) {
						echo '<b style="margin-left:30px; color:red;">Age must be a positive integer value.</b>';
					}
					if($updated) {
						echo '<span id="notify" style="margin-left:30px; color:red; font-weight:normal;">Profile Data Saved Successfully.</span>';										
					}
					echo '</div>';
					echo'<br/>
					Gender<br/>
					<span style="font-weight:normal;">Male</span>
					<input type="radio" name="gender"';
					if($row['gender'] == "M") { echo 'checked="checked"'; } 
					echo' value="M" style="margin-top:10px;"/>
					<span style="font-weight:normal;">Female</span>
					<input type="radio" name="gender"';
					if($row['gender'] == "F") { echo 'checked="checked"'; } 
					echo ' value="F"/>';
					if($gender === false) {
						echo '<b style="margin-left:58px; color:red;">Please select your gender.</b>';
					}
					echo '<br/><br/>
					<div style="border:dotted 1px rgb(200,200,200); border-width:1px 0px 1px 0px; margin-left: 0px; padding:10px 0px 20px 0px; width:500px;">
					Are you an athlete?<br/>
					<span style="font-weight:normal;">Yes</span> <input type="radio" name="athletic" value="true" ';
					if($row['athletic'] == true) { echo 'checked="checked"'; }
					echo ' style="margin-top:10px;"/>
					<span style="font-weight:normal;">No</span> <input type="radio" name="athletic" value="false" '; 
					if($row['athletic'] == false) { echo 'checked="checked"'; }
					echo' style="margin-top:10px;"/>';
					if($athletic === false) { 
						echo '<b style="margin-left:90px; color:red;">Are you an athlete?</b>';					
					}
					echo '<br/><br/>
					If so, in what sport/activity?<br/>
					<input type="text" name="sport" value="'.$row['sport'].'" style="margin-top:10px; width:150px;"/>';
					if($incfilled) {
						echo '<br/><b style="margin-left:0px; color:red;">You must be athletic to enter a sport.</b>';											
					}else if($notfilled) {
						echo '<br/><b style="margin-left:0px; color:red;">Please enter your chosen sport.</b>';										
					}
					echo '<br/><br/>
					At what level (competitive,social,etc.)<br/><br/>
					<select id="level" name="level" style=" width:250px;">
						<option value="" default>--</option>
						<option value="Social" ';
							if($row['level'] == "Social") {echo 'selected="selected"';}
							echo '>Social</option>
						<option value="State Competitions" '; 
							if($row['level'] == "State Competitions") {echo 'selected="selected"';}
							echo '>State Competitions</option>
						<option value="State Representatives/ National Competitions" '; 
							if($row['level'] == "State Representatives/ National Competitions") {echo 'selected="selected"';}
							echo '>State Representatives/ National Competitions</option>
						<option value="International Competitions" ';
							if($row['level'] == "International Competitions") {echo 'selected="selected"';}
							echo '>International Competitions</option>
						<option value="World Championships/ Olympic Games" ';
							if($row['level'] == "World Championships/ Olympic Games") {echo 'selected="selected"';}
							echo '>World Championships/ Olympic Games</option>																								
					</select>';
					if($lvlfilled) {
						echo '<br/><b style="margin-left:0px; color:red;">You must be athletic to enter a competition level.</b>';											
					}else if($lvlnotfilled) {
						echo '<br/><b style="margin-left:0px; color:red;">Please enter your level of competition.</b>';										
					}
					
					echo '</div>
					<br/>
					<input type="submit" name="update" value="Update Info"/>';
				echo '</form>
				<br/><br/>
				<a href="home.php" class="links">Back to Home</a>';
			}else {
				echo mysql_error();	
			}
		?>
		
		<! Initialise the jquery select menus>
		<script type="text/javascript">
			$(function(){
				$('#level').selectmenu({width:300});	
			});
		</script>
		
		</div>
	</div>
	<div id="right-nav" style="width:250px; min-height:250px; height:auto; margin-top:20px; font-size:10pt;">
		<b>Edit Profile</b><hr/>Here you can edit your personal information<br/><br/>
		This information may be updated at any time<br/><br/>If you are athletic, you must also specify the activity and at what level you participate.
	</div>
</div>
<?php draw_footer(); ?>
