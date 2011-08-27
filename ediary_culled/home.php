<?php
	include 'layout.php';
	include 'config.php';
	include 'connect.php';	
	include 'calendar.php';
	check_student();
	draw_headerin();
	draw_navin();
	$group = stripslashes($_SESSION['group_name']);
	
	function weeklyTotal() {
		$id = $_SESSION['username'];
		$group = $_SESSION['group_name'];
		$query = 'select WEEK(tr1.daydate,5) as week_number, sum(mets)* tr1.duration as Total_mets FROM training_records1 tr1,compcodes 
			where compcodes.compcode = tr1.compcode and tr1.student_id='.$id.' and tr1.class="'.$group.'"
			GROUP BY week_number';
		$result = mysql_query($query);

		if($result) {
			while($row=mysql_fetch_array($result)) {
				if(date('W') == $row['week_number']) {
					$weeklyTotal = $row['Total_mets'];
				}
			}
		}else {
			echo mysql_error();
			exit();
		}
		return $weeklyTotal;
	}
	
	function weeklyAverage() {
		$id = $_SESSION['username'];
		$group = $_SESSION['group_name'];
		$query = 'select WEEK(tr1.daydate,5) as week_number, AVG((mets)* tr1.duration) as Avg_mets, STD((mets)* tr1.duration) as STD_mets
		FROM training_records1 tr1,compcodes
		where compcodes.compcode = tr1.compcode and tr1.student_id='.$id.' and tr1.class="'.$group.'"
		GROUP BY week_number';
		$result = mysql_query($query);
		if($result) {
			while($row=mysql_fetch_array($result)) {
				if(date('W') == $row['week_number']) {
					$weeklyAvg = $row['Avg_mets'];
					$weeklyAvg = number_format($row['Avg_mets'],2);
				}
			}
		}else {
			echo mysql_error();
			exit();
		}
		return $weeklyAvg;
	}

	$groupname = stripslashes($_SESSION['group_name']);
?>
<script type="text/javascript">
	function time_breakdown_popup(){
		document.getElementById("dialog").innerHTML = '<img src="visualisations/time_breakdown_3dpie.php">';
		$("#dialog").dialog({ hide: 'slide',show: 'fold', position:[400,150], close: function(event,ui) { $("#dialog").dialog("destroy"); }, 
			buttons: { "Close": function() { $(this).dialog("close"); } }, width: 630, height: 550 });
					
	}
</script>

<div id="main">
	<div id="dialog" name="dialog"></div>
	<div id="anchor" style="display:none;"></div>
	<div id="content_wide" style="padding-top:0px; padding-right:0px; width:1009px; height:410px; background-color:white;">
	<?php 
		echo '<div id="right-nav" style="width:249px; min-height:400px; height:auto; margin:0px 0px 0px 0px; -moz-border-radius:10px; -webkit-border-radius:10px; font-family:Arial; font-size:10pt;">
		<b style="font-size:12pt; color:black;">
		'.$group.' 
		</b><hr/>
		<b>Total METs this week:</b> '. weeklyTotal() . 
		'<br/><br/><b>Average METs/ Session:</b> ' . weeklyAverage() .
		'<br/><br/>
		<span style="font-weight:bold; text-decoration:underline"> Visualisations</span><br/><br/>
		<a class="graph_link" href="#anchor"><img src="images/bullet-link.gif" style="float:left; border:none; margin: 5px 5px 0px 0px;"/>	
		<span onclick="time_breakdown_popup();">Exercise Time Breakdown</span></a>
		</div>';
		draw_cal(); 
	echo '</div>';
	?>
	
</div>
<?php draw_footer(); ?>
