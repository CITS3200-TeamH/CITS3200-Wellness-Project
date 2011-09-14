<?php
	if ($_FILES["file"]["error"] <= 0 && $_FILES["file"]["type"] == "text/xml") {
		echo "File Data";
		echo "<br />\n";
		echo "<p>\n";

		$xml = simplexml_load_file($_FILES["file"]["tmp_name"]);

		echo (string) $xml->array->array->dict->key;
		echo (string) $xml->array->array->dict->key;
		echo (string) $xml->array->array->dict->key;
		echo (string) $xml->array->array->dict->key;
		echo "</p>\n";
	} else {
		echo "Something went WRONG";
	}			
?>