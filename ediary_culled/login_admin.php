<?php
	include 'layout.php';
	draw_headerout();
	draw_navout();
	
	//if the user is already logged in, redirect to home_admin
	session_start();
	if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == "admin" ) {
		header("location:home_admin.php");
	}
?>

<div id="main">
	<div id="welcome-box">
		<img src="images/cyclist.jpg" style="width:280px; height:220px; float:right;"/>	
		<p style="font-weight:800; font-size:30px;">2011 Exercise eDiary Administrator Login</p>
		<p style="font-family:Georgia,serif; color:#564C23">
		Welcome to the home of the Exercise eDiary for <?php echo date ('Y'); ?>.
		</p>
	</div>
	<div id="login_box">
		<form action="checkloginadmin.php" method="post" class="blue_form" style="float:left; margin-left:20px; width:335px;">
			Your UWA Staff ID Number :<br/> <input type="text" name="admin_id" /><br/><br/>
			Your Administrator Password: <br/><input type="password" name="admin_pw" /><br/><br/>
			<input type="submit" Value="Login to"/>
		</form>
		<div id="login_notification">
			<?php 	if( $_GET['login'] == 'false') {
					echo '<b style="color:red;">Incorrect username or password!</b>';
				}
			?>
		</div>
	</div>
	<div id="login_info"><b>Please log in to the system with your current user ID and password</b></div>
	<!--<div id="right-nav">Some<br/><br/>Other<br/><br/>Links<br/><br/>Or<br/><br/>General<br/><br/>Information</div>-->
</div>

<?php draw_footer(); ?>
