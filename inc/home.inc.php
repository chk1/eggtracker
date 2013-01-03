<script src="static/openlayers/OpenLayers.mobile.js"></script>
<script src="static/mobile.js"></script>

<div id="map"><script>init();</script></div>

<script type="text/javascript">
	pp = new OpenLayers.LonLat(7,52);

	vectors = new OpenLayers.Layer.Vector("Vector Layer");
	point = new OpenLayers.Geometry.Point(pp.lon, pp.lat); // immer 0,0??
	vectors.addFeatures([new OpenLayers.Feature.Vector(point)]);
	map.addLayer(vectors); 
</script>
