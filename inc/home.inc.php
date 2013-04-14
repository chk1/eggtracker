<?php
/*
	The "home" page: Map view
*/

require_once("inc/config.inc.php");

/*
	initial map center point is either the config value
	or, when set and valid, the eggid's coordinates
*/
if(isset($_GET["id"]) && is_numeric($_GET["id"])) $id = $_GET["id"];
?>

<div id="map"></div>

<script src="static/openlayers/OpenLayers.debug.js"></script>
<script src="static/map.js"></script>

<script type="text/javascript">
	// IE9 can't handle position:relative and width/height=100%, therefore use
	// position:absolute and calculate map height based on window height
	if(navigator.appName == "Microsoft Internet Explorer") {
		var mapheight = window.innerHeight-18*5; // 1em = 16px by default, 5 = header+footer height in em
		document.getElementById("map").style.height = mapheight + 'px';	
	}
</script>

<script type="text/javascript">
	var wgs84 = new OpenLayers.Projection("EPSG:4326"); // WGS84
	var osm_sphm = new OpenLayers.Projection("EPSG:3857"); // OSM Spherical Mercator

	var home = new OpenLayers.LonLat(<?= $conf["location"]["lon"] ?>, <?= $conf["location"]["lat"] ?>);
	home.transform(wgs84, osm_sphm);
	map.setCenter(home, 13);

<?php 
		$dbconn = pg_connect("host=". $conf["db"]["host"] .
							" port=". $conf["db"]["port"] . 
							" dbname=". $conf["db"]["db"] .
							" user=". $conf["db"]["user"] .
							" password=". $conf["db"]["pass"]);
		$result = pg_query($dbconn, 'SELECT eggid, cosmid, ST_Y(geom) as y, ST_X(geom) as x, link, about FROM eggs WHERE active = true');
		if(!$result) { die('SQL Error'); }
		while($row = pg_fetch_assoc($result)) {
			echo "\t"."var point = new OpenLayers.Geometry.Point(".$row['x'].", ".$row['y'].");".PHP_EOL;
			echo "\t"."point.transform(wgs84, osm_sphm);".PHP_EOL;

			$attributes = array();
			$streams = array("CO", "humidity", "NO2", "O3", "temperature");
			foreach($streams as $stream) {
				$query_params = array($row['eggid']);
				$result_ = pg_query_params($dbconn, "SELECT time, {$stream} FROM {$stream} WHERE eggid = $1 ORDER BY time DESC LIMIT 1 ", $query_params);
				$row_ = pg_fetch_assoc($result_);
				$attributes[] .= $stream .': "'. $row_[strtolower($stream)] .'"';
			}
			$attributes[] .= 'eggid: '. $row["eggid"] .'';
			$attributes[] .= 'cosmid: '. $row["cosmid"] .'';
			$attributes[] .= 'link: "'. $row["link"] .'"';
			$attributes[] .= 'about: "'. $row["about"] .'"';

			$attributestring = implode(", ", $attributes);
			if($row["cosmid"] >= 1000000) {
				echo "\t"."lanuv_layer.addFeatures([new OpenLayers.Feature.Vector(point, { ".$attributestring." } )]);".PHP_EOL;
			} else {
				echo "\t"."egg_layer.addFeatures([new OpenLayers.Feature.Vector(point, { ".$attributestring." } )]);".PHP_EOL;
			}		
		}
		pg_close($dbconn);
?>
	
	<?php if(isset($id)): ?> zoomToEggId(<?= $id ?>); <?php endif; ?>
</script>