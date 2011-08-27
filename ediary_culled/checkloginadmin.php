<?php
include 'config.php';
include 'connect.php';
$tbl_name = 'admin';

//username and password sent from form
$myusername=$_POST['admin_id'];
$mypassword=$_POST['admin_pw'];

//to protect against MySQL injection
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);

$sql="SELECT * FROM $tbl_name WHERE id='$myusername' and binary(password)='$mypassword'";
$result=mysql_query($sql);

//get count
$count=mysql_num_rows($result);
$row = mysql_fetch_array($result);

// If result matched $myusername and $mypassword, table row must be 1 row
if($result) {
if($count==1){
	// Register $myusername, $mypassword and redirect to file "home_admin.php"
	session_register("myusername");
	session_register("mypassword");
	$_SESSION['username'] = $_POST['admin_id'];
	$_SESSION['password'] = $_POST['student_pw'];
	$_SESSION['user_type'] = "admin";
	$_SESSION['first'] = $row['first'];
	$_SESSION['last'] = $row['last'];
	if($row['is_super'] == 1) {
		$_SESSION['is_super'] = "true";
	}else {
		$_SESSION['is_super'] = "false";
	}
	header("location:home_admin.php");
}else {
	header("location:login_admin.php?login=false");
}
} else {

echo mysql_error();

}
?>
