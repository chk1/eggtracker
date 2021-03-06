<?php
include("inc/config.inc.php");
#Übergabe der Verbindungsdaten
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);
if(!$dbconn) { die('<p>Die Datenbankverbindung konnte nicht hergestellt werden, bitte versuchen Sie es später noch einmal.</p>'); }

#Abfrage des Dropdownmenüs
$abfrage = "SELECT cosmid FROM eggs";
$eggs = pg_query($dbconn, $abfrage);

#Prüfung ob cosmID auch wirklich in der Datenbank ist
$i = 0;
$res = array();

echo "<div class= \"listvalues\">";

echo "</br>Zurück zum <a href=\"/eggtracker/?action=list_values\">Ausgabeformular</a></br></br>";

while ($helper = pg_fetch_assoc($eggs)){
	$res[$i] = $helper;
	$i++;
}

Foreach ($res as $k => $V) {
	$res2 [$k] = $V ['cosmid'];
}

//test if Egg was chosen
if (empty($_POST["CosmID"])){
	print "Bitte w&aumlhlen Sie ein Ei aus, von dem Sie die Daten ansehen m&oumlchten.";
	die();}

//test if measuring parameter was chosen
if(empty($_POST["Parameter"])){
	print "Bitte w&aumlhlen Sie einen Messparameter aus.";
	die();}

//test if datapoints were chosen
if(empty($_POST["Wert"]["id"]) AND empty($_POST["Wert"]["time"]) AND empty($_POST["Wert"]["value"]) AND empty($_POST["Wert"]["validated"]) AND empty($_POST["Wert"]["outlier"])){
	print "Bitte w&aumlhlen Sie mindestens einen der Datenpunkte aus.";
	die();}

//test if startdate was chosen
if($_POST["von"] =="Von (YYYY-MM-TT)"){
	print "Bitte w&aumlhlen Sie einen Anfangszeitpunkt aus.";
	die();}

//test if enddate was chosen	
if($_POST["bis"] =="Bis (YYYY-MM-TT) "){
	print "Bitte w&aumlhlen Sie einen Endzeitpunkt aus.";
	die();}


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
if(isset($_POST["Wert"]["validated"]) && $_POST["Wert"]["validated"] == 1)
	$was .= "validated, ";
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
 echo "Fehler!!! Keine Daten zur Auswahl angegeben";
  exit();
}
#Abfrage ob die Tabelle leer ist
$leer = pg_num_rows($result);
 if ($leer >0){
	echo "<h3>$leer Werte f&uumlr $wo von EggID $ei</br></h3>";
 }
 else{
	 echo "<strong>Keine Werte f&uumlr $wo von EggID $ei</strong>";
 exit();
 }

#Erstellung einer Tabelle mit den Ausgewählten Werten
echo "<table><tr>";
$tabelle ="";
#Tabellenüberschriften einfügen
$header = explode (", ", $was);
$i = 0;
while($i <= count($header)-1){
	$tabelle .= "<th>".$header[$i]."</th>";
	$i++;
}

$tabelle .= "</tr>";
echo $tabelle;

while ($row = pg_fetch_row($result)) {

  echo "<tr>";
  switch ($was) {
    case "id";
    case "$wo";
	case "validated";
    case "outlier";
    	echo "<td>", "$row[0]", "</td>";
    	break;
    case "time";
    	echo "<td>".date("Y-m-d H:i:s", strtotime($row[0]))."</td>";
    	break;
	
	#10 Möglichkeiten
	case "id, time";
		echo "<td>", "$row[0]", "</td>";
		echo "<td>".date("Y-m-d H:i:s", strtotime($row[1]))."</td>";
    	break;
	case "id, validated";
	case "id, outlier";
	case "id, $wo"; 
	case "time, $wo";
	case "time, validated";
	case "time, outlier";
		echo "<td>".date("Y-m-d H:i:s", strtotime($row[0]))."</td>";
		echo "<td>", "$row[1]", "</td>";
		break;
	case "$wo, outlier";
	case "$wo, validated";
	case "validated, outlier";
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		break;
		
	#10 Möglichkeiten
	case "id, time, $wo";
	case "id, time, validated";
	case "id, time, outlier";
		echo "<td>", "$row[0]", "</td>";
		echo "<td>".date("Y-m-d H:i:s", strtotime($row[1]))."</td>";
		echo "<td>", "$row[2]", "</td>";
    	break;
    case "time, $wo, validated";
	case "time, $wo, outlier";
	case "time, validated, outlier";
		echo "<td>".date("Y-m-d H:i:s", strtotime($row[0]))."</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		break;
	case "id, $wo, validated";
	case "id, validated, outlier";
	case "id, $wo, outlier";	
	case "$wo, validated, outlier";  
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		break;
		
	#5 Möglichkeiten	
	case "id, time, $wo, validated"; 
	case "id, time, $wo, outlier";
	case "id, time, validated, outlier";
		echo "<td>", "$row[0]", "</td>";
		echo "<td>".date("Y-m-d H:i:s", strtotime($row[1]))."</td>";
		echo "<td>", "$row[2]", "</td>";
		echo "<td>", "$row[3]", "</td>";
		break;
	case "time, $wo, validated, outlier";
		echo "<td>".date("Y-m-d H:i:s", strtotime($row[0]))."</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		echo "<td>", "$row[3]", "</td>";
		break;
	case "id, $wo, validated, outlier";
		echo "<td>", "$row[0]", "</td>";
		echo "<td>", "$row[1]", "</td>";
		echo "<td>", "$row[2]", "</td>";
		echo "<td>", "$row[3]", "</td>";
		break;
	
	case "id, time, $wo, validated, outlier"; 		
		echo "<td>", "$row[0]", "</td>";
		echo "<td>".date("Y-m-d H:i:s", strtotime($row[1]))."</td>";
		echo "<td>", "$row[2]", "</td>";
		echo "<td>", "$row[3]", "</td>";
		echo "<td>", "$row[4]", "</td>";
		break;	
echo "</tr>";
  }
}
echo "</table></br></br>";
echo "</div>";
?>
