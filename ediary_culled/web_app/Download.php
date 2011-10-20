<?php
include "../api_authFunctions.php";

if (isset($_POST["token"]) || isset($_GET["token"])){
	$id;
	if(isset($_POST["token"])){
		$id=validateToken($_POST["token"]);
	} else if(isset($_GET["token"])){
		$id=validateToken($_GET["token"]);
	}
	if($id != "invalid") {
		sync($id);
	} else {
		echo "error-2";
	}
} else {
	echo "Submission_Error";
}

function sync($username){
	$sql="Select * From student, classmap, class Where id='$username' And id=student_id And name=class_name";
	$result = mysql_fetch_array(mysql_query($sql));
	$lower = strtotime($result["start"]);
	$window = $result["window"];
	$class = $result["name"];

	//Define each table as an empty table
	$arr["student"] = array();
	$arr["class"] = array();
	$arr["classmap"] = array();
	$arr["compcodes"] = array();
	$arr["training_records1"] = array();
	$arr["training_records2"] = array();
	$arr["fitness_test"] = array();
	$arr["rating_item"] = array();
	$arr["rating_item_map"] = array();
	
	$arr["student"][0]["id"] = $username;
	$arr["student"][0]["first"] = $result["first"];
	$arr["student"][0]["last"] = $result["last"];
	$arr["student"][0]["password"] = $result["password"];
	$arr["student"][0]["last_login"] = 1000*strtotime($result["last_login"]);
	$arr["student"][0]["age"] = $result["age"];
	$arr["student"][0]["active"] = $result["active"];
	$arr["student"][0]["gender"] = $result["gender"];
	$arr["student"][0]["athletic"] = $result["athletic"];
	$arr["student"][0]["sport"] = $result["sport"];

	$arr["class"][0]["name"] = $result["name"];
	$arr["class"][0]["start"] = 1000*strtotime($result["start"]);
	$arr["class"][0]["finish"] = 1000*strtotime($result["finish"]);
	$arr["class"][0]["window"] = $result["window"];

	$arr["classmap"][0]["student_id"] = $username;
	$arr["classmap"][0]["class_name"] = $result["name"];

		$windowEndDate = date("Y-m-d", mktime(0,0,0,date("m"),(date("d")-$window+1),date("Y")));	
		
		$sql = "Select daydate,student_id,class,heart_rate,sleep,health,ratings From training_records2 Where student_id='$username' And daydate>='$windowEndDate'";
		$compcodes = mysql_query($sql);
		$row = 0;
		while(($compcode = mysql_fetch_array($compcodes)) !=null) {
			$arr["training_records2"][$row]["daydate"] = 1000*strtotime($compcode["daydate"]);
			$arr["training_records2"][$row]["student_id"] = $compcode["student_id"];
			$arr["training_records2"][$row]["class"] = $compcode["class"];
			$arr["training_records2"][$row]["heart_rate"] = $compcode["heart_rate"];
			$arr["training_records2"][$row]["sleep"] = $compcode["sleep"];
			$arr["training_records2"][$row]["health"] = $compcode["health"];
			$arr["training_records2"][$row]["ratings"] = $compcode["ratings"];
			$row++;
		}

		$sql = "Select groupname, id From rating_item_map Where groupname='$class'";
		$ratingitemmaps = mysql_query($sql);
		$row = 0;
		while(($ratingitemmap = mysql_fetch_array($ratingitemmaps))!=null){
			$arr["rating_item_map"][$row]["groupname"] = $ratingitemmap["groupname"];
			$arr["rating_item_map"][$row]["id"] = $ratingitemmap["id"];
			$row++;
		}
		
		
		$sql =  "Select id,description,summary From rating_item Where id In (Select id From rating_item_map Where groupname='$class')";
		$ratingitems = mysql_query($sql);
		$row = 0;
		while(($ratingitem = mysql_fetch_array($ratingitems))!=null){
			$arr["rating_item"][$row]["id"] = $ratingitem["id"];
			$arr["rating_item"][$row]["description"] = $ratingitem["description"];
			$arr["rating_item"][$row]["summary"] = $ratingitem["summary"];
			$row++;
		}

		$sql = "Select * From fitness_test Where subject_id='$username' And group_id='$class' And daydate>='$windowEndDate'";
		$fitnesstests = mysql_query($sql);
		$row = 0;
		while(($fitnesstest = mysql_fetch_array($fitnesstests))!=null){
			$arr["fitness_test"][$row]["subject_id"] = $fitnesstest["subject_id"];
			$arr["fitness_test"][$row]["group_id"] = $fitnesstest["group_id"];
			$arr["fitness_test"][$row]["daydate"] = 1000*strtotime($fitnesstest["daydate"]);
			$arr["fitness_test"][$row]["test_num"] = $fitnesstest["test_num"];
			$arr["fitness_test"][$row]["pushup"] = $fitnesstest["pushup"];
			$arr["fitness_test"][$row]["situp"] = $fitnesstest["situp"];
			$arr["fitness_test"][$row]["chinup"] = $fitnesstest["chinup"];
			$arr["fitness_test"][$row]["hang"] = $fitnesstest["hang"];
			$arr["fitness_test"][$row]["sitreach1"] = $fitnesstest["sitreach1"];
			$arr["fitness_test"][$row]["sitreach2"] = $fitnesstest["sitreach2"];
			$arr["fitness_test"][$row]["height"] = $fitnesstest["height"];
			$arr["fitness_test"][$row]["mass"] = $fitnesstest["mass"];
			$arr["fitness_test"][$row]["waist"] = $fitnesstest["waist"];
			$arr["fitness_test"][$row]["hip"] = $fitnesstest["hip"];
			$row++;
		}
		
		$sql = "Select * From training_records1 Where student_id='$username' And class='$class' And daydate>='$windowEndDate'";
		$trainingrecords = mysql_query($sql);
		$row = 0;
		while(($trainingrecord = mysql_fetch_array($trainingrecords))!=null){
			$arr["training_records1"][$row]["daydate"] = 1000*strtotime($trainingrecord["daydate"]);
			$arr["training_records1"][$row]["compcode"] = $trainingrecord["compcode"];
			$arr["training_records1"][$row]["duration"] = $trainingrecord["duration"];
			$arr["training_records1"][$row]["start"] = $trainingrecord["start"];
			$arr["training_records1"][$row]["end"] = $trainingrecord["end"];
			$arr["training_records1"][$row]["class"] = $trainingrecord["class"];
			$arr["training_records1"][$row]["student_id"] = $trainingrecord["student_id"];
			$arr["training_records1"][$row]["time_of_day"] = $trainingrecord["time_of_day"];
			$row++;
		}


		$sql = "Select * From compcodes";
		$compcodes = mysql_query($sql);
		$row = 0;
		while(($compcode = mysql_fetch_array($compcodes))!=null){
			$arr["compcodes"][$row]["compcode"] = $compcode["compcode"];
			$arr["compcodes"][$row]["mets"] = $compcode["mets"];
			$arr["compcodes"][$row]["type"] = $compcode["type"];
			$arr["compcodes"][$row]["heading"] = $compcode["heading"];
			$arr["compcodes"][$row]["description"] = $compcode["description"];
			$row++;
		}

		echo json_encode($arr);
}
?>
