<?php
//redirect if not a registered session
session_start();
if(!session_is_registered(myusername) || $_SESSION['user_type'] != "admin"){
	header("location:login.php");
}

//includes for db connection
include 'config.php';
include 'connect.php';
$tbl_name = 'student';
$id = $_POST['delete_student'];
$first = $_POST['first'];
$last = $_POST['last'];

//remove entry
$sql = "DELETE FROM $tbl_name WHERE id=$id";
if(mysql_query($sql) != 1) { 
	echo "could not remove student: $first" ." ". "$last<br/>";
}else {
	echo "successfully removed student: $first" ." ". "$last<br/>";
}
$tbl_name = 'classmap';
$sql = "DELETE FROM $tbl_name WHERE student_id=$id";
if(mysql_query($sql) != 1) { 
	echo "could not remove student: $first" ." ". "$last from groups<br/>";
}else {
	echo "successfully removed student: $first" ." ". "$last from all groups<br/>";
}
echo '<br/><a href="view_students.php">Back to Students<a/>';
echo '<br/><a href="home_admin.php">Back to Admin Tasks<a/>';

?>

