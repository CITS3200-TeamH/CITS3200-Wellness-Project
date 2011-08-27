<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_admin();
	
	$sql = "SELECT * FROM admin";
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

<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">
			Administrator: Create Group
		</p>
	</div>
	<div id="content">
		<?php if($_SESSION['is_super'] == "true" ) {
			//GET ALL RATING ITEMS
			$res = mysql_query('SELECT * FROM rating_item ORDER BY id');
			if(!$res) {echo mysql_error();}
			echo '<div class="std_form" style="margin-left:auto; margin-right:auto; height:500px;"><form action="create_class.php" method="post">
			<div style="width:500px; height:50px; float:left;">
				Project Name:<br/> 
				<input type="text" name="class_name" />';
				if($_GET['name'] == "false") {
					echo '<b style="color:red; margin-left:20px;">Invalid group name.</b>';
				}
				if($_GET['name_used'] == "true") {
					echo '<b style="color:red; margin-left:20px;">Group name already used.</b>';
				}
				if($_GET['create'] == "true") {
					echo '<b style="color:red; margin-left:105px;">Group successfully created.</b>';	
				}
				if($_GET['create'] == "false") {
					echo '<b style="color:red; margin-left:105px;">Group not created, may already exist.</b>';	
				}
			echo '</div>
			<div style="width:500px; height:50px; float:left;">
				Admin ID:<br/> 
				<select name="admin_id">';
				while($row = mysql_fetch_array($result) ) {
					echo "<option value = ".$row['id'].">" . $row['first'] . " - " . $row['id'] . "</option>";
				}
				echo '
				</select>
			</div>
			<div style="width:500px; height:50px; float:left;">
				Start Date (YYYY-MM-DD):<br/> 
				<input type="text" id="start_date" name="start_date" />';
				if($_GET['start'] == "false") {
					echo '<b style="color:red; margin-left:20px;">Invalid start date.</b>';	
				}
			echo '</div>
			<div style="width:500px; height:50px; float:left;">
				End Date (YYYY-MM-DD):<br/> 
				<input type="text" id="end_date" name="end_date" />';
				if($_GET['finish'] == "false") {
					echo '<b style="color:red; margin-left:20px;">Invalid finish date.</b>';	
				}
				if($_GET['dates'] == "false") {
					echo '<b style="color:red; margin-left:20px;">Finish date must come after start date.</b>';	
				}
			echo '</div>
			<div style="width:500px; height:50px; float:left;">
				Data Entry Window:<br/> 
				<input type="text" name="window" />';
				if($_GET['window'] == "false") {
					echo '<b style="color:red; margin-left:20px;">Invalid window value.</b>';	
				}
			//LIST THE RATING ITEMS FOR SELECTION
			echo '</div>
			Rating Items:<br/>
			<div style="float:left; height:200px; width:434px; overflow-y:auto; margin-bottom:20px;">
			<table border="1">';
			$cnt = 0;
			while($row = mysql_fetch_array($res)) {
				$cnt++;
				echo '<tr style="background-color:white;">
				<td style="width:300px;">'.$row['description'].'</td>
				<td style="width:100px;"><input type="checkbox" name="item'.$cnt.'" value="'.$row['id'].'" ></td>
				</tr>';
			}
			echo '</table></div>
			<div style="width:500px; height:50px; float:left;">
				<input type="hidden" name="count" value="'.$cnt.'" />
				<input type="submit" Value="Create Group"/>';
			echo '</div>
			</form></div>';
		}else {
			echo '<b>Can\'t perform this operation unless given Level 1 administrative privileges</b>';
		}
		echo '<br/><br/>
		  	  <a href="home_admin.php" class="links">Back to Admin Tasks</a>';
		?>
	</div>
	<div id="right-nav">
		<b>Create Group</b><hr/>Here you can create new project groups<br/><br/>
		Groups must initially be tied to one administrator, although additional collaborators may be added <a href="new_collaborator.php">here</a><br/><br/>
	</div>
</div>
<?php draw_footer(); ?>
