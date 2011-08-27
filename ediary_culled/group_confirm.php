<?php
	session_start();
	//set the session variable for group_name, and then redirect to home
	$_SESSION['group_name'] = $_POST['group_name'];
	header("location:home.php");
?>
