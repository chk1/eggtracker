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

$streams = array("no2", "o3");
$chemical_weights = array("no2" => 46.01, "o3" => 48);

/*
	For each Lanuv station iterate over all possible streams/parameters. 
	Check when the last measurement was inserted and then fetch data for the day afterwards.
*/
foreach($lanuvstations as $cosmid => $identifier) {
	$query_params = array($cosmid);
	$result = pg_query_params($dbconn, "SELECT eggid FROM eggs WHERE cosmid=$1;", $query_params);
	$fetchresult = pg_fetch_assoc($result);
	$eggid = $fetchresult["eggid"];

	foreach($streams as $stream) {
		$query_params = array($eggid);
		$result_ = pg_query_params($dbconn, "SELECT MAX(time) as last_entry_date FROM {$stream} WHERE eggid=$1;", $query_params);
		if(!$result_) { die('SQL Error'); }
		$row_ = pg_fetch_assoc($result_);

		if($row_["last_entry_date"] == "") {
			// if there are no rows in the database, start our data collection at November first
			// continuus data fetching since 2012-11-01
			echo "No entries for ".$stream." yet.<br>".PHP_EOL;
			$day = strtotime("2012-11-01");
		} else {
			echo "Last entry in ".$stream.": ".$row_["last_entry_date"]."<br>".PHP_EOL;
			$tmp_day = strtotime($row_["last_entry_date"]);
			if($tmp_day <= strtotime($row_["last_entry_date"])) {
				// find the earliest day from streams where data is present
				$day = strtotime($row_["last_entry_date"])+3600*23; // add 1 day
				if($day >= time()) { $stop = true; }
			}
		};
	}
	if(!isset($stop)) getDataSince($day, $eggid, $identifier);
	echo "<hr>";
}


/*
	This is the function that actually parses Lanuv's website and extracts the information.
	Calls insertIntoDatabase() to insert the data.

	$day format: md (example: november 1st is 1101)
	$identifier: LANUV station identifier
	$cosmid: 	 unique database id for that station
*/
function getDataSince($timestamp, $eggid, $identifier) {
	global $chemical_weights;
	// database date format: Y-m-d\TH:i:s
	// http://www.lanuv.nrw.de/luft/temes/0326/VMS2.htm
	// http://www.lanuv.nrw.de/luft/temes/0326/MSGE.htm
	$day = date("md", $timestamp);
	echo "Fetch ".$day."<br>";

	$url = "http://www.lanuv.nrw.de/luft/temes/".$day."/".$identifier.".htm";
	$content = file_get_contents($url);

	$dom = new DOMDocument;
	@$dom->loadHTML($content); // suppress parsing/invalid html errors
	$table = $dom->getElementsByTagName('table')->item(0); // first table = data value table
	$tbody = $table->getElementsByTagName('tbody')->item(0);
	$rows = $tbody->getElementsByTagName('tr');
	for ($i = 0; $i < $rows->length; $i++) {
		$cols = $rows->item($i)->getElementsByTagName('td');

		if(!$cols->item(1)) {} else { // skip rows with less than two columns
			$time = trim(utf8_decode($cols->item(0)->nodeValue));
			
			$datetime = date("Y-m-d", $timestamp)." ".$time;

			$ozon = trim(utf8_decode($cols->item(3)->nodeValue));
			if($ozon != "") { insertIntoDatabase($eggid, "o3", microgramPerMeterToPPB($ozon, $chemical_weights["o3"]), $datetime); }

			$no2 = trim(utf8_decode($cols->item(4)->nodeValue));
			if($no2 != "") { insertIntoDatabase($eggid, "no2", microgramPerMeterToPPB($no2, $chemical_weights["no2"]), $datetime); }

			echo $datetime." ".$ozon." ".$no2."<br>";
		}
		
	}
}

/*
	Insert function for measurements
*/
function insertIntoDatabase($eggid, $stream, $data_value, $data_datetime) {
	global $dbconn;
	// use "upsert" technique to INSERT only when no duplicate key exists and thereby eliminating script errors
	$query_params = array($data_value, $eggid, $data_datetime);
	$insert1 = pg_query_params($dbconn, "UPDATE {$stream} SET {$stream}=$1 WHERE eggid=$2 AND time=$3;", $query_params);
	$insert2 = pg_query_params($dbconn, "INSERT INTO {$stream} (eggid, time, {$stream}) 
				SELECT $2, $3, $1
				WHERE NOT EXISTS 
				(SELECT 1 FROM {$stream} WHERE eggid = $2 AND time=$3);", $query_params);
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
	Convert Lanuv's measurement units from microgram per m^3 (um/m3) to parts per billion (ppb, like AQE on Cosm)
*/
function microgramPerMeterToPPB($value, $chemical_weight) {
	// more information: http://www.smarte.org/smarte/dynamic/resource/sn-units-of-measure.xml.pdf
	$ppm = 24.25 * $value / $chemical_weight;
	$ppb = $ppm * 1000;
	return $ppb;
}
?>