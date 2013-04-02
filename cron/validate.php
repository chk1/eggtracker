<?php
include("../inc/config.inc.php");
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

$n = 50;

$result = pg_query($dbconn, 'SELECT * FROM co LIMIT '.$n);
if(!$result) { die('SQL Error'); }

$total = 0;
$x = array();
while($row = pg_fetch_assoc($result)) {
	$total += $row['co'];
	$x[] = $row['co'];
}

$average = $total/$n;
echo "Average: ". $average;
$numerator = 0;
foreach($x as $val) {
	$numerator += pow(($average-$val), 2);
}
$var = $numerator/$n;
echo "<br>";
echo "Varianz: ".$var;
$sd = sqrt($var);
echo "<br>";
echo "Std. Dev.: ".$sd;
$limit_up = $average+3*$sd;
$limit_down = $average-3*$sd;
echo "<br>";
echo "Average + 3x Std. Dev.: ".$limit_up;
echo "<br>";
echo "Average - 3x Std. Dev.: ".$limit_down;

foreach($x as $val) {
	if($val <= $limit_down or $val >= $limit_up) {
		echo "Ausreisser ".$val."<br>";
	} else {
		echo "".$val."<br>";
	}
}
?>