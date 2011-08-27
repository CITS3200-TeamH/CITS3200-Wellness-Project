<?php
	// A pie chart representation of time of exercise to date
	include '../layout.php';
	include '../config.php';
	include '../connect.php';
	include("../pChart/pData.class");
	include("../pChart/pChart.class");
	check_student();
	$id = $_SESSION['username'];
	$group = $_SESSION['group_name'];
	check_student();
	if($res = mysql_query('SELECT time_of_day FROM training_records1 WHERE class="'.$group.'" AND student_id='.$id.'')) {
		$set = array(0,0,0,0,0,0,0,0);
		$cats = array("Night","Early Morning","Morning","Mid Morning","Midday","Early Afternoon","Late Afternoon","Evening");		
		while($row = mysql_fetch_array($res)) {
			switch($row['time_of_day']) {
				case 'Night':
					$set[0]++; break;
				case 'Early Morning':
					$set[1]++; break;
				case 'Morning':
					$set[2]++; break;
				case 'Mid Morning':
					$set[3]++; break;				
				case 'Midday':
					$set[4]++; break;				
				case 'Early Afternoon':
					$set[5]++; break;				
				case 'Late Afternoon';
					$set[6]++; break;				
				case 'Evening':
					$set[7]++; break;				
			}
		}
		for($i=0;$i<8;$i++) {
			if($set[$i] == 0) {
				unset($set[$i]);
				unset($cats[$i]);
			}
		}
		$set = array_values($set);
		$cats = array_values($cats);		
	}else {
		echo mysql_error();
	}


	// Dataset definition 
	$DataSet = new pData;
	$DataSet->AddPoint($set,"Serie1");
	$DataSet->AddPoint($cats,"Serie2");
	$DataSet->AddAllSeries();
	$DataSet->SetAbsciseLabelSerie("Serie2");

	// Initialise the graph
	$Test = new pChart(600,400);
	$Test->drawFilledRoundedRectangle(7,7,593,393,5,240,240,240);
	$Test->drawRoundedRectangle(5,5,595,395,5,230,230,230);
	//$Test->createColorGradientPalette(195,204,56,223,110,41,5);
	$Test->createColorGradientPalette(25,67,200,200,10,45,8);	

	// Draw the pie chart
	$Test->setFontProperties("../Fonts/tahoma.ttf",10);
	$Test->AntialiasQuality = 0;
	$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),300,180,180,PIE_PERCENTAGE_LABEL,FALSE,40,40,10);
	$Test->drawPieLegend(460,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);

	// Write the title
	$Test->setFontProperties("../Fonts/GeosansLight.ttf",15);
	$Test->drawTitle(20,30,"Exercise Time Breakdown To Date",0,0,0);

	$Test->Stroke("time_breakdown_3dpie.png");
?>
