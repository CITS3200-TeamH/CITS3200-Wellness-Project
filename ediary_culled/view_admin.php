<?php
//redirect if not a registered session
session_start();
if(!session_is_registered(myusername) || $_SESSION['user_type'] != "admin"){
header("location:login.php");
}

include 'config.php';
include 'connect.php';
$tbl_name = 'admin';
$id = $_POST['admin_id'];

echo "<b>Administrator: " . $id ."<br/><br/>Groups Managed:<br/></b>";
$result = mysql_query("SELECT * FROM groups WHERE admin_id = $id GROUP BY name ");

while($row = mysql_fetch_array($result)) 
{
  	//echo "'" . $row['name'] . "'";
  	echo 	"<form action=\"view_group.php\" method=\"post\">
				<input type=\"hidden\" name=\"group_name\" Value=\"".$row['name']."\" /><br/>
				<input type=\"submit\" Value=\" " . $row['name'] . "\" style=\"border:none; background:none;font-weight:bold;text-decoration:underline;\"/>
			</form>";
}

echo '<a href="home_admin.php">Back to Admin Tasks<a/>';


?>

