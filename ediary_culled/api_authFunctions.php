<?php
    
// --- Modifiable Variables --- //
$validPeriod = 86400;
// --- End Of Modifiable Variables --- //


include "config.php";
include "layout.php";
include "connect.php";
   
   function generateToken($id) {          
        $token = (string) idate("U");
        $token = $token . " " . (string) $id;
        
        $domDoc = new DOMDocument;
        $rootElt = $domDoc->createElement('token');
        $rootNode = $domDoc->appendChild($rootElt);
        $textNode = $domDoc->createTextNode($token);
        $rootNode->appendChild($textNode);
        echo htmlentities($domDoc->saveXML());
    }

    function validateToken($token) {
		
       // if($id != null){
            
            $time = (int) substr($token,0 ,strpos($token," "));
            
            if((idate("U") - $time) < $validPeriod) {
                $id = (int) substr($token,strpos($token," "));
                return $id;
            } else {
                return "error-2";
            }
                
           
        //}else{
        //    return "error-3";
        //}      
    }
?>