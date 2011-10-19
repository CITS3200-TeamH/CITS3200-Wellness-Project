<?php
/**
	Author: Enda McCauley/20511314
	Version: 18/10/2011
*/
include "api_authFunctions.php";

if (isset($_POST["token"]) || isset($_GET["token"])){ //check to see if the token has been sent
	$id;
	$xmlChoice = "POST";
	if (isset($_POST["token"]) && isset($_POST["xml"])) { //now we check so see if we have both the token AND the xml data
		$id = validateToken($_POST["token"]); //validate the token
	} else if (isset($_GET["token"]) && isset($_GET["xml"])) {
		$id = validateToken($_GET["token"]);
		$xmlChoice = "GET";
	} else {
		echo "error-3";
		exit();
	}
		
	if ($id != "error-2") {
		uploadXML($id);
	} else {
		echo "error-2"; //an invalid token should produce an error
	}
} else {
	echo "error-1";
}

function uploadXML($username) {	
	$ratingValues = array("Excellent" => 5, "Good" => 4, "OK" => 3, "Poor" => 2, "Awful" => 1);
	$sql="SELECT * FROM student, classmap, class WHERE id='$username' AND id=student_id AND name=class_name";
	$result = mysql_fetch_array(mysql_query($sql));
	$lower = strtotime($result["start"]);
	$upper = strtotime($result["finish"]);
	$today = strtotime(date("Y-m-d"));
	$window = (0 + $result["window"]); //ensure it is an integer

	if ($today <= $upper && $today >= $lower) { //check to make sure the user should be uploading (i.e. still within the start/end times)
		$xml;
		if ($xmlChoice = "GET") {
			$xml = simplexml_load_string($_GET["xml"]); //load the XML string
		} else {
			$xml = simplexml_load_string($_POST["xml"]); //load the XML string
		}
		//From here to the end of the page will need to be changed to reflect the new database schema
		foreach ($xml->array as $day) { //get each day. It is best to see the XML schema to understand what each loop is doing.
			$date = $day->string;
			$dateValue = strtotime($date);
			
			if ($dateValue <= $today && $dateValue >= ($today - ($window *  86400))) { //check to make sure that we are within the data entry window
				$healthItemCount = 0;
				foreach ($day->dict as $healthItem) { //loops for each data entry options i.e. Rating Items, Health Items and Activities
					$healthItemCount++;
					foreach ($healthItem->array as $dataOptions) {
						$healthData = null;
						$ratingChanged = false;
						$index = 0;
						
						foreach ($dataOptions->dict as $data) {
							$counter = 0;
							$name;
							foreach ($data->string as $display) {
								$counter++;
							
								if ($healthItemCount != 3) {
									if ($counter == 1) { //Record the name of the data we are capturing (e.g. Heart Rate etc...)
										$name = (string) $display;									
									} else if ($counter % 2 == 0) { //Store the data
										if ($healthItemCount == 1) {
											$healthData[$index] = $display;
											$index++;
										} else {
											$healthData[$name] = $display;
											
											if ($display != "") {
												$ratingChanged = true;
											}
										}
									}
								} else {
									$healthData[$index] = $display;
									$index++;
								}
							}
						}
						
						$sql = "SELECT classmap.class_name FROM classmap WHERE classmap.student_id='$username'";
						$rows = mysql_query($sql) or die("error-5");
						
						$values = mysql_fetch_array($rows);
						$class = $values["class_name"];
						
						if ($healthItemCount == 1 && ($healthData[0] != "" || $healthData[1] != "" || $healthData[2] != "")) { //update the Wellness Data if any of its fields heart rate, sleep hours or health has changed
							$sql = "SELECT * FROM training_records2 WHERE student_id='$username' && daydate='$date'";
							$rows = mysql_query($sql) or die(mysql_error());
							
							$heart = false;
							$sleep = false;
							
							if (mysql_num_rows($rows) != 0) { //If wellness data already exists we update it
								$sql = "UPDATE training_records2 SET ";
								
								//Only update what has been edited
								if ($healthData[0] != "") {
									$heart = true;
									$sql .= ("heart_rate=" . $healthData[0]);
								}
								
								if ($healthData[1] != "") {
									$sleep = true;
									
									if ($heart) { //Need to check this to ensure if we need to add a comma. Previous data may not have been updated and hence would not be included -> no comma needed
										$sql .= (", sleep=" . $healthData[1]);
									} else {
										$sql .= ("sleep=" . $healthData[1]);
									}
								}
								
								if ($healthData[2] != "") {
									if ($sleep || $heart) {
										$sql .= (", health=" . $ratingValues["$healthData[2]"]);
									} else {
										$sql .= ("health=" . $ratingValues["$healthData[2]"]);
									}
								}
								$sql .= " WHERE student_id='$username' AND daydate='$date'";
								$rows = mysql_query($sql) or die(mysql_error());
							} else { //No wellness data already exists								
								$sql = "INSERT INTO training_records2 VALUES ('$date', '$username', '$class', ";
								//Insert the data that has been edited OR insert the default values if it has not been edited
								if ($healthData[0] != "") {
									$sql .= $healthData[0];
								} else {
									$sql .= "0"; //Don't need to worry about commas here as all possible fields must have a value.
								}
								
								if ($healthData[1] != "") {
										$sql .= (", " . $healthData[1]);
								} else {
										$sql .= ", 0";
								}
								
								if ($healthData[2] != "") {
									$sql .= (", " . $ratingValues["$healthData[2]"]);
								} else {
									$sql .= (", 5");
								}
								
								$sql .= ", \"\")";
								$rows = mysql_query($sql) or die(mysql_error());
							}						
						} else if ($healthItemCount == 2 && $ratingChanged){ //We know that at least one rating item has changed and hence must update. 
							$data;
							$i = 0;
							//Unfortunately it is not possible to update only one piece of rating data, we must update all of it (even if only one value changed)
							foreach ($healthData as $value) {
								if ($value != "") { //If the rating data is edited or is not edited but is non default value we include it in the update
									$data .= $ratingValues["$value"];
								} else {
									$data .= "5";  //Otherwise use the default value
								}
								
								if ($i != (count($healthData) - 1)) {
									$data .= ","; //Must add commas after each entry (except for the last)
								}
								$i++;
							}
							$sql = "SELECT * FROM training_records2 WHERE student_id='$username' AND daydate='$date'";
							$rows = mysql_query($sql) or die(mysql_error());
							
							if (mysql_num_rows($rows) != 0) { //Wellness data already exists
								$sql = "UPDATE training_records2 SET ratings='$data' WHERE student_id='$username' AND daydate='$date'";
								$rows = mysql_query($sql) or die(mysql_error());
							} else { //Wellness data does not already exist
								$sql = "INSERT INTO training_records2 VALUES ('$date', '$username', '$class',null,null,null,'$data')";
								$rows = mysql_query($sql) or die(mysql_error());
							}
						} else if ($healthItemCount == 3 && $index != 0) {
							$compcode;
							$start;
							$end;
							$comment;						
							$count = 0;
							
							for ($i = 1; $i <= $index; $i++) {
								switch ($count) {
									case 0:
										$commment = $healthData[($i - 1)];
										$count++;
										break;
									case 1:
										$compcode = $healthData[($i - 1)];
										$count++;
										break;
									case 2:
										$end = $healthData[($i - 1)];
										$count++;
										break;
									case 3:
										$start = $healthData[($i - 1)];
										$count++;
										break;
								}
																
								if (($i % 4) == 0) {
									$count = 0;
									if ($compcode != "" && $start != "" && $end != "") {
										if (strtotime($start) <= strtotime($end)) {
											$duration = (strtotime($end) - strtotime($start)) / 60;
											$TOD = getTOD($start);

											$sql = "SELECT * FROM training_records1 WHERE student_id='$username' AND daydate='$date' AND compcode='$compcode' AND class='$class' AND time_of_day='$TOD'";
											$rows = mysql_query($sql) or die(mysql_error());

											if (mysql_num_rows($rows) != 0) {
												$sql = "UPDATE training_records1 SET start='$start', end='$end', duration='$duration', comments=\"" . htmlentities($comment, ENT_QUOTES, "UTF-8") . "\" WHERE student_id='$username' AND daydate='$date' AND compcode='$compcode' AND class='$class' AND time_of_day='$TOD'";
												mysql_query($sql) or die(mysql_error()); 

											} else {										
												$sql = "INSERT INTO training_records1 VALUES('$date', '$compcode', $duration, '$start', '$end', '$username', '$class', '$TOD',\"" . htmlentities($comment,ENT_QUOTES,"UTF-8") . "\")";
												mysql_query($sql) or die(mysql_error());
											}
										}
									}
								}
							}
						}						
					}
				}
			} else {
				echo "error-3";
				exit();
			}
		} echo "success";
	} else {
		echo "error-3";
		exit();
	}
}

function getTOD($start) {
	if(strtotime($start) >= strtotime("21:00")) {
		$tod = "Night";
	}else if(strtotime($start) >= strtotime("18:00")) {
		$tod = "Evening";
	}else if(strtotime($start) >= strtotime("16:00")) {
		$tod = "Late Afternoon";
	}else if(strtotime($start) >= strtotime("14:00")) {
		$tod = "Early Afternoon";						
	}else if(strtotime($start) >= strtotime("11:30")) {
		$tod = "Midday";						
	}else if(strtotime($start) >= strtotime("09:00")) {
		$tod = "Mid Morning";												
	}else if(strtotime($start) >= strtotime("06:00")) {
		$tod = "Morning";												
	}else if(strtotime($start) >= strtotime("04:00")) {
		$tod = "Early Morning";																		
	}else {
		$tod = "Night";																								
	}
	return $tod;
}
?>
