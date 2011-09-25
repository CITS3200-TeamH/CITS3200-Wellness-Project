<?php
    
    $tbl_name = "student";
	
    include "config.php";
    include "layout.php";
	include "connect.php";
    
	if (isset($_GET["username"], $_GET["password"])) {
		$username = escape_data($_GET["username"]);
		$password = escape_data($_GET["password"]);
        
		if (is_int_val($username)) {
			$sql="SELECT * FROM $tbl_name WHERE binary(id)='$username' AND binary(password)='$password'";
			$result = mysql_query($sql);
        
			if (mysql_num_rows($result) != 0) {
				//generate token
                $row=mysql_fetch_array($result);
                generateToken($row['id']); // Should really check if student is active (student.active bool in db)
                
                
                
			} else {
				echo "Invalid username and/or password";
			}
		} else {
			echo "Username is not an integer value";
		}
	} 

    

    function generateToken($id) {          
        $token = (string) idate("U");
        $token = $token . "+" . (string) $id;
        $encoded = convert_uuencode($token);
        
        $domDoc = new DOMDocument;
        $rootElt = $domDoc->createElement('token');
        $rootNode = $domDoc->appendChild($rootElt);
        $textNode = $domDoc->createTextNode($encoded);
        $rootNode->appendChild($textNode);
        //Header('Content-type: text/xml');
        echo htmlentities($domDoc->saveXML());
        
        /*
        echo "<br>";
        echo $token;
        echo "<br>";
        echo convert_uudecode($encoded);
        echo "<br>";
        echo validateToken($encoded);
         */
        
    }

    function validateToken($token) {
        
        $token = convert_uudecode($token);
        
        $time = (int) substr($token,0,strpos($token,"+"));
        
        if((idate("U")-$time)<86400) {
            $id = (int) substr($token,strpos($token,"+"));
        }
        
        if(isset($id)){
            return $id;
        }else{
            return "invalid";
        }
        
        
    }