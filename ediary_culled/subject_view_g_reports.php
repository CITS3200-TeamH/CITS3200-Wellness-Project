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
	 	> Group Summary Reports
	</span>
	<div id="welcome-box" style="height:50px;">
	 	<p style="font-weight:800; font-size:20px;">
			Group Summary Reports
		</p>
	</div>
	<div id="content" style="padding-top:0px;">
	<div style="background-color:rgb(245,245,245); border:solid 1px rgb(210,210,210); padding:10px;">
	<ol class="ind_reports_list" style="font-size:10pt; line-height:1.7;">
        <li>Rating Item Data</li>

            <ul>
                <li><a href="individual_g_Life_w_reports.php"> Average & STD per week </a></li>
                <li><a href="individual_g_Life_d_reports.php"> Average & STD by day of the week</a></li>
            </ul>

        <li>General Health & Wellness Data</li>
        	<ul>
        		<li><a href="individual_g_health_w_reports.php"> Average & STD per week</a></li>
        		<li><a href="individual_g_health_d_reports.php"> Average & STD by day of the week</a></li>        		
        	</ul>
        <li>Physical Activity Data</li>

        <ul>
            <li>MET</li>

                        <ul>
                        <li><a href="individual_g_avg_met_w_reports.php"> Average & SD METs per week</a></li>
                        <li><a href="individual_g_avg_met_d_reports.php"> Average & SD METs per day</a></li>
                        <li><a href="individual_g_avg_met_dw_reports.php"> Average & SD METs for each day of the week</a></li>
                        </ul>

        </ul>

        <ul>
            <li>Minutes</li>

                        <ul>
                        <li><a href="individual_g_avg_min_w_reports.php"> Average & SD hours per week</a></li>
                        <li><a href="individual_g_avg_min_d_reports.php"> Average & SD hours per day</a></li>
                        <li><a href="individual_g_avg_min_dw_reports.php"> Average & SD hours for each day of the week</a></li>
                        </ul>
        </ul>
        <ul>
            <li>Fitness Components </li>

                        <ul>
                        <li><a href="individual_g_prop_e_reports.php">METs as a proportion of fitness component</a></li>
                        <!--
                        <li><a href="individual_g_avg_e_min_w_reports.php"> Average & SD hours per week</a></li>
                        <li><a href="individual_g_avg_e_min_d_reports.php"> Average & SD hours per day</a></li>
                        <li><a href="individual_g_avg_e_min_dw_reports.php"> Average & SD hours for each day of the week</a></li>
                         -->
                        </ul>
        </ul>
        </li>


</ol>
</div>
	</div>
	<div id="right-nav"><b>Group Summary Reports</b><hr/>These reports summarise the group exercise data on the basis of MET's, Duration, Type and Intensity.<br/><br/>
	To generate your personal summary reports, please click <a href="subject_view_s_reports.php" class="links">here</a>.
	</div>
</div>
<?php draw_footer(); ?>
