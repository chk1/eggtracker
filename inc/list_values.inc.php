<?php
include("../inc/config.inc.php");
#Übergabe der Verbindungsdaten
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

#Abfrage des Dropdownmenüs
$abfrage = "SELECT cosmid FROM eggs";
$eggs = pg_query($dbconn, $abfrage);

#Prüfung ob cosmID auch wirklich in der Datenbank ist
$i = 0;
$res = array();

while ($helper = pg_fetch_assoc($eggs)){
	$res[$i] = $helper;
	$i++;
}

Foreach ($res as $k => $V) {
	$res2 [$k] = $V ['cosmid'];
}


if (in_array ( $_POST["CosmID"], $res2) == true){
	$ei = $_POST["CosmID"];
}

$wo = "";

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
	$wo = rtrim($wo, ', ');

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

$query_params = array($ei, $von, $bis);
$query = "SELECT $was FROM $wo NATURAL INNER JOIN eggs WHERE cosmid = $1 AND time BETWEEN $2 AND $3";
$result = pg_query_params($dbconn, $query, $query_params);

if (!$result) {
  echo "Fehler " . $query . "<br />";
  echo pg_last_error();
  exit();
}
echo "<table border = 1";
while ($row = pg_fetch_row($result)) {
  echo "<tr>";
  //echo $was;
  switch ($was) {
    case "$ei, id" ;
    case "$ei, time";
    case "$ei, $wo";
	case "$ei, valid";
    case "$ei, outlier";
    	echo "<td>", "$row[1]", "</td>";
  	    break;
    
	case "$ei, id, time";
	case "$ei, id, valid"; //?
	case "$ei, id, outlier"; //?
	case "$ei, id, $wo"; 
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		break;
	
	case "$ei, id, time, $wo"; 
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		break;
		
	case "$ei, id, time, $wo, valid"; 
	case "$ei, id, time, $wo, outlier"; 
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		echo "<td>", "$row[3]", "</td>";
		break;
	
	case "$ei, id, time, $wo, valid, outlier"; 		
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		echo "<td>", "$row[3]", "</td>";
		echo "<td>", "$row[4]", "</td>";
		break;	
echo "</tr>";
  }
}
echo "</table>";
?>
