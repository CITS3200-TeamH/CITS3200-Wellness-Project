
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
		
    // Decode the json array
        $json_arr=json_decode($_GET['data'],true);
        
    // Print the given array
        echo json_encode($json_arr) . "<br>";
        
    // Print the student id    
        echo "student id = ". $json_arr[student] . "<br>";
        
    // Print some training data
        echo "training data = ". $json_arr[training_records1][0][daydate] . "<br>";
        
    // Print success story
        echo "<br> Yay :D it worked, below we'll start to build the actual page. <br><br><br><br>";



    // Student data
	echo "age:2   <br>";






    // Training Records 1
	for($i=0; $i<count($json_arr[training_records1]); $i++) {
	   // Get needed data
		$daydate_millisec = $json_arr[training_records1][$i][daydate];
		$compcode = $json_arr[training_records1][$i][compcode];
		$start = $json_arr[training_records1][$i][start];
		$end = $json_arr[training_records1][$i][end];
		$TOD = $json_arr[training_records1][$i][time_of_day];
		$class = $json_arr[training_records1][$i]['class'];

	  // Work variables to use with database
		$daydate = date("Y-m-d", $daydate_millisec/1000); 
		$duration = (strtotime($end)-strtotime($start))/60;	 

	 // Query whether entry is already in database
		
		$sql = "SELECT * FROM training_records1 WHERE student_id='$username' AND daydate='$daydate' AND compcode='$compcode' AND class='$class' AND time_of_day='$TOD'";
		$rows = mysql_query($sql) or die("error-5");
	
	 // If an entry was found update it, otherwise create a new entry
		if (mysql_num_rows($rows) != 0) {
			$sql = "UPDATE training_records1 SET start='$start', end='$end', duration='$duration'  WHERE student_id='$username' AND daydate='$daydate' AND compcode='$compcode' AND class='$class' AND time_of_day='$TOD'";
			mysql_query($sql) or die("error-5"); 
		} else {										
			$sql = "INSERT INTO training_records1 VALUES('$daydate', '$compcode', $duration, '$start', '$end', '$username', '$class', '$TOD',\' \' )";
			mysql_query($sql) or die("error-5");
		}
	}
   // End of Trainging Records 1




  // Training Records 2
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        /*
        
        $sql="SELECT * FROM student, classmap, class WHERE id='$username' AND id=student_id AND name=class_name";
		$result = mysql_fetch_array(mysql_query($sql));
		$lower = strtotime($result["start"]);
		$upper = strtotime($result["finish"]);
		$today = strtotime(date("Y-m-d"));
		$window = $result["window"];
		
		//!!!!!!!!!!!!!!!!! change this to POST
		$json_arr=json_decode($_GET['data'],true);
		echo "<br>" . $json_arr;
		echo "<br> Received JSON message: <br>";
		echo json_encode($json_arr);
		echo "<br>";
			
		//Code for student
	//echo "number of students given: " . count($json_arr[student]);
	//echo "<br>";
		//for($i=0; $i<count($json_arr[student]); $i++){
			echo "student id =". $json_arr["student"] . "<br>";
			
		//}
			update student with id=$arr["student"][i]["student_id"]
				variables to update:
					age
					active
					gender
					athletic
					sport
				
			
			
		//Code for training_records1
//	echo  $json_arr["training_records1"][0];
//	echo $record["daydate"];
	
		for($i=0; $i<count($json_arr[training_records1]); $i++) {
                		//echo json_decode($json_arr[training_records1][$i]) . "<br>";
				$training_record = json_decode($json_arr[training_records1][$i], true);
		                echo $training_record;
				//echo "daydate = " . $training_record[daydate] . "<br>";
                		//echo "compcode = " . $training_record[compcode] . "<br>";
			}					

        
        for($j=0; $j<count($json_arr[training_records1][$i]); $j++) {
            echo "value for $i $j  = " . $json_arr[training_records1][$i][$j] . " <br>";
            
             
             }	


			

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
        
        for($i=0; $i<count($json_arr[training_records2]); $i++) {
            for($j=0; $j<count($json_arr[training_records2][$i]); $j++) {
                echo "value for $i $j  = " . $json_arr[training_records2][$i][$j] . " <br>";
            }
        }
			
		
			for($i=0;$i<count($arr["training_records1"]);i++){
				
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
             
        for($i=0; $i<count($json_arr[fitness_test]); $i++) {
            for($j=0; $j<count($json_arr[fitness_test][$i]); $j++) {
                echo "value for $i $j  = " . $json_arr[fitness_test][$i][$j] . " <br>";
            }
        }
        
             
			for($i=0;$i<count($arr["fitness_test"]);i++){
				
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
