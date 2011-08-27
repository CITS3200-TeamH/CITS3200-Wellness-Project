<?php
	include 'layout.php';
	include 'calendar.php';
	check_student();
	draw_headerin();
	draw_navin();
?>
<div id="main">
	<div id="welcome-box">
		<img src="images/cyclist.jpg" style="width:280px; height:220px; float:right;"/>	
		<p style="font-weight:800; font-size:30px;">
			Welcome, <?php echo $_SESSION['first'] . " " . $_SESSION['last'] ." (" . $_SESSION['group_name'] . ")"; ?>
		</p>
		<p style="font-family:Georgia,serif; color:#564C23">
		This is the home of the Exercise eDiary for <?php echo date('Y'); ?>.<br/><br/>
		For more information please see the <a href=""> About</a> section.
		</p>
	</div>
	<div id="content">
	<h3> Student Reports</h3>
	<ol>
        <li>Lifestyle data</li>

            <ul>
                <li><a href="individual_Life_w_reports.php"> Average & STD per week </a></li>
                <li><a href="individual_Life_d_reports.php"> Average & STD by day of the week</a></li>
            </ul>

        <li>Physical Activity Data</li>

        <ul>
            <li>MET</li>

                        <ul>
                        <li><a href="individual_total_met_reports.php"> Total METs </a></li>
                        <li><a href="individual_total_met_w_reports.php"> Total METs for each week</a></li>
                        <li><a href="individual_total_met_d_reports.php"> Total METs for each day</a></li>
                        <li><a href="individual_avg_met_w_reports.php"> Average & SD METs per week</a></li>
                        <li><a href="individual_avg_met_d_reports.php"> Average & SD METs per day</a></li>
                        <li><a href="individual_avg_met_dw_reports.php"> Average & SD METs for each day of the week</a></li>
                        </ul>

        </ul>

        <ul>
            <li>Hours</li>

                        <ul>
                        <li><a href="individual_total_min_reports.php"> Total mins</a></li>
                        <li><a href="individual_total_min_w_reports.php"> Total mins for each week</a></li>
                        <li><a href="individual_avg_min_w_reports.php"> Average & SD hours per week</a></li>
                        <li><a href="individual_avg_min_d_reports.php"> Average & SD hours per day</a></li>
                        <li><a href="individual_avg_min_dw_reports.php"> Average & SD hours for each day of the week</a></li>
                        </ul>
        </ul>
        <ul>
            <li>Hours broken for each fitness component </li>

                        <ul>
                        <li><a href="individual_total_e_min_reports.php"> Total mins</a></li>
                        <li><a href="individual_total_e_min_w_reports.php"> Total mins for each week</a></li>
                        <li><a href="individual_avg_min_e_w_reports.php"> Average & SD hours per week</a></li>
                        <li><a href="individual_avg_min_e_d_reports.php"> Average & SD hours per day</a></li>
                        <li><a href="individual_avg_min_e_dw_reports.php"> Average & SD hours for each day of the week</a></li>
                        </ul>
        </ul>
        </li>
        <ul>
            <li>Stats </li>

                        <ul>
                        <li><a href="individual_total_rest_reports.php"> No. of days without exercise</a></li>
                        <li><a href="individual_total_e_min_w_reports.php"> Most common day without exercise </a></li>
                        </ul>
        </ul>
  <li>Illness & injury data</li>
      <ul>
                <li><a href="individual_occasion_reports.php"> Occasions (counts)</a></li>
                <li><a href="individual_avg_dur_reports.php"> Average duration (no of days)</a></li>
                <li><a href="individual_ill_ex_reports.php"> No of days ill but also exercised</a></li>
                <li><a href="individual_ill_reports.php"> No of days ill but did not exercise</a></li>
            </ul>
</ol>

	</div>
	<div id="right-nav">Some<br/><br/>user-specific<br/><br/>stats<br/><br/><br/><br/>Weekly Average<br/><br/>Group Average etc</div>
</div>
<?php draw_footer(); ?>
