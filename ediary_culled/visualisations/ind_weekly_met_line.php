<?php   
	include '../config.php';
	include '../connect.php';
	include '../layout.php';
	include("../pChart/pData.class");
	include("../pChart/pChart.class");
	include("../pChart/pCache.class");
	check_student();
	$class = $_SESSION['group_name'];
	$id = $_SESSION['username'];


	$res2 = mysql_query('SELECT start,finish FROM class WHERE name="'.$class.'"');
	if(!$res2) {
		echo mysql_error();
	}
	$row2 = mysql_fetch_array($res2);
	$start = $row2['start'];
	$end = $row2['finish'];	

	$result = mysql_query('SELECT CEILING(DATEDIFF(tr1.daydate,\''.$start.'\')/7+(1/10000000)) as week_number, sum(mets* tr1.duration) as Total_mets FROM training_records1 tr1,compcodes WHERE compcodes.compcode = tr1.compcode and tr1.student_id='.$id.' AND tr1.class="'.$class.'" GROUP BY week_number');
	if(!$result) {
		echo mysql_error();
	}
	//GET THE MAXIMUM WEEK 
	$date = date('Y-m-d');
	if(strtotime($date) > strtotime($end)) {
		$date = $end;
	}
	$maxquery = mysql_query('SELECT CEILING(DATEDIFF(\''.$date.'\',\''.$start.'\')/7+(1/10000000)) as max');
	if(!$maxquery) { echo mysql_error(); }
	$maxrow = mysql_fetch_array($maxquery);
	$max = $maxrow['max'];
	
	for($i=0;$i<=$max;$i++) {
		$dt[1][$i] = $i;
		$dt[0][$i] = 0;
	}
	
	//TODO  use current date or end date. end if current date > end, current date if end has not been reached. 
	//		this makes sure the graph plots any current empty weeks if they lie after the full weeks. 
	
	while($row = mysql_fetch_array($result)) {
		$dt[0][$row['week_number']] = $row['Total_mets'];
	}
  
	// Dataset definition
	$DataSet = new pData;
	//$DataSet->AddPoint(array(100,320,200,10,43),"Serie1");
	$DataSet->AddPoint($dt[0],"Serie1");	
	//$DataSet->AddPoint(array(23,432,43,153,234),"Serie2");
	//$DataSet->AddPoint(array(1217541600,1217628000,1217714400,1217800800,1217887200),"Serie3");
	$DataSet->AddPoint($dt[1],"Serie3");	
	$DataSet->AddSerie("Serie1");
	//$DataSet->AddSerie("Serie2");
	$DataSet->SetAbsciseLabelSerie("Serie3");
	$DataSet->SetSerieName("Total METs","Serie1");
	//$DataSet->SetSerieName("Outgoing","Serie2");
	$DataSet->SetYAxisName("Total METs");
	$DataSet->SetXAxisName("Week");	
	//$DataSet->SetYAxisFormat("time");
	//$DataSet->SetXAxisFormat("date");

	// Initialise the graph   
	$Test = new pChart(700,245);
	$Test->setFontProperties("../Fonts/tahoma.ttf",8);
	$Test->setGraphArea(85,30,650,200);
	$Test->drawFilledRoundedRectangle(7,7,693,238,5,240,240,240);
	$Test->drawRoundedRectangle(5,5,695,240,5,230,230,230);
	$Test->drawGraphArea(255,255,255,TRUE);
	$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
	$Test->drawGrid(4,TRUE,230,230,230,50);

	// Draw the 0 line   
	$Test->setFontProperties("../Fonts/tahoma.ttf",6);
	$Test->drawTreshold(0,143,55,72,TRUE,TRUE);

	// Draw the line graph
	$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
	$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

	// Finish the graph
	$Test->setFontProperties("../Fonts/tahoma.ttf",8);
	$Test->drawLegend(90,35,$DataSet->GetDataDescription(),255,255,255);
	$Test->setFontProperties("../Fonts/tahoma.ttf",10);
	$Test->drawTitle(60,22,"Total METs/Week",50,50,50,585);
	$Test->Stroke("ind_weekly_met_line.png");
?>
