
<br>
This page is allowing invalid tokens!!
<br>

<?php
include "../api_authFunctions.php";
include "../rating_calc.php";

if (isset($_POST["token"]) || isset($_GET["token"])){ //check to see if the token has been sent
	if (isset($_POST["token"]) && isset($_POST["data"])) { //now we check so see if we have both the token AND the xml data
		$id = validateToken($_POST["token"]); //validate the token
	} else if (isset($_GET["token"]) && isset($_GET["data"])) {
		$id = validateToken($_GET["token"]);
	} else {
		echo "error-2";
	}
	if (isset($id) && $id != "error-2") {
		uploadXML($id);
	} else {
		echo "error-2"; //an invalid token should produce an error
	}
} else {
	echo "error-1";
}


function uploadXML($username) {
		
// Decode the json array
        //$json_arr=json_decode($_GET['data'],true);
        $json_arr=json_decode($_POST['data'],true);
    // Print the given array
        echo json_encode($json_arr) . "<br>";
        
    // Print the student id    
        echo "student id = ". $json_arr[student][0][id] . "<br>";
        
    // Print some training data
        echo "training data = ". $json_arr[training_records1][0][daydate] . "<br>";
        
    // Print success story
        echo "<br> Yay :D it worked, below we'll start to build the actual page. <br><br><br><br>";



    // Student
    echo "Starting Student<br>";
        // Get needed data
        $age = $json_arr[student][0][age];
        $active = $json_arr[student][0][active];
        $gender = $json_arr[student][0][gender];
        $athletic = $json_arr[student][0][athletic];
        $sport = $json_arr[student][0][sport];
    
        // Update given fields
        $sql = "UPDATE student SET age='$age', active='$active', gender='$gender', athletic='$athletic', sport='$sport' WHERE id='$username'";
        mysql_query($sql) or die("error-5 #1 <br>");
        echo "query #1 run"; 
    echo "Student done<br>";
    // End of Student
        
        

    // Training Records 1
    echo "Starting Training Records 1<br>";
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
		$rows = mysql_query($sql) or die("error-5 #1");
		echo "query #1 run <br>";		
		echo "rows found:" . mysql_num_rows($rows) . "<br>";		

	 // If an entry was found update it, otherwise create a new entry
		if (mysql_num_rows($rows) != 0) {
			$sql = "UPDATE training_records1 SET start='$start', end='$end', duration='$duration'  WHERE student_id='$username' AND daydate='$daydate' AND compcode='$compcode' AND class='$class' AND time_of_day='$TOD'";
			mysql_query($sql) or die("error-5 #2 <br>");
			echo "query #2 run"; 
		} else {										
			$sql = "INSERT INTO training_records1 VALUES('$daydate', '$compcode', $duration, '$start', '$end', '$username', '$class', '$TOD',\" \" )";
			mysql_query($sql) or die("error-5 #3");
			echo "query #3 run";
		}
	}
    echo "Training Records 1 done<br>";
    // End of Training Records 1



 
    // Training Records 2
    echo "Starting Training Records 2<br>";
    for($i=0; $i<count($json_arr[training_records2]); $i++) {
        // Get needed data
        $daydate_millisec = $json_arr[training_records2][$i][daydate];
        $class = $json_arr[training_records2][$i]['class'];
        $sleep = $json_arr[training_records2][$i][sleep];
        $heart_rate = $json_arr[training_records2][$i][heart_rate];
        $health = $json_arr[training_records2][$i][health];
        $ratings = $json_arr[training_records2][$i][ratings];

        // Work variables to use with database	
		$daydate = date("Y-m-d", $daydate_millisec/1000);
        
        // Query whether entry is already in database
		
		$sql = "SELECT * FROM training_records2 WHERE student_id='$username' AND daydate='$daydate' AND class='$class'";
        $rows = mysql_query($sql) or die("error-5 #1");
        echo "query #1 run <br>";		
		echo "rows found:" . mysql_num_rows($rows) . "<br>";		
        
        // If an entry was found update it, otherwise create a new entry
        if (mysql_num_rows($rows) != 0) {
            $sql = "UPDATE training_records2 SET sleep='$sleep', heart_rate='$heart_rate', health='$health', ratings='$ratings' WHERE student_id='$username' AND daydate='$daydate' AND class='$class'";
            mysql_query($sql) or die("error-5 #2 <br>");
			echo "query #2 run"; 
		} else {
            $sql = "INSERT INTO training_records2 VALUES('$daydate', '$username', '$class', '$heart_rate', '$sleep', '$health', '$ratings')";
            mysql_query($sql) or die("error-5 #3");
			echo "query #3 run";
		}
	}
    echo "Training Records 2 done<br>";    
    // End of Training Records 2
        
        
    
    // Fitness Test
    echo "Starting fitness test<br>";
    for($i=0; $i<count($json_arr[fitness_test]); $i++) {    
        // Get needed data
        $subject_id  = $json_arr[fitness_test][$i][subject_id];
        $group_id  = $json_arr[fitness_test][$i][group_id];
        $daydate_millisec  = $json_arr[fitness_test][$i][daydate];
 //       $test_num = $json_arr[fitness_test][$i][test_num];
        $pushup  = $json_arr[fitness_test][$i][pushup];
        $situp  = $json_arr[fitness_test][$i][situp];
        $chinup  = $json_arr[fitness_test][$i][chinup];
        $hang  = $json_arr[fitness_test][$i][hang];
        $sitreach1  = $json_arr[fitness_test][$i][sitreach1];
        $sitreach2  = $json_arr[fitness_test][$i][sitreach2];
        $height  = $json_arr[fitness_test][$i][height];
        $mass  = $json_arr[fitness_test][$i][mass];
        $waist  = $json_arr[fitness_test][$i][waist];
        $hip  = $json_arr[fitness_test][$i][hip];
        
        // Work variables to use with database	
		$daydate = date("Y-m-d", $daydate_millisec/1000);
        $bmi = round($mass/ pow(($height/100),2),3);
        $ratio = round($waist/$hip,3);
        $wh_rating = getWHRating($age,$gender,$ratio);
        $bmi_rating = getBMIRating($bmi);
        
        // Generate a test_num
        $sql = "SELECT test_num FROM fitness_test WHERE test_num = (SELECT MAX(test_num) FROM fitness_test WHERE subject_id='$username')";
        $result = mysql_query($sql) or die("error-5 #1");
        echo "query #1 run <br>";		
		echo "rows found:" . mysql_num_rows($result) . "<br>";
        if ($row = mysql_fetch_assoc($result)) {
            $test_num = $row["test_num"];
        } else {
            $test_num = 1;
        }
        echo "test_num" . $test_num . "<br>";
       $test_num++; 
        
        // Create and execute query
        $sql = "INSERT INTO fitness_test VALUES('$subject_id', '$group_id', '$daydate', '$test_num', '$pushup', '$situp', '$chinup', '$hang', '$sitreach1', '$sitreach2', '$height', '$mass', '$bmi', '$bmi_rating', '$waist', '$hip', '$ratio', '$wh_rating')";
        mysql_query($sql) or die("error-5 #2");
        echo "query #2 run";
	echo "End of fitness test<br>";	
    }
        
    // End of Fitness Test
		
    
        
    }
?>        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
     
