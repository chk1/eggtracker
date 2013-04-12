<?php
include('inc/config.inc.php');
?> 
<div class="contactform">

<p align="justify">Bei Fragen und Anregungen benutzen Sie bitte das Kontaktformular unten.<br>
Wir werden uns dann so schnell wie möglich bei Ihnen melden.</p>

<?
// enter submitted data into the form again on submission errors (missing, invalid fields, etc.)
$name = "";
$mail = "";
$message = "";

$success = false;

if(isset($_POST['submit'])) { // mail form was submitted
	$name = $_POST['realname'];
	$mail = $_POST['mail'];
	$message = $_POST['message'];

	if(empty($name) OR empty($mail) OR empty($message) OR (filter_var($mail, FILTER_VALIDATE_EMAIL) == false)) {
		echo '<span style="color:red;text-align:justify;">Sie haben eines der Felder nicht oder falsch ausgefüllt. Prüfen Sie beispielsweise, ob sie Ihre Emailadresse korrekt eingegeben haben.</span>';
	} elseif(!empty($_POST["name"])) {
		// botspam
	} else {
		$subject = $name.", Helpdesk ID: ".md5(uniqid(rand(), TRUE));
		$mailbody = "Neue Nachricht aus dem Eggtrackerformular\n\n Absender: $name \n Email: $mail\n _______\n$message\n_______\n"; 
		$sendmail = @mail($conf["email"], $subject, $mailbody, "From: $mail"); 

		if($sendmail){ // email was sent successfully
			echo 'Ihre Mail wurde erfolgreich an das Eggtracker-Team versandt. </br></br>
					Der Vorgang wird unter folgendem Betreff bearbeitet: "'. $subject .'"</br>
					Bitte speichern Sie diese ID für zukünftigen Kontakt in Bezug auf diese Email.</br>
					</br>
					VIELEN DANK!'; 
		} else {
			echo "<span style=color:red;text-align:justify\">Ihre Mail konnte leider nicht an das Eggtracker-Team versandt werden. Probieren Sie es später noch einmal.</span>";
		} 
	}
}

if(!$success):
?>

<form action="?action=contact" method="POST"> 
	<span style=display:none>Name:</span>
	<span style=display:none>input type="text" name="name"</span>

	<label for="realname">Ihr Name:</label>
	<input id="realname" type="text" name="realname" value="<?= htmlentities($name) ?>"><br>

	<label for="mail">Ihre Email:</label>
	<input id="mail" type="text" name="mail" value="<?= htmlentities($mail) ?>"><br>

	<label for="message">Ihre Nachricht:</label>
	<textarea id="message" name="message"><?= htmlentities($message) ?></textarea><br>
	<br>
	<label for="submit">&nbsp;</label><input type="submit" value="Abschicken" name="submit" id="submit">
</form>
<?php
endif;
?>
</p>
 
</div>
