<?php
    
    $tbl_name = "student";
	
    include "config.php";
    include "layout.php";
	include "connect.php";
    
	if (isset($_POST["username"], $_POST["password"])) {
		$username = escape_data($_POST["username"]);
		$password = escape_data($_POST["password"]);
		
		if (is_int_val($username)) {
			$sql="SELECT * FROM $tbl_name, classmap WHERE student.id='$username' AND student.password='$password' AND classmap.id=student_id";
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

    
    // I think we're going to have to make an assumption about the length of the users id, I think if they use UWA's id's 8 should be fine.
    function generateToken($id) {          
              $token = (string) idate("U");
              $token = $token . "+" . (string) $id;
            // should encrypt the string here.  Possibly use convert_uuencode($token);
              
        $encoded = convert_uuencode($token);
        
        $reply = new SimpleXMLElement("<reply></reply>");
        $reply->addAttribute('token', $encoded);
        Header('Content-type: text/xml');
        echo $reply->asXML();
    }

    function validateToken($token) {
        // should decrypt the string here. Possibly use convert_uudecode($token);
        $time = (int) substr($token,0,strpos($token,"+"));
        
        if((idate("U")-$time)<86400) {
            $id = (int) substr($token,strpos($token,"+"));
        }
        
        if(isset($id)){
            return $id;
        }else{
            return null;
        }
        
        
    }