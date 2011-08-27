<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_admin();
	$tbl_name = 'admin';

	//set variables from the post data
	$id = $_POST['admin_id'];
	$first = $_POST['admin_first'];
	$last = $_POST['admin_last'];
	//if checkbox was set to true for level 1 admin
	$password = $_POST['admin_password'];
	if(isset($_POST['admin_issuper'] ) ) {
		$is_super = $_POST['admin_issuper'];
	}else {
		$is_super = "false";
	}

	//check vars
	if($id == "" || !is_int_val($id) || !is_numeric($id) || $id < 0) {
		header("location:new_admin.php?id=false");
		exit();
	}
	if($first == "") {
		header("location:new_admin.php?first=false");
		exit();
	}
	if($last == "") {
		header("location:new_admin.php?last=false");
		exit();
	}
	if($password == "" || strlen($password) < 4) {
		header("location:new_admin.php?password=false");
		exit();
	}

	//insert into admin table
	$sql=mysql_query("INSERT INTO $tbl_name (id,first,last,password,last_login,is_super) VALUES($id,'$first','$last','$password',NULL,$is_super)");
	if(!$sql) { 
		header("location:new_admin.php?create=false");
	}else {
		header("location:new_admin.php?create=true");
	}
	exit();
?>

