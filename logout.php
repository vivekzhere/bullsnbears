<?php
require_once("includes/global.php");
require_once("fb-sdk/facebook.php");

	if (!isset($_SESSION['id'])) header("Location: index.php") && die();
	$facebook = new Facebook($fbArray);
	$facebook->destroySession();
	$_SESSION=array();
	if (isset($_COOKIE[session_name()])) setcookie(session_name(),'',time()-42000,'/');
	session_destroy();
	header("Location: index.php") && die();