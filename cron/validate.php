<?php
include("../inc/config.inc.php");
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

$stream = "co";

$result = pg_query($dbconn, "SELECT * FROM eggs;");
if(!$result) { die('SQL Error'); }
while($row = pg_fetch_assoc($result)) {
	validatefifty($stream, $row['eggid']);
};

function validatefifty($stream, $eggid, $offset = 0) {
	global $dbconn;
	if(!is_int($offset)) { return false; }

	$n = 50; // number of measurements to consider

	if($offset != 0) {
		$query = 'SELECT * FROM '.$stream.' WHERE eggid = '.$eggid.' AND validated = \'false\' AND id BETWEEN \''. $offset .'\' AND \''. ($offset+50) .'\' LIMIT '.$n;
	} else {
		$query = 'SELECT * FROM '.$stream.' WHERE eggid = '.$eggid.' AND validated = \'false\' LIMIT '.$n;
	}
	$result = pg_query($dbconn, $query);
	if(!$result) { die('SQL Error'); }
	$num = pg_num_rows($result); // should be 50 like $n, but database might give less results depending on parameters

	if($num != 0) {
		$last = 0;
		$total = 0;
		$x = array(); // temp array to store values
		while($row = pg_fetch_assoc($result)) {
			$total += $row[$stream];
			$x[$row['id']] = $row[$stream];
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
		foreach($x as $id => $val) {
			if($val < $limit_down or $val > $limit_up) {
				echo "<b>".$val."</b> outlier<br>";
				$query = 'UPDATE '.$stream.' SET outlier = true AND validated = true WHERE id = '.$id;
			} else {
				echo "".$val."<br>";
				$query = 'UPDATE '.$stream.' SET outlier = false AND validated = true WHERE id = '.$id;
			}
			$result = pg_query($dbconn, $query);
		}
		return $last;
	}
	echo "0 rows for stream ".$stream.", egg ".$eggid."<br>";
	return false;
}


?>