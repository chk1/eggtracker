<?php
/*
	Diagram page
*/
?>

<script src="static/jquery-1.9.1.min.js"></script>
<script src="static/flot/jquery.flot.js"></script>
<script src="static/flot/jquery.flot.time.js"></script>
<div id="placeholder" style="width:600px;height:300px"></div>
<script type="text/javascript">
var data = [];
<?php
	$dbconn = pg_connect("host=". $conf["db"]["host"] .
						" port=". $conf["db"]["port"] . 
						" dbname=". $conf["db"]["db"] .
						" user=". $conf["db"]["user"] .
						" password=". $conf["db"]["pass"]);
	$result = pg_query($dbconn, 'SELECT eggid, cosmid, ST_Y(geom) as y, ST_X(geom) as x FROM eggs WHERE active = true');
	if(!$result) { die('SQL Error'); }
	$streams = array("CO", "humidity", "NO2", "O3", "temperature");
	while($row = pg_fetch_assoc($result)) {
		foreach($streams as $stream) {
			$values[$stream] = array();
			$result_ = pg_query($dbconn, "SELECT time, {$stream} FROM {$stream} WHERE eggid = {$row['eggid']} ORDER BY time DESC LIMIT 200");
			while($row_ = pg_fetch_assoc($result_)) {
				 $values[$stream][] = "[".strtotime($row_["time"]).", ".$row_[strtolower($stream)]."]";
			}
			$datastring = implode(", ", $values[$stream]);
			echo "var data_". $stream ."_". $row['eggid'] ." = [". $datastring ."];".PHP_EOL;
		}

	}
?>
$.plot("#placeholder", [ data_temperature_1, data_temperature_2, data_temperature_3 ], { xaxis: { mode: "time" } });
</script>

