<?php
	include 'layout.php';
	check_admin();
	drawHeaderAdmin();
	drawNavAdmin();
?>

<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">
			Administrator: Create Administrator
		</p>
	</div>
	<div id="content">
		<?php if($_SESSION['is_super'] == "true" ) {
			//FORM START
			echo '
			<div class="std_form" style="margin-left:auto; margin-right:auto;">
				<form action="create_admin.php" method="post">
					ID:<br/> <input type="text" name="admin_id" />';
					if($_GET['id'] == "false") {echo '<b style="color:red; font-size:10pt; margin-left:30px;">ID must be a positive integer value.</b>'; }
					if($_GET['create'] == "true") {echo '<b style="color:red; font-size:10pt; margin-left:30px;">Admin was successfully created.</b>'; }
					if($_GET['create'] == "false") {echo '<b style="color:red; font-size:10pt; margin-left:30px;">Admin was not created, may already exist.</b>'; }
					echo'<br/><br/>
					First Name:<br/> 
					<input type="text" name="admin_first" />';
					if($_GET['first'] == "false") {echo '<b style="color:red; font-size:10pt; margin-left:30px;">First name was not specified.</b>'; }
					echo '<br/><br/>
					Last Name:<br/>
					<input type="text" name="admin_last" />';
					if($_GET['last'] == "false") {echo '<b style="color:red; font-size:10pt; margin-left:30px;">Last name was not specified.</b>'; }
					echo '<br/><br/>
					Password:<br/>
					<input type="text" name="admin_password" />';
					if($_GET['password'] == "false") {echo '<b style="color:red; font-size:10pt; margin-left:30px;">Password must be at least 4 characters long.</b>'; }
					echo'<br/><br/>
					Level 1 Administrator Privileges:<br/>
					<input type="checkbox" value="true" name="admin_issuper" /><br/><br/>
					<input type="submit" Value="Create User"/>
				</form>
			</div>';
			//FORM END
		}else {
			echo '<b>Can\'t perform this operation unless given level 1 admin privileges</b>';
		}
		echo '<br/><br/>
			  <a href="home_admin.php" class="links">Back to Admin Tasks</a>';
		?>
	</div>
	<div id="right-nav">
		<b>Create Administrator</b><hr/>Here you can create additional administrators<br/><br/>
		Standard (Level 2) administrators can only manage their assigned project groups and their associated subjects<br/><br/>
		Level 1 privileges may be granted to allow administrators to create and delete groups and administrators, and also to produce reports for any given group
	</div>
</div>
<?php draw_footer() ?>
