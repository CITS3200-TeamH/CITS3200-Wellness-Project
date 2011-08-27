<?php
	//validate session
	include 'layout.php';
	check_admin();

	//prepare query
	include 'config.php';
	include 'connect.php';
	$name = $_POST['group_name'];
        $name = stripslashes($name);
        $name = mysql_real_escape_string($name);
        $sql= "select * from student, classmap where student.id=classmap.student_id and class_name='$name'" ;
	$result = mysql_query($sql);

	drawHeaderAdmin();
	drawNavAdmin();
?>
<div id="main">
	<div id="welcome-box">
		<h1 style="font-weight:800; font-size:30px;">Administrator: View/Edit Groups</h1><br/>
		<img src="images/cyclist.jpg" style="width:280; height:220; float:right; margin-top:-104px; margin-right:-10px;"/>
		<span style="font-family:Georgia,serif; color:#564C23">
		This page facilitates the addition of individual subjects.<br/><br/>
		For more information please see <a href=" ">About</a>.</span>
	</div>
	<div id="content_wide">
	<?php echo "<b>Subjects Currently In  " . $name . ":</b><br/><br/>";
	while($row = mysql_fetch_array($result))
	{

                 echo 	"<form action=\"view_subject_report.php\" method=\"post\" style=\"margin:0px 0px 0px 0px;\">
				<input type=\"hidden\" name=\"group_name\" value=\"".$name."\" />
                                <input type=\"hidden\" name=\"student_id\" value=\"".$row['id']."\" />
				<input type=\"text\" value=\"" . $row['id'] . "\" style=\"border:solid 1px black; background-color:#E9FAFE; height:30px; width:200px; font-weight:bold;\"/></br>
				<input type=\"submit\" value=\"Raw Data\" style=\"border:solid 1px black; background-color:#E9FAFE; height:30px; width:100px; font-weight:bold;\"/>
				</form>";
                 echo 	"<form action=\"view_subject_report_met.php\" method=\"post\" style=\"margin:0px 0px 0px 0px;\">
                                <input type=\"hidden\" name=\"group_name\" value=\"".$name."\" />
                                <input type=\"hidden\" name=\"student_id\" value=\"".$row['id']."\" />
				<input type=\"submit\" value=\"Total Met\" style=\"border:solid 1px black; background-color:#E9FAFE; height:30px; width:100px; font-weight:bold;\"/>
				</form>";
                 echo   "</br>";

	}
	echo '<br/><a href="home_admin.php">Back to Admin Tasks<a/>';
	?>
	</div>
</div>
<?php draw_footer();
?>

