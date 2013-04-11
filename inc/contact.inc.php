<?php
// Grundlegende Einstellungen: 
$Mail = "test@eggtracker.de"; //Hier die eigene E-Mail Adresse einfügen. 
?> 
<div class="contactform">
<h2>Eggtracker Kontakt</h2> 

<p align="justify">Bei Fragen und Anregungen benutzen Sie bitte das Kontaktformular unten.<br>
Wir werden uns dann so schnell wie möglich bei Ihnen melden.</p>
<p align="justify">Damit der Kontakt hergestellt werden kann f&uumlllen Sie bitte alle Felder vollständig aus:</p>

<form action="<?php print $_SERVER['PHP_SELF']; ?>?action=contact" method="POST"> 

Name: <input type="text" name="helper">
<span style=display:none>Name:</span> <span style=display:none>input type="text" name="Name"</span><br>

E-Mail:<input type="text" name="Mail"><br><br>
Ihre Nachricht:<br>
<textarea name="Eintrag" cols="40" rows="20"></textarea><br>
<input type="submit" value="abschicken" name="abschicken">
<input type="reset" value="zurücksetzen" name="reset"><br>
</form>
</p>
<?php

if(empty($_POST['name'])){
   
  if(isset($_POST['abschicken'])){ // Der abschicken button wurde gedrückt. 
    
    $helper = strip_tags($_POST['helper']);
	$mail = strip_tags($_POST['Mail']);
	$eintrag = strip_tags($_POST['Eintrag']);
	
    
    if(empty($helper) OR empty($mail) OR empty($eintrag) OR (filter_var($mail, FILTER_VALIDATE_EMAIL)) == false ) {
      print "<span style=color:red; align=\"justify\">Sie haben eines der Felder nicht oder falsch ausgefüllt. Prüfen Sie beispielsweise, ob sie Ihre Emailadresse korrekt eingegeben haben.\n</span>";
      print "</br></br>Sie haben folgende Daten angegeben: </br>
      Name: ".$helper."</br>
      Email: ".$mail."</br>
      Nachricht: ".$eintrag;
    } 

    else{ 
      $Abs_Mail = $mail;
      $Abs_Name = $name; 
      $Abs_Nachricht = $eintrag; 
      $Betreff = $helper.", Helpdesk ID: ".md5(uniqid(rand(), TRUE));
      $Nachricht = "Neue Nachricht aus dem Eggtrackerformular\n\n Absender: $Abs_Name \n Email: $Abs_Mail\n _______\n$Abs_Nachricht\n_______\n"; 
  
      //Nun kommt die Mail funktion: 
      $senden = mail($Mail, $Betreff, $Nachricht,"From: $Abs_Mail"); 
  
     if($senden){ // Wenn die Mail versandt wurde, dann diesen Text ausgeben: 
        print "Ihre Mail wurde <b>erfolgreich</b> an das Eggtracker-Team versandt. </br></br>
        Der Vorgang wird unter folgendem Betreff bearbeitet: \"".$Betreff."\"</br>
        Bitte speichern Sie diese ID für zukünftigen Kontakt in Bezug auf diese Email.</br>
        </br>
        
		VIELEN DANK!
"; 
      } 
  
      else { //Sonst diesen : 
        print "Ihre Mail konnte leider nicht an das Eggtracker-Team versandt werden. 
			 Probieren Sie es später noch einmal"; 
      } 
       
    } 
  } 
   
  else{ //Der abschicken button wurde noch nicht gedrückt 
    print "Um Ihre Nachricht zu senden dr&uumlcken Sie bitte den \"abschicken\" Button<br>"; 
  } 
  }
else{
	 echo "stfu"; // botspam
	};
?> 
</div>
