<?php
$action = ""; 
if(isset($_GET["action"])) $action = $_GET["action"];

$menu_entries = array(""=>"Map", 
	"list" => "List Eggs",
	"diagram" => "Diagram",
	"more" => "More",
	"about" => "About");

/*
	header
*/
include("inc/header.inc.php");

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
}

/*
	footer
*/
include("inc/footer.inc.php"); 
?>