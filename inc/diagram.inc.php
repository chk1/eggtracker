<?php
/*
	Diagram page
*/

$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);
$eggs = pg_query($dbconn, 'SELECT eggid, cosmid, ST_Y(geom) as y, ST_X(geom) as x FROM eggs WHERE active = true');
if(!$eggs) { die('SQL Error'); }
$streams = array("CO", "humidity", "NO2", "O3", "temperature");
?>

<script src="static/jquery-1.9.1.min.js"></script>
<script src="static/flot/jquery.flot.js"></script>
<script src="static/flot/jquery.flot.time.js"></script>
<?php
	foreach($streams as $stream) {
		echo '<div id="graph'.$stream.'" style="width:600px;height:300px"></div>'.PHP_EOL;
	}
?>
<script type="text/javascript">
<?php
	
	foreach($streams as $stream) {
		echo "var data".$stream." = [];".PHP_EOL;
	}

	while($egg = pg_fetch_assoc($eggs)) { // for each egg do...
		foreach($streams as $stream) {
			// fetch the latest data
			$values[$stream] = array();
			$result_ = pg_query($dbconn, "SELECT time, {$stream} FROM {$stream} WHERE eggid = {$egg['eggid']} ORDER BY time DESC LIMIT 200");
			while($row_ = pg_fetch_assoc($result_)) {
				 $values[$stream][] = "[".strtotime($row_["time"]).", ".$row_[strtolower($stream)]."]";
			}
			$datastring = implode(", ", $values[$stream]);
			echo "var dataset = {";
				echo '"egg'.$egg['eggid'].''.$stream .'": { '.PHP_EOL;
					echo "\t".'label: "Egg '.$egg['eggid'].' '.$stream.'", '.PHP_EOL;
					echo "\t"."data: [". $datastring ."],".PHP_EOL;
				echo "}";
			echo "};".PHP_EOL;
			echo 'data'.$stream.'.push(dataset["egg'.$egg['eggid'].''.$stream .'"]);'.PHP_EOL;
		}

	}
	foreach($streams as $stream) {
		echo '$.plot("#graph'.$stream.'", data'.$stream.', { xaxis: { mode: "time" } });'.PHP_EOL;
	}
?>
</script>