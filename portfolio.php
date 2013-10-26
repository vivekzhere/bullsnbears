<?php
require_once("includes/global.php"); 
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}
	metadetails();
	if (isset($_GET['t'])  && $_GET['t'] == 'Shorted') $t = 'Shorted';
	else $t = 'Bought';
?>
</head>
<body onload="updatePortfolio()">
	<div id="banner"></div>
	<?php Menu(); ?>
	<div id="content">
		<div id="portfolio"></div>
	</div>

	<script>
	 	    function updatePortfolio(a, b)
		    {
		    	a = a || 'NULL';
		    	pr = document.getElementById("portfoliorefresh");
		    	if (a == 'NULL' || !(pr.disabled) || b == 'NULL') {
		    		AjaxGet('updateportfolio.php?t=' + a, 'portfolio');
		    		setTimeout(function() { pr = document.getElementById("portfoliorefresh"); pr.className = pr.className.replace(" btn-green",""); pr.disabled = true; }, 5000);
		    		setTimeout(function() { pr = document.getElementById("portfoliorefresh"); pr.className = pr.className + " btn-green"; pr.disabled = false; }, 35000);		    		
		    	}
			}
	</script>

	<?php require_once("includes/ticker.php"); AjaxGet(); Load_Anim(); ?>
</body>
</html>