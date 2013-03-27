<p>
<?php
include("inc/config.inc.php");
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

$tabelle = "<table border=\"0\">
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
				<a href="?action=&lon='.$row['y'].'&lat='.$row['x'].'">'.$row['y'].' '.$row['x'].'</a>
			</td>
		</tr>';
}
$tabelle.= "</table>";
echo "Air Quality Eggs:";
echo $tabelle;

$tabelle = "<table border=\"0\">
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
			<a href="?action=&lon='.$row['y'].'&lat='.$row['x'].'">'.$row['y'].' '.$row['x'].'</a>
		</td>
	</tr>';
	$o++;
}
$tabelle.= "</table>";

echo "</br>";
echo "</br>";
echo "</br>";
echo "LANUV Messstationen:";
echo $tabelle;


pg_close($dbconn);
?>
</p>