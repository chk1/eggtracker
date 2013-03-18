<?php
include("../inc/config.inc.sample.php");
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

$query = "SELECT $was FROM $wo natural inner join eggs WHERE cosmid = $ei AND time BETWEEN timestamp $von AND timestamp $bis";
$result = pg_query($dbconn, $query);

$doc = new DomDocument("1.0");
$doc->formatOutput = true;

$root = $doc->createElement('data');
$root = $doc->appendChild($root);

while($row = pg_fetch_assoc($result)){
    $node = $doc->createElement('collection');
    $node = $root->appendChild($node);

    foreach($row as $fieldname => $fieldvalue){
       $node->appendChild($doc->createElement($fieldname, $fieldvalue));
    }
}
header("content-type: application/xml");
echo $doc->saveXML();

?>

