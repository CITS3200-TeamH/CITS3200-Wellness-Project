<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';
	check_admin();
	drawHeaderAdmin();
	drawNavAdmin();

	//prepare query
	$name = $_GET['group'];
	$name = stripslashes($name);
	//$name = mysql_real_escape_string($name);
?>
<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px;">
			Administrator: <?php echo $name;?> > Group Summary Reports
		</p>
	</div>
	<div id="content" style="padding-top:0px;">
	<span class="view_subjects_tracker"><a href="view_students.php">Group Listing</a> > Group Summary Reports</span><br/><br/>
	<div style="padding:20px; background-color:rgb(245,245,245); border:solid 1px rgb(210,210,210); margin-top:0px;">
	<ol class="ind_reports_list" style="font-size:10pt; line-height:1.7;">
        <li>Rating Item data</li>

            <ul>
                <li><a href="admin_g_Life_w_reports.php?group=<?php echo $name;?>" class="links"> Average & STD per week </a></li>
                <li><a href="admin_g_Life_d_reports.php?group=<?php echo $name;?>" class="links"> Average & STD by day of the week</a></li>
            </ul>

        <li>General Health & Wellness Data</li>
        	<ul>
                <li><a href="admin_g_health_w_reports.php?group=<?php echo $name;?>" class="links"> Average & STD per week </a></li>
                <li><a href="admin_g_health_d_reports.php?group=<?php echo $name;?>" class="links"> Average & STD by day of the week</a></li>
            </ul>
            
        <li>Physical Activity Data</li>

        <ul>
            <li>METs</li>

                        <ul>
                        <li><a href="admin_g_avg_met_w_reports.php?group=<?php echo $name;?>" class="links"> Average & SD METs per week</a></li>
                        <li><a href="admin_g_avg_met_d_reports.php?group=<?php echo $name;?>" class="links"> Average & SD METs per day</a></li>
                        <li><a href="admin_g_avg_met_dw_reports.php?group=<?php echo $name;?>" class="links"> Average & SD METs for each day of the week</a></li>
                        </ul>

        </ul>

        <ul>
            <li>Duration (minutes)</li>

                        <ul>
                        <li><a href="admin_g_avg_min_w_reports.php?group=<?php echo $name;?>" class="links"> Average & SD hours per week</a></li>
                        <li><a href="admin_g_avg_min_d_reports.php?group=<?php echo $name;?>" class="links"> Average & SD hours per day</a></li>
                        <li><a href="admin_g_avg_min_dw_reports.php?group=<?php echo $name;?>" class="links"> Average & SD hours for each day of the week</a></li>
                        </ul>
        </ul>
        <ul>
            <li>Fitness components </li>

                        <ul>
                        <li><a href="admin_g_prop_e_reports.php?group=<?php echo $name;?>" class="links">METs as a proportion of fitness component</a></li>
                        </ul>
        </ul>
        </li>


	</ol>
	</div>
	<br/>
	<a href="view_students.php" class="links">Back</a>

	</div>
	<div id="right-nav" style="min-height:300px; height:auto; margin-top:57px;"><b>Group Summary Reports</b><hr/>These reports summarise a group's exercise data on the basis on MET's, Duration, Type and Intensity and operate at group level.<br/><br/>
	</div>
</div>
<?php draw_footer(); ?>

