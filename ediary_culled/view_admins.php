<?php
	//	AUTHOR: Jake Dallimore jrhdallimore@gmail.com
	//	DATE:	Oct 14th 2010
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_admin();
	$id = $_SESSION['username'];
	drawHeaderAdmin();
	drawNavAdmin();

	//draw the contents
	echo '<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">
			Administrator: Manage Administrators
		</p>
	</div>
	<div id="content">';

	if($_SESSION['is_super'] == "true" ) {
		if(isset($_POST['delete'])) {
			//iterate throught the form and delete where the checki's suggest to do so
			$num = $_POST['count'];
			$i = 1;
			echo '<div style="width:710px; height:400px; background-color:white; overflow-y:auto; float:left;">';
			while($i <= $num) {
				//delete if the check box suggests to do so
				if(isset($_POST['check'.$i]) && $_POST['check'.$i] == "true") {
					if($_POST['group'.$i] == "") {
						//admin has no group so just remove altogether
						$res = mysql_query('DELETE FROM admin WHERE id='.$_POST['admin'.$i].'');
						if($res) {
							echo '<b style="font-size:10pt;">Successfully removed '.$_POST['fullname'.$i].' from the system</b><br/>';
						}else {
							echo mysql_error();
						}
					}else {
						//only remove if there is another group admin so check for this
						$res = mysql_query('SELECT admin_id FROM class WHERE name=\''.$_POST['group'.$i].'\'');
						if($res) {
							if(mysql_num_rows($res) <= 1) {
								echo '<b style="color:red; font-size:10pt;">Cannot remove administrator "'.$_POST['fullname'.$i].'" from the group "'.$_POST['group'.$i].'", as they are the sole administrator.</b><br/>';
							}else {
								//remove the admin with group = groupi and name = admini
								$res = mysql_query('DELETE FROM class WHERE admin_id='.$_POST['admin'.$i].' AND name=\''.$_POST['group'.$i].'\'');
								if($res) {
									echo '<b style="font-size:10pt;">Successfully removed '.$_POST['fullname'.$i].' from group '.$_POST['group'.$i].'</b><br/>';
								}else {
									echo mysql_error();
								}	
							}
						}else {
							echo mysql_error();
						}
					}
				}
				$i++;
			}
			echo '</div><br/><br/><a href="home_admin.php" class="links">Back to Admin Tasks<a/>';
			echo '<a href="view_admins.php" style="margin-left:20px;" class="links">Back to Manage Administrators<a/>';
		}else {
			//get all admins, both as collaborators and as stand alone admins
			$result = mysql_query("SELECT * FROM admin,class WHERE admin.id=class.admin_id ORDER BY name");
			$result2 = mysql_query("SELECT * FROM admin WHERE id NOT IN('$id') AND id NOT IN(SELECT admin_id FROM class)");
			if($result && $result2) {
				$i = 1;
				echo '<div style="width:710px; height:400px; background-color:white; float:left;">
				<table style="font-size:10pt;">
				<tr>
				<td style="width:200px; font-weight:bold;">Group</td>
				<td style="width:200px; font-weight:bold;">Administrator</td>
				<td style="width:100px; font-weight:bold;">Select</td>
				</tr></table>
				<div style="width:500px; height:350px; overflow-y:auto; float:left; border-width:1px 0px 0px 0px; border-style:solid; border-color:gray; margin-right:30px;">
				<form action="view_admins.php" method="post">
				<table style="font-size:10pt; float:left; border-width:0px 0px 0px 1px; border-style:solid; border-color:gray; background-color:rgb(240,240,240);">';
				while($row = mysql_fetch_array($result)) 
				{
				  	echo '<tr>
					<td style="width:200px; border-width:0px 1px 1px 0px; border-style:solid; border-color:gray;">
						<input type="hidden" name="group'.$i.'" value="'.$row['name'].'" />
						'.$row['name'].'
					</td>
				  	<td style="width:200px; border-width:0px 1px 1px 0px; border-style:solid; border-color:gray;">
						<input type="hidden" name="admin'.$i.'" value="'.$row['id'].'" />
						<input type="hidden" name="fullname'.$i.'" value="'.$row['first'].' '.$row['last'].'" />
						'.$row['first'].' '.$row['last'].'
					</td>
					<td style="width:100px; border-width:0px 1px 1px 0px; border-style:solid; border-color:gray; text-align:center;">
						<input type="checkbox" name="check'.$i.'" value="true"/>
					</td>
					</tr>';
					$i++;
				}
				while($row2 = mysql_fetch_array($result2)) {
					echo '<tr>
					<td style="width:250px; border-width:0px 1px 1px 0px; border-style:solid; border-color:gray;">
						<input type="hidden" name="group'.$i.'" value="" />
						--None--
					</td>
				  	<td style="width:250px; border-width:0px 1px 1px 0px; border-style:solid; border-color:gray;">
						<input type="hidden" name="admin'.$i.'" value="'.$row2['id'].'" />
						<input type="hidden" name="fullname'.$i.'" value="'.$row2['first'].' '.$row2['last'].'" />
						'.$row2['first'].' '.$row2['last'].'
					</td>
					<td style="width:100px; border-width:0px 1px 1px 0px; border-style:solid; border-color:gray; text-align:center;">
						<input type="checkbox" name="check'.$i.'" value="true"/>
					</td>
					</tr>';
					$i++;
				}
				echo '<input type="hidden" name="count" value="'.($i-1).'"/>';
				echo '</table></div>
				<b>Actions</b><hr/>';
				echo '<input type="submit" name="delete" value="Remove Admin(s)" style="border:none; display:block; text-decoration:underline; text-align:left; padding-left:0px; background-color:white; color:green; font-family:Arial,Helvetica,sans-serif; font-size:10pt; font-weight:bold; margin-left:0px;"/>
				</form></div>';
			}else {
				echo mysql_error();
			}
			echo '<br/><br/><a href="home_admin.php" class="links">Back to Admin Tasks<a/>';
		}
	}else {
		echo '<b>Can\'t perform this operation unless given Level 1 administrative privileges</b>';
		echo '<br/><br/><a href="home_admin.php" class="links">Back to Admin Tasks<a/>';
	}
	
	echo '</div>
	<div id="right-nav" style="height:320px;"><b>Manage Administrators</b><hr/>Here you can remove administrators from project groups and from the system.<br/><br/>
		Administrators may not be removed from a group if they are the sole administrator of that group.<br/><br/>
		Only administrators that are not tied to a group (indicated by --NONE--) may be removed from the system entirely.<br/><br/>
		This prevents any project group existing without a designated administrator.
	</div>
	</div>';
	draw_footer();
?>
