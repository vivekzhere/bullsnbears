<?php
require_once("includes/global.php");		
	
	if(isset($_SESSION['username'])) header("Location: home.php");	
	if($_ISSET['error_reason'])	header('Location: index.php');
	elseif($_GET['state'] =="8ad4c82bdbf012cf77c6538f5a976279")	header('Location: fb-login.php?key=sasjkhaF_ndSsjkan');
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
	<div id="fb-root"></div>

	<!-- Facebook Javascript SDK -->
	<script>
	  window.fbAsyncInit = function() {
		FB.init({
		  appId	  : '106257179526701', // App ID
		  status	 : true, // check login status
		  cookie	 : true, // enable cookies to allow the server to access the session
		  xfbml	  : true  // parse XFBML
		});

		// Additional initialization code here
	  };

	(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=106257179526701";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	</script>

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
			<div id="fb" style="height: 300px;">
				<br/><img src = "images/tathva200.PNG"><br/><br/>
				<h3>Visit <a href = "http://tathva.org">Tathva '13 </h3></a>
				<br/><br/>
				<div class="fb-like" data-href="https://www.facebook.com/tathva" data-width="450" data-show-faces="true" data-send="false"></div>
				<br/><br/>
				<a href="https://www.facebook.com/dialog/oauth/?client_id=106257179526701&redirect_uri=http://bullsnbears.tathva.org/index.php&state=8ad4c82bdbf012cf77c6538f5a976279&scope=email&display=popup"><div id="loginbutton" style="width=100px;height=20px;"></div></a>
			</div><!-- fb box -->
		</div><!-- content_main -->
	</div>
</body>
</html>