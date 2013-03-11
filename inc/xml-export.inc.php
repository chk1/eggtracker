<?php

include("../inc/config.inc.sample.php");
#Übergabe der Verbindungsdaten
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);

$was = "o3id";
$wo = "o3";
$von = "'2013-03-10 01:00:00'";
$bis = "'2013-03-10 23:00:00'";

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