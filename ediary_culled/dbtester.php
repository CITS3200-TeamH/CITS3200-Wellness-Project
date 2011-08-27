<?php 
include 'config.php';
include 'connect.php';
	for($i=1000000;$i<500000;$i++) {
		mysql_query('INSERT INTO ratings VALUES("some group name",12345678,"2010-01-21",'.$i.',4)');
	}
?>
