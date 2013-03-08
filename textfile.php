point	title	description	icon
<?
require_once("inc/config.inc.php");

$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);
$result = pg_query($dbconn, 'SELECT eggid, cosmid, ST_Y(geom) as y, ST_X(geom) as x FROM eggs WHERE active = true');
if(!$result) { die('SQL Error'); }
while($row = pg_fetch_assoc($result)) {
	echo $row['y'].",";
	echo $row['x']."\t";
	echo $row['cosmid']."\t";
	echo "<table>";
		
		$data = pg_query($dbconn, 'SELECT time, CO FROM CO ORDER BY time DESC LIMIT 3 ');
		while($row_ = pg_fetch_assoc($data)) {
			echo "<tr>";
				echo "<td>Zeit</td>";
				echo "<td>CO</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>".date("d.m.Y H:i:s", strtotime($row_["time"]))."</td>";
				echo "<td>".$row_["co"]."</td>";
			echo "</tr>";
		}
		
	echo "</table>"."\t";
	echo "./img/eggicon.png";
	echo PHP_EOL;

}

pg_close($dbconn);
?>