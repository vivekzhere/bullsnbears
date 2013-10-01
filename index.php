<?php
require_once("includes/global.php");		
	if(isset($_SESSION['username'])) header("Location: home.php");
?>

<!DOCTYPE html>
<html>	
<head>
	<title>Bulls n Bears - Organized by Tathva 2013 </title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="description" content="Virtual Stock Market" />
	<meta name="keywords" content="tathva, bulls and bears" />
	<meta http-equiv="content-language" content="en"/>
	<link rel="stylesheet" type="text/css" href="stylesheets/frontpage.css" />
	<link rel="shortcut icon" href="images/logo.jpg" />
	<link href='http://fonts.googleapis.com/css?family=Electrolize' rel='stylesheet' type='text/css'>
</head>

<body>
	<div id="content">
		<div id="banner">
		</div><!-- banner -->
		
		<div id="content_main">
			<div id="leftbox">
				<div id="tathvaad"> 
				</div>
				</div>
			<div id="rightbox">
				<div id="sponsorad">
				</div>
			</div>
		</div>
	</div>
</body>
</html>

