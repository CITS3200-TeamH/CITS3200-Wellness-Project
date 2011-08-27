<?php
	include 'layout.php';
	include 'calendar.php';
	check_student();
	draw_headerin();
	draw_navin();
?>
<div id="main">
	<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px; margin-top:20px;">Summary Reports</p>
	</div>
	<div id="content">
	<a href="subject_view_s_reports.php" style="color:black; font-size:16pt; border:none; text-decoration:none;">
	<div class="blue_form" style="margin-left:auto; margin-right:auto; width:500px; height:60px; background-color:rgb(245,245,245); border-color:rgb(210,210,210);">
		    <img src="images/user.png" style="border:none; float:left; margin-bottom:20px;"/>
		    <span style="margin:20px 0px 0px 30px; font-size:16pt; float:left; color:rgb(80,80,80);">Individual Reports</span>
    </div>
    </a>
    <a href="subject_view_g_reports.php" style="color:black; font-size:16pt; border:none; text-decoration:none;">
	<div class="blue_form" style="margin-left:auto; margin-right:auto; margin-top:40px; width:500px; height:60px; background-color:rgb(245,245,245); border-color:rgb(210,210,210);">
		    <img src="images/group.png" style="float:left; border:none;"/>
		    <span style="margin:20px 0px 0px 30px; font-size:16pt; float:left; color:rgb(80,80,80);">Group Reports</span>		    
	</div>
	</a>

	</div>
	<div id="right-nav" style="width:249px; font-size:10pt;"><b>Summary Reports</b><hr/>Individual reports contain summaries of your data to date.<br/><br/>Group reports contain summaries of the data for the entire group.<br/><br/></div>
	</div>
<?php draw_footer(); ?>
