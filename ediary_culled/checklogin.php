<?php
include 'config.php';
include 'connect.php';
include 'layout.php';
$tbl_name = 'student';

// username and password sent from form
$myusername=$_POST['student_id'];
$mypassword=$_POST['student_pw'];

// To protect MySQL injection
//$myusername = stripslashes($myusername);
//$mypassword = stripslashes($mypassword);
//$myusername = mysql_real_escape_string($myusername);
//$mypassword = mysql_real_escape_string($mypassword);
$myusername = escape_data($myusername);
$mypassword = escape_data($mypassword);
if(!is_int_val($myusername)) {	header("location:login.php?login=false"); exit(); }
$sql="SELECT * FROM $tbl_name,classmap WHERE binary(id)='$myusername' AND binary(password)='$mypassword' AND id=student_id";
$result = mysql_query($sql);

// Mysql_num_row is counting table row
$count = mysql_num_rows($result);

// If result matched $myusername and $mypassword
if($count >0) {
		//Register $myusername, $mypassword and redirect to file "home.php"
		$row = mysql_fetch_array($result);
		session_register("myusername");
		session_register("mypassword");
		session_register("user_type");
		session_register("group_name");
		$_SESSION['username'] = $myusername;
		$_SESSION['password'] = $mypassword;
		$_SESSION['user_type'] = "student";
		$_SESSION['first'] = $row['first'];	
		$_SESSION['last'] = $row['last'];
		//lock all days that are currently unlocked and have data entered
		$res = mysql_query('DELETE FROM exception WHERE EXISTS(SELECT * FROM training_records2 tr2 WHERE tr2.student_id=exception.subject_id AND tr2.daydate=exception.daydate AND tr2.class=exception.group_id AND tr2.student_id='.$_SESSION['username'].')');
		$res = mysql_query('DELETE FROM exception WHERE EXISTS(SELECT * FROM training_records1 tr1 WHERE tr1.student_id=exception.subject_id AND tr1.daydate=exception.daydate AND tr1.class=exception.group_id AND tr1.student_id='.$_SESSION['username'].')');
	if($count==1){
		//if the student is in only one group
		$_SESSION['group_name'] = $row['class_name'];
		header("location:home.php");
	}elseif($count > 1) {
		//if the student is in more that one group
		header("location:query_group.php");
	}

}else {
	header("location:login.php?login=false");
}
?>
