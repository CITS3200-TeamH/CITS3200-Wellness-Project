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
				echo "<strong> " . $date . "</strong> <br />\n";
				$healthItemCount = 0; //1 is Wellness, 2 is rating items and 3 is Activities.
				foreach ($day->dict as $healthItem) {
					$healthItemCount++;
					//echo "<span style='font-style:italic;'> " . $healthItemCount . " " . $healthItem->string . "</span>";
					//echo "<br />\n";
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
							echo "<strong> Wellness Data </strong>\n";
							echo "<br />";
							
							foreach ($healthData as $key => $value) {
								echo "$key ---> $value";
								echo "<br />";
							}
							//$sql = "UPDATE training_records1 SET heart_rate=" . $healthData[0] . " AND sleep=" . $healthData[1] . " AND health=" . $healthData[2] . " WHERE ..."
							
						} else if ($healthItemCount == 2){ //rating data
							echo "<strong> Rating Data </strong>";
							echo "<br />";
							$data;
							$i = 0;
							
							foreach ($healthData as $value) {
								$data .= $value;
								
								if ($i != (count($healthData) - 1)) {
									$data .= ",";
								}
								$i++;
							}
							echo "$data";
							echo "<br/>";
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
