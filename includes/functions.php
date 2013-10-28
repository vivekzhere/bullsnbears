<?php	
require_once("includes/config.php");
	//	Add Arrow based on Negative or Positibe.
	function addarrow($value){
		if ($value >= 0) $out = '<img src="images/up_g.gif" width="10" height="14" alt="up" /> <span style="color:green;">';
		elseif ($value < 0)	$out = '<img src="images/down_r.gif" width="10px" height="14px" alt="down" /> <span style="color:red;">';
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
	<link rel="icon" type="image/png" href="images/logo.png" />
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

	function Menu() {
		echo <<<CONTENT
			<div id="Menu" style="width: 149px;" onclick="ToggleMenu();">
				<ul style="width: 100%; height: 100%;">
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'home.php'" class="Menu-btn" style="border-top-right-radius: 10px;">Home</button></li>
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'portfolio.php'" class="Menu-btn">Portfolio</button></li>
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'trade.php'" class="Menu-btn">Trade</button></li>
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'schedule.php'" class="Menu-btn">Schedule</button></li>
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'lookup.php'" class="Menu-btn">Lookup</button></li>
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'markets.php'" class="Menu-btn">Market</button></li>
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'rankings.php'" class="Menu-btn">Rankings</button></li>
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'history.php'" class="Menu-btn">History</button></li>
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'help.php'" class="Menu-btn">Help</button></li>
					<li class="Menu-li"><button type="button" onclick="window.location.href = 'logout.php'" class="Menu-btn" style="border-bottom-right-radius: 10px;">Logout</button></li>
				</ul>
			</div>
			<script>
				function ToggleMenu() {
					var Menu = document.getElementById('Menu'), x;
					x = Menu.style.width;
					if (x == "149px") { Menu.style.left = "0px"; Menu.style.width = "150px"; }
					else { Menu.style.width = "149px"; Menu.style.left = "-150px"; }
				}
			</script>
CONTENT;
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
							if (Req.responseText != "failed!") document.getElementById(b).innerHTML = Req.responseText;
							else alert("Failed!");
						} else alert("Failed!");
					}
  				}
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
							if (Req.responseText != "failed!") { document.getElementById(b).innerHTML = Req.responseText; document.getElementById(b).value = Req.responseText; }
							else alert("Failed!");
						} else alert("Failed!");
					}
  				}
				Req.open("POST", a, true);
				Req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				Req.send(c);
			}
		</script>
CONTENT;
	}

	function Load_Anim() {
		echo <<<CONTENT
	<div id="loadOverlay"><div id="circularG"><div id="circularG_1" class="circularG"></div><div id="circularG_2" class="circularG"></div>
	<div id="circularG_3" class="circularG"></div><div id="circularG_4" class="circularG"></div><div id="circularG_5" class="circularG"></div>
	<div id="circularG_6" class="circularG"></div>	<div id="circularG_7" class="circularG"></div><div id="circularG_8" class="circularG"></div>
	</div></div>
CONTENT;
	}

?>