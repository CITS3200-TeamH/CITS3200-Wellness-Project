<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_admin();
	drawHeaderAdmin();
	drawNavAdmin();

	/*$name = $_POST['group_name'];
	$_SESSION['group_name']=$name;

	$name = stripslashes($name);
	$name = mysql_real_escape_string($name);

	$id = $_POST['student_id'];
	$_SESSION['student_id']=$id;*/
	$id = $_GET['id'];
	$group = $_GET['group'];
	$group = stripslashes($group);
	//$id = stripslashes($id);
	//$id = mysql_real_escape_string($id);
?>

<div id="main">
	<div id="welcome-box" style="height:50px;">
	 	<p style="font-weight:800; font-size:20px;">
				Administrator: <?php echo $id ;?> > Individual Summary Reports 
		</p>
	</div>
	<div id="content" style="padding-top:0px;">
	<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a> > <a href="view_students.php?group=<?php echo $group; ?>"><?php echo $group; ?></a> > <a href="view_students.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"><?php echo $id; ?></a> > Individual Summary Reports</span><br/><br/>	
	<div style="background-color:rgb(245,245,245); border:solid 1px rgb(210,210,210); padding:20px;">
	<ol class="ind_reports_list" style="font-size:10pt; line-height:1.7;">
        <li>Rating Item Data</li>

            <ul>
                <li><a href="admin_Life_w_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Average & STD per week </a></li>
                <li><a href="admin_Life_d_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Average & STD by day of the week</a></li>
            </ul>

		<li>General Health & Wellness Data</li>
			<ul>
                <li><a href="admin_health_w_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Average & STD per week </a></li>
                <li><a href="admin_health_d_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Average & STD by day of the week</a></li>
            </ul>
        <li>Physical Activity Data</li>

        <ul>
            <li>MET</li>

                        <ul>

                        <li><a href="admin_total_met_w_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Total METs for each week</a></li>
                        <li><a href="admin_total_met_d_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Total METs for each day</a></li>
                        <li><a href="admin_avg_met_dw_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Average METs for each day of the week</a></li>
                        </ul>

        </ul>

        <ul>
            <li>Duration (mins)</li>

                        <ul>
                        <li><a href="admin_total_min_w_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Total mins for each week</a></li>
                        <li><a href="admin_avg_min_d_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Total mins for each day</a></li>
                        <li><a href="admin_avg_min_dw_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Average & SD hours for each day of the week</a></li>
                        </ul>
        </ul>
        <ul>
            <li> <a href="admin_prop_e_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Times & METS allocated to types of activities</a> </li>
            <li> <a href="admin_prop2_e_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Times & METS allocated to activity in different activity zones</a> </li>
        </ul>
        </li>
        <ul>
            <li> <a href="admin_stats_reports.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Statistics </a></li>

        </ul>
  <li>Illness & injury data</li>
      <ul>
                <li><a href="admin_ind_illness_summary.php?group=<?php echo $group; ?>&id=<?php echo $id; ?>"> Illness Summary</a></li>
            </ul>
</ol>
</div>

	</div>
	<div id="right-nav" style="min-height:300px; height:auto; margin-top:57px;"><b>Individual Summary Reports</b><hr/>These reports summarise a subject's exercise data on the basis on MET's, Duration, Type and Intensity and operate at an individual level.<br/>
	</div>
</div>
<?php draw_footer(); ?>
