<?php
	include 'layout.php';
	check_admin();

	//validate form data sent over, if bad redirect back with error msg
	if($_POST['class_name'] == '') {
		header("location:new_student.php?class=false");
		exit();
	}elseif($_POST['subject_id'] == '' || !is_int_val($_POST['subject_id'])) {
		header("location:new_student.php?id=false");
		exit(); 
	}elseif($_POST['first'] == '') {
		header("location:new_student.php?first=false");
		exit();
	}elseif($_POST['last'] == '') {
		header("location:new_student.php?last=false");
		exit();
	}

	//includes for db connection
	include 'config.php';
	include 'connect.php';

	//set variables from the post data
	$id = $_POST['subject_id'];
	$first = escape_data($_POST['first']);
	$last = escape_data($_POST['last']);
	$class = $_POST['class_name'];

			$pw = $last[0].$last[1].$last[2].$last[3].$id[4].$id[5].$id[6].$id[7];
	//insert into student table
	$res = mysql_query("INSERT INTO student (id,first,last,password,active) VALUES($id,'$first','$last','$pw',true)");
	if(!$res) { 
		//try to insert into classmap table anyway
		$res = mysql_query("INSERT INTO classmap (student_id,class_name) VALUES($id,'$class')");
		if($res) { 
			header("location:new_student.php?create=false&add=true");
		}else {
			header("location:new_student.php?create=false&add=false");
		}
		exit();
	}
	//insert into classmap table
	$res = mysql_query("INSERT INTO classmap (student_id,class_name) VALUES($id,'$class')");
	if(!$res) { 
		header("location:new_student.php?create=true&add=false");
		exit();
	}
	header("location:new_student.php?create=true&add=true");
	exit();
?>

