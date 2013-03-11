// http://openlayers.org/dev/examples/mobile.html
// Get rid of address bar on iphone/ipod
var fixSize = function() {
	window.scrollTo(0,0);
	document.body.style.height = '100%';
	if (!(/(iphone|ipod)/.test(navigator.userAgent.toLowerCase()))) {
		if (document.body.parentNode) {
			document.body.parentNode.style.height = '100%';
		}
	}
};
setTimeout(fixSize, 700);
setTimeout(fixSize, 1500);

function onSelectFeatureFunction(feature) {
	alert("" + feature.geometry.x + " @ " + feature.geometry.y + ": " + feature.attributes.CO);
}
function onUnselectFeatureFunction(feature) {
	return;
}

var vectors = new OpenLayers.Layer.Vector(
	"Vector Layer",
	{
		styleMap: new OpenLayers.StyleMap({
			externalGraphic: './img/eggicon.png',
			pointRadius: 10
		})
	}
);

var map = new OpenLayers.Map({
	div: "map",
	theme: null,
	controls: [
		new OpenLayers.Control.Attribution(),
		new OpenLayers.Control.TouchNavigation({
			dragPanOptions: {
				enableKinetic: true
			}
		}),
		new OpenLayers.Control.Geolocate,
		new OpenLayers.Control.SelectFeature(
			vectors, {
				autoActivate: true,
				onSelect: onSelectFeatureFunction,
				onUnselect: onUnselectFeatureFunction,
		}),
		new OpenLayers.Control.Zoom()
	],
	layers: [
		new OpenLayers.Layer.OSM("OpenStreetMap", null, {
			transitionEffect: 'resize'
		}),
		vectors
	],
	center: new OpenLayers.LonLat(848871.118504,6791357.34679),
	zoom: 12
});