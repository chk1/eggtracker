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
function new_eggs() {
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
	$params2 = "?lat=". $conf["location"]["lat"] ."&lon=". $conf["location"]["lon"] ."&distance=25.0&distance_units=kms&q=aqe";

	$result = pg_prepare($dbconn, 'egginsert', 'INSERT INTO eggs (cosmid, geom) VALUES ($1, ST_GeomFromText($2, 4326))');

	$f = @file_get_contents("http://api.cosm.com/v2/feeds/".$params2);
	$d = json_decode($f, true);
	echo "Found ". count($d["results"]) ."<hr>";
	foreach($d["results"] as $egg) {
		$point = "POINT(".$egg["location"]["lon"]." ".$egg["location"]["lat"].")";
		if(!$result = @pg_execute($dbconn, 'egginsert', array($egg["id"], $point))) {
			echo pg_last_error()."<br>";
		} else {
			echo 'Added Egg ID '. $egg["id"] ." with location (";
			echo $egg["location"]["lon"] ."|";
			echo $egg["location"]["lat"] .")<br>";
		}
	}

	/*$log = print_r($eggs, true);
	$logtext = $log.PHP_EOL.PHP_EOL;
	file_put_contents("../log/query_eggs.txt", $logtext, FILE_APPEND);*/
}

// iterate over all eggs in database and 
// set 404'd eggs inactive
function old_eggs() {
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

	$result = pg_query($dbconn, 'SELECT eggid, cosmid FROM eggs');
	if(!$result) { die('SQL Error'); }
	while($row = pg_fetch_assoc($result)) {
		$f = @get_headers("http://api.cosm.com/v2/feeds/".$row['cosmid']);
		if($f[0] == "HTTP/1.1 404 Not Found") {
			if($result = pg_query($dbconn, 'UPDATE eggs SET active = false WHERE eggid = '.$row['eggid'].'')) {
				echo "Egg cosm#".$row["cosmid"]." was set inactive<br>";
			} else {

			}

		}
	}

}

new_eggs();
old_eggs();
?>