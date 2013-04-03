<?php
function createDropDown() {
	#Übergabe der Verbindungsdaten
	include("inc/config.inc.php");
	$dbconn = pg_connect("host=". $conf["db"]["host"] .
						" port=". $conf["db"]["port"] . 
						" dbname=". $conf["db"]["db"] .
						" user=". $conf["db"]["user"] .
						" password=". $conf["db"]["pass"]);
	$query = 'SELECT cosmid	FROM eggs';
	$result = pg_query($dbconn, $query);
	
#Dropdown menu fuer die Eier
	$dropdown = '<select name="CosmID">
	<option value="">Select...</option>';
	while ($result2 = pg_fetch_assoc($result)) {
			$dropdown .= '<option value="'.$result2['cosmid'].'">'."Cosm.com ID: ".$result2['cosmid'].'</option>'; 
	}		
	$dropdown .= '</select>';

	return $dropdown;
}

$dropped = createDropDown();

print '<p>
<form method="POST" action="inc/list_values.inc.php">
	Von welchem Ei m&oumlchten Sie die Daten sehen?<br>
	'.$dropped.'<br><br>
  Was m&oumlchten Sie sehen?<br>
    <input type="radio" name="Parameter" value="1"> Ozon<br>
    <input type="radio" name="Parameter" value="2"> Stickstoffdioxid<br>
    <input type="radio" name="Parameter" value="3"> Kohlenstoffmonoxid<br>
    <input type="radio" name="Parameter" value="4"> Temperatur<br>
    <input type="radio" name="Parameter" value="5"> Luftfeuchtigkeit<br>
    <br>
    Welche Parameter m&oumlchten Sie anzeigen lassen?<br>
	<input type="checkbox" name="Wert[id]" value="1"> Werte ID<br>
    <input type="checkbox" name="Wert[time]" value="1"> Zeitstempel<br>
    <input type="checkbox" name="Wert[value]" value="1"> Wert<br>
    <input type="checkbox" name="Wert[valid]" value="1"> Validiert?<br>
    <input type="checkbox" name="Wert[outlier]" value="1"> Ausrei&szliger?<br>
   	<br>
   	Aus welchem Zeitraum m&oumlchten Sie Daten erhalten?<br>
   	<input type="text" name="von" value="Von (YYYY-MM-TT)"><br>
	<input type="text" name="bis" value="Bis (YYYY-MM-TT) "><br>
	<br>
	<input type="submit" value="Button">
</form>
</p>'
 
?>
