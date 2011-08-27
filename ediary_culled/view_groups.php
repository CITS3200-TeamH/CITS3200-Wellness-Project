<?php
	//validate session
	include 'layout.php';
	check_admin();

	//prepare query
	include 'config.php';
	include 'connect.php';
	$tbl_name = 'class';
	$id = $_SESSION['username'];
	$result = mysql_query("SELECT * FROM $tbl_name WHERE admin_id = $id GROUP BY name");
	if($_SESSION['is_super'] == "true" ) {
		$result = mysql_query("SELECT * FROM $tbl_name GROUP BY name");
	}
	
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
	<?php echo "<b>Groups Currently Managed by " . $_SESSION['first'] . " " . $_SESSION['last'] . ":</b><br/><br/>";
	while($row = mysql_fetch_array($result)) 
	{
		echo "<a href=\"view_group.php?group=".$row['name']."\">".$row['name']."</a><br/><br/>";
	}
	echo '<br/><a href="home_admin.php">Back to Admin Tasks<a/>';
	?>
	</div>
</div>
<?php draw_footer();
?>

