<?php
include 'layout.php';
check_admin();

//includes for db connection
include 'config.php';
include 'connect.php';
$tbl_name = 'class';

//set variables from the post data
$name = $_POST['class_name'];
$name = stripslashes($name);
$name = mysql_real_escape_string($name);
$admin_id = $_POST['admin_id'];
$start = $_POST['start_date'];
$finish = $_POST['end_date'];
$window = $_POST['window'];

//check group name
if($_POST['class_name'] == "") {
	header("location:new_group.php?name=false");
	exit();
}

//check name not used already
$res = mysql_query('SELECT name FROM class WHERE name=\''.$name.'\'');
if($res) {
	if(mysql_num_rows($res) > 0) {
		header("location:new_group.php?name_used=true");
		exit();
	}
}else {
	echo mysql_error();
}

//check start
if($_POST['start_date'] != "") {
	if(!check_iso_date($start)) { header("location:new_group.php?start=false"); exit(); }
}else {
	header("location:new_group.php?start=false");
	exit();
}

//check finish
if($finish != "") {
	if(!check_iso_date($finish)) { header("location:new_group.php?finish=false"); exit(); }
}else {
	header("location:new_group.php?finish=false");
	exit();
}

//check date order
if(strtotime($start) > strtotime($finish)) {
	header("location:new_group.php?dates=false");
	exit();
}

//check window
if($_POST['window'] == "" || $_POST['window'] <= 0) {
	header("location:new_group.php?window=false");
	exit();
}

//the data is valid, so insert in groups
$res = mysql_query("INSERT INTO $tbl_name (name, admin_id, start, finish, window) VALUES('$name',$admin_id,'$start','$finish',$window)");

//ADD ALL SELECTED RATING ITEMS TO THE 'rating_item_map' DB
$i = 1;
while($i <= $_POST['count']) {
	if(isset($_POST['item'.$i]) ) {
		$res = mysql_query('INSERT INTO rating_item_map(groupname,id) VALUES("'.$name.'",'.$_POST['item'.$i].')');
	}
	$i++;
} 
if($res) {
	header("location:new_group.php?create=true");
	exit();
}else {
	header("location:new_group.php?create=false");
	exit();
}
?>

