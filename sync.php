<?php

	include "config.php";
	include "layout.php";
	include "connect.php";
	
	$tbl_name = "student";
	
	if (isset($_GET["username"]) && isset($_GET["password"])) {
		$username = escape_data($_GET['username']);
		$password = escape_data($_GET['password']);
		
		if (is_int_val($username)) {
			$sql="SELECT * FROM $tbl_name, classmap WHERE student.id='$username' AND student.password='$password' AND classmap.id=student_id";
			$result = mysql_query($sql);
		
			if (mysql_num_rows($result) != 0) {
				sync($username, $password);
			} else {
				echo "NO";
			}
		} else {
			echo "NO";
		}
	} else {
		echo "NO";
	}

	function sync($username, $password) {
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n";
		echo "<plist version=\"1.0\">\n";
		
		$result = mysql_fetch_array(mysql_query($sql));
		$lower = new DateTime($result["start"]);
		$upper = new DateTime($result["finish"]);
		$today = new DateTime(date("Y-m-d"));
		$window = $result["window"];
		
		if ($today <= $upper && $ $today >= $lower) {
			$sql = "SELECT * FROM training_records1 WHERE id='$username'";
			$fitnessData = mysql_query($sql);
			
			$sql = "SELECT * FROM training_records2 WHERE id='$username'";
			$ratingData = mysql_query($sql);
			
		} else {
			echo "outside of window";
		}
		
		
		
		
<array>
?>