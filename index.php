<?php
$action = ""; 
if(isset($_GET["action"])) $action = $_GET["action"];

$menu_entries = array(""=>"Karte", 
	"list" => "Ei Liste",
	"diagram" => "Diagramm",
	"more" => "Mehr",
	"about" => "Info",
	"contact" => "Kontakt");

/*
	header
*/
if(is_file("inc/config.inc.php")) { 
	include("inc/header.inc.php");
} else {
	die("It appears that you do not have set up your config file yet. Please check the installation manual (INSTALL).");
}

/*
	content:

	switch different pages:
		index.php?action=about
		index.php?action=list
		etc.
*/
switch($action) {
	default:
		include("inc/home.inc.php"); 
		break;
	case "list":
		include("inc/list_eggs.inc.php"); 
		break;
	case "diagram":
		include("inc/diagram.inc.php"); 
		break;
	case "more":
		include("inc/more.inc.php"); 
		break;
	case "about":
		include("inc/about.inc.php"); 
		break;
	case "mobile_home":
		include("inc/mobile_home.inc.php"); 
		break;
	case "export_form":
		include("inc/export_form.inc.php"); 
		break;
	case "list_values":
		include("inc/list_values_form.inc.php"); 
		break;
	case "list_values_table":
		include("inc/list_values.inc.php"); 
		break;
	case "contact":
		include("inc/contact.inc.php");
		break;
	case "blank":
		include("inc/template.inc.php");
		break;
}

/*
	footer
*/
include("inc/footer.inc.php"); 
?>
