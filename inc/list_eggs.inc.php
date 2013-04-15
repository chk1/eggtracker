<p>
<?php
include("inc/config.inc.php");
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

$tabelle = "<table>
	<tr>
		<th>Cosm.com ID</th>
		<th>Geographische Koordinaten</th>
	</tr>";

$result = pg_query($dbconn, 'SELECT eggid, cosmid, ST_Y(geom) as y, ST_X(geom) as x FROM eggs WHERE active = true AND cosmid < 1000000');
if(!$result) { die('SQL Error'); }
while($row = pg_fetch_assoc($result)) {
	$tabelle .= '<tr>
			<td align="center" valign="middle">
				<a title="Öffne dieses Egg auf Cosm" href="https://cosm.com/feeds/'.$row["cosmid"].'">'.$row['cosmid'].' <img src="img/page_white_go.png" alt="Öffne dieses Egg auf Cosm"></a>
			</td>
			<td align="center" valign="middle">
				<a title="Öffne dieses Ei auf der Karte" href="?id='.$row['eggid'].'">'.number_format($row['y'],6).' '.number_format($row['x'],6).' <img src="img/map.png" alt="Öffne dieses Ei auf der Karte"></a>
			</td>
		</tr>';
}
$tabelle.= '</table><p class="listtext">In dieser Tabelle werden alle Air Quality Eggs gelistet,
						 die in einem Radius von 25km um das Stadtzentrum
						 von Münster liegen und Luftdaten sammeln. Diese
						 Daten werden dann von unserem System verarbeitet</table>';
echo "<div class=\"listeggs\"><h3 style=\"margin-bottom:5px\">Air Quality Eggs:</h3>".$tabelle;

$tabelle = "<table>
	<tr>
		<th>LANUV-Referenz</th>
		<th>Geographische Koordinaten</th>
	</tr>";

$Messstationen_array[1][1] = "http://www.lanuv.nrw.de/luft/messorte/steckbriefe/vms2.htm";
$Messstationen_array[1][2] = "M&uumlnster, Weseler Str.";
$Messstationen_array[2][1] = "http://www.lanuv.nrw.de/luft/messorte/steckbriefe/msge.htm";
$Messstationen_array[2][2] = "M&uumlnster-Geist";

$o = 1;

$result = pg_query($dbconn, 'SELECT eggid, cosmid, ST_Y(geom) as y, ST_X(geom) as x FROM eggs WHERE active = true AND cosmid > 1000000');
if(!$result) { die('SQL Error'); }
while($row = pg_fetch_assoc($result)) {
	$tabelle .= '<tr>
		<td align="center" valign="middle">
			<a title="Öffne diese Messstation auf LANUV" href="'.$Messstationen_array[$o][1].'">'.$Messstationen_array[$o][2].' <img src="img/page_white_go.png" alt="Öffne diese Messstation auf LANUV"></a>
		</td>
		<td align="center" valign="middle">
			<a title="Öffne diese Messstation auf der Karte" href="?id='.$row['eggid'].'">'.$row['y'].' '.$row['x'].' <img src="img/map.png" alt="Öffne diese Messstation auf der Karte"></a>
		</td>
	</tr>';
	$o++;
}
$tabelle.= '</table><td><p style="listtext" >In dieser Tabelle werden alle benutzten LANUV-Stationen
						gelistet, die in unserem System zu Datenvalidierung
						verwendet werden. Diese Stationen liegen genau wie die
						Air Quality Eggs im Stadtgebiet von Münster</p></table>';

echo "</br>";
echo "</br>";

echo "<h3 style=\"margin-bottom:5px\">LANUV Messstationen:</h3>".$tabelle;

echo "Wenn Sie Daten zu den einzelnen Messstationen herunterladen möchten,
nutzen Sie bitte unser <a href=\"http://giv-geosoft2a.uni-muenster.de/eggtracker/?action=export_form\">Downloadformular</a>.</div>";
pg_close($dbconn);
?>
</p>
