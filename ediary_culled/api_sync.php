<?php
/**
	Author: Enda McCauley/20511314
	Version: 13/10/2011
*/
include "api_authFunctions.php";

if (isset($_POST["token"]) || isset($_GET["token"])) { //check to see that we have recieved a token
	$id;
	if (isset($_POST["token"])) {
		$id = validateToken($_POST["token"]); //validate the token
	} else if (isset($_GET["token"])) {
		$id = validateToken($_GET["token"]);
	}
		
	if ($id != "invalid") {
		sync($id);
	} else {
		echo "error-2"; //an invalid token should produce an error
	}
} else {
	echo "Submission_Error";
}

function sync($username) {
	$sql="SELECT * FROM student, classmap, class WHERE id='$username' AND id=student_id AND name=class_name";
	$result = mysql_fetch_array(mysql_query($sql));
	$lower = strtotime($result["start"]);
	$upper = strtotime($result["finish"]);
	$today = strtotime(date("Y-m-d"));
	$window = $result["window"];

	if ($today <= $upper && $today >= $lower) { //check that we are still within the start/end times of the $username's group
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n";
		echo "<plist version=\"1.0\">\n";
		echo "<array>\n";

		$month= date("m");
		$date= date("d");
		$year= date("Y");

		for ($i = 0; $i < $window; $i++) { //use this to iterate through all available days within the data entry window
			$hr = "Enter Data";
			$sleepHours = "Enter Data";
			$health = "Enter Data";
			$ratings;

			echo "<array>\n";

			$nextDate = date("Y-m-d", mktime(0, 0, 0, $month, ($date-$i), $year)); //make the date based on how far we have gone backwards from the current date
			//the section from here to the bottom of the page will have to be modified to reflect the new schema. The XML schema used will almost certainly change as well
			$sql = "SELECT * FROM training_records1 WHERE id='$username' AND daydate='$nextDate'";
			$fitnessData = mysql_query($sql); //has fitness data

			$sql = "SELECT * FROM training_records2 WHERE student_id='$username' AND dayDate='$nextDate'";
			$ratingData = mysql_query($sql); //has heart rate, sleep hours, health and ratings. Ratings are in form 2,4,5,3,1 etc... where 2 corresponds to the value for the first rating for this group etc...

			$sql = "SELECT rating_item.description FROM rating_item, rating_item_map, classmap WHERE classmap.student_id=\"$username\" AND rating_item_map.groupname=classmap.class_name AND rating_item.id=rating_item_map.id";
			$availableRatings = mysql_query($sql); //use this to get the names of the ratings

			$sql = "SELECT compcodes.heading, training_records1.duration FROM training_records1, compcodes WHERE training_records1.student_id=\"$username\" AND daydate='$nextDate' AND compcodes.compcode=training_records1.compcode";
			$activityData = mysql_query($sql);//use this to get the names of the activities

			if (mysql_num_rows($ratingData) != 0) { //we have an entry for this day
				$temp = mysql_fetch_array($ratingData);

				if ($temp["heart_rate"] != "NULL") { //The way data is entered will mean that if one is null the others will be as well
					$hr = $temp["heart_rate"];
					$sleep = $temp["sleep"];
					$health = $temp["health"];
				}
				
				if ($temp["ratings"] != "") { //then user must have entered rating data
					$ratings = explode(",", $temp["ratings"]);
				}
			}
								
			echo "<dict>\n";
			echo "<key>title</key>\n";
			echo "<string>Wellness Data</string>\n";
			echo "<key>completed</key>\n";
		
			if ($hr != "Enter Data") { //Set the complete value in the XML file
				echo "<string>YES</string>\n";
			} else {
				echo "<string>NO</string>\n";
			}
			//this is all trival and will depend on the XML schema
			echo "<key>data</key>\n";
			echo "<array>\n";			
			echo "<dict>\n";
			echo "<key>name</key>\n";
			echo "<string>Resting HR</string>\n";
			echo "<key>rating</key>\n";
			echo "<string>$hr</string>\n";
			echo "<key>picker</key>\n";
			echo "<true/>\n";
			echo "<key> edited </key>\n";
			echo "<string>NO</string>\n";
			echo "</dict>\n";
			echo "<dict>\n";
			echo "<key>name</key>\n";
			echo "<string>Sleep hours</string>\n";
			echo "<key>rating</key>\n";
			echo "<string>$sleep</string>\n";
			echo "<key>picker</key>\n";
			echo "<true/>\n";
			echo "<key> edited </key>\n";
			echo "<string>NO</string>\n";
			echo "</dict>\n";
			echo "<dict>\n";
			echo "<key>name</key>\n";
			echo "<string>Health</string>\n";
			echo "<key>rating</key>\n";
			echo "<string>$health</string>\n";
			echo "<key>picker</key>\n";
			echo "<false/>\n";
			echo "<key> edited </key>\n";
			echo "<string>NO</string>\n";
			echo "</dict>\n";
			echo "</array>\n";
			echo "</dict>\n";
			
			echo "<dict>\n";
			echo "<key>title</key>\n";
			echo "<string>Exercise Data</string>\n";
			echo "<key>completed</key>\n";

			if (isset($ratings)) { //we may have no rating data
				echo "<string>YES</string>\n";
				echo "<key>data</key>\n";
				echo "<array>\n";

				$count = 0;
				while (($r = mysql_fetch_array($availableRatings)) != null) { //iterate through the names of the ratings which belong to $username's group
					echo "<dict>\n";
					echo "<key>name</key>\n";
					echo "<string>" . $r[0] . "</string>\n"; //the rating name
					echo "<key>rating</key>\n";
					echo "<string>" . $ratings[$count] . "</string>\n"; //the rating value
					echo "<key>picker</key>\n";
					echo "<false/>\n";
					echo "<key> edited </key>\n";
					echo "<string>NO</string>\n";
					echo "</dict>\n";

					$count++;	
				}					
			} else {
				echo "<string>NO</string>\n";
				echo "<key>data</key>\n";
				echo "<array>\n";

			while (($r = mysql_fetch_array($availableRatings)) != null) { //same as above
					echo "<dict>\n";
					echo "<key>name</key>\n";
					echo "<string>" . $r[0] . "</string>\n";
					echo "<key>rating</key>\n";
					echo "<string>Enter Data</string>\n"; //use the default value this time
					echo "<key>picker</key>\n";
					echo "<false/>\n";
					echo "<key> edited </key>\n";
					echo "<string>NO</string>\n";
					echo "</dict>\n";
				}					
			}

			echo "</array>\n";
			echo "</dict>\n";

			echo "<dict>\n";
					
			echo "<key>title</key>\n";
			echo "<string>Rating Items</string>\n";
			echo "<key>completed</key>\n";

			if (mysql_num_rows($activityData) != 0) {
				echo "<string>YES</string>\n";
				echo "<key>data</key>\n";
				echo "<array>\n";
				
				while (($activities = mysql_fetch_array($activityData)) != null) {
					echo "<dict>\n";
					echo "<key>name</key>\n";
					echo "<string>" . $activities[0] . "</string>\n"; //activity name
					echo "<key>rating</key>\n";
					echo "<string>" . $activities[1] . "</string>\n"; //activity duration
					echo "<key>picker</key>\n";
					echo "<false/>\n";
					echo "<key>edited</key>\n";
					echo "<string>NO</string>\n";
					echo "</dict>\n";
				}	
			} else {
				echo "<string>NO</string>\n";
				echo "<key>data</key>\n";
				echo "<array>\n";
				echo "<dict>\n";
				echo "<key>name</key>\n";
				echo "<string>Activity</string>\n";
				echo "<key>rating</key>\n";
				echo "<string>Enter Data</string>\n";
				echo "<key>picker</key>\n";
				echo "<false/>\n";
				echo "<key>edited</key>\n";
				echo "<string>NO</string>\n";
				echo "</dict>\n";
			}

			echo "</array>\n";
			echo "</dict>\n";
			echo "<string>$nextDate</string>\n";
			echo "</array>\n";		
		}
		echo "</array>\n";
		echo "</plist>\n";	
	} else {
		echo "error-3";
	}
}
?>
