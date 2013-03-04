<?php

#Datum von gestern für den Link abfragen
$date = date('md', time() - 86400);

#Link Lanuv übergeben
$url = "http://www.lanuv.nrw.de/luft/temes/".$date."/MSGE.htm";
$contents = file_get_contents($url);

#Daten aus der Website ausschneiden
$content = strstr ($contents ,"Zeit");
$count = strrpos ($content ,"Zeit");

$data = substr ($content , "0" , $count);
 
#Daten von überflüssigen strings säubern --> xx:30, " ", etc. 
$pattern = "/\d\d:30/";
$data_clean = preg_replace ($pattern , "", $data);

$data_clean = strstr ($data_clean ,"01:00");
echo $data_clean;

#Datenbankverbindung
include("../inc/config.inc.php");

$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);


?>