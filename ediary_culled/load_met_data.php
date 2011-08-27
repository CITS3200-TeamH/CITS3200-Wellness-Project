<?php
	include 'config.php';
	include 'connect.php';
	include 'layout.php';
	check_admin();
	drawHeaderAdmin();
	drawNavAdmin();
?>

<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">
			Administrator: Upload Activities Listing
		</p>
	</div>
	<div id="content">
		<?php
		if($_SESSION['is_super'] == "true" ) {
			if(isset($_POST['submit'])) {
			
				//remove existing entries
				$res = mysql_query('DELETE FROM compcodes');
				if(!$res) { echo mysql_error(); }
			
				//init upload info and counters
				$target_path = "uploads/";
				$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
				
				//try to upload
				if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
					//the file was successfully uploaded
					$upload_status = true;
				} else{
					echo "<b style=\"color:red;\">The file could not be uploaded</b>";
				}
				
				$myFile = $target_path;
				echo '<div style="width:auto; height:300px; overflow-y:auto;">';				
				if (($fh = fopen($myFile, "r")) !== FALSE) {
					while (($data = fgetcsv($fh, 1000, ",")) !== FALSE) {
						$num = count($data);
						if($data[3] == '') { $cardio = "false";}else{$cardio = "true";}
						if($data[4] == '') { $muscle = "false";}else{$muscle = "true";}
						if($data[5] == '') { $flex = "false";}else{$flex = "true";}
						if($data[6] == '') { $body = "false";}else{$body = "true";}
						$data[2] = addslashes($data[2]);
						$data[7] = addslashes($data[7]);
						$data[8] = addslashes($data[8]);
						$data[10] = addslashes($data[10]);																		
						$query = 'INSERT INTO compcodes VALUES('.$data[0].','.$data[1].',\''.$data[2].'\','.$cardio.','.$muscle.','.$flex.','.$body.',\''.$data[7].'\',\''.$data[8].'\',\''.$data[10].'\')';
						$res = mysql_query($query);
						if($res) {
							echo '
							<span style="font-size:8pt;">
								Added Activity: '.$data[0].', '.$data[1].', '.$data[2].', '.$cardio.', '.$muscle.', '.$flex.', '.$body.', '.$data[7].', '.$data[8].', '.$data[10].'
							</span><br/>';	
						}else {
							echo mysql_error() . '<br/>';
						}
					}
					fclose($handle);
				}
				
				echo '</div>';
				if($upload_status) {
					echo '
					<div style="width:auto; margin-top:20px; height:50px; color:green; text-align:center;">
						Activities Uploaded Successfully!
					</div>';
				}
			}else {
				echo '
				<div class="std_form" style="margin-left:auto; margin-right:auto;">
					<form enctype="multipart/form-data" action="load_met_data.php" method="POST">
						<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
						<br/><br/>Choose an Activities List to Upload:<br/><br/> 
						<input name="uploadedfile" type="file" style="margin-top:5px;"/>';
						if($_GET['file'] == "false") {
							echo '<b style="color:red; margin-left:30px;">No File Specified.</b>';
						}
						echo '<br/><br/><input type="submit" name="submit" value="Upload File" />
					</form>
				</div>';
			}
		}else {
			echo '<b>Can\'t perform this operation unless given Level 1 administrative privileges</b>
			<br/><br/><a href="home_admin.php" class="links">Back to Admin Tasks</a>';
		}
		echo '<br/><br/><a href="home_admin.php" class="links">Back to Admin Tasks</a>';
		?>
	</div>
	<div id="right-nav">
	<b>Upload Activities</b><hr/>
	This page facilitates the uploading of the activities csv.<br/><br/>
	Note that uploading a new csv file will first remove all existing activities, and then upload the new listing.
	</div>
</div>

<?php draw_footer();
