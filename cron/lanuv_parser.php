<?php

#Datum von gestern für den Link abfragen
$date = date('md', time() - 86400);

#Link Lanuv übergeben
$url_array[1] = "http://www.lanuv.nrw.de/luft/temes/".$date."/MSGE.htm";
$url_array[2] = "http://www.lanuv.nrw.de/luft/temes/".$date."/VMS2.htm";

for($q = 1; $q <= count($url_array); $q++){

$contents = file_get_contents($url_array[$q]);

#Daten aus der Website ausschneiden
#von $content
$content = strstr ($contents ,"Zeit");

#bis $content + Anzahl der zeichen
$count = strrpos ($content ,"Zeit");
$data = substr ($content , "0" , $count);
 
#Aufteilen der Daten nach <tr> </tr>
preg_match_all("/<tr( style\=\"font-weight:bold\")?>.<td\sclass\=\"mw_.+<\/tr>/msU", $data, $matches, PREG_SET_ORDER);
#print_r($matches[1][0]);

for($w = 0; $w <= 48; $w++){
	preg_match_all("/<td\sclass\=\"mw_.+<\/td>/U", $matches[$w][0], $matches2[$w], PREG_SET_ORDER);
}
#print_r($matches2[30]);

#HTML-Tags/Leerzwichen/leere Datensätze entfernen
$e = 0;
for ($i = 0; $i <= 47; $i++){
	$i++;
	$e++;
	for ($o = 0; $o <= 10; $o++) {
		$matches3[$e][$o][0] = trim(strip_tags($matches2[$i][$o][0]));
	}
}
#print_r($matches3);

#O3-Array für Datenbankzugriff
for ($p = 1; $p <= 24; $p++){
	$postgreSQl_arrayO3[$p][o3id];
	$postgreSQl_arrayO3[$p][eggid] = $q;
	$postgreSQl_arrayO3[$p][time] = date("Y-m-d", time()-86400)." ".$matches3[$p][0][0]; 
	$postgreSQl_arrayO3[$p][o3] = $matches3[$p][2][0];
	$postgreSQl_arrayO3[$p][valid] = "true";
}

#NO2-Array für Datenbankzugriff
for ($p = 1; $p <= 24; $p++){
	$postgreSQl_arrayNO2[$p][no2id];
	$postgreSQl_arrayNO2[$p][eggid] = $q;
	$postgreSQl_arrayNO2[$p][time] = date("Y-m-d", time()-86400)." ".$matches3[$p][0][0]; 
	$postgreSQl_arrayNO2[$p][no2] = $matches3[$p][4][0];
	$postgreSQl_arrayNO2[$p][valid] = "true";
}

#Datenbankverbindung
include("../inc/config.inc.sample.php");

#Übergabe der Verbindungsdaten
$dbconn = pg_connect("host=". $conf["db"]["host"] .
					" port=". $conf["db"]["port"] . 
					" dbname=". $conf["db"]["db"] .
					" user=". $conf["db"]["user"] .
					" password=". $conf["db"]["pass"]);
	
#Einfügen der Ozon Werte in die Datenbank
for ($i = 1; $i <=24; $i++) {				
	pg_insert ($dbconn , "o3" , $postgreSQl_arrayO3[$i]);
}

#Einfügen der NO2 Werte in die Datenbank
for ($i = 1; $i <=24; $i++) {				
	pg_insert ($dbconn , "no2" , $postgreSQl_arrayNO2[$i]);
}
}

?>