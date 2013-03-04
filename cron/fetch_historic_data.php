<?php
/*
	Query COSM API for egg data.

	Checks the database for the latest data points, then querys COSM for data after that. 
	COSM API limits us to query only a certain amount of data per minute and day:
	 - Each time this script runs, it querys a 1-day period of data with a data resolution of 60sec
	 - Max. 1000 datapoints per request
*/

include("../inc/config.inc.php");

// create file_get_contents context
// set GET method and add our API key to request headers
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"X-ApiKey: ".$conf["apikey"]
  )
);

$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

// $eggid   cosm egg id
// $stream  datastream, like CO, temperature...
// $start   start date
function fetchJsonFromCosm($eggid, $stream, $start = "2012-11-01T00:00:01") {
	global $opts;
	$end = date("Y-m-d\TH:i:s", strtotime($start)+(3600*24)); // +1 day
	$url = "http://api.cosm.com/v2/feeds/".$eggid."/datastreams/".$stream.".json?start=".urlencode($start)."&end=".urlencode($end)."&interval=60&limit=1000";
	$f = file_get_contents($url, false, stream_context_create($opts));
	$json = json_decode($f, true);
	return $json;
}

function insertIntoDatabase($eggid, $stream, $data_value, $data_datetime) {
	global $dbconn;
	// use "upsert" technique to INSERT only when no duplicate key exists and thereby eliminating script errors
	$insert1 = pg_query($dbconn, "UPDATE {$stream} SET {$stream}={$data_value} WHERE eggid='{$eggid}' AND time = '{$data_datetime}';");
	$insert2 = pg_query($dbconn, "INSERT INTO {$stream} (eggid, time, {$stream}) 
				SELECT {$eggid}, '{$data_datetime}', '{$data_value}'
				WHERE NOT EXISTS 
				(SELECT 1 FROM {$stream} WHERE eggid = '{$eggid}' AND time = '{$data_datetime}');");
	if(!$insert1 || !$insert2) {
		echo "Database error: ".pg_last_error()."".PHP_EOL;
	} else {
		echo "Inserted measurement: ";
		echo $data_datetime.": ".$data_value."<br>".PHP_EOL;
		return true;
	}
	return false;
}


/*
	For every stream and egg, we are going to query the Cosm API for data.
	If data is found, insert it into our own database.

	Available datastreams for Air Quality Eggs in the Cosm API:
		button, CO, humidity, NO2, O3, temperature
*/
$streams = array("CO", "humidity", "NO2", "O3", "temperature"); // same as database tables
foreach ($streams as $stream) {
	echo "<h2>".$stream."</h2>";

	// query eggs
	$result = pg_query($dbconn, 'SELECT cosmid, eggid FROM eggs WHERE active = true;');
	if(!$result) { die('SQL Error'); }

	// iterate eggs
	while($row = pg_fetch_assoc($result)) {
		echo "<h3>".$row['cosmid']."</h3>";

		// find the latest insertion for egg and datastream
		$result1 = pg_query($dbconn, "SELECT MAX(time) as last_entry_date FROM {$stream} WHERE eggid = '{$row['cosmid']}';");
		if(!$result1) { die('SQL Error'); }
		$row1 = pg_fetch_assoc($result1);

		// start and end parameters for the cosm data api query
		// end is always set to 1 day from start
		if($row1["last_entry_date"] == "") {
			// if there are no rows in the database, start our data collection at November first
			// continuus data fetching since 2012-11-01
			echo "No entries for ".$stream." yet.<br>".PHP_EOL;
			$start = "2012-11-01T00:00:01";
			$end   = "2012-11-02T00:00:01";
		} else {
			echo "Last entry in ".$stream.": ".$row1["last_entry_date"]."<br>".PHP_EOL;
			$start = $row1["last_entry_date"];
			$end   = date("Y-m-d\TH:i:s", strtotime($row1["last_entry_date"])+3600*24); // +1 day
		};

		if($cosmdata = fetchJsonFromCosm($row["cosmid"], $stream, $start)) {
			echo "JSON Download: <b>".$start."</b> - <b>".$end."</b><br>".PHP_EOL;
			
			if(!isset($cosmdata['datapoints'])) {

				// if there are no datapoints and it's not "today",
				// then there's likely a data gap = no data from cosm for this day

				// guess where the next data could be by querying every day until today, or until data found
				$i=0;
				while(!isset($cosmdata_['datapoints'])) {
					$newstart = date("Y-m-d\TH:i:s", strtotime($start)+(3600*24)*$i);
					$newend   = date("Y-m-d\TH:i:s", strtotime($newstart)+(3600*24)); // +1 day
					echo $i.": ".$newstart." - ".$newend."<br>";
					$i++;
					flush();
					if(strtotime($newstart) > time()) break;

					$cosmdata_ = fetchJsonFromCosm($row["cosmid"], $stream, $newstart);
				}
				$cosmdata = $cosmdata_;

			}

			foreach($cosmdata['datapoints'] as $datapoint) {
				insertIntoDatabase($row['cosmid'], $stream, $datapoint['value'], $datapoint['at']);
			}
			
		} else {
			echo "Cosm API error.".PHP_EOL;
		}
		echo "<hr>";
	}
}

pg_close($dbconn);
?>