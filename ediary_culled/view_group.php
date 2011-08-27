<?php
	include 'layout.php';
	check_admin();
	//prepare query
	include 'config.php';
	include 'connect.php';
	$tbl_name = 'class';
	//$name = $_POST['group_name'];
	$name = $_GET['group'];
	$name = stripslashes($name);
	$sql = 'SELECT * FROM '.$tbl_name.' WHERE name="'.$name.'" GROUP BY name';
	$result = mysql_query($sql);

	drawHeaderAdmin();
	drawNavAdmin();
?>

<! Initialise the jquery datepicker fields>
<script type="text/javascript"> 
	$(function() {
		$('#start_date').datepicker({ dateFormat: 'yy-mm-dd' });
		$('#end_date').datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>

<div id ="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">Administrator: View/Edit Groups > <?php echo $name; ?></p>
	</div>
	<div id="content" style="margin-top:0px;">
	<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a> > Group Summary Reports</span><br/><br/>
	<?php
		echo '<b style="color:black; text-decoration:underline;">' . $name . '</b><br/><br/>';
		if($result) {
			if(mysql_num_rows($result) > 0) {
				$row = mysql_fetch_array($result);
			  	echo '<div class="std_form" style="margin-left:auto; margin-right:auto;">
			  	<form action="modify_class.php" method="post">
		  		<input type="hidden" name="class_name" value="'.$name.'" />
		  		Start Date (YYYY-MM-DD):<br/> 
		  		<input type="text" name="start_date" id="start_date" value="' .$row['start'].'"/>';
		  		if($_GET['start'] == "false") { 
		  			echo '<b style="color:red; margin-left:30px;">Invalid start date.</b>';	
		  		}
		  		echo '<br/><br/><input type="hidden" name="old_start_date" value="' .$row['start'].'" />
				End Date (YYYY-MM-DD):<br/>
				<input type="text" name="end_date" id="end_date" value="' .$row['finish'].'"/>';
				if($_GET['finish'] == "false") {
		  			echo '<b style="color:red; margin-left:30px;">Invalid finish date.</b>';	
				}
				if($_GET['dates'] == "false") {
		  			echo '<b style="color:red; margin-left:30px;">Finish date must come after start date.</b>';	
				}
				echo '<br/><br/><input type="hidden" name="old_end_date" value="' .$row['finish'].'"/>
				Data Entry Window:<br/>
				<input type="text" name="window" value="' .$row['window'].'"/>';
				if($_GET['window'] == "false") {
	  				echo '<b style="color:red; margin-left:30px;">Invalid window value.</b>';	
				}
				echo '<br/><br/><input type="hidden" name="old_window" value="' .$row['window'].'"/>
				Delete Class?<br/><input type="checkbox" value="true" name="delete_class"/><br/><br/>
				<input type="submit" Value="Modify Class"/>';
				if($_GET['update'] == "true") {
					echo '<b style="color:red; margin-left:120px;">Group successfully updated.</b>';	
				}
				if($_GET['update'] == "false") {
					echo '<b style="color:red; margin-left:120px;">Group could not be updated.</b>';					
				}
				if($_GET['delete'] == "false") {
					echo '<b style="color:red; margin-left:120px;">Group could not be removed</b>';					
				}
				echo '</form></div>';
			}else{
				if($_GET['delete'] == "true") {
					echo '<b style="color:red; margin-left:120px;">Group successfully removed.</b>';					
				}else {
					echo '<span style="color:red; font-size:10pt">No group by that name</span><br/><br/>';
				}
			}
			echo '<br/><br/><a href="home_admin.php" class="links">Back to Admin Tasks</a>';
			echo '<a href="view_students.php" style="margin-left:30px;" class="links">Back to Group Listing</a><br/>';
		}else {
			echo mysql_error();
		}
	?>
	</div>
	<div id="right-nav" style="width:249px; height:350px; font-size:10pt;">
		<b>View/Edit Group</b><hr/>Here you can edit project group parameters<br/><br/>
		Any modifications to start date, finish date or data entry window will take effect immediately.<br/><br/>
	</div>
</div>
<?php draw_footer(); ?>
