<?php
	require_once("fb-php-sdk/facebook.php");
	
	if(isset($_SESSION['username']))
		header("Location: home.php");
	
	// Setting APP Details and initializing API
	$app_id = '106257179526701';
	$app_secret = '9ffd564e1becb3b9691be3c9b13a3e26';
	$app_namespace = 'bullsnbears';
	$app_url = 'https://apps.facebook.com/' . $app_namespace . '/';
	$scope = 'email';
	$facebook = new Facebook( array(
		                   'appId' => $app_id,
		                   'secret' => $app_secret,
		                 ));
		                
	$facebook->destroySession();
	$_SESSION=array();
	if(isset($_COOKIE[session_name()])){
		setcookie(session_name(),'',time()-42000,'/');
	}
	session_destroy();
	header("Location: index.php");
	
	
?>



