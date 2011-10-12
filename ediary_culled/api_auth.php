<?php
    
    /**
     * @author Ruvan Muthu-Krishna 20507884
     * @unit CITS3200
     * @group H
     * @client Nat Benjanuvatra <nat.benjanuvatra@uwa.edu.au>
     * @date 12/10/2011
     *
     * This page has two main functions:
     * 1) To accept a given username/password combination and if correct to generate a token (which is returned to the client) with a predetermined expiry time/date.
     * 2) To validate a token and return the userid if it is valid, this is a function which is designed to be called from another page by using the include function to make this function available.
     *
     *
     *
     **/
    include "api_authFunctions.php";
    // --- Modifiable Variables --- //
    $tbl_name = "student";
    // --- End Of Modifiable Variables --- //
        
	//if (isset($_POST["username"], $_POST["password"])) {
    if (isset($_POST["username"]) || isset($_GET["username"])) {
        if(isset($_POST["username"])) { 
            $username = escape_data($_POST["username"]);
            $password = escape_data($_POST["password"]);
        }
        if(isset($_GET["username"])) { 
            $username = escape_data($_GET["username"]);
            $password = escape_data($_GET["password"]);
        }
        
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
    } else {
        echo "didn't get any data";
    }