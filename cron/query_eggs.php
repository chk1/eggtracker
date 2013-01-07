<?php
/*
	Query COSM Api for Air Quality Eggs in and around Münster
	and insert eggs into the database.
*/

include("../inc/config.inc.php");

$dbconn = pg_connect("host= ". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

$eggs = array();

$conf['apikey'] = '4bE4sqC8vY54oL8QSbNYWgqW1omSAKxBYVZCbmxOQ2xWOD0g';

// create file_get_contents context
// set GET method and add our API key to request headers
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"X-ApiKey: ".$conf["apikey"]
  )
);

// query all "münster aqe" eggs
// 2 methods:

// search by tags...
$params1 = "?tag=".urlencode("münster")."&tag=aqe"; 
// ... or search by spatial radius
$params2 = "?lat=51.95&lon=7.63&distance=15.0&distance_units=kms&q=aqe";

$result = pg_prepare($dbconn, 'egginsert', 'INSERT INTO eggs (cosmid, geom) VALUES ($1, ST_GeomFromText($2, 4326))');

$f = @file_get_contents("http://api.cosm.com/v2/feeds/".$params2, false, stream_context_create($opts));
$d = json_decode($f, true);
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

$log = print_r($eggs, true);
file_put_contents("../log.txt", time().PHP_EOL.$log.PHP_EOL.PHP_EOL, FILE_APPEND);
?>