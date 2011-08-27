<?php
//validate session
include 'layout.php';
check_admin();
	
//includes for db connection
include 'config.php';
include 'connect.php';
$tbl_name = 'class';

$name = $_POST['class_name'];
//get the group variables to reinsert
$sql0 = "SELECT * FROM class WHERE name='$name'";
$result0 = mysql_query($sql0);
$row0 = mysql_fetch_array($result0);

//set variables from the post data and the query
$id = $_POST['admin_name'];
$start = $row0['start'];
$finish = $row0['finish'];
$window = $row0['window'];

//check group
if($name == "") {
	header("location:new_collaborator.php?group=false");
	exit();
}

//check administrator
if($id == "") {
	header("location:new_collaborator.php?id=false");
	exit();
}

//insert into class table
$res = mysql_query("INSERT INTO $tbl_name(name,admin_id,start,finish,window) VALUES('$name',$id,'$start','$finish',$window)");
if($res) {
	header("location:new_collaborator.php?create=true");
	exit();
}else {
	header("location:new_collaborator.php?create=false");
	exit();
}

?>
