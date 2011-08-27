<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_admin();

	$id = $_SESSION['username'];
	$sql = "SELECT * FROM class WHERE admin_id=$id GROUP BY name";
	if($_SESSION['is_super'] == "true") {	
		$sql = "SELECT * FROM class GROUP BY name";
	}
	$result = mysql_query($sql);
	
	drawHeaderAdmin();
	drawNavAdmin();
?>
<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">Administrator: Add New Subject</p>
	</div>
	<div id="content">
		<?php
			if($result) {
				echo '<form action="create_subject.php" class="std_form" method="post" style="margin-left:auto; margin-right:auto;">
				Group:<br/> 
				<select name="class_name" style="width:185px;">
				<option value="">Please select a group</option>';
				while($row = mysql_fetch_array($result) ) {
					echo '<option value=\''.$row['name'].'\'>' . $row['name'] . '</option>';
				}
				echo '</select>';
				if($_GET['class'] == "false") {
					echo '<b style="color:red; margin-left:30px;">Group not specified.</b>';
				}
				echo '<br/><br/>Subject ID:<br/> 
				<input type="text" name="subject_id" />';
				if($_GET['id'] == "false") {
					echo '<b style="color:red; margin-left:30px;">ID not specified, or invalid.</b>';			
				}
				echo '<br/><br/>First Name:<br/> 
				<input type="text" name="first" />';
				if($_GET['first'] == "false") {
					echo '<b style="color:red; margin-left:30px;">First name not specified.</b>';			
				}
				echo '<br/><br/>Last Name:<br/> 
				<input type="text\" name="last" />';
				if($_GET['last'] == "false") {
					echo '<b style="color:red; margin-left:30px;">Last name not specified.</b>';			
				}
				echo '<br/><br/><input type="submit" Value="Add Subject"/>';
				if($_GET['create'] == "true") {
					if($_GET['add'] == "true") {
						echo '<b style="color:red; margin-left:115px;">User successfully added to the system.</b>';
					}else if($_GET['add'] == "false") {
						echo '<b style="color:red; margin-left:115px;">User successfully added to the system.</b>';
					}		
				}
				if($_GET['create'] == "false") {
					if($_GET['add'] == "true") {
						echo '<b style="color:red; margin-left:115px;">User exists, but successfully added to group.</b>';
					}else if($_GET['add'] == "false") {
						echo '<b style="color:red; margin-left:115px;">User already exists.</b>';
					}			
				}
				echo '</form>
				<br/><br/><a href="home_admin.php" class="links">Back to Admin Tasks</a>';
			}else {
				echo mysql_error();
			}
		?>
	</div>
	<div id="right-nav">
		<b>Add Subject</b><hr/>Here you can add a single subject to a group<br/><br/>
		The process must be repeated to add the subject to multiple groups<br/><br/>
		To add a group of subjects click <a href="upload.php">here</a>
	</div>
</div>
<?php draw_footer(); ?>
