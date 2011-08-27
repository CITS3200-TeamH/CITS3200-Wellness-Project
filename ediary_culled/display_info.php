<?php
//redirect if not a registered session
session_start();
if(!session_is_registered(myusername)){
header("location:login_admin.php");
}

include 'config.php';
include 'connect.php';
$tbl_name = 'student';
$result = mysql_query("SELECT * FROM $tbl_name");
while($row = mysql_fetch_array($result)) 
{
  	echo 	"id: " . $row['id'] . "<br/>first: " . $row['first'] . "<br/>last: " . $row['last'] . "<br/>password: " . $row['password']
			. "<br/>last_login: " . $row['last_login'] . "<br/>active: " . $row['active'] . "<br/>age: " . $row['age'] . "<br/>gender: "
			. $row['gender'] . "<br/>height: " . $row['height'] . "<br/>mass: " . $row['mass'] . "<br/>athletic: " . $row['athletic']
			. "<br/>sport: " . $row['sport'];
  	echo "<br /><br/>";
}

echo '<a href="home_admin.php">Back to Admin Tasks<a/>';


?>

