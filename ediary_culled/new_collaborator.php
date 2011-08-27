<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_admin();
	
	$id = $_SESSION['username'];
	if($_SESSION['is_super'] == "true") {
		$sql = "SELECT * FROM class GROUP BY name";
	}else {
		$sql = "SELECT * FROM class WHERE admin_id = '$id' GROUP BY name";
	}
	$result = mysql_query($sql);
	
	//set up query 2, find all collaborators
	$sql2 = "SELECT * FROM admin";
	$result2 = mysql_query($sql2);
	
	drawHeaderAdmin();
	drawNavAdmin();
?>
<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">Administrator: Add Group Collaborator</p>
	</div>
	<div id="content">
	<?php 
		if($_SESSION['is_super'] == "true") { 
			echo' <div class="std_form" style="width:550px; height:180px; margin-left:auto; margin-right:auto;">
			<form action="create_collaborator.php" method="POST">
			Select a group to add collaborator to:<br/> 
			<select name="class_name" style="margin-top:5px; width:220px;">
			<option value="">Please Select A Group</option>';
		 		while($row = mysql_fetch_array($result) ) {
					echo '<option value=\'' . $row['name'] . '\'>' . $row['name'] . '</option>';
				}
			echo '</select>';
			if($_GET['group'] == "false") {
				echo '<b style="color:red; margin-left:30px;">Group not specified.</b>';	
			}
			if($_GET['create'] == "true") {
				echo '<b style="color:red; margin-left:30px;">Collaborator successfully added.</b>';	
			}
			if($_GET['create'] == "false") {
				echo '<b style="color:red; margin-left:30px;">Collaborator not added, may already exist.</b>';	
			}
			echo '<br/><br/>Select a new collaborator:<br/>
			<select name="admin_name" style="margin-top:5px; width:220px;">
			<option value="">Please Select A Collaborator</option>';
			 	while($row2 = mysql_fetch_array($result2) ) {
					echo '<option value=\'' . $row2['id'] . '\'>' . $row2['first'] . ' ' . $row2['last'] .'</option>';
				}
		
			echo '</select>';
			if($_GET['id'] == "false") {
				echo '<b style="color:red; margin-left:30px;">Administrator not specified.</b>';	
			}
			echo'<br/><br/><br/>
			<input type="submit" Value="Add Collaborator"/>';
			echo '</form></div>
			<br/><br/>
			<a href="home_admin.php" class="links">Back to Admin Tasks</a>';
	  	}else{
			echo '<b>Can\'t perform this operation unless given super admin privilidges</b><br/>';
			echo '<br/><a href="home_admin.php" class="links">Back to Admin Tasks</a>';
		}
	?>
	
	</div>
	<div id="right-nav">
		<b>Add Collaborator</b><hr/>Here you can assign administrators to project groups<br/><br/>
		Group administrators can modify a groups start date, finish date and diary entry window<br/><br/>
		Administrators must exist before they can be added as project collaborators; click <a href="new_admin.php">here</a> to create a new administrator<br/><br/>
				
	</div>
</div>
<?php draw_footer(); ?>
