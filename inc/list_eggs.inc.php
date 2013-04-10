<p>
<?php
include("inc/config.inc.php");
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

$tabelle = "<table border=\"0\">
	<colgroup>
    <col width=\"400\">
    <col width=\"250\">
    </colgroup>
	<td>
<table border=\"1\">
	<tr>
		<th>Eggtracker ID</th>
		<th>Cosm.com ID</th>
		<th>Geographische Koordinaten</th>
	</tr>";

$result = pg_query($dbconn, 'SELECT eggid, cosmid, ST_Y(geom) as y, ST_X(geom) as x FROM eggs WHERE active = true AND cosmid < 1000000');
if(!$result) { die('SQL Error'); }
while($row = pg_fetch_assoc($result)) {
	$tabelle .= '<tr>
			<td align="center" valign="middle">
				'.$row['eggid'].'
			</td>
			<td align="center" valign="middle">
				<a href="https://cosm.com/feeds/'.$row["cosmid"].'">'.$row['cosmid'].'</a>
			</td>
			<td align="center" valign="middle">
				<a href="?id='.$row['eggid'].'">'.number_format($row['y'],6).' '.number_format($row['x'],6).'</a>
			</td>
		</tr>';
}
$tabelle.= "</table><td> <p align=\"justify\" >In dieser Tabelle werden alle Air Quality Eggs,
						 die in einem Radius von 25km um das Stadtzentrum
						 von Münster liegen und Luftdaten sammeln. Diese
						 Daten werden dann von unserem System verarbeitet</table>";
echo "<div class=\"listeggs\"><h3>Air Quality Eggs:</h3>".$tabelle;

$tabelle = "<table border=\"0\">
	<colgroup>
    <col width=\"400\">
    <col width=\"250\">
    </colgroup>
	<td>
	
	<table border=\"1\">
	<tr>
		<th>Eggtracker ID</th>
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
			'.$row['eggid'].'
		</td>
		<td align="center" valign="middle">
			<a href="'.$Messstationen_array[$o][1].'">'.$Messstationen_array[$o][2].'</a>
		</td>
		<td align="center" valign="middle">
			<a href="?id='.$row['eggid'].'">'.$row['y'].' '.$row['x'].'</a>
		</td>
	</tr>';
	$o++;
}
$tabelle.= "</table><td><p align=\"justify\" >In dieser Tabelle werden alle benutzten LANUV-Stationen
						gelistet, die in unserem System zu Datenvalidierung
						verwendet werden. Diese Stationen liegen genau wie die
						Air Quality Eggs im Stadtgebiet von Münster</p></table>";

echo "</br>";
echo "</br>";
echo "</br>";

echo "<h3>LANUV Messstationen:</h3>".$tabelle."<div>";
pg_close($dbconn);
?>
</p>
