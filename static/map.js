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

function onSelectFeatureFunction(feature, evt) {
	var str = "<br><table>";
	for(var attr in feature.attributes) {
		str = str + "<tr><td class='l'>" + attr + "</td><td class='r'>" + feature.attributes[attr] + "</td></tr>";
	}
	str = str + "</table>";

	popup = new OpenLayers.Popup(feature.id,
		feature.geometry.getBounds().getCenterLonLat(),
		new OpenLayers.Size(200,150),
		str,
		true);

	map.addPopup(popup);
	map.panTo(new OpenLayers.LonLat(feature.geometry.x, feature.geometry.y));
}

function onUnselectFeatureFunction(feature) {
	return;
}

var egg_layer = new OpenLayers.Layer.Vector(
	"Air Quality Eggs",
	{
		styleMap: new OpenLayers.StyleMap({
			externalGraphic: './img/eggicon.png',
			pointRadius: 10
		})
	}
);

var lanuv_layer = new OpenLayers.Layer.Vector(
	"Lanuv Stations",
	{
		styleMap: new OpenLayers.StyleMap({
			externalGraphic: './img/asterisk_orange.png',
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
			{
				onSelect: onSelectFeatureFunction,
				onUnselect: onUnselectFeatureFunction,
			},
			lanuv_layer,
			egg_layer
		),
		new OpenLayers.Control.Zoom()
	],
	layers: [
		new OpenLayers.Layer.OSM("OpenStreetMap", null, {
			transitionEffect: 'resize'
		}),
		egg_layer,
		lanuv_layer
	],
	zoom: 12
});