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
	
#dropdown menu fuer die Auswahl der Eier
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
<form method="POST" action="export.php">
  <div class="tabledownload" align="left"><h2>Datendownload</h2>
    Von welchem Ei möchen Sie die Daten exportieren?<br>
        '.$dropped.'<br />
        <br>
    <table width="650">
          <tr>
            <th><div align="left">Was möchten Sie exportieren?</div></th>
            <th><div align="left">Welche Parameter möchten Sie exportieren?</div></th>
          </tr>
          <tr>
            <th width="260"><div align="left">
              <input type="radio" name="Parameter" value="1" />
              Ozon<br />
              <input type="radio" name="Parameter" value="2" />
              Stickstoffdioxid<br />
              <input type="radio" name="Parameter" value="3" />
              Kohlenstoffmonoxid<br />
              <input type="radio" name="Parameter" value="4" />
              Temperatur<br />
              <input type="radio" name="Parameter" value="5" />
            Luftfeuchtigkeit</div></th>
            <th width="378"><div align="left">
              <input type="checkbox" name="Wert[id]" value="1" />
              Werte ID<br />
              <input type="checkbox" name="Wert[time]" value="1" />
              Zeitstempel<br />
              <input type="checkbox" name="Wert[value]" value="1" />
              Wert<br />
              <input type="checkbox" name="Wert[valid]" value="1" />
              Validiert?<br />
              <input type="checkbox" name="Wert[outlier]" value="1" />
            Ausreißer?</div></th>
          </tr>
          <tr>
            <td>Aus welchem Zeitraum möchten sie die Daten erhalten.<br />
              <input id="datumvon" type="text" name="von" value="Von (YYYY-MM-TT)"><br>
		<input id="datumbis" type="text" name="bis" value="Bis (YYYY-MM-TT) "></td>
            <td><div align="left"><br />
              Datenformat<br />
              <input type="radio" name="format" value="xml" />
XML<br />
<input type="radio" name="format" value="csv" />
CSV<br />
<input type="radio" name="format" value="json" />
JSON</div>              </p></td>
          </tr>
    </table>
        <br />
        <input type="submit" value="Speichern"/>
  </div>
</form>
</p>';
 
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
