success
<?php
include "..\api_authFunctions.php";

if (isset($_POST["token"]) || isset($_GET["token"])){ //check to see if the token has been sent
	$id;
	if (isset($_POST["token"]) && isset($_POST["data"])) { //now we check so see if we have both the token AND the xml data
		$id = validateToken($_POST["token"]); //validate the token
	} else if (isset($_GET["token"]) && isset($_GET["data"])) {
		$id = validateToken($_GET["token"]);
	} else {
		echo "error-3";
	}
	echo "hello";
	if ($id != "invalid") {
		echo "upload";
		//uploadXML($id);
	} else {
		echo "error-2"; //an invalid token should produce an error
	}
} else {
	echo "error-1";
}

	/*function uploadXML($username) {
		$sql="SELECT * FROM student, classmap, class WHERE id='$username' AND id=student_id AND name=class_name";
		$result = mysql_fetch_array(mysql_query($sql));
		$lower = strtotime($result["start"]);
		$upper = strtotime($result["finish"]);
		$today = strtotime(date("Y-m-d"));
		$window = $result["window"];
		
		echo "hello";
		echo $_GET["data"];
		$arr = json_decode($_GET["data"]);//!!!!!!!!!!!!!!!!! change this to POST
		echo "fahsfhsaf------|";
		echo json_encode($arr);
		echo "|-----------fasfsdfsafa";
		
		
	}*/



?>