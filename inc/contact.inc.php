<?php
// Grundlegende Einstellungen: 
$Mail = "test@eggtracker.de"; //Hier die eigene E-Mail Adresse einfügen. 
?> 
<div class="contactform">
<h2>Eggtracker Kontakt</h2> 

Bei Fragen und Anregungen benutzen Sie bitte das Kontaktformular unten. </br>
Wir werden uns dann so schnell wie m&oumlglich bei Ihnen melden. </br></br>
Damit der Kontakt hergestellt werden kann f&uumlllen Sie bitte alle Felder vollst&aumlndig aus:</br>

<form action="<?php print $_SERVER['PHP_SELF']; ?>?action=contact" method="POST"> 
<table style="width:400px;"> 
<tr><td>Name:</td><td><input type="text" name="helper"></td> <td style=display:none>Name:</td><td style=display:none><input type="text" name="Name"</td></tr>

<tr><td>E-Mail:</td><td><input type="text" name="Mail"></td></tr> 
<tr><td >Ihre Nachricht:</td><td colspan="3"><textarea name="Eintrag" cols="70" rows="20"></textarea></td></tr> 
<tr><td><input type="submit" value="abschicken" name="abschicken"></td><td><input type="reset" value="zur&uuml;cksetzen" name="reset"></td></tr> 
</table> 
</form></p> 

<?php

if(empty($_POST['name'])){
   
  if(isset($_POST['abschicken'])){ // Der abschicken button wurde gedrückt. 
    
    if(empty($_POST['helper']) OR empty($_POST['Mail']) OR empty($_POST['Eintrag']) OR (filter_var($_POST['Mail'], FILTER_VALIDATE_EMAIL)) == false ) {
      print "<font color=red>Sie haben eines der Felder nicht oder falsch ausgef&uumlllt. Pr&uumlfen Sie beispielsweise, ob sie Ihre Emailadresse korrekt eingegeben haben.\n</font>";
      print "</br>Sie haben folgende Daten angegeben: </br>
      Name: ".$_POST['helper']."</br>
      Email: ".$_POST['Mail']."</br>
      Nachricht: ".$_POST['Eintrag'];
    } 

    else{ 
      $Abs_Mail = strip_tags($_POST['Mail']);  
      $Abs_Name = strip_tags($_POST['Name']); 
      $Abs_Nachricht = strip_tags($_POST['Eintrag']); 
      $Betreff = $_POST['helper'].", Helpdesk ID: ".md5(uniqid(rand(), TRUE));
      $Nachricht = "Neue Nachricht aus dem Eggtrackerformular\n\n Absender: $Abs_Name \n Email: $Abs_Mail\n _______\n$Abs_Nachricht\n_______\n"; 
  
      //Nun kommt die Mail funktion: 
      $senden = mail($Mail, $Betreff, $Nachricht,"From: $Abs_Mail"); 
  
     if($senden){ // Wenn die Mail versandt wurde, dann diesen Text ausgeben: 
        print "Ihre Mail wurde <b>erfolgreich</b> an das Eggtracker-Team versandt. </br></br>
        Der Vorgang wird unter folgendem Betreff bearbeitet: \"".$Betreff."\"</br>
        Bitte speichern Sie diese ID f&uumlr zuk&uumlnftigen Kontakt in Bezug auf diese Email.</br>
        </br>
        
		VIELEN DANK!
"; 
      } 
  
      else { //Sonst diesen : 
        print "Ihre Mail konnte leider nicht an das Eggtracker-Team versandt werden. 
 Probieren Sie es sp&auml;ter noch einmal
"; 
      } 
       
    } 
  } 
   
  else{ //Der abschicken button wurde noch nicht gedrückt 
    print "Um Ihre Nachricht zu senden dr&uumlcken Sie bitte den \"abschicken\" Button"; 
  } 
  }
else{
	 echo "stfu"; // botspam
	};
?> 
</div>