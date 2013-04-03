<?php
/*
	Query COSM Api for Air Quality Eggs in and around Münster
	and insert eggs into the database & set deleted eggs inactive.
*/

include("../inc/config.inc.php");

$dbconn = pg_connect("host= ". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

// query Cosm API to find all eggs around Münster and insert them into our database
function newEggs() {
	global $dbconn;
	global $conf;
	
	stream_context_set_default(
		array(
			'http' => array(
				'method' => 'GET',
				'header'=>"X-ApiKey: ".$conf["apikey"]
			)
		)
	);

	// query all "münster aqe" eggs
	// 2 methods available:
	// search by tags "aqe münster"...
	$params1 = "?tag=".urlencode("münster")."&tag=aqe"; 
	// ... or search by spatial radius - we are using this one
	// find eggs around 51.95N 7.63E with radius 25 kilometers
	$params2 = "?lat=". urlencode($conf["location"]["lat"]) ."&lon=". urlencode($conf["location"]["lon"]) ."&distance=25.0&distance_units=kms&q=aqe";

	if(!$result = pg_prepare($dbconn, 'egginsert', 'INSERT INTO eggs (cosmid, geom) VALUES ($1, ST_GeomFromText($2, 4326))'))
		echo "Prepared statement failed, please check database structure.<br>";

	$f = @file_get_contents("http://api.cosm.com/v2/feeds/".$params2);
	$d = json_decode($f, true);
	echo "Found ". count($d["results"]) ."<hr>";
	foreach($d["results"] as $egg) {
		// duplicate check
		$duplicate_result = pg_query_params($dbconn, 'SELECT eggid FROM eggs WHERE cosmid=$1', array($egg["id"]));
		if(pg_num_rows($duplicate_result) >= 1) {
			echo "Cosm id ".$egg["id"]." ignored, already in database<br>";
		} else {

			// insert new eggs into database
			$point = "POINT(".$egg["location"]["lon"]." ".$egg["location"]["lat"].")";
			if(!$result = @pg_execute($dbconn, 'egginsert', array($egg["id"], $point))) {
				$error = pg_last_error();
				echo $error."<br>";
			} else {
				echo 'Added Egg ID '. $egg["id"] ." with location (";
				echo $egg["location"]["lon"] ."|";
				echo $egg["location"]["lat"] .")<br>";
			}

		}
	}

	/*$log = print_r($eggs, true);
	$logtext = $log.PHP_EOL.PHP_EOL;
	file_put_contents("../log/query_eggs.txt", $logtext, FILE_APPEND);*/
}

// iterate over all eggs in database and set deleted eggs inactive
function oldEggs() {
	global $dbconn;
	global $conf;

	stream_context_set_default(
		array(
			'http' => array(
				'method' => 'HEAD',
				'header'=>"X-ApiKey: ".$conf["apikey"]
			)
		)
	);

	$result = pg_query($dbconn, 'SELECT eggid, cosmid FROM eggs WHERE cosmid < 1000000');
	if(!$result) { die('SQL Error'); }
	while($row = pg_fetch_assoc($result)) {
		$f = @get_headers("http://api.cosm.com/v2/feeds/".urlencode($row['cosmid']));
		if($f[0] == "HTTP/1.1 404 Not Found") {
			$query_params = array($row['eggid']);
			if($result = pg_query_params($dbconn, 'UPDATE eggs SET active = false WHERE eggid=$1', $query_params)) {
				echo "Cosm id ".$row["cosmid"]." was set inactive for being deleted<br>";
			} else {
				echo pg_last_error()."<br>";
			}

		}
	}

}

newEggs();
oldEggs();
?>