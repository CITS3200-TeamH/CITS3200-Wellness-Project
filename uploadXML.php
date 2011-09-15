<?php
/**
* NOTES
* May need to change the "edited" tag. Something like <string> Yes/No </string> would be easier
* Which tag holds the data? I worked on the assumption it was the tag which contained the "Enter Data" value.
*/
	if ($_FILES["file"]["error"] <= 0 && $_FILES["file"]["type"] == "text/xml") {
		echo "<p>\n";

		$xml = simplexml_load_file($_FILES["file"]["tmp_name"]);

		foreach ($xml->array as $topLevel) {
			foreach ($topLevel->array as $day) {
				$date = $day->string;
				echo "<strong> " . $date . "</strong> <br />\n";
				$healthItemCount = 0;									//1 is Wellness, 2 is rating items and 3 is Activities.
				foreach ($day->dict as $healthItem) {
					$healthItemCount++;
					echo "<span style='font-style:italic;'> " . $healthItemCount . " " . $healthItem->string . "</span>";
					echo "<br />\n";
					foreach ($healthItem->array as $dataOptions) {
						foreach ($dataOptions->dict as $data) {
							$counter = 0;
							foreach ($data->string as $display) {
								$counter++;
								if ($counter == 1) {
									echo $display . " --> ";
								} else if ($counter % 2 == 0) {
									echo $display;
								}
							}
							echo "<br />\n";
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
