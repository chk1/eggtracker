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
	echo '<a href="http://cosm.com/feeds/'.$row['cosmid'].'">Egg '.$row['cosmid']."</a>"."\t";
	echo "<table>";
		

		$streams = array("CO", "humidity", "NO2", "O3", "temperature"); // same as database tables

		foreach($streams as $stream) {
			$result_ = pg_query($dbconn, "SELECT time, {$stream} FROM {$stream} ORDER BY time DESC LIMIT 1 ");
			$row_ = pg_fetch_assoc($result_);

			echo "<tr>";
			echo "<td>$stream</td>";
			//$tb .= "<td>".date("d.m.Y H:i:s", strtotime($row_["time"]))."</td>";
			echo "<td>".$row_[strtolower($stream)]."</td>";
			echo "</tr>";
		}

	echo "</table>"."\t";
	echo "./img/eggicon.png";
	echo PHP_EOL;

}

pg_close($dbconn);
?>