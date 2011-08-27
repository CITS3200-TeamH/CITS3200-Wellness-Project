<?php
include 'layout.php';

//prepare query
include 'config.php';
include 'connect.php';
$name = $_POST['group_name'];
$name = stripslashes($name);
$name = mysql_real_escape_string($name);

$id = $_POST['student_id'];
$id = stripslashes($id);
$id = mysql_real_escape_string($id);


$sql= "select tr2.daydate, tr2.student_id , tr2.qos, tr2.energy_level,tr2.confidence,tr2.soreness,tr2.motivation, tr2.atw,tr2.health,tr2.heart_rate,tr2.sleep
FROM training_records2 tr2
where tr2.class = '$name' and tr2.student_id = '$id'
order by daydate
";

$result = mysql_query($sql);

$file = 'export';

$i = 0;
if (mysql_num_rows($result) > 0) {
while ($i<11) {
$row = mysql_fetch_assoc($result);
$fieldname=mysql_field_name($result,$i);
$csv_output .= $fieldname . ",";
$i++;
}
}
$csv_output .= "\n";

$values = mysql_query($sql);
while ($rowr = mysql_fetch_row($values)) {
for ($j=0;$j<$i;$j++) {
$csv_output .= $rowr[$j].",";
}
$csv_output .= "\n";
}

$filename = $file."_".date("Y-m-d"). "_" .$id . "_" .$name;
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header( "Content-disposition: filename=".$filename.".csv");
print $csv_output;
exit;
?>