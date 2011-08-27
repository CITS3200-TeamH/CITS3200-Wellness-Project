<?php
	include 'config.php';
	include 'connect.php';
	include 'layout.php';
	check_student();
	draw_headerin();
	draw_navin();
	$id = $_SESSION['username'];
	$group = $_SESSION['group_name'];
	
	//get the ratings from training records 2
	$res = mysql_query('SELECT * FROM training_records2 WHERE class="'.$group.'" AND student_id='.$id.' AND health<=2 ORDER BY daydate');
?>

<script type="text/javascript">
	function graph() {
		document.getElementById("dialog").innerHTML = '<img src="visualisations/time_breakdown_3dpie.php">';
		$("#dialog").dialog({ hide: 'slide', show:'fold', close: function(event,ui) { $("#dialog").dialog("destroy"); }, 
			buttons: { "Close": function() { $(this).dialog("close"); } }, width: 630, height: 520 });
	}
</script>

<div id="main">
<div id="dialog" style="display:none;"></div>
<!-- BREADCRUMBS -->
<span class="reporting_breadcrumbs" style="margin:-5px 0px 10px 0px; font-size:8pt; font-family:Arial,Helvetica,sans-serif; float:left; color:black;">
	<a href="subject_select_reports.php">Reporting</a>
 	> <a href="subject_view_s_reports.php">Individual Summary Reports</a> > Summary of Illness
</span>
<div id="welcome-box" style="height:50px;">
		<p style="font-weight:800; font-size:20px; margin-top:20px;">Summary of Illness</p>
</div>
<div id="content_wide" style="overflow-x:auto;">
		
<?php
	if($res) {
		if(mysql_num_rows($res) > 0) {
			//scan the data to find number of periods of illness and average length of these
			$i = 0;
			$num = 0;
			while($row = mysql_fetch_array($res)) {
				//get the date
				$date = $row['daydate'];
				
				//compare the dates on all passes except the initial pass
				if($i > 0) {
					//if date is consecutive, increment current period counter
					if(strtotime("+ 1 day",strtotime($last)) == strtotime($date)) {
						$period[$num] = $period[$num] + 1;
					}else {
						//consecutive days have ended so start a new counter, for a new period
						$num++;
						$period[$num] = 1;
					}
				}else {
					$period[$num] = 1;
				}
				$last = $date;
				$i++;
			}
			
			echo '<div style="background-color:rgb(245,245,245); min-height:300px; border:solid 1px rgb(210,210,210); padding:20px;">';
			echo '
			<p style="font-size:10pt; background-color:white; padding:10px;	-moz-border-radius:10px; -webkit-border-radius:10px; border:solid 1px rgb(210,210,210);">
			<b>Number of Days Ill:</b> '.mysql_num_rows($res).'<br/><br/>';
			
			$res2 = mysql_query('SELECT distinct(tr1.daydate) FROM training_records1 tr1,training_records2 tr2 WHERE tr1.class="'.$group.'" AND tr1.student_id='.$id.' AND tr2.health<=2 AND tr2.class="'.$group.'" AND tr2.student_id='.$id.' AND tr1.class=tr2.class AND tr1.student_id=tr2.student_id AND tr1.daydate=tr2.daydate');
			if(!$res2) { echo mysql_error(); }
			
			echo '
			<b>Number of Days Ill and Also Exercised:</b> '.mysql_num_rows($res2).'<br/><br/>
			<b>Number of Days Ill but Did Not Exercise:</b> '.(mysql_num_rows($res)-mysql_num_rows($res2)).'<br/><br/>
			<b>Number of Separate Periods Of Illness:</b> '.count($period).'<br/><br/>
			<b>Average Duration of Illness:</b> '.round((mysql_num_rows($res)/count($period)),3).' days</p></div>';			
		}else {
			echo '
			<div class="std_form" style="margin:0px 0px 20px 0px;">
			No instance of illness has been recorded yet.<br/><br/>
			The illness summary will become active once the first instance is logged.
			</div>
			<a href="subject_view_s_reports.php" class="links">Back</a>';
		}
	}else {
		echo mysql_error();
	}
	echo '
	</div>
	</div>';
	draw_footer();
?>
