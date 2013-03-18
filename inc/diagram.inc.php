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
<div style="margin:20px;">
<?php
	foreach($streams as $stream) {
		echo '<div class="flotgraph" id="graph'.$stream.'"></div>'.PHP_EOL;
	}
?>
</div>
<script type="text/javascript">
/*
	options for all graphs
*/
var options = { 
			yaxis: { 
				labelWidth: 50 
			}, 
			xaxis: { 
				mode: "time", 
				timeformat: "%y-%m-%d %H:%M",
			}, 
			grid: { 
				backgroundColor: "#ffffff",
				hoverable: true
			}
		}; 

/*
	create empty arrays for all possible datastream<->egg combinations
*/
<?php
	foreach($streams as $stream) {
		echo "var data".$stream." = [];".PHP_EOL;
	}

	while($egg = pg_fetch_assoc($eggs)) { // for each egg do...
		foreach($streams as $stream) {
			// fetch the latest data
			$values[$stream] = array();

			// remove time..BETWEEN later, just for demo
			$result_ = pg_query($dbconn, "SELECT time, {$stream} FROM {$stream} WHERE eggid = {$egg['eggid']} AND time BETWEEN '2012-11-29' AND '2012-11-30' ORDER BY time DESC LIMIT 200");
			while($row_ = pg_fetch_assoc($result_)) {
				 $values[$stream][] = "[". strtotime($row_["time"])*1000 .", ".$row_[strtolower($stream)]."]";
			}
			$datastring = implode(", ", $values[$stream]);

			// for each egg<->datastream combination, create a dataset...
			echo "var dataset = {";
				echo '"egg'.$egg['eggid'].''.$stream .'": { '.PHP_EOL;
					echo "\t".'label: "Egg '.$egg['eggid'].' '.$stream.'", '.PHP_EOL;
					echo "\t"."data: [". $datastring ."],".PHP_EOL;
				echo "}";
			echo "};".PHP_EOL;

			// ... and merge it into a dataset per datastream for display
			echo 'data'.$stream.'.push(dataset["egg'.$egg['eggid'].''.$stream .'"]);'.PHP_EOL;
		}

	}
	foreach($streams as $stream) {
		echo '$.plot("#graph'.$stream.'", data'.$stream.', options);'.PHP_EOL;
	}
?>
</script>