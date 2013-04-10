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
	
#DropDownMenue fuer die Eier und Lanuv Daten
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
	Von welchem Ei möchten Sie die Daten sehen?<br>
	'.$dropped.'<br><br>
   <div class="tabledownload"> <table>
    <tr>
      <th>Was möchten Sie sehen?<br/>
        <input type="radio" name="Parameter" value="1" />
        Ozon<br />
  <input type="radio" name="Parameter" value="2" />
        Stickstoffdioxid<br />
  <input type="radio" name="Parameter" value="3" />
        Kohlenstoffmonoxid<br />
  <input type="radio" name="Parameter" value="4" />
        Temperatur<br />
  <input type="radio" name="Parameter" value="5" />
      Luftfeuchtigkeit</th>
      <th>Welche Parameter möchten Sie anzeigen lassen?<br />
        <input type="checkbox" name="Wert[id]" value="1" />
        Werte ID<br />
  <input type="checkbox" name="Wert[time]" value="1" />
        Zeitstempel<br />
  <input type="checkbox" name="Wert[value]" value="1" />
        Wert<br />
  <input type="checkbox" name="Wert[valid]" value="1" />
        Validiert?<br />
  <input type="checkbox" name="Wert[outlier]" value="1" />
      Ausreißer?</th>
    </tr>
    <tr>
      <td>Aus welchem Zeitraum möchten Sie Daten erhalten?</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><input type="text" name="von" value="Von (YYYY-MM-TT)" />
        <br />
      <input type="text" name="bis" value="Bis (YYYY-MM-TT) " /></td>
      <td><input type="submit" value="Abrufen" /></td>
    </tr>
  </table></div>
</form>
</p>'

#Datapicker 
?>

<link rel="stylesheet" type="text/css" href="static/css/jquery-ui-1.10.2.custom.min.css">
<script src="static/jquery/jquery-1.9.1.min.js"></script>
<script src="static/jquery/jquery-ui-1.10.2.custom.min.js"></script> <!-- custom = Core + Datepicker + Slider -->
<script src="static/jquery/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
var options = { dateFormat: "yy-mm-dd", timeFormat: "hh:mm" };
$('#datumvon').datetimepicker(options);
$('#datumbis').datetimepicker(options);
</script>
