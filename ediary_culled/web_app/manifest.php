CACHE MANIFEST
<?php
header("Content-Type: text/cache-manifest");
?>

#html pages
Logon.html
NoLogons.html
InvalidLogon.html
Upload.html
Home.html
Fitness.html
NewFitness.html
OldFitness.html
Calendar.html
Day.html
Ratings.html
Wellness.html
Exercise.html
User.html
About.html
LoginTimedOut.html

#javascript
jQuery.js
javascript/functions.js
sqlLite.js

#css
css/style.css

#images
images/homescreen.png
images/About.png
images/arrow.png
images/background.png
images/Calendar.png
images/checkbox.png
images/ExerciseCompleted.jpg
images/ExerciseNotCompleted.jpg
images/Fitness.png
images/navbutton.png
images/navbuttonblack.png
images/navbuttonblue.png
images/navleft.png
images/navlinkleft.png
images/navlinkright.png
images/navright.png
images/play.gif
images/radiobutton.png
images/RatingCompleted.png
images/RatingNotCompleted.png
images/startup.png
images/sync.png
images/tributton.png
images/User.png
images/WellnessCompleted.jpg
images/WellnessNotCompleted.jpg

<?php


$today = strtotime(strtotime(d-m-Y,getdate()));
for($i=0;$i<50;$i++){
$time = 1000*mktime(0,0,0,date("m"),date("d")+$i,date("Y"));
echo "Day.html?date=".$time."\n";
echo "Ratings.html?date=".$time."\n";
echo "Exercise.html?date=".$time."\n";
echo "Wellness.html?date=".$time."\n";
echo "OldFitness.html?test=".$i."\n";
}




?>
