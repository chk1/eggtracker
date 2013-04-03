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
#Abfrage der Radiobuttons aus dem values-Formular
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

$was = "";
#Abfrage der Checkboxen aus dem values-Formular
if(isset($_POST["Wert"]["id"]) && $_POST["Wert"]["id"] == 1)
	$was .= "id, ";
if(isset($_POST["Wert"]["time"]) && $_POST["Wert"]["time"] == 1)
	$was .= "time, ";
if(isset($_POST["Wert"]["value"]) && $_POST["Wert"]["value"] == 1)
	$was .= "$wo, ";
if(isset($_POST["Wert"]["validatedated"]) && $_POST["Wert"]["validated"] == 1)
	$was .= "validatedated, ";
if(isset($_POST["Wert"]["outlier"]) && $_POST["Wert"]["outlier"] == 1)
	$was .= "outlier, ";
$was = rtrim ($was, ', ');

#Abfrage der Datumsfelder aus dem values-Formular
$von = "'".$_POST['von']."'";
$bis = "'".$_POST['bis']."'";

#Abfrage der Datenbank
$query = "SELECT $was FROM $wo NATURAL INNER JOIN eggs WHERE cosmid = $ei AND time between $von AND $bis";
$result = pg_query($dbconn, $query);

#Fehlerhafte Abfrage
if (!$result) {
  echo "Fehler " . $query . "<br />";
  echo pg_last_error();
  exit();
}
#Abfrage ob die Tabelle leer ist
$leer = pg_num_rows($result);
 if ($leer >0){
	echo "<strong>$leer total values for $wo from EggID $ei</strong>";
 }
 else{
	 echo "<strong>No values for $wo from EggID $ei</strong>";
 exit();
 }

#Erstellung einer Tabelle mit den Ausgewählten Werten
echo "<table border>";
while ($row = pg_fetch_row($result)) {
  echo "<tr>";
 // echo $was;
  switch ($was) {
    case "id";
    case "time";
    case "$wo";
	case "validated";
    case "outlier";
    	echo "<td>", "$row[0]", "</td>";
	break;
    
	case "id, time";
	case "id, validated";
	case "id, outlier";
	case "id, $wo"; 
	case "time, $wo";
	case "time, validated";
	case "time, outlier";
	case "$wo, outlier";
	case "$wo, validated";
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		break;
	
	case "id, time, $wo";
	case "id, time, validated";
	case "id, time, outlier";
	case "id, $wo, validated";
	case "id, $wo, outlier";
	case "id, validated, outlier";	
	case "time, $wo, validated";
	case "time, $wo, outlier";
	case "id, $wo, validated";
	case "id, $wo, outlier";
	case "$wo, validated, outlier";  
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		break;
		
	case "id, time, $wo, validated"; 
	case "id, time, $wo, outlier";
	case "id, $wo, validated, outlier";
	case "time, $wo, validated, outlier"; 	
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		echo "<td>", "$row[3]", "</td>";
		break;
	
	case "id, time, $wo, validated, outlier"; 		
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		echo "<td>", "$row[3]", "</td>";
		echo "<td>", "$row[4]", "</td>";
		break;	
echo "</tr>";
  }
}
//echo "Keine weiteren Werte";
echo "</table>";
?>
