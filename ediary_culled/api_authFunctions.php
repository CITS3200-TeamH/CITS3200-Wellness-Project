<?php

include "config.php";
include "layout.php";
include "connect.php";
   
   function generateToken($id) {          
        $token = (string) idate("U");
        $token = $token . "+" . (string) $id;
        
        $domDoc = new DOMDocument;
        $rootElt = $domDoc->createElement('token');
        $rootNode = $domDoc->appendChild($rootElt);
        $textNode = $domDoc->createTextNode($token);
        $rootNode->appendChild($textNode);
        echo htmlentities($domDoc->saveXML());
    }

    function validateToken($token) {
		$validPeriod = 86400;
        $id = null;
		
        $time = (int) substr($token,0 ,strpos($token," "));

        if((idate("U") - $time) < $validPeriod) {
            $id = (int) substr($token,strpos($token," "));
        }
        
        if($id != null){
            return $id;
        }else{
            return "invalid";
        }      
    }
?>