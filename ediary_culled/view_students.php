<?php
	//	AUTHOR: Jake Dallimore jrhdallimore@gmail.com
	//	DATE:	Oct 14th 2010
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	include 'queries.php';
	check_admin();
	drawHeaderAdmin();
	drawNavAdmin();
	if($_SESSION['is_super'] == "true") {
		$level1 = true;	
	}else {
		$level1 = false;
	}
?>	

	<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">
			Administrator: Manage Subjects/Groups
		</p>
	</div>
	<div id="content_wide">
<?php
	if(!isset($_GET['group'])) {
		if($level1) {
			$result = mysql_query("SELECT * FROM class,admin WHERE admin_id=id GROUP BY name,admin_id");
		}else {
			$result = mysql_query('SELECT * FROM class,admin WHERE admin_id='.$_SESSION['username'].' AND admin_id=id GROUP BY name,admin_id');
		}
		if($result) {
			//LEAVE BREADCRUMBS
			echo '<span class="view_subjects_tracker">Group Listing</span><br/><br/>';
			$last = null;	// variable used to eliminate classes being listed twice or more if there are multiple admins.
			$server = $_SERVER['PHP_SELF'];
			
			echo '<div style="width:989px; height:440px; background-color:white; float:left;">
						<table style="font-weight:bold; font-size:10pt;"><tr>
							<td style="width:150px;">Group Name</td>
							<td style="width:150px;">Administrator(s)</td>
							<td style="width:60px;">Window</td>
							<td style="width:80px;">Ph. Activity</td>
							<td style="width:75px; padding-left:5px;">Profiles</td>
							<td style="width:95px; padding-left:5px;">Compliance</td>
							<td style="width:70px; padding-left:10px;">Fitness</td>
							<td style="width:100px; padding-left:10px;">Summary</td>
							<td style="width:60px; padding-left:10px;">Reports</td>
							<td style="width:80px;">Edit/Delete</td>
						</tr></table>
						<div style="width:989px; height:400px; overflow-y:auto; float:left; border-width:0px 0px 0px 0px; border-style:solid; border-color:gray;">
				  		<table style="font-size:10pt; float:left; border-width:0px 0px 0px 0px; border-style:solid; border-color:gray; background-color:wh;">';
				  		$k = 1;
			while($row = mysql_fetch_array($result)) {
				if($last != $row['name']) {
					echo '<tr><td style="width:150px; height:25px; '; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">
					<a class="links" style="color:darkgreen;" href="view_students.php?group='.$row['name'].'">'.$row['name'].'</a></td>';		
				}elseif($last == $row['name']) {
					echo '<tr><td style="width:150px; height:25px; text-align:center;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'"></td>';
				}
				echo '<td style="width:150px;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">' . $row['first'] . ' ' .$row['last']. '</td>';
				if($last != $row['name']) {
					echo '<td style="width:60px; text-align:center;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">'.$row['window'].'</td>';
					
					//****
					echo '<td style="width:80px; height:25px; font-weight:bold; border-width:0px 0px 0px 0px; border-style:solid; text-align:center; border-color:gray;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">
					<form action="view_group_f_report.php" method="post" style="margin:0px;">
						<input type="hidden" name="group_name" value="'.$row['name'].'"/>
						<input type="submit" value="P.A. Raw" class="submit_custom_1" style="width:auto; cursor:pointer; height:20px; padding-top:2px"/>
					</form>
					</td>
					<td style="width:80px; font-weight:bold; border-width:0px 0px 0px 0px; border-style:solid; text-align:center; border-color:gray;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">
						<form action="group_profiles.php" method="post" style="margin:0px;">
							<input type="hidden" name="group_name" value="'.$row['name'].'"/>
							<input type="submit" value="Profiles" class="submit_custom_1" style="width:auto; cursor:pointer; height:20px; padding-top:2px"/>
						</form>
					</td>
					<td style="width:100px; font-weight:bold; border-width:0px 0px 0px 0px; border-style:solid; text-align:center; border-color:gray;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">
						<form action="compliance_info.php" method="post" style="margin:0px;">
							<input type="hidden" name="group_name" value="'.$row['name'].'"/>
							<input type="submit" value="Compliance" class="submit_custom_1" style="width:auto; cursor:pointer; height:20px; padding-top:2px"/>
						</form>
					</td>
					<td style="width:80px; font-weight:bold; border-width:0px 0px 0px 0px; border-style:solid; text-align:center; border-color:gray;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">
						<form action="fitness_summary.php" method="post" style="margin:0px;">
							<input type="hidden" name="group_name" value="'.$row['name'].'"/>
							<input type="submit" value="Fitness" class="submit_custom_1" style="width:auto; cursor:pointer; height:20px; padding-top:2px"/>
						</form>
					</td>
					<td style="width:110px; font-weight:bold; border-width:0px 0px 0px 0px; border-style:solid; text-align:center; border-color:gray;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">
						<form action="full_summary.php" method="post" style="margin:0px;">
							<input type="hidden" name="group_name" value="'.$row['name'].'"/>
							<input type="submit" value="Full Summary" class="submit_custom_1" style="width:auto; cursor:pointer; height:20px; padding-top:2px"/>

						</form>
					</td>
					<td style="width:60px; text-align:center;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">
						<a class="links" href="view_group_report_met.php?group='.$row['name'].'" style="color:darkgreen;">View</a>
					</td>
					<td style="width:80px; text-align:center;'; if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'">
					<a class="links" style="color:darkgreen;" href="view_group.php?group='.$row['name'].'">Edit</a></td></tr>';
				}

				if($last == $row['name']) {
					echo '
					<td style="text-align:center;';if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'"></td>
					<td style="text-align:center;';if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'"></td>
					<td style="text-align:center;';if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'"></td>
					<td style="text-align:center;';if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'"></td>
					<td style="text-align:center;';if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'"></td>
					<td style="text-align:center;';if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'"></td>
					<td style="text-align:center;';if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'"></td>
					<td style="text-align:center;';if($k == 1) {echo 'background-color:rgb(240,240,240);'; } echo'"></td></tr>';
				}
				$last = $row['name'];
				if($k == 0) { $k =1;
				}else {$k=0;}
			}
			echo'</table></div></div><a href="home_admin.php" class="links" style="float:left;">Back to Admin Tasks</a>';
		}else {
			echo 'MySQL Error, Please see your system administrator';
		}
	}elseif(isset($_GET['group'])) {
		$group = $_GET['group'];
		$group = stripslashes($group);
		//CHECK ADMIN ACCESS RIGHTS
		if(!$level1){
			//not level 1, so check if the group is valid for viewing
			$res = mysql_query('SELECT * FROM class WHERE admin_id='.$_SESSION['username'].' AND name="'. $group .'"');
			if($res) {
				if(mysql_num_rows($res) > 0) {
					$query = 'SELECT * FROM student,classmap WHERE class_name="'.$group.'" AND student_id=id ORDER BY last';
					$result = mysql_query($query);
				}else {
			 		echo '<b style="color:red">You do not have the privileges to view the specified group, or the group does not exist.</b><br/<br/>
					<a href="view_students.php" class="links">Back To Group Listing</a>';			 		
					break;
				}
			}else {
				echo mysql_error();
			}
		}?>
		<! JAVASCRIPT FUNCTIONS THAT APPLY WHEN ?GROUP IS SET>
		<script type="text/javascript">
			var num = 0;
			var	subjects_log = '';
			
			//necessary js for selecting all students
			function checkAll(form) {
				num=0;
				for (i = 0; i < form.elements.length; i++) {
					if(document.select_all_subjects.selecter.checked == true) {
						if(form.elements[i].className == "check") {
			  				form.elements[i].checked = true;
			  				num++;
			  				//form.elements[i].parentNode.parentNode.style.background = 'rgb(240,250,180)';
		  				}
	  				}else {
	  					if(form.elements[i].className == "check") {
			  				form.elements[i].checked = false;
			  				//form.elements[i].parentNode.parentNode.style.background = 'rgb(240,240,240)';		  				
		  				}
	  				}
				}
				if(document.select_all_subjects.selecter.checked == true) {
					document.getElementById("deleted").value = document.getElementById("deleted").value.substring(0,15) + ' ('+num+')';									
					document.getElementById("numCheckedSpan").innerHTML = num;
				}else {
					num = 0;
					document.getElementById("deleted").value = document.getElementById("deleted").value.substring(0,15);					
					document.getElementById("numCheckedSpan").innerHTML = num;
				}
			}
			
			function setLog(form) {
				form.elements["subjects_log_send"].value = subjects_log;
			}
	
			function setNumChecked(formitem) {
				if(formitem.checked == true) {
					//increment
					num++;
					document.getElementById("deleted").value = document.getElementById("deleted").value.substring(0,15) + ' ('+num+')';
					document.getElementById("numCheckedSpan").innerHTML = (num);					
				}else {
					//decrement
					num--;
					if(num != 0) {
						document.getElementById("deleted").value = document.getElementById("deleted").value.substring(0,15) + ' ('+num+')';
					}else {
						document.getElementById("deleted").value = document.getElementById("deleted").value.substring(0,15) + '';						
					}
					document.getElementById("numCheckedSpan").innerHTML = (num);
				}
			}
			
			function clearSubjectsLog() {
				subjects_log = '';			
				document.getElementById("subjects_log").innerHTML = '';
				document.getElementById("clear_history_span").innerHTML = '';				
				document.getElementById("csv_subjects_log_send").value = '';
				document.getElementById("delete_subjects_log_send").value = '';
			}
			
			function fillAdmins() {
				var j = 0;
				var k = 0;
				var ncurrent = 0;
				document.getElementById("list_admins_form_span").innerHTML = '';				
				for(i=0;i<admins.length;i++) {
					if(admins[i][3] == "1") {
						ncurrent++;
					}
				}
				
				for(i=0;i<admins.length;i++) {
					if(admins[i][3] == "0") {
						document.addCollabForm.admins.options[j] = new Option(admins[i][0]+', '+admins[i][1], admins[i][2], false, false);
						j++;
					}else {
						if(k == 0) {
							document.getElementById("list_admins_label").innerHTML = '<span style="text-decoration:underline">Group Administrators:</span>';
							k++;
						}
						e = document.createElement("span");
						e.innerHTML = ' - '+admins[i][1]+' '+admins[i][0];
						e.setAttribute("style","float:left; width:300px;");	
						e.setAttribute("class","admin_listing");											
						document.getElementById("list_admins_form_span").appendChild(e);

						
						if(ncurrent > 1) {
							e = document.createElement("input");
							e.setAttribute("type", "submit");
							e.setAttribute("class", "submit_custom_2");							
							e.setAttribute("name", admins[i][2]+'_'+admins[i][1]+'_'+admins[i][0]);
							e.setAttribute("style","float:right;");
							e.setAttribute("value", "-");
							document.getElementById("list_admins_form_span").appendChild(e);							

						}
					}
				}
				if(j == 0) {
					document.addCollabForm.innerHTML = 'There are no administrators that have not already been assigned';
				}
			}
			
			/* 	COMMENTS RELATING TO THE JAVASCRIPT-BASED ACCORDION-STYLE BUTTON EFFECT BELOW
			*	The span within this function cannot be accessed via js until it had been run once. 
			*	SOLUTION: We provide the plugin with an updater function which it runs on each click event to update.
			*
			*	Update functions have grown to handle 3 cases:
			*	- Non initialised spans -> left alone as the click handler is active
			*	- Initialised spans that are currently hidden -> left alone as the click handler is active
			*	- Open spans -> removed and re-initialised as the click handler is free at the time
			*
			*	Display block (necessary) is forced on the first initialisation of the confirmation boxes as the cases above rely upon it 
			*	and otherwise it would only appear after the steps init -> hide() -> click(). ie the accordion behaviour fails 
			*	first time without it.
			*/
			
			
			function renderDelete() {
				$('#deleted').confirm({
					msg:'<span style="color:red; font-weight:bold;">Warning!</span><br/><span id="numCheckedSpan">0</span> subjects to be removed.<br/><br/>Deleting subjects will permanently remove all data pertaining to those subjects from the system. This data will no longer be retrievable.<br/><br/> Confirm Delete?  ',
					wrapper: '<span class="confirm_dropdown" id="delete_confirm_dropdown"></span>',
					dialogShow:'slideDown',
					dialogSpeed:'fast',
					updatefn: 
					'<\script type="text/javascript">'+
						'document.getElementById("delete_confirm_dropdown").style.display = "block";'+
						'document.getElementById("numCheckedSpan").innerHTML = num;'+
						'if(document.getElementById("upload_csv_dropdown") && document.getElementById("upload_csv_dropdown").style.display == "block") {'+
			  				'$(\'#upload_csv_dropdown\').remove();'+
			  				'$(\'#addcsv\').show();'+
			  				'renderAddCsv();'+
		  				'}'+
						'if(document.getElementById("add_single_dropdown") && document.getElementById("add_single_dropdown").style.display == "block") {'+
							'$(\'#add_single_dropdown\').remove();'+
							'$(\'#addsingle\').show();'+
							'renderAddSingle();'+
						'}'+
						'if(document.getElementById("add_collab_dropdown") && document.getElementById("add_collab_dropdown").style.display == "block") {'+
							'$(\'#add_collab_dropdown\').remove();'+
							'$(\'#addcollab\').show();'+
							'renderAddCollab();'+
						'}'+
					'</\script>' ,
					buttons: {
					wrapper:'<button style="color:#0864A5;"></button>',
					separator:'  '
					}  
				});	
			}
			
			function renderAddCsv() {
				$('#addcsv').confirm({
					msg:'<form enctype="multipart/form-data" action="view_students.php?group='+"<?php echo $group; ?>"+'" method="post" style="margin-bottom:30px; height:130px;"><input type="hidden" name="MAX_FILE_SIZE" value="1000000" /><label>Class List: </label><input type="file" name="subjectsfile"><input type="hidden" name="subjects_log_send" id="csv_subjects_log_send" value="'+"<?php echo $subjects_log; ?>"+'"><input type="hidden" name="groupname" value="'+"<?php echo $group; ?>"+'"/><br/><br/><input type="submit" value="Upload" name="csvadd" style="margin:0px; float:left;"></form>',
				    wrapper: '<span class="confirm_dropdown" id="upload_csv_dropdown"></span>',				  					
					dialogShow:'slideDown',
					dialogSpeed:'fast',
					updatefn: 
					'<\script type="text/javascript">'+
						'document.getElementById("upload_csv_dropdown").style.display = "block";'+
						'document.getElementById("csv_subjects_log_send").value = subjects_log;'+
						'if(document.getElementById("delete_confirm_dropdown") && document.getElementById("delete_confirm_dropdown").style.display == "block") {'+
							'$(\'#delete_confirm_dropdown\').remove();'+
				  			'$(\'#deleted\').show();'+
				  			'renderDelete();'+
						'}'+
						'if(document.getElementById("add_single_dropdown") && document.getElementById("add_single_dropdown").style.display == "block") {'+
							'$(\'#add_single_dropdown\').remove();'+
							'$(\'#addsingle\').show();'+
							'renderAddSingle();'+
						'}'+
						'if(document.getElementById("add_collab_dropdown") && document.getElementById("add_collab_dropdown").style.display == "block") {'+
							'$(\'#add_collab_dropdown\').remove();'+
							'$(\'#addcollab\').show();'+
							'renderAddCollab();'+
						'}'+
					'</\script>' ,				  
					normal:false,
					buttons: {
					cancel: 'Cancel'
					}
				});
			}
			
			function renderAddSingle() {
				$('#addsingle').confirm({
					msg:'<form name="addSingleForm" action="view_students.php?group='+"<?php echo $group; ?>"+'" method="post" style="margin-bottom:30px; height:130px;"><label style="margin:5px 5px 0px 0px; width:80px; float:left;">ID</label><input name="subject_id" type="text" style="float:left;"/><br/><br/><label style="margin:5px 5px 0px 0px; float:left; width:80px;">Firstname</label><input name="subject_first" type="text" style="float:left;"/><br/><br/><label style="margin:5px 5px 0px 0px; float:left; width:80px;">Surname</label><input name="subject_last" type="text" style="float:left;"/><br/><br/><input type="hidden" name="subjects_log_send" id="single_subjects_log_send" value="'+"<?php echo $subjects_log; ?>"+'"><input type="hidden" name="groupname" value="'+"<?php echo $group; ?>"+'"/><input type="submit" value="Add Subject" name="addsinglesubmit" style="margin:10px 0px 0px 0px; float:left;"></form>',
					wrapper: '<span class="confirm_dropdown" id="add_single_dropdown"></span>',				  
					dialogShow:'slideDown',
					dialogSpeed:'fast',
					updatefn:
			  		'<\script type="text/javascript">'+
						'document.getElementById("add_single_dropdown").style.display = "block";'+			  		
				  		'document.getElementById("single_subjects_log_send").value = subjects_log;'+
				  		'if(document.getElementById("delete_confirm_dropdown") && document.getElementById("delete_confirm_dropdown").style.display == "block") {'+
				  			'$(\'#delete_confirm_dropdown\').remove();'+
				  			'$(\'#deleted\').show();'+
				  			'renderDelete();'+
			  			'}'+
			  			'if(document.getElementById("upload_csv_dropdown") && document.getElementById("upload_csv_dropdown").style.display == "block") {'+
				  			'$(\'#upload_csv_dropdown\').remove();'+
				  			'$(\'#addcsv\').show();'+
				  			'renderAddCsv();'+
			  			'}'+
			  			'if(document.getElementById("add_collab_dropdown") && document.getElementById("add_collab_dropdown").style.display == "block") {'+
							'$(\'#add_collab_dropdown\').remove();'+
							'$(\'#addcollab\').show();'+
							'renderAddCollab();'+
						'}'+
			  			'fillAdmins();'+
			  		'<\/script>' ,
					normal:false,
					buttons: {
					cancel: 'Cancel'
					}
				});
			}
			
			function renderAddCollab() {
				$('#addcollab').confirm({
					msg:'<span id="list_admins_label" style="float:left;"></span><br/><div id="list_admins" style="height:80px; width:390px; float:left; overflow-y:auto; margin-top:5px;"><form id="list_admins_form" action="view_students.php?group='+"<?php echo $group; ?>"+'" method="post"><input type="hidden" name="remove_admin_true"><input type="hidden" name="subjects_log_send" id="remove_collab_log_send" value="'+"<?php echo $subjects_log; ?>"+'"><input type="hidden" name="groupname" value="'+"<?php echo $group; ?>"+'"/><span id="list_admins_form_span"></span></form></div><form name="addCollabForm" action="view_students.php?group='+"<?php echo $group; ?>"+'" method="post" style="margin:10px 0px 10px 0px; height:30px; width:380px; float:left;"><label style="float:left; margin:5px 5px 0px 0px;">Administrator: </label><input type="hidden" name="subjects_log_send" id="add_collab_log_send" value="'+"<?php echo $subjects_log; ?>"+'"><input type="hidden" name="groupname" value="'+"<?php echo $group; ?>"+'"/><select name="admins" style="float:left; margin-right:5px;"></select><input type="submit" value="Add" name="addcollabsubmit" style="float:left;"></form>',
					wrapper: '<span class="confirm_dropdown" id="add_collab_dropdown"></span>',				  
					dialogShow:'slideDown',
					dialogSpeed:'fast',
					updatefn:
			  		'<\script type="text/javascript">'+
						'document.getElementById("add_collab_dropdown").style.display = "block";'+			  		
				  		'document.getElementById("add_collab_log_send").value = subjects_log;'+
				  		'document.getElementById("remove_collab_log_send").value = subjects_log;'+				  		
				  		'if(document.getElementById("delete_confirm_dropdown") && document.getElementById("delete_confirm_dropdown").style.display == "block") {'+
				  			'$(\'#delete_confirm_dropdown\').remove();'+
				  			'$(\'#deleted\').show();'+
				  			'renderDelete();'+
			  			'}'+
			  			'if(document.getElementById("upload_csv_dropdown") && document.getElementById("upload_csv_dropdown").style.display == "block") {'+
				  			'$(\'#upload_csv_dropdown\').remove();'+
				  			'$(\'#addcsv\').show();'+
				  			'renderAddCsv();'+
			  			'}'+
			  			'if(document.getElementById("add_single_dropdown") && document.getElementById("add_single_dropdown").style.display == "block") {'+
							'$(\'#add_single_dropdown\').remove();'+
							'$(\'#addsingle\').show();'+
							'renderAddSingle();'+
						'}'+
			  			'fillAdmins();'+
			  		'<\/script>' ,
					normal:false,
					buttons: {
					cancel: 'Cancel'
					}
				});
			}
			
			$(function(){
				renderDelete();		
				renderAddCsv();
				renderAddSingle();
				renderAddCollab();
			});
		</script>
		
		<?php	
		if(isset($_GET['id'])) {
			$id = $_GET['id'];
			$group = $_GET['group'];
			$group = stripslashes($group);
			//IF DATE FOR DIARY ENTRY IS PROVIDED
			if(isset($_GET['date'])) {
				//LEAVE BREADCRUMBS
				echo '<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a>  >  <a href="view_students.php?group='.$group.'">'.$group.'</a> >
				<a href="view_students.php?group='.$group.'&id='.$id.'">'.$id.'</a> > <a href="view_students.php?group='.$group.'&id='.$id.'&record=true">Records</a> > '.$_GET['date'].'</span><br/><br/>';
				$sql = 'SELECT * FROM training_records1 tr1,compcodes cc WHERE class="'.$group.'" AND student_id='.$id.' AND daydate="'.$_GET['date'].'" AND tr1.compcode=cc.compcode';
				$result = mysql_query($sql);
				if($result)	{
					echo '<table border="0" style="float:left;">';
					while($row = mysql_fetch_array($result) ) {
						echo '<tr><td style="width:150px; font-weight:bold;">Date</td><td style="width:400px;">'.$row['daydate'].'</td></tr>
						<tr><td style="width:150px; font-weight:bold;">Compcode</td><td style="width:400px;">'.$row['compcode'].'</td></tr>
						<tr><td style="width:150px; font-weight:bold;">Type</td><td style="width:400px;">'.$row['type'].'</td></tr>
						<tr><td style="width:150px; font-weight:bold;">Activity</td><td style="width:400px;">'.$row['heading'].'</td></tr>
						<tr><td style="width:150px; font-weight:bold;">Description</td><td style="width:400px;">'.$row['description'].'</td></tr>
						<tr><td style="width:150px; font-weight:bold;">Duration</td><td style="width:400px;">'.$row['duration'].'</td></tr>
						<tr><td style="width:150px; height:30px;"></td></tr>';
					}
					echo '</table>';
				}else {
					echo 'MySQL Error, Please see your system administrator';
				}
			//IF DIARY ENTRIES ARE REQUESTED
			}else if(isset($_GET['record']) && $_GET['record'] == "true") {
				//LEAVE BREADCRUMBS
				echo '<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a>  >  <a href="view_students.php?group='.$group.'">'.$group.'</a> >
					 <a href="view_students.php?group='.$group.'&id='.$id.'">'.$id.'</a> > Records</span><br/><br/>';
				$sql = 'SELECT daydate FROM training_records1 WHERE class="'.$group.'" AND student_id='.$id.' GROUP BY daydate DESC';
				$result = mysql_query($sql);
				if($result) {
					echo '<table border="1" style="font-size:10pt;"><tr><td style="width:200px;"><b>Date of Record</b></td></tr>';
					while($row = mysql_fetch_array($result)) {
						echo '<tr><td><a style="color:green; font-weight:bold;" href="view_students.php?group='.$group.'&id='.$id.'&date='.$row['daydate'].'">'.$row['daydate'].'</a></td></tr>';
					}
					echo '</table>';
				}else {
					echo 'MySQL Error, Please see your system administrator';
				}
			//IF UNLOCK IS SPECIFIED
			}else if(isset($_GET['unlock']) && $_GET['unlock'] == "true") {
				//IF THE DATES HAVE BEEN SELECTED AND SENT, UNLOCK THEM
				if(isset($_POST['unlock'])) {
					//LEAVE BREADCRUMBS
					echo '<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a>  >  <a href="view_students.php?group='.$group.'">'.$group.'</a> >
					<a href="view_students.php?group='.$group.'&id='.$id.'">'.$id.'</a> > <a href="view_students.php?group='.$group.'&id='.$id.'&unlock=true">Unlock Records</a> > Confirmation</span><br/><br/>';
					$dur = $_POST['duration'];
					for($i=1;$i<=$dur;$i++) {
						if(isset($_POST['check'.$i])) {
							$date = $_POST['check'.$i];
							//unlock the day ie add to exceptions
							$result = mysql_query('INSERT INTO exception VALUES("'.$group.'",'.$id.',"'.$date.'")');
							if($result) {
								echo '<b>Added exception for '.$group.': '.$id.': '.$date.'  </b><br/>';
							}else {
								echo '<b>Day already unlocked ('.$date.').</b><br/>';
							}
						}
					}
					echo '<br/><br/><a href="view_students?group='.$group.'&id='.$id.'&unlock=true" class="links">Back to Unlock</a>';

				}else if(isset($_POST['relock'])){
					//LEAVE BREADCRUMBS
					echo '<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a>  >  <a href="view_students.php?group='.$group.'">'.$group.'</a> >
					<a href="view_students.php?group='.$group.'&id='.$id.'">'.$id.'</a> > <a href="view_students.php?group='.$group.'&id='.$id.'&unlock=true">Unlock Records</a> > Confirmation</span><br/><br/>';
					$dur = $_POST['duration'];
					for($i=1;$i<=$dur;$i++) {
						if(isset($_POST['check'.$i])) {
							$date = $_POST['check'.$i];
							//relock the day ie remove from exceptions
							$result = mysql_query('DELETE FROM exception WHERE group_id="'.$group.'" AND subject_id='.$id.' AND daydate="'.$date.'"');
							if($result) {
								if(mysql_affected_rows() == 1) {
									echo '<b>Removed exception for '.$group.': '.$id.': '.$date.'  </b><br/>';
								}else {
									echo '<b>Day is already locked, or falls under the current entry window ('.$date.').</b><br/>';
								}
							}else {
								echo mysql_error();
							}
						}
					}
					echo '<br/><br/><a href="view_students?group='.$group.'&id='.$id.'&unlock=true" class="links">Back to Unlock</a>';
				}else {
				//LIST ALL DATES FOR UNLOCKING
					//LEAVE BREADCRUMBS
					echo '<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a>  >  <a href="view_students.php?group='.$group.'">'.$group.'</a> >
					<a href="view_students.php?group='.$group.'&id='.$id.'">'.$id.'</a> > Unlock Records</span><br/><br/>';
					//GET RECORD DATES
					$sql = 'SELECT daydate FROM training_records1 WHERE class="'.$group.'" AND student_id='.$id.' GROUP BY daydate DESC';
					$result = mysql_query($sql);

					//GET EXCEPTION DATES
					$sql3 = 'SELECT daydate FROM exception WHERE group_id="'.$group.'" AND subject_id='.$id.' GROUP BY daydate DESC';
					$result3 = mysql_query($sql3);

					//FIND THE START AND END DATES FOR THE GROUP (AND THUS DURATION)
					$sql2 = 'SELECT start, finish, window FROM class WHERE name="'.$group.'"';
					$result2 = mysql_query($sql2);
					if($result2) {
						$row2 = mysql_fetch_array($result2);
						$start = $row2['start'];
						$finish = $row2['finish'];
						$window = $row2['window'];
						$duration = (strtotime($finish) - strtotime($start)) / (60 * 60 * 24) +1;
					}else {
						echo mysql_error();
					}
					//CREATE TABLE ROW PER DAY AND COLOR IF ENTRIES EXIST
					if($result && $result3) {
						echo '<table style="font-weight:bold; font-size:10pt;"><tr><td style="width:280px;">Date</td><td style="width:200px;">Locked Status</td><td style="width:200px;">Select</td></tr></table>';
						echo '<div style="width:auto; height:400px; border:solid 1px gray; overflow-y:scroll; background-color:rgb(240,240,240);" >
						<form action="" method="POST">
						<table border="1" style="font-size:10pt;">';
						$i = 1;
						$date = date($finish);
						$row = mysql_fetch_array($result);
						$row3 = mysql_fetch_array($result3);
						while($i<=$duration) {
//!!!!!!!!!!!!!!!!!!!!!!!SHOULD CHECK ENTERED DATA HERE OR REMOVE THE QUERY1 ie $result and $sql
							//IF EXCEPTION, COLOR ORANGE
							if(strtotime($row3['daydate']) == strtotime($date)) {
								echo '<tr><td style="width:280px;"><a style="color:orange; font-weight:bold;" href="view_students.php?group='.$group.'&id='.$id.'&date='.$date.'">'.$date.'</a></td>
								<td style="width:200px;">Unlocked</td>
								<td style="width:200px;"><input type="checkbox" name="check'.$i.'" value="'.$date.'" ></td></tr>';
								$row3 = mysql_fetch_array($result3);
							//IF WITHIN WINDOW
							}else if(strtotime($date) >= strtotime('-'.($window-1).' day', strtotime(date('Y-m-d'))) && strtotime($date) <= strtotime(date('Y-m-d')) ) {
								echo '<tr><td style="width:280px;"><a style="color:blue; font-weight:bold;" href="view_students.php?group='.$group.'&id='.$id.'&date='.$date.'">'.$date.'</a></td>
								<td style="width:200px;">Unlocked (window)</td>
								<td style="width:200px;"><input type="checkbox" name="check'.$i.'" value="'.$date.'" ></td></tr>';

							}else {
								echo '<tr><td style="width:280px;"><a style="color:red; font-weight:bold;" href="view_students.php?group='.$group.'&id='.$id.'&date='.$date.'">'.$date.'</a></td>
	  							<td style="width:200px;">Locked</td>
								<td style="width:200px;"><input type="checkbox" name="check'.$i.'" value="'.$date.'" ></td></tr>';
							}
							$i++;
							$date = date('Y-m-d',strtotime('-1 day',strtotime($date)));
						}
						echo '</table></div><br/>
						<input type="hidden" name="duration" value="'.$duration.'">
						<input type="submit" name="unlock" value="Unlock Selected Entries">
						<input type="submit" name="relock" value="Re-lock Selected Entries"> </form>';
					}else {
						echo 'MySQL Error, Please see your system administrator';
					}
				}
			}else if( (isset($_GET['resetpw']) && $_GET['resetpw'] == "true") || isset($_POST['resetpw'])) {
				//confirmation form here
				echo '<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a>  >  <a href="view_students.php?group='.$group.'">'.$group.'</a> >
					<a href="view_students.php?group='.$group.'&id='.$id.'">'.$id.'</a> > Reset Password</span><br/><br/>';				
				echo '
				<div style="background-color:rgb(245,245,245); border:solid 1px rgb(210,210,210); height:auto; min-height:300px; padding:20px">';
					if(isset($_POST['resetpw'])) {
						//reset the password
						$lastqry = mysql_query('SELECT last FROM student WHERE id='.$id.'');
						if($lastqry) { $lastrow = mysql_fetch_array($lastqry); $lastname = $lastrow['last']; }
						$pw = genPassword($id,$lastname);
						$pwres = mysql_query('UPDATE student SET password="'.$pw.'" WHERE id='.$id.'');
						
					}
					echo '
					<form style="margin-left:auto; margin-right:auto; padding:10px; font-size:10pt; color:rgb(60,60,60); -moz-border-radius:10px; -webkit-border-radius:10px; width:600px;  background-color:white; border:solid 1px rgb(210,210,210);" action="view_students.php?group='.$group.'&id='.$id.'" method="POST">';
						if(!isset($_POST['resetpw'])) {
							echo '<b style="color:red">WARNING!</b><br/>
							This action will reset the password to its default value. Click \'Reset\' to confirm or \'Cancel\' to return to '.$id.'\'s profile.<br/><br/>
							<input type="submit" value="Reset" name="resetpw"/>
							<input type="submit" value="Cancel" name="cancel"/>	';
						} else {
							if($pwres) {			
								echo '<span style="font-size:10pt; margin:0px 0px 10px 5px; color:red; float:left;">Password has been successfully reset.</span><br/><br/>';
							}else {
								echo '<span style="font-size:10pt; margin:0px 0px 10px 5px; color:red; float:left;">Password could not be reset, please contact support.</span><br/><br/>';
							}
							echo '<input type="submit" value="Ok" name="ok"/>';
						}
					echo '</form>';
				echo '</div>';
			
			}else {
				$result = mysql_query("SELECT * FROM student WHERE id=$id");
				if($result) {
					$row = mysql_fetch_array($result);
					//LEAVE BREADCRUMBS
					echo '<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a>  >  <a href="view_students.php?group='.$group.'">'.$group.'</a> > '.$id.'</span><br/><br/>';
					//STUDENT INFO TABLE/FORM
			  		echo '
			  		<div style="width:990px; height:300px; background-color:white; float:left;">
						<form action="'.$server.'?group='.$group.'&id='.$id.'"  method="POST" style="margin-bottom:5px;">
							<div style="height:300px; width:500px; float:left">
							<table style="font-size:10pt; float:left; margin:0px 30px 0px 0px; background-color:white;">
		  			 			<tr><td style="width:150px; background-color:rgb(240,240,240);"><b>Subject ID</b></td><td style="width:280px; background-color:rgb(240,240,240)">'.$row['id'].'</td></tr>
				  			 	<tr><td><b>First name</b></td><td>'.$row['first'].'</td></tr>
				 	  			<tr><td style="background-color:rgb(240,240,240);"><b>Last name</b></td><td style="background-color:rgb(240,240,240);">'.$row['last'].'</td></tr>
				 	  			<tr><td><b>Age</b></td><td>';if(isset($row['age'])) {echo $row['age'];}else{echo '-';} echo'</td></tr>
				 	  			<tr><td style="background-color:rgb(240,240,240);"><b>Gender</b></td><td style="background-color:rgb(240,240,240);">';if(isset($row['gender'])) {echo $row['gender'];}else{echo '-';} echo'</td></tr>
				 	  			<tr><td><b>Height</b></td><td>';if(isset($row['height'])) {echo $row['height'];}else{echo '-';} echo'</td></tr>
				 	  			<tr><td style="background-color:rgb(240,240,240);"><b>Mass</b></td><td style="background-color:rgb(240,240,240);">';if(isset($row['mass'])) {echo $row['mass'];}else{echo '-';} echo'</td></tr>
				 	  			<tr><td><b>Is Athletic</b></td><td>';if(isset($row['athletic'])) {echo $row['athletic'];}else{echo '-';} echo'</td></tr>
				 	  			<tr><td style="background-color:rgb(240,240,240);"><b>Sport</b></td><td style="background-color:rgb(240,240,240);">';if(isset($row['sport'])) {echo $row['sport'];}else{echo '-';} echo'</td></tr>
				 	  			<tr><td><b>Level</b></td><td>';if(isset($row['level'])) {echo $row['level'];}else{echo '-';} echo'</td></tr>			 	  			
			  				</table>
			  				</div>
			  				<b>Subject Tasks</b><hr/>
		  			 		<input type="submit" class="submit_custom_1" name="delete" value="Delete Subject"><br/>
						</form>
						<form action="view_students?group='.$group.'&id='.$id.'&record=true" method="post" style="margin-bottom:5px;">
		  			    	<input type="submit" class="submit_custom_1" value="View Diary Records"/>
	  			    	</form>
						<form action="view_students?group='.$group.'&id='.$id.'&unlock=true" method="post" style="margin-bottom:5px;">
		  			    	<input type="submit" class="submit_custom_1" value="Unlock a Record"/>
		  			    </form>
		                <form action="view_subject_report.php" method="post" style="margin-bottom:5px;">
			            	<input type="hidden" name="group_name" value="'. $group .'" >
		                    <input type="hidden" name="student_id" value="'. $id .'">
			                <input type="submit" class="submit_custom_1" value="Lifestyle raw" > <br/>
			          	</form>
			          	<form action="view_subject_f_report.php" method="post" style="margin-bottom:5px;">
			            	<input type="hidden" name="group_name" value="'. $group .'" >
		                    <input type="hidden" name="student_id" value="'. $id .'">
			                <input type="submit" class="submit_custom_1" value="Physical activity raw" > <br/>
			          	</form>
			          	<form action="view_subject_report_met.php?group='.$group.'&id='.$id.'" method="post" style="margin-bottom:5px;">
			            	<input type="submit" class="submit_custom_1" value="Summary Reports"/>
		            	</form>
		            	<form action="view_students?group='.$group.'&id='.$id.'&resetpw=true" method="post" style="margin-bottom:5px;">
			            	<input type="submit" class="submit_custom_1" value="Reset Password"/>
		            	</form>
  			 		</div>
  			 		<br/><a href="view_students.php?group='.$group.'" class="links">Back To '.$group.'</a>
			 		&nbsp;&nbsp;&nbsp;&nbsp;<a href="view_students.php?group='.$group.'" class="links">Back To Group Listing</a>';
				}else{
					echo mysql_error();
				}
			}
		}else {
			$logempty = false;
			//IF REMOVAL IS REQUESTED
			$deleted_now = false;
			if(isset($_POST['deleted']) && isset($_POST['count'])) {
				$count = $_POST['count'];
				$deleted_now = true;
				$subjects_log = $_POST['subjects_log_send'];
				echo '
				<script type="text/javascript">
					subjects_log = "'.$subjects_log.'";
				</script>';
				//LOOP THROUGH AND REMOVE SUBJECTS ACCORDINGLY
				if(isset($_POST['check'])) {
					//delete students
					if($subjects_log != '') {
						echo'
						<script type="text/javascript">
							subjects_log += "<hr style=\'color:white; background-color:white; border:dotted 1px rgb(220,220,220); border-width:0px 0px 1px 0px;\'/>";
						</script>';
					}	
					$subjects_log = 'dummy_fill_string';
					foreach($_POST['check'] as &$id) {
						//REMOVE FROM GROUP
						$result = mysql_query('DELETE FROM classmap WHERE student_id='.$id.' AND class_name="'.$group.'"');

						//REMOVE ALL TRAINING RECORD DATA FOR THE CURRENY GROUP (necessary for the scripts in full_summary.php and hence queries.php to work correctly!)
						$result3 = mysql_query('DELETE FROM training_records1 WHERE student_id='.$id.' AND class="'.$group.'"');
						$result4 = mysql_query('DELETE FROM training_records2 WHERE student_id='.$id.' AND class="'.$group.'"');

						//REMOVE ALL FITNESS TEST DATA FOR THE CURRENT GROUP
						$result5 = mysql_query('DELETE FROM fitness_test WHERE subject_id='.$id.' AND group_id="'.$group.'"');

						//REMOVE ALL EXCEPTION DATA FOR THE CURRENT GROUP
						$result6 = mysql_query('DELETE FROM exception WHERE subject_id='.$id.' AND group_id="'.$group.'"');

						//REMOVE FROM SUBJECTS ONLY IF NOT PRESENT IN ANY GROUPS NOW
						if( mysql_num_rows(mysql_query('SELECT * FROM classmap WHERE student_id='.$id.'')) == 0 ) {
							$result2 = mysql_query('DELETE FROM student WHERE id='.$id.'');
						}

						//CONFIRMATION
						if($result) {
							echo '
							<script type="text/javascript">
								subjects_log += "Removed subject \''.$id.'\' from group \''.$group.'\'<br/>";
							</script>';
							//$subjects_log .= 'Removed subject '.$id. ' from group \'' .$group.'\'<br/>';
						}
						if($result2) {
							echo '
							<script type="text/javascript">
								subjects_log += "Removed subject \''.$id.'\' from the system<br/>";
							</script>';
							//$subjects_log .= 'Removed subject '.$id. ' from the system<br/>';
						}
					}
				}
				if($subjects_log == '') {$logempty = true;}
			}
			//IF CSV FILE IS UPLOADED
			$uploaded_now = false;
			if(isset($_POST['csvadd'])) {
				$subjects_log = $_POST['subjects_log_send'];
				echo '
				<script type="text/javascript">
					subjects_log = "'.$subjects_log.'"; 
				</script>';			
				$upgroup = $_POST['groupname'];
				$upfileinfo[0] = basename($_FILES['subjectsfile']['name']);
				$upfileinfo[1] = $_FILES['subjectsfile']['tmp_name'];
				$info = uploadClassList($upfileinfo,$upgroup,true);
				if($info[0] != '') { 
					if($subjects_log != '') {
						echo'
						<script type="text/javascript">
							subjects_log += "<hr style=\'color:white; background-color:white; border:dotted 1px rgb(220,220,220); border-width:0px 0px 1px 0px;\'/>";
						</script>';
					}
					$subjects_log = 'dummy_fill_string';
					echo '
					<script type="text/javascript">
						subjects_log += "Uploaded file \''.$info[0].'\'<br/>";
						subjects_log += "'.$info[1].' subjects were uploaded<br/>";
						subjects_log += "'.$info[2].' subjects were already present<br/>";
						subjects_log += "'.$info[3].' subjects added to group \''.$upgroup.'\'<br/>";
						subjects_log += "'.$info[4].' subjects already present in group \''.$upgroup.'\'<br/>";																								
					</script>';
				}
				if($subjects_log == '') { $logempty = true;}
				$uploaded_now = true;
			}
			
			//ADD COLLABORATOR
			$collab_added_now = false;
			if(isset($_POST['addcollabsubmit'])) {
				$subjects_log = $_POST['subjects_log_send'];
				echo '
				<script type="text/javascript">
					subjects_log = "'.$subjects_log.'"; 
				</script>';					
				//get dates
				$res = mysql_query('SELECT * FROM class WHERE name="'.$_POST['groupname'].'"');
				$row = mysql_fetch_array($res);
				//get names for log
				$res2 = mysql_query('SELECT last,first FROM admin where id='.$_POST['admins']);
				$row2 = mysql_fetch_array($res2);
				//add collaborator here
				$res3 = mysql_query('INSERT INTO class(name,admin_id,start,finish,window) VALUES("'.$_POST['groupname'].'",'.$_POST['admins'].',"'.$row['start'].'","'.$row['finish'].'",'.$row['window'].')');
				if($subjects_log != '') {
					echo'
					<script type="text/javascript">
						subjects_log += "<hr style=\'color:white; background-color:white; border:dotted 1px rgb(220,220,220); border-width:0px 0px 1px 0px;\'/>";
					</script>';
				}
				
				if($res3) {
					$collab_added_now = true;
					echo'
					<script type="text/javascript">
						subjects_log += "Added '.$row2['first'].' '.$row2['last'].' as group administrator";
					</script>';
				}else {
					//cant add so must exist already
					$collab_added_now = true;
					echo'
					<script type="text/javascript">
						subjects_log += "'.$row2['first'].' '.$row2['last'].' is already a group administrator";
					</script>';
				}
			}
			
			//REMOVE COLLABORATOR
			$collab_removed_now = false;
			if(isset($_POST['remove_admin_true'])) {
				$n = 0;
				foreach($_POST as $key=>$value) {
					if($n == 3) { 
						//id_first_last
						$splitres = preg_split('/_/', $key);
					}
					$n++;
				}
				
				$res = mysql_query('DELETE FROM class WHERE name="'.$_POST['groupname'].'" AND admin_id='.$splitres[0]);
				$subjects_log = $_POST['subjects_log_send'];
				echo '
				<script type="text/javascript">
					subjects_log = "'.$subjects_log.'";
				</script>';
				if($subjects_log != '') {
					echo'
					<script type="text/javascript">
						subjects_log += "<hr style=\'color:white; background-color:white; border:dotted 1px rgb(220,220,220); border-width:0px 0px 1px 0px;\'/>";
					</script>';
				}
				echo '
				<script type="text/javascript">
					subjects_log += "Removed '.$splitres[1].' '.$splitres[2].' as group administrator";
				</script>';
				$collab_removed_now = true;			
			}
			
			//ADD SINGLE SUBJECT
			$added_single_now = false;
			if(isset($_POST['addsinglesubmit'])) {
				$subjects_log = $_POST['subjects_log_send'];
				echo '
				<script type="text/javascript">
					subjects_log = "'.$subjects_log.'";
				</script>';			
				if(is_int_val($_POST['subject_id']) && $_POST['subject_first'] != '' && $_POST['subject_last'] != '') {
					//valid key value and filled firstname and lastname so add the subject
					if($subjects_log != '') {
						echo'
						<script type="text/javascript">
							subjects_log += "<hr style=\'color:white; background-color:white; border:dotted 1px rgb(220,220,220); border-width:0px 0px 1px 0px;\'/>";
						</script>';
					}
					$a = addSubject($_POST['subject_id'],$_POST['subject_first'],$_POST['subject_last'],$_POST['groupname']);
					if($a == 1) {
						echo '
						<script type="text/javascript">
							subjects_log += "Added subject \''.$_POST['subject_first'].' '.$_POST['subject_last'].'\' to the group";
						</script>';	
						$added_single_now = true;
					}else if($a == 2) {
						echo '
						<script type="text/javascript">
							subjects_log += "Subject \''.$_POST['subject_id'].'\' already exists but was added to the group, firstname and surname remain unchanged";
						</script>';
						$added_single_now = true;
					}else if($a == 3) {
						echo '
						<script type="text/javascript">
							subjects_log += "Subject \''.$_POST['subject_id'].'\' is already a member of this group";
						</script>';
						$added_single_now = true;
					}else {
						//an error occurred contact admin
					}
				}else {
					//invalid form data. 
					//do something
				}	
			}
			
			//subjects query
			$query = 'SELECT * FROM student,classmap WHERE class_name="'.$group.'" AND student_id=id ORDER BY last';
			$result = mysql_query($query);
			//admins query and javascript fill
			$res2 = mysql_query('SELECT *,(SELECT COUNT(*) FROM admin,class WHERE id=admin_id AND id=a.id AND name="'.$group.'") AS cnt FROM admin a ORDER BY last');
			$admin_fill_script = '<script type="text/javascript"> var admins = new Array();';
			$i = 0;
			while($row = mysql_fetch_array($res2)) {
				$admin_fill_script .= 'admins['.$i.'] = new Array("'.$row['last'].'","'.$row['first'].'","'.$row['id'].'","'.$row['cnt'].'");';
				$i++;
			}
			$admin_fill_script .= '</script>';
			echo $admin_fill_script;
			
			if($result) {
				//LEAVE BREADCRUMBS
				echo '<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a>  >  '.$group.'</span><br/><br/>';
				//IF THE GROUP IS EMPTY
				if(mysql_num_rows($result) == 0) {
					echo '<span style="color:red">*** No Subjects Present ***</span><br/><br/>
					<a href="view_students.php" class="links">Back To Group Listing</a>';
					break;
			 	}
				//GROUP BASED SUBJECT LISTING
				$cnt = 1;
				echo '
				<div style="width:990px; height:400px; background-color:white; float:left;">
					<table style="font-weight:bold; font-size:10pt; margin-right:30px; float:left; width:515px;"><tr>
					<td style="width:275px;">Name</td>
					<td style="width:140px;">Subject ID</td>
					<td style="width:100px;">
						<form name="select_all_subjects" style="float:left;">
							<span style="float:left;">Select All</span>
							<input type="checkbox" name="selecter" style="margin:2px 0px 0px 10px; float:left;" onClick="checkAll(document.student_selection);"/>
						</form>
					</td>
					</tr></table>

					<div style="width:515px; height:350px; overflow-y:auto; float:left; border-width:0px 0px 0px 0px; border-style:solid; border-color:gray; margin-right:30px;">
						<table style="font-size:10pt; float:left; border-width:0px 0px 0px 0px; border-style:solid; border-color:gray; background-color:white;">
							<form name="student_selection" id="student_selection" action="'.$server.'?group='.$group.'"  method="POST">';
							$i=true;
							while($row = mysql_fetch_array($result)) {
								echo '<tr'; if($i) { echo ' style="background-color:rgb(240,240,240);"'; $i = (!$i);}else {$i = (!$i);} echo'>
								<td style="width:275px; border-width:0px 0px 0px 0px; border-style:solid; border-color:gray;">
									<a class="links" style="color:darkgreen; font-weight:normal;" href="view_students.php?group='.$group.'&id='.$row['id']. '">'.$row['last'].', '.$row['first'].'<a/></td>
								<td style="width:140px; border-width:0px 0px 0px 0px; border-style:solid; border-color:gray;">' . $row['id'] .'</td>
								<td style="width:100px; border-width:0px 0px 0px 0px; border-style:solid; border-color:gray; text-align:center;"><input type="checkbox" onclick="setNumChecked(this);" class="check" name="check[]" value="'.$row['id'].'"></td>
							  	</tr>';
							  	$cnt++;
							}
						echo '
						</table>
					</div>
					<b>Group Tasks</b>						
					<hr/>
					<div style="width:445px; height:335px; float:left;">
						<div style="width:180px; height:335px; float:left; ">
							<input type="hidden" name="count" value="'.($cnt-1).'">
							<input type="hidden" id="delete_subjects_log_send" name="subjects_log_send" value="">							
							<input type="submit" id="deleted" name="deleted" class="submit_custom_1" value="Delete Selected" onClick="setLog(document.student_selection);" style="margin-bottom:5px;"/>
							<input type="submit" id="addcsv" name="addcsv" class="submit_custom_1" value="Add Subjects via CSV" style="margin-bottom:5px;"/>
							<input type="submit" id="addsingle" name="addsingle" class="submit_custom_1" value="Add Single Subject" style="margin-bottom:5px;"/>';
							if($level1) {
								echo '<input type="submit" id="addcollab" name="addcollab" class="submit_custom_1" value="Administrators"/>';
							}
						echo '
						</div>
						<div id="subjects_log" style="width:265px; min-height:50px; max-height:330px; float:left; font-size:8pt; color:rgb(80,80,80); padding-top:5px; overflow-y:auto;">';
							if($deleted_now || $uploaded_now || $collab_added_now || $collab_removed_now || $added_single_now) {
								echo '
								<script type="text/javascript">
									document.getElementById("subjects_log").innerHTML = subjects_log;
									document.getElementById("subjects_log").scrollTop = document.getElementById("subjects_log").scrollHeight;
								</script>';				
							}
						echo'																											
						</div>
						<span id="clear_history_span" style="margin-top:5px; float:right;">';
							if(($deleted_now || $uploaded_now || $collab_added_now || $collab_removed_now || $added_single_now) && !$logempty) {	
								echo '<a href="#clear" onClick="clearSubjectsLog();" class="links" style="float:right; font-size:8pt; margin-top:3px;">Clear History</a>';
							}
						echo '
						</span>
					</div>					
					</form>
				</div>';
			}else {
				echo mysql_error();
			}
			echo '<a href="view_students.php" class="links" style="float:left;">Back To Group Listing</a>';
			
		}
	}	
?>
	</div>
</div>
<?php draw_footer(); ?>
