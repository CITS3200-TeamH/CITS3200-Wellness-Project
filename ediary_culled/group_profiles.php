<?php
	include 'layout.php';
	check_admin();
	include 'config.php';
	include 'connect.php';
	
	$group = $_POST['group_name'];
	$group = stripslashes($group);
	$group = mysql_real_escape_string($group);
	
	$res = mysql_query('SELECT * FROM student s,classmap c WHERE c.class_name="'.$group.'" AND s.id=c.student_id ORDER BY s.last');
	
	$csv .= 'Subject ID,Surname,First,DOB,Gender,Athletic,Sport,Level'."\n";	
	//if(!$res) {  $csv .= '1,2,3,4,5,error'."\n";}
	
	while($row = mysql_fetch_array($res)) {
		$csv .= $row['id'] .',"'. $row['last'] .'","'. $row['first'] .'",'. $row['age'] .','. $row['gender'] .','. $row['athletic'] .',"'. $row['sport'] .'","'. $row['level'] .'"' ."\n";
	}
	$file = 'export';
	$filename = $file;
	header("Content-type: text/csv");
	header("Content-disposition: csv" . date("Y-m-d") . ".csv");
	header("Content-disposition: filename=".$filename.".csv");
	print $csv;
	exit;
?>
