<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_student();

	$id = $_SESSION['username'];
	$sql = "SELECT * FROM classmap WHERE student_id = '$id'";
	
	$result = mysql_query($sql);
	if(mysql_num_rows($result) == 1) {
		header('location:home.php');
		exit();
	}

	draw_headerin();
	draw_navin();
?>

<div id="main">
	<div id="welcome-box">
		<img src="images/cyclist.jpg" style="width:280px; height:220px; float:right;"/>	
		<p style="font-weight:800; font-size:30px; margin-top:20px;"><?php echo date('Y'); ?> Exercise eDiary Group Selection </p>
		<p style="font-family:Georgia,serif; color:#564C23">You are currently registered for multiple groups within the eDiary system.<br/><br/>To enter data please select a group.</p>
	</div>
	<div id="login_box">
		<?php
			while($row = mysql_fetch_array($result)) {
				echo
				"<form action=\"group_confirm.php\" method=\"post\" style=\"margin-left:30px;\">
					<input type=\"hidden\" name=\"group_name\" Value=\"".$row['class_name']."\" />
					<input type=\"submit\" Value=\" " . $row['class_name'] . "\" style=\"border:solid 1px gray; width:360px; height:40px; background-color:#E9FAFE; font-weight:bold; text-decoration:none; text-align:center; \"/><br/><br/>
				</form>";
			}
		?>
	</div>
	<div id="login_info"><b>Please select which group you wish to enter data for. <br/><br/></b>Note: You may change this at any time once logged in to the system.</div>
	<!--div id="right-nav" style="width:250px;">Some<br/><br/>Other<br/><br/>Links<br/><br/>Or<br/><br/>General<br/><br/>Information</div-->
</div>
<?php draw_footer(); ?>
		
