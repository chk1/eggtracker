<?php
require_once("inc/config.inc.php");
?>

<script src="static/openlayers/OpenLayers.mobile.js"></script>
<script src="static/mobile.js"></script>
<script type="text/javascript">
	// IE9 can't handle position:relative and width/height=100%, therefore use
	// position:absolute and calculate map height based on window height
	var mapheight = window.innerHeight-16*7; // 1em = 16px by default
	document.write('<!--[if IE]><style type="text/css">div#content #map { height:' + mapheight + 'px; width:100%; position:absolute; </style><![endif]-->');
</script>


<div id="map"><script>init();</script></div>

<script type="text/javascript">
	var wgs84 = new OpenLayers.Projection("EPSG:4326"); // WGS84
	var osm_sphm = new OpenLayers.Projection("EPSG:3857"); // OSM Spherical Mercator
	vectors = new OpenLayers.Layer.Vector(
		"Vector Layer",
		{
			styleMap: new OpenLayers.StyleMap({
				externalGraphic: './img/eggicon.png',
				pointRadius: 10
			})
		}
	);
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
	echo "\t"."vectors.addFeatures([new OpenLayers.Feature.Vector(point)]);".PHP_EOL;

}

pg_close($dbconn);
?>
	map.addLayer(vectors); 
</script>