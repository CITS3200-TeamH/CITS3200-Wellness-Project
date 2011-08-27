<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_admin();
	drawHeaderAdmin();
	drawNavAdmin();
?>

<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">Administrator: Rating Items</p>
	</div>
	<div id="content_wide">
	<?php 
		//REMOVE ITEMS IF REQUESTED
		if(isset($_POST['delete'])) {
			$i = 1;
			while($i <= $_POST['count']) {
				if(isset($_POST['check'.$i])) {
					//remove the checked item here
					$temp_id = $_POST['check'.$i];
					$res = mysql_query('DELETE FROM rating_item WHERE id='.$temp_id.'');
				}
				$i++;
			}
		}	
	
		//UPDATE THE RATING ITEMS DATABASE
		if(isset($_POST['create'])) {
			$check = true;
			if($_POST['description'] != '') {
				//check that the rating item does not already exist in the table
				$res = mysql_query('SELECT * FROM rating_item WHERE description="'.$_POST['description'].'"');
				if(mysql_num_rows($res) > 0) {
					$check = false;
				}else {
				//update the database with the new information
					$insert = mysql_query('INSERT INTO rating_item(description, summary) VALUES("'.$_POST['description'].'","'.$_POST['summary'].'")');
				}
			}
		}
	
		//LIST EXISTING RATING ITEMS
		$res = mysql_query('SELECT * FROM rating_item ORDER BY id');
		$res2 = mysql_query('SELECT id FROM rating_item_map GROUP BY id ORDER BY id');
		if($res2) {
			$row2 = mysql_fetch_array($res2);
			$numres = mysql_num_rows($res2);
		}else { 
			echo mysql_error(); 
		}
		echo '<div style="width:580px; height:380px; float:left;">
			<table style="border:1px solid gray; float:left; border-width:0px 0px 1px 0px;">
			<tr>
				<td style="width:101px; font-weight:bold">ID</td>
				<td style="width:301px; font-weight:bold">Description</td>
				<td style="width:71px; font-weight:bold">In Use</td>
				<td style="width:71px; font-weight:bold">Delete ?</td>			
			</tr>
			</table>
			<div style="width:580px; height:300px; overflow-y:auto; float:left;">
			<form action="create_rating_item.php" method="POST">
			<table style="float:left; border:solid 1px gray; border-width:0px 0px 0px 1px;">';
			$cnt = 0;
			while($row = mysql_fetch_array($res)) {
				$inuse = true;
				$cnt++;
				echo '
				<tr style="background-color:rgb(240,240,240); font-size:10pt;">
					<td style="width:100px; border:1px solid gray; border-width:0px 1px 1px 0px;">'.$row['id'].'</td>
					<td style="width:300px; border:1px solid gray; border-width:0px 1px 1px 0px;">'.$row['description'].'</td>
					<td style="width:70px; border:1px solid gray; border-width:0px 1px 1px 0px;">'; 
					//remove all map rows with id of less than the checking id
					//$idval = $row2['id'];
					$i = 1;
					while(true && $i <= $numres) {
						if($row['id'] > $row2['id']) {
							$row2 = mysql_fetch_array($res2); 						
						}else {
							break;
						}
						$i++;
					}
					if($row2['id'] == $row['id']) { 
						echo '<span style="color:green">Yes</span>'; 
						$row2 = mysql_fetch_array($res2); 						
					}else { 
						echo '<span style="color:red">No</span>';
						//$row2 = mysql_fetch_array($res2);
						$inuse = false;
					}				
					echo '</td>
					<td style="width:70px; text-align:center; border:1px solid gray; border-width:0px 1px 1px 0px; font-weight:bold; ">'; 
					if(!$inuse) {
						echo '<input type="checkbox" name="check'.$cnt.'" value="'.$row['id'].'">';
					}
					echo'</td>
				</tr> ';
			}
			echo '
			</table>
			</div>
			<input type="hidden" name="count" value="'.$cnt.'">
			<input type="submit" name="delete" value="Remove Selected Items" style="margin-top:10px;">
		</form>
		<br/><br/><a href="home_admin.php" class="links">Back to Admin Tasks</a>
		</div>';
		
		//DISPLAY NEW RATING ITEM FORM
		echo '
		<div class="std_form" style="float:right; margin:0px; width:300px;">
		<h3 style="margin-top:0px;">Create New Rating Item</h3>
			<form method="POST" action="create_rating_item.php" name="rating_item">
				Rating Item Description <br/>(as it will appear to subjects in the group)<br/><br/>
				<input type="text" name="description" style="width:300px"/><br/><br/>
				Rating Item Summary <br/><br/>
				<textarea name="summary" style="width:300px;" ROWS=6></textarea>';
				if(!$insert) {
					echo mysql_error();
				}else {
					echo '<p style="color:green;">Rating item \''.$_POST['description'].'\' Added Successfully</p>';
				}
				if(isset($_POST['create']) && !$check) {
					echo '<p style="color:red; font-size:10pt;">* Rating Item \''.$_POST['description'].'\' Already Present</p>';
				}
				if(isset($_POST['create']) && $_POST['description'] == '') {
					echo '<p style="color:red; font-size:10pt;">* Description field must not be empty</p>';
				}
				echo '<input type="submit" name="create" value="Create Item" style="margin-top:10px;"/>
			</form>	
		</div><br/>';
		?>
	</div>
</div>
	<?php draw_footer(); ?>
