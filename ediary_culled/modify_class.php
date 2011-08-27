<?php
	include 'layout.php';
	check_admin();

	//prepare query
	include 'config.php';
	include 'connect.php';
	$tbl_name = 'class';
	$name = $_POST['class_name'];
	$name = stripslashes($name);
	$name = mysql_real_escape_string($name);
	$admin_id = $_POST['admin_id'];
	$start = $_POST['start_date'];
	$finish = $_POST['end_date'];
	$window = $_POST['window'];

	if($_POST['start_date'] != "") {
		if(!check_iso_date($start)) { header("location:view_group.php?group=$name&start=false"); exit(); }
	}else {
		header("location:view_group.php?group=$name&start=false");
		exit();
	}

	//check finish
	if($finish != "") {
		if(!check_iso_date($finish)) { header("location:view_group.php?group=$name&finish=false"); exit(); }
	}else {
		header("location:view_group.php?group=$name&finish=false");
		exit();
	}

	//check date order
	if(strtotime($start) > strtotime($finish)) {
		header("location:view_group.php?group=$name&dates=false");
		exit();
	}

	//check window
	if($_POST['window'] == "" || $_POST['window'] <= 0) {
		header("location:view_group.php?group=$name&window=false");
		exit();
	}
	
	//ok to insert or delete
	$delete = $_POST['delete_class'];
	if ($delete == "true") {
		//remove from groups
		$res = mysql_query("DELETE FROM class WHERE name='$name'");
		if(!$res) {
			header("location:view_group.php?group=$name&delete=false");
			exit();
		}
		
		//remove from classmap
		$res = mysql_query("DELETE FROM classmap WHERE class_name='$name'");
		if(!$res) {
			header("location:view_group.php?group=$name&delete=false");
			exit();		
		}
		
		//remove from rating_item_map
		$res = mysql_query("DELETE FROM rating_item_map WHERE groupname='$name'");
		if(!$res) {
			header("location:view_group.php?group=$name&delete=false");
			exit();		
		}
		
		//if all deleted correctly
		header("location:view_group.php?group=$name&delete=true");
		exit();
		
		//if students are only listed in this group, remove them (and all records) from the database too
	}

	//insert into admin table
	$res = mysql_query("UPDATE $tbl_name SET start='$start', finish='$finish', window=$window where name='$name'");
	if($res) {
		header("location:view_group.php?group=$name&update=true");
	}else{
		header("location:view_group.php?group=$name&update=false");
	}
	exit();
?>

