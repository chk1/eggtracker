/*
	http://openlayers.org/dev/examples/mobile.html
*/

// initialize map when page ready
var map;

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

function oncl(feature) {
	alert(feature);
}

var init = function () {
	// create map
	map = new OpenLayers.Map({
		div: "map",
		theme: null,
		controls: [
			new OpenLayers.Control.Attribution(),
			new OpenLayers.Control.TouchNavigation({
				dragPanOptions: {
					enableKinetic: true
				}
			}),
			new OpenLayers.Control.Zoom()
		],
		layers: [
			new OpenLayers.Layer.OSM("OpenStreetMap", null, {
				transitionEffect: 'resize'
			})
		],
		center: new OpenLayers.LonLat(848871.118504,6791357.34679),
		zoom: 12
	});
	
	vectors = new OpenLayers.Layer.Vector(
		"Vector Layer",
		{
			styleMap: new OpenLayers.StyleMap({
				externalGraphic: './img/eggicon.png',
				pointRadius: 10
			})
		}
	);

/*	var selectControl;
	var selectedFeature;

	function onPopupClose(evt) {
		selectControl.unselect(selectedFeature);
	}
	function onPopupFeatureSelect(feature) {
		selectedFeature = feature;
		popup = new OpenLayers.Popup.FramedCloud("chicken",
			feature.geometry.getBounds().getCenterLonLat(),
			null, feature.name, null, true, onPopupClose);
		popup.panMapIfOutOfView = false;
		feature.popup = popup;
		map.addPopup(popup);
	}
	function onPopupFeatureUnselect(feature) {
		map.removePopup(feature.popup);
		feature.popup.destroy();
		feature.popup = null;
	}
	selectControl = new OpenLayers.Control.SelectFeature(vectors,
	{
		onSelect: onPopupFeatureSelect,
		onUnselect: onPopupFeatureUnselect 
	});
	map.addControl(selectControl);
	selectControl.activate();*/
};