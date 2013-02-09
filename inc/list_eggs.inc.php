<p>
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
	echo "#". $row['eggid'] .", #". $row['cosmid'] ." @ (". $row['x'] ."|". $row['y'] .")<br>";
}

pg_close($dbconn);
?>
</p>