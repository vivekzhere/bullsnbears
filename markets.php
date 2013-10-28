<?php require_once("includes/global.php");
if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}
	metadetails();
?>
</head>
<body onload="updateMarket()">
	<div id="banner"></div>
	<?php Menu(); ?>
	<div id="content">
		<button id="marketrefresh" style="float: right; margin-right: 0;" class="button btn-green" onclick="updateMarket()">Refresh</button>
		<div id="markets"></div>
	</div>


	<script>
 	    function updateMarket()
	    {
	    	pr = document.getElementById("marketrefresh");
    		AjaxGet('updatemarkets.php', 'markets');
			pr.className = pr.className.replace(" btn-green",""); pr.disabled = true;
	    	setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);		    		
		}
	</script>
	<?php require_once("includes/ticker.php"); AjaxGet(); Load_Anim(); ?>
</body>
</html>