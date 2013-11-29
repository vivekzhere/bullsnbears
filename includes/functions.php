<?php	
require_once("config.php");
	//	Add Arrow based on Negative or Positibe.
	function addarrow($value){
		if ($value >= 0) $out = '<span class="up_arrow">';
		elseif ($value < 0)	$out = '<span class="down_arrow">';
		$value = $out.number_format(abs($value),2,'.','').'</span>';
		return $value;
	}

	//	Format Number in INR
	setlocale(LC_MONETARY, 'en_IN');
	function ininr($number){
		if ($number  < 0) $number = -1 * $number;
		$number = money_format('%i', $number);
		$number = substr($number, 4, strlen($number) - 7);
		return "&#8377; ".$number;
	}

	function metadetails($i = "normal") {
		header('Content-type: text/html; charset=utf-8');
		echo <<<CONTENT
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Bulls n' Bears | Tathva '13</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="height=device-height, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
CONTENT;
	if ($i == "normal") echo '<link href="stylesheets/global.css" media="screen, projection" rel="stylesheet" type="text/css" />';
	elseif ($i == "index") echo '<link href="stylesheets/frontpage.css" media="screen, projection" rel="stylesheet" type="text/css" />';
	}

	function FacebookJS($appId) {
		echo <<<CONTENT
			<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
CONTENT;
	echo 'js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId='.$appId.'";';
	echo <<<CONTENT
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
CONTENT;
	}

	function jQuery() {
		echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>';
	}

	function AjaxGet() {
		echo <<<CONTENT
		<script>
			function AjaxGet(a, b) {
				load = document.getElementById('loadOverlay');
				load.style.display = 'block';
				Req = new XMLHttpRequest();
				Req.onreadystatechange = function()
				{	if (Req.readyState == 4) {
						load.style.display = 'none';					
						if (Req.status == 200 || Req.status == 304) {
							if (Req.responseText == "failed!") Ajax_Failure(a, b, Req.responseText);
							else Ajax_Success(a, b, Req.responseText);
						} else Ajax_Failure(a, b, Req.responseText);
					}
  				};
				Req.open("GET", a, true);
				Req.send();
			}
		</script>
CONTENT;
	}


	function AjaxPost() {
		echo <<<CONTENT
		<script>
			function AjaxPost(a, b, c) {
				load = document.getElementById('loadOverlay');
				load.style.display = 'block';
				Req = new XMLHttpRequest();
				Req.onreadystatechange = function()
				{	if (Req.readyState == 4) {
						load.style.display = 'none';					
						if (Req.status == 200 || Req.status == 304) {
							if (Req.responseText == "failed!") Ajax_Failure(a, b, Req.responseText);
							else Ajax_Success(a, b, Req.responseText);
						} else Ajax_Failure(a, b, Req.responseText);
					}
  				};
  				Req.open("POST", a, true);
				Req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				Req.send(b);
			}
		</script>
CONTENT;
	}

	function Load_Anim() {
		echo <<<CONTENT
	<div id="loadOverlay"><div id="circularG" class="center"><div id="circularG_1" class="circularG"></div><div id="circularG_2" class="circularG"></div>
	<div id="circularG_3" class="circularG"></div><div id="circularG_4" class="circularG"></div><div id="circularG_5" class="circularG"></div>
	<div id="circularG_6" class="circularG"></div>	<div id="circularG_7" class="circularG"></div><div id="circularG_8" class="circularG"></div>
	</div></div>
CONTENT;
	}

?>