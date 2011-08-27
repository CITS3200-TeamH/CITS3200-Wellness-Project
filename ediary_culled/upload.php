<?php
	//check session
	include 'layout.php';
	check_admin();
	
	//set up query
	include 'config.php';
	include 'connect.php';
	$id = $_SESSION['username'];
	if($_SESSION['is_super'] == "true") {
		$sql = "SELECT * FROM class GROUP BY name";
	}else {
		$sql = "SELECT * FROM class WHERE admin_id = '$id' GROUP BY name";
	}
	$result = mysql_query($sql);
	
	drawHeaderAdmin();
	drawNavAdmin();
?>

<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">
			Administrator: Add Subject Group
		</p>
	</div>
	<div id="content">
		<?php 
			if($result) {
				echo '
				<div class="std_form" style="margin-left:auto; margin-right:auto;">
					<form enctype="multipart/form-data" action="uploader.php" method="POST">
						<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
						Select a group to add the subjects to:<br/> 
						<select name="class_name" style="width:200px; margin-top:5px;">
						<option value="">Please Select A Group</option>';
						while($row = mysql_fetch_array($result) ) {
							echo '<option value="'.$row['name'].'">' . $row['name'] . '</option>';
						}
						echo '</select>';
						if($_GET['class'] == "false") {
							echo '<b style="color:red; margin-left:100px;">No Group Specified.</b>';
						}
						echo '<br/><br/>Choose a subject file to upload:<br/> 
						<input name="uploadedfile" type="file" style="margin-top:5px;"/>';
						if($_GET['file'] == "false") {
							echo '<b style="color:red; margin-left:30px;">No File Specified.</b>';
						}
						echo '<br/><br/><input type="submit" value="Upload File" />
					</form>
				</div>';
			}else {
				echo mysql_error();
			}
		?>
		<br/><br/>
		<a href="home_admin.php" class="links">Back to Admin Tasks</a>
	</div>
	<div id="right-nav" style="height:auto;">
		<b>Add Subjects</b><hr/>Here you can add a collection of subjects to a group by uploading a list of subjects in the form of a CSV file<br/><br/>
		Each line of the CSV file should be of the format "subject id, first name, last name"<br/><br/>
		The file may be uploaded to the same group multiple times, however existing subjects will not be removed from a group in this way; This must be done manually by clicking <a href="view_students.php">here</a>.
	</div>
</div>
<?php draw_footer(); ?>
