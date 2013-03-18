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

$query = "SELECT $was FROM $wo WHERE time BETWEEN $von AND $bis";
$result = pg_query($dbconn, $query);

if (!$result) {
  echo "Fehler " . $query . "<br />";
  echo pg_last_error();
  exit();
}
echo "<table border>";
while ($row = pg_fetch_row($result)) {
  echo "<tr>";
  //echo $was;
  switch ($was) {
    case id ;
    case time;
    case $wo;
	case valid;
    case outlier;
    	echo "<td>", "$row[0]", "</td>";
  	    break;
    
	case 'id, time';
	case 'id, valid'; //?
	case 'id, outlier'; //?
	case 'id, $wo'; //fehler
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		break;
	
	case 'id, time, $wo'; //fehler
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		break;
		
	case 'id, time, $wo, valid'; //fehler
	case 'id, time, $wo, outlier'; //fehler
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		echo "<td>", "$row[3]", "</td>";
		break;
	
	case 'id, time, $wo, valid, outlier'; //fehler		
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
