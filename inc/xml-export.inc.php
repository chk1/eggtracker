<?php
include("../inc/config.inc.sample.php");
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

$query = "SELECT $was FROM $wo WHERE time BETWEEN timestamp $von AND timestamp $bis";
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


#$bla = table_to_xml("o3", nulls , tableforest boolean);
#echo $bla;
?>
