<?php
include("../inc/config.inc.php");

// create file_get_contents context
// set GET method and add our API key to request headers
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"X-ApiKey: ".$conf["apikey"]
  )
);

/*
available datastreams for Air Quality Eggs in the Cosm API:
	button
	CO
	humidity
	NO2
	O3
	temperature
*/
$streams = array("CO", "humidity", "NO2", "O3", "temperature"); // same as database tables

$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

foreach ($streams as $stream) {
	echo "<h2>".$stream."</h2>";

	$result = pg_query($dbconn, 'SELECT cosmid, eggid FROM eggs WHERE active = true;');
	if(!$result) { die('SQL Error'); }
	while($row = pg_fetch_assoc($result)) {

		echo "<h3>".$row['cosmid']."</h3>";

		$result1 = pg_query($dbconn, "SELECT MAX(time) as last_entry_date FROM {$stream} WHERE eggid = '{$row['cosmid']}';");
		if(!$result1) { die('SQL Error'); }
		$row1 = pg_fetch_assoc($result1);

		// start and end parameters for the cosm data api query
		// end is always set to 1 day from start
		if($row1["last_entry_date"] == "") {
			// if there are no rows in the database, start our data collection at November first
			echo "No entries for ".$stream." yet.<br>".PHP_EOL;
			$start = "2012-11-01T00:00:01";
			$end   = "2012-11-02T00:00:01";
		} else {
			echo "Last entry in ".$stream.": ".$row1["last_entry_date"]."<br>".PHP_EOL;
			$start = $row1["last_entry_date"];
			$end   = date("Y-m-d\TH:i:s", strtotime($row1["last_entry_date"])+3600*24); // +1 day
		};

		$url = "http://api.cosm.com/v2/feeds/".$row["cosmid"]."/datastreams/".$stream.".json?start=".urlencode($start)."&end=".urlencode($end)."&interval=60";
		echo $url."<br>";
		echo urldecode($url)."<br>";
		if($f = file_get_contents($url, false, stream_context_create($opts))) {
			$streamjson = json_decode($f, true);
			echo "JSON Download: <b>".$start."</b> - <b>".$end."</b><br>".PHP_EOL;
			if(!isset($streamjson['datapoints'])) {
				print_r($streamjson);
			} else {
				print_r($streamjson);
				foreach($streamjson['datapoints'] as $datapoint) {
					// use "upsert" technique to INSERT when no duplicate key exists
					$ins1 = pg_query($dbconn, "UPDATE {$stream} SET {$stream}={$datapoint['value']} WHERE eggid='{$row['cosmid']}' AND time = '{$datapoint['at']}';");
					$ins2 = pg_query($dbconn, "INSERT INTO {$stream} (eggid, time, {$stream}) 
								SELECT {$row['cosmid']}, '{$datapoint['at']}', '{$datapoint['value']}'
								WHERE NOT EXISTS 
								(SELECT 1 FROM {$stream} WHERE eggid = '{$row['cosmid']}' AND time = '{$datapoint['at']}');");
					if(!$ins1 || !$ins2) {
						echo "Database error: ".pg_last_error()."".PHP_EOL;
						flush();
					} else {
						echo "Inserted measurement :";
						echo $datapoint['at'].": ".$datapoint['value']."<br>".PHP_EOL;
						flush();
					}
				}
			}
		} else {
			echo "Cosm API error.".PHP_EOL;
		}
		echo "<hr>";
	}
}

pg_close($dbconn);
?>