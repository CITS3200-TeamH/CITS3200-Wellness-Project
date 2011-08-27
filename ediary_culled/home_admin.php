<?php
	include 'layout.php';
	check_admin();
	drawHeaderAdmin();
	drawNavAdmin();
?>
<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">Administrator Home</p>
	</div>
	<div id="content" style="width:709px;">
		<div id="admin_task_button"> 
			<b>Groups</b><hr/>
			<a href="new_group.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Create a new project group</span></a><br/>
			<a href="new_collaborator.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Add a group collaborator</span></a><br/>
			<a href="view_students.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Manage current groups</span></a><br/>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>Modify group parameters</span><br/>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>Remove group</span><br/>
			<a href="create_rating_item.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Rating Items</span></a><br>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>View/ Create Rating Items</span><br/>							
		</div>
		<div class="task_button_spacer"></div>
		<div id="admin_task_button"> 
			<b>Subjects + Diary</b><hr/>
			<a href="upload.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Add subjects via CSV file</span></a><br/>
			<a href="new_student.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Add individual subject</span></a><br/>
			<a href="view_students.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Manage current subjects</span></a><br/>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>Remove subjects</span><br/>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>View diary records</span><br/>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>Unlock diary records</span><br/>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>Reset User Passwords</span><br/>						
		</div>
		<div id="admin_task_button"> 
			<b>General Administrative</b><hr/>
			<a href="new_admin.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Create new administrator</span></a><br/>
			<a href="view_admins.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Manage administrators</span></a><br/>
			<a href="load_met_data.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Upload Activities Listing</span></a><br/>			
		</div>
		<div class="task_button_spacer"></div>
		<div id="admin_task_button"> 
			<b>Reporting</b><hr/>
			<a href="view_students.php"><img src="images/nav-arrow.gif"/><span class="admin_task_link">Summaries and Raw Data</span></a><br/>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>Group Summaries</span><br/>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>Individual Summaries</span><br/>
			<span style="font-size:10pt;"><img src="images/bullet-link.gif" style="margin-left:15px;"/>Raw Data</span><br/>
		</div>
	</div>
	<div id="right-nav" style="font-size:10pt; min-height:300px; height:auto; margin-top:37px;"><b>Administrative Tasks</b><hr/>Tasks are grouped by relevance<br/><br/>Some tasks may require a higher (Level 1) level of administrative privilege</div>
</div>
<?php draw_footer(); ?>
