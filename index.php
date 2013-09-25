<?php
        header("Location: rankings.php");
        
	if(isset($_SESSION['username']))
		header("Location: home.php");	
        
  
	
	
        if($_ISSET['error_reason'])
        {
        	header('Location: index.php');
        }
        else if($_GET['state'] =="8ad4c82bdbf012cf77c6538f5a976279")
        {        	
        
               header('Location: fb-login.php?key=sasjkhaF_ndSsjkan');
        }       
         
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html >
<head>
  <title>Bulls n Bears - Organized by Tathva 2012 </title>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta name="description" content="Stock simulator" />
  <meta name="keywords" content="tathva, bulls and bears" />
  <meta http-equiv="content-language" content="en"/>
  <link rel="stylesheet" type="text/css" href="stylesheets/frontpage.css" />
  <link rel="shortcut icon"  href="images/logo.jpg" />
		  <link href='http://fonts.googleapis.com/css?family=Electrolize' rel='stylesheet' type='text/css'>
  


</head>
<body>


<!-- Facebook Javascript SDK -->
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '106257179526701', // App ID
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true  // parse XFBML
    });

    // Additional initialization code here
  };

  // Load the SDK Asynchronously
  (function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
	}(document));
</script>

<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=405599909493107";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>


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
	<div id="fb">
        <br/><br/>
	<a href="https://www.facebook.com/dialog/oauth/?client_id=106257179526701&redirect_uri=http://bullsnbears.tathva.org/index.php&state=8ad4c82bdbf012cf77c6538f5a976279&scope=email&display=popup"><div id="loginbutton" style="width=100px;height=20px;"></div>
        </a>
	
        <br/>
	<div class="fb-like" data-href="http://facebook.com/bullsnbearscommunity" data-send="true" data-width="300" data-show-faces="true" style="text-align:center;"></div>	

<br />
	<!--<div class="fb-activity" data-href="http://www.facebook.com/bullsnbearscommunity" data-app-id="106257179526701" data-width="300" data-height="200" data-header="true" data-recommendations="true"></div>-->
<br/>
<img src = "images/tathva200.PNG"><br/>
<br/><h3>Visit <a href = "http://tathva.org">Tathva '12 </h3></a>
<br/>

	</div><!-- middle box -->
	</div><!-- content_main -->
	


</div>

</div><!-- content -->
</body>
</html>
