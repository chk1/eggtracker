<?php
require_once("inc/config.inc.php");
?>

<div id="map"></div>

<script src="static/openlayers/OpenLayers.js"></script>
<script src="static/mobile.js"></script>
<script type="text/javascript">
	// IE9 can't handle position:relative and width/height=100%, therefore use
	// position:absolute and calculate map height based on window height
	var mapheight = window.innerHeight-16*7; // 1em = 16px by default
	document.write('<!--[if IE]><style type="text/css">div#content #map { height:' + mapheight + 'px; width:100%; position:absolute; </style><![endif]-->');
</script>

<script type="text/javascript">
	//alert("test");

	var wgs84 = new OpenLayers.Projection("EPSG:4326"); // WGS84
	var osm_sphm = new OpenLayers.Projection("EPSG:3857"); // OSM Spherical Mercator
<?php 
		$dbconn = pg_connect("host=". $conf["db"]["host"] .
							" port=". $conf["db"]["port"] . 
							" dbname=". $conf["db"]["db"] .
							" user=". $conf["db"]["user"] .
							" password=". $conf["db"]["pass"]);
		$result = pg_query($dbconn, 'SELECT eggid, cosmid, ST_Y(geom) as y, ST_X(geom) as x FROM eggs WHERE active = true');
		if(!$result) { die('SQL Error'); }
		while($row = pg_fetch_assoc($result)) {
			echo "\t"."var point = new OpenLayers.Geometry.Point(".$row['x'].", ".$row['y'].");".PHP_EOL;
			echo "\t"."point.transform(wgs84, osm_sphm);".PHP_EOL;

			$attributes = array();
			$streams = array("CO", "humidity", "NO2", "O3", "temperature");
			foreach($streams as $stream) {
				$result_ = pg_query($dbconn, "SELECT time, {$stream} FROM {$stream} ORDER BY time DESC LIMIT 1 ");
				$row_ = pg_fetch_assoc($result_);
				$attributes[] .= $stream .': "'. $row_[strtolower($stream)] .'"';
			}
			$attributestring = implode(", ", $attributes);

			echo "\t"."vectors.addFeatures([new OpenLayers.Feature.Vector(point, { ".$attributestring." } )]);".PHP_EOL;
		}

		pg_close($dbconn);
?>

</script>