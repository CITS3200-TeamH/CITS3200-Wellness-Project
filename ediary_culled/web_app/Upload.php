
This page is allowing invalid tokens.
<br>
Stuck? try this: Upload.php?data={"student":[1,2,3,4]}
<br>

<?php
include "../api_authFunctions.php";

if (isset($_POST["token"]) || isset($_GET["token"])){ //check to see if the token has been sent
	$id;
	if (isset($_POST["token"]) && isset($_POST["data"])) { //now we check so see if we have both the token AND the xml data
		$id = validateToken($_POST["token"]); //validate the token
	} else if (isset($_GET["token"]) && isset($_GET["data"])) {
		$id = validateToken($_GET["token"]);
	} else {
		echo "error-3";
	}
	if ($id != "error-2") {
		uploadXML($id);
	} else {
		echo "error-2"; //an invalid token should produce an error
	}
} else {
	//echo "error-1";
	uploadXML(1234567);
}

	function uploadXML($username) {
		$sql="SELECT * FROM student, classmap, class WHERE id='$username' AND id=student_id AND name=class_name";
		$result = mysql_fetch_array(mysql_query($sql));
		$lower = strtotime($result["start"]);
		$upper = strtotime($result["finish"]);
		$today = strtotime(date("Y-m-d"));
		$window = $result["window"];
		
		//!!!!!!!!!!!!!!!!! change this to POST
		$json_arr=json_decode($_GET['data'],true);
		echo "<br> Received JSON message: <br>";
		
		
		//Uncomment this if you wish it will reprint the json array
		echo json_encode($json_arr);
		echo "<br>";
		//echo " "+$json_arr;	
		//Code for student
	echo "number of students given: " . count($json_arr[student]);
	echo "<br>";
		for($i=0; $i<count($json_arr[student]); $i++){
			echo "student id =". $json_arr[student][$i] . "<br>";
			
		}
		/*	update student with id=$arr["student"][i]["student_id"]
				variables to update:
					age
					active
					gender
					athletic
					sport
				*/
			
			
	/*	//Code for training_records1
			for($i=0;$i<count($arr["training_records1"]);i++){
				/*
				insert into training_records1 variables:
					$arr["training_records1"][i]["daydate"] !!warning in milliseconds
					$arr["training_records1"][i]["compcode"]
					$arr["training_records1"][i]["start"]
					$arr["training_records1"][i]["end"]
					$arr["training_records1"][i]["student_id"]
					$arr["training_records1"][i]["time_of_day"]
					$arr["training_records1"][i]["class"]
				
			}
			
		//Code for training_records2
			for($i=0;$i<count($arr["training_records1"]);i++){
				/*
				check if there is a record yet for this student,date and class
				then update or insert appropriately
				
				training_records2 variables:
					$arr["training_records2"][i]["daydate"] !!warning in milliseconds
					$arr["training_records2"][i]["heart_rate"]
					$arr["training_records2"][i]["sleep"]
					$arr["training_records2"][i]["ratings"]
					$arr["training_records2"][i]["student_id"]
					$arr["training_records2"][i]["health"]
					$arr["training_records2"][i]["class"]
				
			}
			
		//Code for fitness_test
			for($i=0;$i<count($arr["fitness_test"]);i++){
				/*
				insert into training_records1 variables:
					$arr["fitness_test"][i]["daydate"] !!warning in milliseconds
					$arr["fitness_test"][i]["subject_id"]
					$arr["fitness_test"][i]["group_id"]
					$arr["fitness_test"][i]["situp"]
					$arr["fitness_test"][i]["pushup"]
					$arr["fitness_test"][i]["chinup"]
					$arr["fitness_test"][i]["hang"]
					$arr["fitness_test"][i]["sitreach1"]
					$arr["fitness_test"][i]["sitreach2"]
					$arr["fitness_test"][i]["height"]
					$arr["fitness_test"][i]["mass"]
					$arr["fitness_test"][i]["waist"]
					$arr["fitness_test"][i]["hip"]
				
			}
    */
    
	}
?>
