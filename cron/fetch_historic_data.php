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
	$result = pg_query($dbconn, 'SELECT MAX(time) as last_entry_date FROM '.$stream.';');
	if(!$result) { die('SQL Error'); }
	$row = pg_fetch_assoc($result);
	if($row["last_entry_date"] == "") {
		echo "No entries for ".$stream." yet.<br>".PHP_EOL;
		$start = "2012-11-01T00:00:01";
		$end   = "2012-11-02T00:00:01";
	} else {
		echo "Last entry in ".$stream.": ".$row["last_entry_date"]."<br>".PHP_EOL;
		$start = $row["last_entry_date"];
		$end   = date("Y-m-d\TH:i:s", strtotime($row["last_entry_date"])+3600*24); // +1 Tag
	};
	
	$result = pg_query($dbconn, 'SELECT cosmid, eggid FROM eggs WHERE active = true;');
	if(!$result) { die('SQL Error'); }
	while($row = pg_fetch_assoc($result)) {
		$insert = pg_prepare($dbconn, $stream.$row['cosmid'], 'INSERT INTO '.$stream.' (eggid, time, '.$stream.') VALUES ('.$row['cosmid'].', $1, $2);');
		$url = "http://api.cosm.com/v2/feeds/".$row["cosmid"]."/datastreams/".$stream.".json?start=".urlencode($start)."&end=".urlencode($end)."&interval=60";
		if($f = file_get_contents($url, false, stream_context_create($opts))) {
			$streamjson = json_decode($f, true);
			echo "Success ".$start."-".$end."<br>".PHP_EOL;
			foreach($streamjson['datapoints'] as $datapoint) {
				if(!$insert = @pg_execute($dbconn, $stream.$row['cosmid'], array($datapoint['at'], $datapoint['value']))) {
					echo "Database error: ".pg_last_error()."<br>".PHP_EOL;
					flush();
				} else {
					echo "Inserted measurement ";
					echo $datapoint['at'].": ".$datapoint['value']."<br>".PHP_EOL;
					flush();
				}
			}
		} else {
			echo "Cosm API error.<br>".PHP_EOL;
		}
	}
}

pg_close($dbconn);
?>