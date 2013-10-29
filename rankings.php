<?php
require_once("includes/global.php");
	metadetails();	
 ?>
</head>
<body onload="updateRankings()">
	<div id="content">
		<?php if (isset($_SESSION['id'])) Menu(); ?>
		<br/>
		<div id="rankings"></div>
		<button id="rankingsRefresh" class="button btn-green" onclick="updateRankings()">Refresh</button>
	</div>

	<script type="text/javascript">
		function updateRankings()
		{
	    	pr = document.getElementById("rankingsRefresh");
    		AjaxGet('updaterankings.php', 'rankings');
			pr.className = pr.className.replace(" btn-green",""); pr.disabled = true;
	    	setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);		    		
		}
	</script>
	<?php AjaxGet(); Load_Anim(); ?>
</div>
</body>