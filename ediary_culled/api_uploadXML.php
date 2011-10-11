<?php
include "config.php";
include "layout.php";
include "api_auth.php";

$username;

if (isset($_POST["token"]) || isset($_GET["token"])){ //XML POST error here also) {
	$id = validateToken($_POST["token"]);
		
	if ($id != null) {
		uploadXML($id);
	} else {
		echo "Invalid_Token";
	}
} else {
	echo "Submission_Error";
}

function uploadXML($username) {
	$inp = fopen("php://input");
	$filename = "../uploads" . $username . date("Y/m/d-H:i:s") . ".xml"
	$outp = fopen($filename, "w");

	if ($inp && $outp) {			
		while (!feof($inp)) {
			$buffer = fread($inp, 8192);
			fwrite($outp, $buffer);
		}

		fclose($inp);
		fclose($outp);
		echo "<p>\n";

		$xml = simplexml_load_file($filename);
		
		if (!$xml) {
			echo "File Read Failed";
			return;
		}

		foreach ($xml->array as $topLevel) {
			foreach ($topLevel->array as $day) {
				$date = $day->string;
				$healthItemCount = 0; //1 is Wellness, 2 is rating items and 3 is Activities.
				foreach ($day->dict as $healthItem) {
					$healthItemCount++;
					foreach ($healthItem->array as $dataOptions) {
						$healthData = null;
						foreach ($dataOptions->dict as $data) {
							$counter = 0;
							$name;
							foreach ($data->string as $display) {
								$counter++;

								if ($counter == 1) {
									$name = (string) $display;									
									//echo $display . " --> "; //name of the health item. e.g. Sleep Hours, Self Esteem etc..
								} else if ($counter % 2 == 0) {
									$healthData[$name] = $display; //gets Heart rate, sleep hours and health.
									//echo $display;
								}
							}
						}
						
						if ($healthItemCount == 1) { //general wellness data
							//echo "<strong> Wellness Data </strong>\n";
							//echo "<br />";
							
							//foreach ($healthData as $key => $value) {
								//echo "$key ---> $value";
								//echo "<br />";
							//}
							$sql = "SELECT * FROM training_records1 WHERE student_id='$username' && daydate='$date'";
							$rows = mysql_query($sql);
							
							if (mysql_num_rows($rows) != 0) {
								$sql = "UPDATE training_records1 SET heart_rate=" . $healthData[0] . " AND sleep=" . $healthData[1] . " AND health=" . $healthData[2] . " WHERE student_id='$username' && datedate='$date'";
							} else {
								$sql = "SELECT classmap.class_name FROM classmap WHERE classmap.student_id='$username'";
								$rows = mysql_query($sql);
								$values = mysql_fetch_array($rows);
								$class = $values["class_name"];
								$sql = "INSERT INTO training_records1 VALUES ('$date', '$username', '$class', " . $healthData[0] . ", " . $healthData[1] . ", " . $healthData[2] . ",,)";
								mysql_query($sql);
							}						
						} else if ($healthItemCount == 2){ //rating data
							//echo "<strong> Rating Data </strong>";
							//echo "<br />";
							$data;
							$i = 0;
							
							foreach ($healthData as $value) {
								$data .= $value;
								
								if ($i != (count($healthData) - 1)) {
									$data .= ",";
								}
								$i++;
							}
							//echo "$data";
							//echo "<br/>";
							
							$sql = "SELECT * FROM training_records1 WHERE student_id='$username' && daydate='$date'";
							$rows = mysql_query($sql);
							
							if (mysql_num_rows($rows) != 0) {
								$sql = "UPDATE training_records1 SET ratings='$data' WHERE student_id='$username' && datedate='$date'";
							} else {
								$sql = "SELECT classmap.class_name FROM classmap WHERE classmap.student_id='$username'";
								$rows = mysql_query($sql);
								$values = mysql_fetch_array($rows);
								$class = $values["class_name"];
								$sql = "INSERT INTO training_records1 VALUES ('$date', '$username', '$class',,,,'$data')";
								mysql_query($sql);
							}
						} else { //activity data
							echo "<strong> Activity Data </strong>\n";
							echo "<br/>";
							
							foreach ($healthData as $key => $value) {
								echo "$key ---> $value";
								echo "<br />";
							}
							//$sql = "UPDATE training_records2 SET ";
						}						
						echo "<br />\n";
					}
				}
			}
		}
		echo "</p>\n";
	} else {
		echo "Something went WRONG";
	}
?>
