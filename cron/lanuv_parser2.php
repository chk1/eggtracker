<!DOCTYPE HTML>
<?php
include("../inc/config.inc.php");

// VMS2 = Münster Weseler Str.
// MSGE = Münster Geist
// numeric IDs like in database
$lanuvstations = array(
	1000001 => "MSGE",
	1000002 => "VMS2" 
	); 

$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

$streams = array("co", "humidity", "no2", "o3", "temperature");

foreach($lanuvstations as $cosmid => $identifier) {
	$query_params = array($cosmid);
	$result = pg_query_params($dbconn, "SELECT eggid FROM eggs WHERE cosmid=$1;", $query_params);
	$eggid = pg_fetch_assoc($result)["eggid"];

	foreach($streams as $stream) {
		$query_params = array($eggid);
		$result_ = pg_query_params($dbconn, "SELECT MAX(time) as last_entry_date FROM {$stream} WHERE eggid=$1;", $query_params);
		if(!$result_) { die('SQL Error'); }
		$row_ = pg_fetch_assoc($result_);

		if($row_["last_entry_date"] == "") {
			// if there are no rows in the database, start our data collection at November first
			// continuus data fetching since 2012-11-01
			echo "No entries for ".$stream." yet.<br>".PHP_EOL;
			$day = "1101";
		} else {
			echo "Last entry in ".$stream.": ".$row_["last_entry_date"]."<br>".PHP_EOL;
			if($tmp_day <= strtotime($row_["last_entry_date"])) {
				// find the earliest day from streams where data is present
				$day = date("md", strtotime($row_["last_entry_date"])+3600*24); // add 1 day
			}
			$tmp_day = strtotime($row_["last_entry_date"]);
		};
	}
	getDataSince($day, $cosmid, $identifier);
}


/*
	$day format: md
		example: november 1st is 1101
	$identifier: LANUV station identifier
	$cosmid: 	 unique database id for that station
*/
function getDataSince($day, $cosmid, $identifier) {
	// db date format: Y-m-d\TH:i:s
	// http://www.lanuv.nrw.de/luft/temes/0326/VMS2.htm
	// http://www.lanuv.nrw.de/luft/temes/0326/MSGE.htm
	echo "Fetch ".$day."<br>";

	$url = "http://www.lanuv.nrw.de/luft/temes/".$day."/".$identifier.".htm";
	$content = file_get_contents($url);

	$dom = new DOMDocument;
	$dom->loadHTML($content);
	$table = $dom->getElementsByTagName('table')->item(0);// first table = data value table
	$rows = $table->getElementsByTagName('tr');
	for ($i = 0; $i < $rows->length; $i++) {
		$cols = $rows->item($i)->getElementsByTagName('td');

		$time = utf8_decode($cols->item(0)->nodeValue);
		$ozon = utf8_decode($cols->item(3)->nodeValue);
		$no2 = utf8_decode($cols->item(4)->nodeValue);
		echo $time." ".$ozon." ".$no2."<br>";
	}
}
?>