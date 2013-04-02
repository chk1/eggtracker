<?php
include("../inc/config.inc.php");
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

function validatefifty($stream, $offset = 0) {
	global $dbconn;
	if(!is_int($offset)) { return false; }

	$n = 50; // number of measurements to consider

	if($offset != 0) {
		$query = 'SELECT * FROM '.$stream.' WHERE id BETWEEN \''. $offset .'\' AND \''. ($offset+50) .'\' LIMIT '.$n;
	} else {
		$query = 'SELECT * FROM '.$stream.' LIMIT '.$n;
	}
	$result = pg_query($dbconn, $query);
	if(!$result) { die('SQL Error'); }
	$num = pg_num_rows($result); // should be 50 like $n, but database might give less results depending on parameters

	$last = 0;
	$total = 0;
	$x = array(); // temp array to store values
	while($row = pg_fetch_assoc($result)) {
		$total += $row[$stream];
		$x[] = $row[$stream];
		$last = $row['id'];
	}

	$average = $total/$num;
	echo "Average: ". $average;

	$numerator = 0;
	foreach($x as $val) {
		$numerator += pow(($average-$val), 2);
	}

	// variance v^2
	$var = $numerator/$num;
	// standard deviation
	$sd = sqrt($var);
	// limit = average ± 3 * standard deviation
	$limit_up = $average+3*$sd;
	$limit_down = $average-3*$sd;

	echo "<br>";
	echo "Varianz: ".$var;
	echo "<br>";
	echo "Std. Dev.: ".$sd;
	echo "<br>";
	echo "Average + 3x Std. Dev.: ".$limit_up;
	echo "<br>";
	echo "Average - 3x Std. Dev.: ".$limit_down;

	echo "<br>";
	foreach($x as $val) {
		if($val < $limit_down or $val > $limit_up) {
			echo "<b>".$val."</b> outlier<br>";
		} else {
			echo "".$val."<br>";
		}
	}
	return $last;
}
$stream = "co";
echo validatefifty($stream);
?>