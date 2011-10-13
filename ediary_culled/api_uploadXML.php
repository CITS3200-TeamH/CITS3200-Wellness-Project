<?php
/**
	Author: Enda McCauley/20511314
	Version: 13/10/2011
*/
include "api_authFunctions.php";

if (isset($_POST["token"]) || isset($_GET["token"])){ //check to see if the token has been sent
	$id;
	if (isset($_POST["token"]) && isset($_POST["xml"])) { //now we check so see if we have both the token AND the xml data
		$id = validateToken($_POST["token"]); //validate the token
	} else if (isset($_GET["token"]) && isset($_GET["xml"])) {
		$id = validateToken($_GET["token"]);
	} else {
		echo "didn't recieve any xml data";
	}
		
	if ($id != "invalid") {
		uploadXML($id);
	} else {
		echo "error-2";
	}
} else {
	echo "didn't recieve any data";
}

function uploadXML($username) {
	$sql="SELECT * FROM student, classmap, class WHERE id='$username' AND id=student_id AND name=class_name";
	$result = mysql_fetch_array(mysql_query($sql));
	$lower = strtotime($result["start"]);
	$upper = strtotime($result["finish"]);
	$today = strtotime(date("Y-m-d"));
	$window = $result["window"];

	if ($today <= $upper && $today >= $lower) { //check to make sure the user should be uploading just in case the active field in the student table is not updated regularly
		$xml = simplexml_load_string($_POST["xml"]);

		foreach ($xml->array as $day) { //loops for each day
			$date = $day->string;
			$healthItemCount = 0;
			foreach ($day->dict as $healthItem) { //loops for each data entry options i.e. Rating Items, Health Items and Activities
				$healthItemCount++;
				foreach ($healthItem->array as $dataOptions) { //it is best to look at the xml to see what is happening here
					$healthData = null;
					$edited = null;
					$ratingChange = false;
					$index = 0;
					foreach ($dataOptions->dict as $data) {
						$counter = 0;
						$name;
						foreach ($data->string as $display) {
							$counter++;
							
							if ($counter == 1) {
								$name = (string) $display;									
							} else if ($counter % 2 == 0) { //store the data
								if ($healthItemCount == 1) {
									$healthData[$index] = $display;
								} else {
									$healthData[$name] = $display;
								}
							} else {
								if ($display == "YES") { //check if the current piece of data is marked as edited or not
									if ($healthItemCount == 2) {
										$ratingChange = true;
									}
									
									$edited[$index] = "YES";
									$index++;
								} else {
									$edited[$index] = "NO";
									$index++;
								}
							}
						}
					}
					
					if ($healthItemCount == 1 && ($edited[0] == "YES" || $edited[1] == "YES" || $edited[2] == "YES")) { //update the Wellness Data if any of its fields heart rate, sleep hours or health has changed
						$sql = "SELECT * FROM training_records2 WHERE student_id='$username' && daydate='$date'";
						$rows = mysql_query($sql) or die (mysql_error());
						
						$heart = false;
						$sleep = false;
						
						if (mysql_num_rows($rows) != 0) { //if wellness data already exists we update it
							$sql = "UPDATE training_records2 SET ";
							
							//only update what has been edited
							if ($edited[0] == "YES") {
								$heart = true;
								$sql .= ("heart_rate=" . $healthData[0]);
							}
							
							if ($edited[1] == "YES") {
								$sleep = true;
								
								if ($heart) { //need to check this to ensure if we need to add a comma. Previous data in may not have been updated and hence would not be included
									$sql .= (", sleep=" . $healthData[1]);
								} else {
									$sql .= ("sleep=" . $healthData[1]);
								}
							}
							
							if ($edited[2] == "YES") {
								if ($sleep || $heart) {
									$sql .= (", health=" . $healthData[2]);
								} else {
									$sql .= ("health=" . $healthData[2]);
								}
							}
							$sql .= " WHERE student_id='$username' AND daydate='$date'";
							mysql_query($sql) or die (mysql_error());
						} else { //no wellness data already exists
							$sql = "SELECT classmap.class_name FROM classmap WHERE classmap.student_id='$username'";
							$rows = mysql_query($sql) or die (mysql_error());
							$values = mysql_fetch_array($rows);
							$class = $values["class_name"];
							
							$sql = "INSERT INTO training_records2 VALUES ('$date', '$username', '$class', ";
							//Insert the data that has been edited OR insert the default values if it has not been edited
							if ($edited[0] == "YES") {
								$sql .= $healthData[0];
							} else if ($edited[0] == "NO") {
								$sql .= "0";
							}
							
							if ($edited[1] == "YES") {
									$sql .= (", " . $healthData[1]);
							} else if ($edited[1] == "NO") {
									$sql .= ", 0";
							}
							
							if ($edited[2] == "YES") {
								$sql .= (", " . $healthData[2]);
							} else if ($edited[2] == "NO") {
								$sql .= (", 5");
							}
							
							$sql .= ", \"\")";
							mysql_query($sql) or die (mysql_error());
						}						
					} else if ($healthItemCount == 2 && $ratingChange){ //we know that at least one rating item has changed and hence must update. 
						$data;
						$i = 0;
						//Unfortunately it is not possible to update only one piece of rating data, we must update all of it (even if only one value changed)
						foreach ($healthData as $value) {
							if ($edited[$i] == "YES" || $value != "Enter Data") { //if the rating data is edited or is not edited but is non default value we include it in the update
								$data .= $value;
							} else {
								$data .= "5"; 
							}
							
							if ($i != (count($healthData) - 1)) {
								$data .= ","; //must add commas after each entry (except for the last)
							}
							$i++;
						}
						$sql = "SELECT * FROM training_records2 WHERE student_id='$username' AND daydate='$date'";
						$rows = mysql_query($sql) or die (mysql_error());
						
						if (mysql_num_rows($rows) != 0) { //Wellness data already exists
							$sql = "UPDATE training_records2 SET ratings='$data' WHERE student_id='$username' AND daydate='$date'";
							mysql_query($sql) or die (mysql_error());
						} else { //Wellness data does not already exist
							$sql = "SELECT classmap.class_name FROM classmap WHERE classmap.student_id='$username'";
							$rows = mysql_query($sql) or die (mysql_error());
							$values = mysql_fetch_array($rows);
							$class = $values["class_name"];
							$sql = "INSERT INTO training_records2 VALUES ('$date', '$username', '$class',null,null,null,'$data')";
							mysql_query($sql) or die (mysql_error());
						}
					} else if ($healthItemCount == 3 && $edited == "YES") {
						echo "<strong> Activity Data </strong>\n";
						echo "<br/>";
						
						foreach ($healthData as $key => $value) {
							echo "$key ---> $value";
							echo "<br />";
						}
						//$sql = "UPDATE training_records2 SET ";
					}						
				}
			}
		}
	} else {
		echo "error-3";
	}
}
?>