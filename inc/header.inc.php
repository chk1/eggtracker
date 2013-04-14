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
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<link rel="stylesheet" type="text/css" href="static/css/style.css">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="static/css/style-ie.css" />
	<![endif]-->
	<script type="text/javascript">
	// hide address bar on some mobile browsers
	window.addEventListener("load",function() {
		setTimeout(function(){
			window.scrollTo(0, 1);
		}, 0);
	});
	</script>
</head>
<body>

<div id="wrapper">

<div id="header">
	
	<a href="./"><img src="img/logo_neu.png" alt="Egg Tracker Logo" title="Egg Tracker" id="logo"></a>
	<a href="?action=mobile_home" id="mobmore">&nbsp;&nbsp;&rarr; Menü</a>
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
