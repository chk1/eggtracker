<script src="static/openlayers/OpenLayers.mobile.js"></script>
<script src="static/mobile.js"></script>

<div id="map"><script>init();</script></div>

<script type="text/javascript">
	//var point = new OpenLayers.Geometry.Point(350000, 5970000);
	var point = new OpenLayers.Geometry.Point(7, 52);

	var wgs84 = new OpenLayers.Projection("EPSG:4326"); // WGS84
	var osm_sphm = new OpenLayers.Projection("EPSG:3857"); // OSM Spherical Mercator
	point.transform(wgs84, osm_sphm);

	vectors = new OpenLayers.Layer.Vector("Vector Layer");
	vectors.addFeatures([new OpenLayers.Feature.Vector(point)]);
	map.addLayer(vectors); 
</script>