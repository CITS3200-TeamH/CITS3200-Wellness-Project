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


$sql= "select tr1.daydate,tr1.student_id, compcodes.type, tr1.duration, tr1.duration*compcodes.mets as Mets,compcodes.cardio,compcodes.muscle,compcodes.flex,compcodes.body, compcodes.heading,compcodes.description 
FROM training_records1 tr1,compcodes
where compcodes.compcode = tr1.compcode  and tr1.class = '$name'   and tr1.student_id = '$id'
order by daydate";

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
for ($j=0;$j<11;$j++) {
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