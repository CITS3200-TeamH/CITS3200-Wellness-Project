<?php
	// A pie chart representation of fitness component contributions to total exercise to date
	include '../layout.php';
	include '../config.php';
	include '../connect.php';
	include("../pChart/pData.class");
	include("../pChart/pChart.class");
	check_admin();
	$class = $_GET['group'];
	$class="Jake's Test Group";
	//GET RELEVANT DATES
	$res2 = mysql_query('SELECT start,finish FROM class WHERE name="'.$class.'"');
	if(!$res2) {
		echo mysql_error();
	}
	$row2 = mysql_fetch_array($res2);
	$start = $row2['start'];
	$end = $row2['finish'];
	$date = date('Y-m-d');
	if(strtotime($date) > strtotime($end)) {
		$date = $end;
	}

    $res = mysql_query('SELECT c.type as type,sum(tr1.duration) as total_mins,Sum(tr1.duration * c.mets) as total_mets,Sum(tr1.duration * c.mets) / (select Sum(tr1.duration * c.mets) 
    FROM training_records1 tr1, compcodes c
	WHERE tr1.class="'.$class.'" and daydate<="'.$date.'" and c.compcode=tr1.compcode) * 100 as prop
	FROM training_records1 tr1, compcodes c
	WHERE tr1.class="'.$class.'" and daydate<="'.$date.'" and c.compcode=tr1.compcode
	GROUP BY c.type');
	
	while($row = mysql_fetch_array($res)) {
		$data[0][] = $row['type'];
		$data[1][] = $row['prop'];
	}

	// Dataset definition 
	$DataSet = new pData;
	$DataSet->AddPoint($data[1],"Serie1");
	$DataSet->AddPoint($data[0],"Serie2");
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
	$Test->drawPieLegend(420,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);

	// Write the title
	$Test->setFontProperties("../Fonts/MankSans.ttf",15);
	$Test->drawTitle(20,30,"Fitness Component Breakdown to Date",0,0,0);

	$Test->Stroke("ind_comp_breakdown_3dpie.png");
?>
