<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_student();
	draw_headerin();
	draw_navin();
	//include 'validations.php';
	
	//check the sent data and update, or report error in the post data
	if(isset($_POST['update'])) {
	
		$res = mysql_query('SELECT password FROM student WHERE id='.$_SESSION['username'].'');
		if($res) {
			$row = mysql_fetch_array($res);
			$pass = $row['password'];
		}else {
			echo mysql_error();
		}
		
		$old = escape_data($_POST['old_pw']);
		if($pass != $old) {
			$old_pw = false;
		}	
		$new = escape_data($_POST['new_pw']);
		$con = escape_data($_POST['confirm_pw']);
		
		if($new != $con) {
			$new_pw = false;
		}else {
			if(strlen($new) < 4) {
				$size = false;
			}
		}
		//validate and update
		if($new_pw!==false && $old_pw!==false && $size!==false) {
			$res = mysql_query('UPDATE student SET password="'.$new.'" WHERE id='.$_SESSION['username'].'');
			if($res) {
				$updated = true;
			}else {
				echo mysql_error();
			}
		}
		
	}
?>
<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">Change Password</p>
	</div>
	<div id="content" style="padding-top:0px;">
		<div style="background-color:rgb(245,245,245); border:solid 1px rgb(210,210,210); padding:20px; min-height:300px;">		
		<?php
			echo '
			<form action="change_pw.php" class="blue_form" method="post" style="margin-left:auto; margin-right:auto; background-color:white; border:solid 1px rgb(210,210,210);">
				Enter Current Password<br/> 
				<input type="password" name="old_pw" value="" style="margin-top:5px; width:150px;"/>';
				if($old_pw === false) {
					echo '<b style="margin-left:30px; color:red;">Current password was incorrect.</b>';
				}
				if($updated) {
					echo '<b style="margin-left:30px; color:red;">Data successfully updated.</b>';										
				}	
				echo'<br/><br/>
				Enter New Password<br/>
				<input type="password" name="new_pw" value="" style="margin-top:5px; width:150px;"/>';
				if($new_pw === false) {
					echo '<b style="margin-left:30px; color:red;">Passwords do not match.</b>';
				}
				echo'<br/><br/>
				Confirm New Password<br/>
				<input type="password" name="confirm_pw" value="" style="margin-top:5px; width:150px;"/>';
				if($new_pw === false) {
					echo '<b style="margin-left:30px; color:red;">Passwords do not match.</b>';
				}
				if($size === false) {
					echo '<b style="margin-left:30px; color:red;">Password must be at least 4 characters long.</b>';
				}
				echo '<br/><br/><input type="submit" name="update" value="Change Password"/>';
			echo '</form>
			<br/><br/>
			<a href="home.php" class="links">Back to Home</a>';
		?>
		</div>
	</div>
	<div id="right-nav" style="width:250px; min-height:330px; height:auto margin-top:20px; font-size:10pt;">
		<b>Change Password</b><hr/>You may change your password as many times as you wish. Your user ID will always remain the same.<br/><br/>
		If you have forgotten your password, please contact your group administrator so that it may be reset.<br/><br/>
	</div>
</div>
<?php draw_footer(); ?>
