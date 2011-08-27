<?php
	include 'config.php';
	include 'connect.php';
	$type = $_GET['type'];
	$res = mysql_query("SELECT * FROM compcodes WHERE type LIKE '$type%'");
	if($res) {	
		$arr = mysql_fetch_array($res);
		echo $arr;
		exit();
	}
	echo mysql_error();
?>
