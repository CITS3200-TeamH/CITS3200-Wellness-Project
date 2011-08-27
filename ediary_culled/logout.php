<?php
	/*session_start();
	if($_SESSION['user_type'] == "admin") {
		$header_loc = "location:login_admin.php";
	}elseif($_SESSION['user_type'] == "student") {
		$header_loc = "location:login.php";	
	}*/
	session_start();
	$header_loc = "location:login.php";
	session_destroy();
	header($header_loc);
?>
