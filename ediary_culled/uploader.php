<?php
	//validate session
	include 'layout.php';
	check_admin();

	//validate form data sent over, if bad redirect back with error msg
	if($_POST['class_name'] == '') {
		header("location:upload.php?class=false"); 
	}elseif($_FILES['uploadedfile']['name'] == '') {
		header("location:upload.php?file=false"); 
	}

	//includes for db connection
	include 'config.php';
	include 'connect.php';
	$tbl_name = 'student';

	//init upload info and counters
	$target_path = "uploads/";
	$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
	$st_success = 0;
	$st_fail = 0;
	$cl_success = 0;
	$cl_fail = 0;
	$stfail_arr = array();
	$stsuccess_arr = array();

	//draw headers
	drawHeaderAdmin();
	drawNavAdmin();
?>
<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">Administrator: Add Subject Group</p>
	</div>
	<div id="content_wide">


	<?php
	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		//the file was successfully uploaded
	} else{
		echo "<b style=\"color:red;\">The file could not be uploaded</b>";
	}

	//split file and insert students in db
	$myFile = $target_path;
	$fh = fopen($myFile, 'r');
	//get class name from form and admin id from session
	$class = $_POST['class_name'];
	$class = stripslashes($class);
	
	$admin_id = $_SESSION['username'];

	while( ($data=fgets($fh,4096)) != NULL) 
	{
		$tbl_name = 'student';
		list($id,$first,$last) = split(",",$data,3);
		$pw = '';
		$last = trim($last);
		if(strlen($last) >= 4) {
			$pw = $last[0].$last[1].$last[2].$last[3].$id[4].$id[5].$id[6].$id[7];
		}else {
			
			for($i=0;$i<strlen($last);$i++) {
				$pw .= $last[$i];
			}
			$pw .= $id[4].$id[5].$id[6].$id[7];
		}
		//insert into students table
		$sql="INSERT INTO $tbl_name (id,first,last,password,active) VALUES($id,'$first','$last','$pw',true)";
		if(mysql_query($sql) != 1) {
			$st_fail++;
			array_push($stfail_arr,$id);
			//echo "could not upload student: $first $last<br/>";
		}else {
			$st_success++;
			array_push($stsuccess_arr,$id);
			//echo "successfully uploaded student: $first $last<br/>";
		}

		//insert into classmap table
		$tbl_name = 'classmap';
		$sql='INSERT INTO '.$tbl_name.'(student_id, class_name) VALUES('.$id.',"'.$class.'")';
		if(mysql_query($sql) != 1) {
			$cl_fail++;
			//echo "could not upload student: $first $last to class $class<br/>";
		}else {
			$cl_success++;
			//echo "successfully uploaded student: $first $last to class $class<br/>";
		}
	}
	echo "<b style=\"color:red;\">Successfully uploaded subject list '" .$_FILES['uploadedfile']['name']. "'.</b><br/><br/>";
	echo "<b>Summary:</b><br/><br/>";
	echo "<b>" . $st_success . " subjects successfully uploaded.</b><br/>";
	echo "<b>" . $st_fail . " subjects were already present in the system.</b><br/><br/>";
	echo "<b>" . $cl_success . " subjects were assigned to the project group '" .$class. "'.</b><br/>";
	echo "<b>" . $cl_fail . " subjects were already present in the project group '" .$class. "'.</b><br/>";
	fclose($fh);
	unlink($myFile);

	echo '<br/><a href="home_admin.php">Back to Admin Tasks</a>
	</div>
</div>';
draw_footer();
?>
