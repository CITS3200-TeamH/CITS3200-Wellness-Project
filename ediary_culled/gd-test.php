<?php

/* Displays details of GD support on your server */

echo '<div style="margin: 10px;">';

echo '<p style="color: #444444; font-size: 130%;">GD is ';

if (function_exists("gd_info")) {

	echo '<span style="color: #00AA00; font-weight: bold;">supported</span> by your server!</p>';

	$gd = gd_info();
        
	foreach ($gd as $k => $v) {

		echo '<div style="width: 340px; border-bottom: 1px solid #DDDDDD; padding: 2px;">';
		echo '<span style="float: left;width: 300px;">' . $k . '</span> ';

		if ($v)
			echo '<span style="color: #00AA00; font-weight: bold;">Yes</span>';
		else
			echo '<span style="color: #EE0000; font-weight: bold;">No</span>';

		echo '<div style="clear:both;"><!-- --></div></div>';
	}

} else {

	echo '<span style="color: #EE0000; font-weight: bold;">not supported</span> by your server!</p>';

}

echo '<p>by <a href="http://www.dagondesign.com">dagondesign.com</a></p>';

echo '</div>';

?>
