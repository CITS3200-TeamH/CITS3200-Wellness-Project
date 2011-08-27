<?php
	include 'layout.php';
	check_student();
	draw_headerin();
	draw_navin();
?>
<div id="main">
	<!-- BREADCRUMBS -->
	<span class="reporting_breadcrumbs" style="margin:-5px 0px 10px 0px; font-size:8pt; font-family:Arial,Helvetica,sans-serif; float:left; color:black;">
		<a href="subject_select_reports.php">Reporting</a>
	 	> Individual Summary Reports
	</span>
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px; margin-top:20px;">Individual Summary Reports</p>
	</div>
	<div id="content" style="padding-top:0px;">
	<div style="background-color:rgb(245,245,245); padding:10px; border:1px solid rgb(210,210,210); float:left; width:690px;">
	<ol class="ind_reports_list" style="font-size:10pt; line-height:1.7; float:left;">
        <li>Rating Item Data</li>
            <ul>
                <li><a href="individual_Life_w_reports.php"> Average & STD per week </a></li>
                <li><a href="individual_Life_d_reports.php"> Average & STD by day of the week</a></li>
            </ul>
        <li>General Health & Wellness Data</li>
        <ul>
        <li><a href="individual_health_w_reports.php">Average & STD per week</a></li>
        <li><a href="individual_health_d_reports.php">Average & STD by day of the week</a></li>        
        </ul>
        <li>Physical Activity Data</li>
        <ul>
            <li>MET</li>
            <ul>
            <li><a href="individual_total_met_w_reports.php"> Total METs for each week</a></li>
            <li><a href="individual_total_met_d_reports.php"> Total METs for each day</a></li>
            <!--
            <li><a href="individual_avg_met_w_reports.php"> Average & SD METs per week</a></li>
            <li><a href="individual_avg_met_d_reports.php"> Average & SD METs per day</a></li>
            -->
            <li><a href="individual_avg_met_dw_reports.php"> Average & SD METs for each day of the week</a></li>
            </ul>
        </ul>
        <ul>
            <li>Duration (mins)</li>
            <ul>
            <li><a href="individual_total_min_w_reports.php"> Total mins for each week</a></li>
            <li><a href="individual_avg_min_d_reports.php">  Total mins for each day</a></li>
            <li><a href="individual_avg_min_dw_reports.php"> Average & SD minutes for each day of the week</a></li>
            </ul>
        </ul>
        <ul>
            <li> <a href="individual_prop_e_reports.php"> Times & METS allocated to types of activities</a> </li>
            <li> <a href="individual_prop2_e_reports.php"> Times & METS allocated to activity in different activity zones</a> </li>
        </ul>
        </li>
        <ul>
            <li> <a href="individual_stats_reports.php"> Statistics </a></li>
        </ul>
  	<li>Illness & injury data</li>
	<ul>
        <li><a href="ind_illness_summary.php"> Summary of Illness to Date</a></li>
    </ul>
</ol>
<a href="ind_raw_data.php" style="text-decoration:none;">
	<div class="ind_raw_button">
		<img src="images/excel_icon.png" style="float:left; border:none; margin:0px;"/>
		<span style="margin:7px 0px 0px 20px; float:left; font-size:8pt; text-decoration:none;">Download<br/>Raw PA Data</span>
	</div>
</a>
<a href="ind_raw_data2.php" style="text-decoration:none;">
	<div class="ind_raw_button">
		<img src="images/excel_icon.png" style="float:left; border:none; margin:0px;"/>
		<span style="margin:7px 0px 0px 20px; float:left; font-size:8pt; text-decoration:none;">Download<br/>Raw Daily Data</span>
	</div>
</a>
<a href="ind_fitness_raw.php" style="text-decoration:none;">
	<div class="ind_raw_button">
		<img src="images/excel_icon.png" style="float:left; border:none; margin:0px;"/>
		<span style="margin:7px 0px 0px 20px; float:left; font-size:8pt; text-decoration:none;">Download Raw<br/>Fitness Test Data</span>
	</div>
</a>
</div>
	</div>
	<div id="right-nav" style="min-height:300px;"><b>Individual Summary Reports</b><hr/>These reports summarise your exercise data on the basis on MET's, Duration, Type and Intensity and operate at an individual level.<br/><br/>
	To generate summary reports at a group level, please click <a href="subject_view_g_reports.php" class="links">here</a>.
	</div>
</div>
<?php draw_footer(); ?>
