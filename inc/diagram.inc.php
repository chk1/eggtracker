<?php
/*
	Diagram page
*/

$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);
$eggs = pg_query($dbconn, 'SELECT eggid, cosmid, ST_Y(geom) as y, ST_X(geom) as x, about FROM eggs WHERE active = true');
if(!$eggs) { die('SQL Error'); }

$streams = array("CO" => "Kohlenstoffmonoxid", 
	"humidity" => "Luftfeuchtigkeit", 
	"NO2" => "Stickstoffdioxid", 
	"O3" => "Ozon", 
	"temperature" => "Temperatur");
?>

<script src="static/jquery/jquery-1.9.1.min.js"></script>
<script src="static/flot/jquery.flot.js"></script>
<script src="static/flot/jquery.flot.time.js"></script>
<script src="static/flot/jquery.flot.selection.js"></script>

<div style="margin:20px;">
<?php
	foreach($streams as $stream => $name) {
		echo '<h2>'.$name.'</h2>'.PHP_EOL;
		echo '<div class="flotgraph" id="'.$stream.'"></div>'.PHP_EOL;
	}
?>
</div>
<script type="text/javascript">
	// options for all graphs
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
			}, 
			selection: {
				mode: "x"
			}
		}; 
	var data = [];
<?php
	// create empty arrays for all possible datastreams
	foreach($streams as $stream => $name) {
		echo "var data".$stream." = [];".PHP_EOL;
		echo 'data["'.$stream.'"] = [];'.PHP_EOL;
	}

	// fill each datastream array with data identified by eggid & datastream
	while($egg = pg_fetch_assoc($eggs)) { // for each egg do...
		foreach($streams as $stream => $name) {
			// fetch the latest data
			$values[$stream] = array();

			$query_params = array($egg['eggid']);
			$result_ = pg_query_params($dbconn, "SELECT time, {$stream} FROM {$stream} WHERE eggid = $1 AND validated = 'true' AND outlier = 'false' AND id % 10 = 0 ORDER BY TIME DESC", $query_params);// AND time BETWEEN '2012-11-05' AND '2012-11-07' ORDER BY time DESC", $query_params);
			while($row_ = pg_fetch_assoc($result_)) {
				 $values[$stream][] = "[". strtotime($row_["time"])*1000 .", ".$row_[strtolower($stream)]."]";
			}
			$datastring = implode(", ", $values[$stream]);

			// for each egg<->datastream combination, create a dataset...
			echo "var dataset = {";
				echo '"egg'.$egg['eggid'].''.$stream .'": { '.PHP_EOL;
					/*if($egg['cosmid'] >= 1000000) {
						echo "\t".'label: "LANUV '.$egg['eggid'].' '.$stream.'", '.PHP_EOL;
					} else {
						echo "\t".'label: "Egg '.$egg['eggid'].' '.$stream.'", '.PHP_EOL;
					}
					*/
					echo "\t".'label: "'.$egg['about'].'", '.PHP_EOL;

					echo "\t"."data: [". $datastring ."],".PHP_EOL;
				echo "}";
			echo "};".PHP_EOL;

			// ... and merge it into a dataset per datastream for display
			echo 'data["'.$stream.'"].push(dataset["egg'.$egg['eggid'].''.$stream .'"]);'.PHP_EOL;
		}

	}

	// make one graph per datastream
	foreach($streams as $stream => $name) {
		echo '$.plot("#'.$stream.'", data["'.$stream.'"], options);'.PHP_EOL;
	}
?>


	$(".flotgraph").bind("plotselected", function (event, ranges) {
		var datastream = $(this).attr("id");
		$.plot($(this), data[datastream], $.extend(true, {}, options, {
			xaxis: {
				min: ranges.xaxis.from,
				max: ranges.xaxis.to
			}
		}));
	});
</script>