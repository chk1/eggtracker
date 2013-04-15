/*
	Creates OpenLayers popup element when user clicked on a feature
*/
function onSelectFeatureFunction(feature, evt) {
	// create a table that displays all relevant measurements for this station
	var str = "<br><table>";

	// title above table: link to station's website
	str = str + '<tr> <td style="text-align:center" colspan="2"><a href="'+ feature.attributes["link"] + '">' + feature.attributes["about"] + ' <img src="img/page_white_go.png" alt="Seite besuchen"></a></td></tr>';

	// check whether measurements are current or old
	if(feature.attributes["warning"] != null) {
		str = str + '<tr> <td style="text-align:center;color:#f00;size:small" colspan="2">' + feature.attributes["warning"] + '</td></tr>';
	}

	// german translation for chemical names
	var names = new Array();
	names["CO"] = "Kohlenstoffmonoxid";
	names["humidity"] = "Luftfeuchtigkeit";
	names["NO2"] = "Stickstoffdioxid";
	names["O3"] = "Ozon";
	names["temperature"] = "Temperatur";
	
	// measurement units
	var units = new Array();
	units["CO"] = '<span title="parts per billion">ppb</span>';
	units["humidity"] = "%";
	units["NO2"] = '<span title="parts per billion">ppb</span>';
	units["O3"] = '<span title="parts per billion">ppb</span>';
	units["temperature"] = "°C";

	// high limits for measurements (multiplied by 1000 to match 'parts per billion')
	var limits = new Array();
	limits["CO"] = 8.59*1000;
	limits["NO2"] = 98.7*1000;
	limits["O3"] = 47.3*1000;
	
	// add each measurement to the table as a row
	for(var attr in feature.attributes) {
		var dontshow = ["eggid", "cosmid", "link", "about", "warning"]
		if(feature.attributes[attr] != "" && dontshow.indexOf(attr) == -1) {
			value = parseFloat(feature.attributes[attr]).toFixed(2)
			var color = ""; 
			var title = "";

			// check whether the high limit is crossed
			if(value >= limits[attr]) { 
				color = "color:#b00;font-weight:bold;border-bottom:1px dashed #b00;cursor:help;"; 
				title = "Der vorgeschriebene Höchstwert wurde um " + Math.round((value/limits[attr])*100) + "% überschritten."; 
			}
			str = str + '<tr> <td class="l">' + names[attr] + '</td> <td class="r"><span style="' + color + '" title="' + title + '">' + value + '</span> ' + units[attr] + '</td></tr>';
		}
	}

	str = str + "</table>";
	
	// match the popup's height to the table
	var popupHeight = 170;
	if(feature.attributes['cosmid'] > 1000000) {
		popupHeight = 120
	} 
	if(feature.attributes["warning"] != null) {
		popupHeight = popupHeight + 40;
	}

	// create the openlayers popup element and display
	popup = new OpenLayers.Popup(feature.id,
		feature.geometry.getBounds().getCenterLonLat(),
		new OpenLayers.Size(285, popupHeight),
		str,
		true);
	map.addPopup(popup);
	map.panTo(new OpenLayers.LonLat(feature.geometry.x, feature.geometry.y));
}

/*
	Required so you can open more than one popup, or else site is "stuck"
*/
function onUnselectFeatureFunction(feature) {
	return;
}

/*
	Air Quality Egg layer
	icon is an egg
*/
var egg_layer = new OpenLayers.Layer.Vector(
	"<img src='./img/eggicon.png' class='legendicon'> Air Quality Egg",
	{
		styleMap: new OpenLayers.StyleMap({
			externalGraphic: './img/eggicon.png',
			pointRadius: 10
		})
	}
);

/*
	Lanuv station layer
	icon is anemometer
*/
var lanuv_layer = new OpenLayers.Layer.Vector(
	"<img src='./img/lanuvstation.png' class='legendicon'> Lanuv Station",
	{
		styleMap: new OpenLayers.StyleMap({
			externalGraphic: './img/lanuvstation.png',
			pointRadius: 10
		})
	}
);

/*
	Create a map object with the following properties:
		- allow for mobile/touch navigation
		- OSM base map
		- lanuv_layer + egg_layer predefined
		- no home coordinate (done on map page)
*/
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
		new OpenLayers.Control.Zoom()
	],
	layers: [
		new OpenLayers.Layer.OSM("OpenStreetMap", null, {
			transitionEffect: 'resize'
		}),
		lanuv_layer,
		egg_layer
	],
	zoom: 12
});


/*
	Define LayerSwitcher outside of the map element, so we can maximize it on page visit
*/
var switcherControl = new OpenLayers.Control.LayerSwitcher();
map.addControl(switcherControl);
switcherControl.maximizeControl();

/*
	Query both egg_layer and lanuv_layer for eggid attribute, then zoom
*/
function zoomToEggId(id) {
	var features = egg_layer.getFeaturesByAttribute("eggid", id); // returns an array
	if(features[0] == null) {
		var features = lanuv_layer.getFeaturesByAttribute("eggid", id);
		if(features[0] == null) {
			return;
		}
	}
	onSelectFeatureFunction(features[0], null);
	map.setCenter(null, 15);
}

/*
	Define Selection Control outside of map object because it doesn't work with multiple layers otherwise
*/
var selectControl = new OpenLayers.Control.SelectFeature([egg_layer, lanuv_layer]);
map.addControl(selectControl);
selectControl.activate();

egg_layer.events.on({
	"featureselected": function(e) { onSelectFeatureFunction(e.feature) },
	"featureunselected": function(e) { onUnselectFeatureFunction(e.feature) }
});

lanuv_layer.events.on({
	"featureselected": function(e) { onSelectFeatureFunction(e.feature) },
	"featureunselected": function(e) { onUnselectFeatureFunction(e.feature) }
});
