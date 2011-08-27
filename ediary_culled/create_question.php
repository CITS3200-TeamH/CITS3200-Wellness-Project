<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_admin();
	drawHeaderAdmin();
	drawNavAdmin();
?>

<div id="main">
	<div id="welcome-box">
		<img src="images/cyclist.jpg" style="width:280px; height:220px; float:right;"/>	
		<p style="font-weight:800; font-size:30px;">Admin: Create New Rating Item</p>
		<p style="font-family:Georgia,serif; color:#564C23">
		Here you can customize rating items to best suit the needs of you project group.<br/><br/>
		Rating items are all rated on a scale of 1 to 5 and may encompass any relevant information.<br/><br/>
		For example, such items may include 'quality of sleep' or 'muscle soreness'.
		</p>
	</div>
	<div id="content_wide">
	<?php 
		if(isset($_POST['create'])) {
			if($_POST['description'] != '') {
				//check that the rating item does not already exist in the table
				$res = mysql_query('SELECT * FROM question WHERE description="'.$_POST['description'].'"');
				$check = true;
				if(mysql_num_rows($res) > 0) {
					echo '<b style="color:red; font-size:10pt;">Rating Item \''.$_POST['description'].'\' Already Present</b>';
					$check = false;
				}
				if($check) {
					//update the database with the new information
					$res = mysql_query('INSERT INTO question(description) VALUES("'.$_POST['description'].'")');
					if(!$res) {
						echo mysql_error();
					}else {
						echo '<b style="color:red; font-size:10pt;">Rating Item \''.$_POST['description'].'\' Successfully Added</b>';
					}
				}
			}else {
				echo '<b style="color:red; font-size:10pt;">Description field must not be empty</b>';
			}
		}
	?>
		<div class="std_form">
			<?php
				echo 
					'<form method="POST" action="create_question.php" name="rating_item">
					Rating Item Description (as it will appear to subjects in the group)<br/><br/>
					<input type="text" name="description" style="width:300px"/><br/><br/>
					<input type="submit" name="create" value="Create Item"/>
					</form>';
			?>
		</div><br/>
		<span style="color:black; font-weight:bold;">Existing rating items </span>
		<span style="color:grey; font-weight:bold;" id="items">[view]</span>
	</div>
</div>
	<?php draw_footer(); ?>
