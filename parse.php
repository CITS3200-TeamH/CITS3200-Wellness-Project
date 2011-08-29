$string='{"name":"Bugs Bunny", "age":"one billion"}';
$json_a=json_decode($string,true);
$json_o=json_decode($string);
echo $json_a[name];
