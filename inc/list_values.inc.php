<?php
include("../inc/config.inc.php");
#Übergabe der Verbindungsdaten
$dbconn = pg_connect("host=". $conf["db"]["host"] .
  				" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

#Abfrage der Radiobuttons aus dem Export-Formular
if($_POST["Parameter"] == 1)
	$wo .= "o3";
if($_POST["Parameter"] == 2)
	$wo .= "no2";
if($_POST["Parameter"] == 3)
	$wo .= "co";
if($_POST["Parameter"] == 4)
	$wo .= "temperature";
if($_POST["Parameter"] == 5)
	$wo .= "humidity";

#Abfrage der Checkboxen aus dem Export-Formular
if($_POST["Wert"][id] == 1)
	$was .= "id, ";
if($_POST["Wert"][time] == 1)
	$was .= "time, ";
if($_POST["Wert"][value] == 1)
	$was .= "$wo, ";
if($_POST["Wert"][valid] == 1)
	$was .= "valid, ";
if($_POST["Wert"][outlier] == 1)
	$was .= "outlier, ";
$was = rtrim ($was, ', ');

#Abfrage der Datumsfelder aus dem Export-Formular
$von = "'".$_POST['von']."'";
$bis = "'".$_POST['bis']."'";

$query = "SELECT $was FROM $wo WHERE time BETWEEN $von AND $bis";
$result = pg_query($dbconn, $query);

if (!$result) {
  echo "Fehler " . $query . "<br />";
  echo pg_last_error();
  exit();
}
while ($row = pg_fetch_row($result)) {
  echo "$was: $row[0]  $wo: $row[1]	$row[3]";
  echo "<br />\n";
}


?>
