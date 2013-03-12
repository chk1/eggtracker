<?php
include("config.inc.php");
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Eggtracker<?php if(isset($action) && isset($menu_entries[$action])) { echo " - ".$menu_entries[$action]; } ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="stylesheet" type="text/css" href="static/style.css">
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="static/style-ie.css" />
	<![endif]-->
</head>
<body>

<div id="wrapper">

<div id="header">
	
	<a href="./"><img src="img/logo_neu.png" alt="Egg Tracker Logo" title="Egg Tracker" id="logo"></a>
	<?php if($action == ""): ?><a href="?action=mobile_home" id="mobmore">&rarr; More</a> <?php endif; ?>
	<div id="menu">
	<ul id="menulist">
	<?php
		foreach($menu_entries as $key => $value) {
			echo '<li><a href="./?action='.$key.'"';
			if($action == $key) { echo ' class="menu_selected"'; }
			echo '>'.$value.'</a></li>'.PHP_EOL;
		}
	?>
	</ul>
	</div>

</div>

<div id="content">
