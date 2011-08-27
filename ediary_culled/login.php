<?php
	include 'layout.php';
 	draw_headerout(); 
	draw_navout();
?>
<div id="main">
	<div id="welcome-box">
		<img src="images/cyclist.jpg" style="width:280px; height:220px; float:right;"/>	
		<p style="font-weight:800; font-size:30px;"><?php echo date('Y'); ?> Exercise eDiary Login</p>
		<p style="font-family:Georgia,serif; color:#564C23">
		Welcome to the home of the Exercise eDiary for <?php echo date ('Y'); ?>.
		</p>
	</div>
	<div id="login_box">
		<form action="checklogin.php" method="post" class="blue_form" style="float:left; margin-left:20px; width:335px;">
			Your UWA Student ID Number :<br/> <input type="text" name="student_id" /><br/><br/>
			Your Password: <br/><input type="password" name="student_pw" /><br/><br/>
			<input type="submit" Value="Login to"/>
		</form>
		<div id="login_notification">
			<?php 	if( $_GET['login'] == 'false') {
					echo '<b style="color:red; font-size:10pt">Incorrect username or password!</b>';
				}
			?>
		</div>
	</div>
	<div id="login_info"><b>Please log in to the system with your current user ID and password</b></div>
	<!--<div id="right-nav">Some<br/><br/>Other<br/><br/>Links<br/><br/>Or<br/><br/>General<br/><br/>Information</div>-->
</div>
<?php draw_footer(); ?>
