<p>
<?php
$eggs = array();

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

$f = @file_get_contents("http://api.cosm.com/v2/feeds/".$params2, false, stream_context_create($opts));
$d = json_decode($f, true);
echo "There are ".count($d["results"])." Air Quality Eggs in and around Münster.<hr>";
foreach($d["results"] as $egg) {
	array_push($eggs, $egg["id"]);
}

// show current data for each egg
foreach($eggs as $egg){
	echo "<div class=\"egg\">";
	$params = $egg.".json";
	$f = @file_get_contents("http://api.cosm.com/v2/feeds/".$params, false, stream_context_create($opts));
	if($f) {
		$d = json_decode($f, true);

		echo "<h2 class=".$d["status"]."><a href=\"https://cosm.com/feeds/".$egg."\">".$d["title"]."</a>
		<sup><a class=\"smallbold\" href=\"https://api.cosm.com/v2/feeds/".$params."\">Feed</a></sup></h2>";
		echo "<table>";
		if(isset($d["datastreams"])) {
			foreach($d["datastreams"] as $stream) {
				echo "<tr>";
				echo "<td>";
				echo $stream["id"];
				echo "</td><td>";
				if(isset($stream["current_value"])) echo $stream["current_value"];
				echo "</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
	} else {
		echo "<h2>Error requesting egg data ".$egg."</h2>";
	}
	echo "</div>";
	flush();
}
?>
</p>