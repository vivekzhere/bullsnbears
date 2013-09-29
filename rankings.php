<?php require_once("includes/global.php");
	metadetails();	
 ?>
</head>
<body onload="updateRankings()">
	<div id="content">
		<?php navigation("rankings"); ?>
		<br/><br/></br/>
		<div id="rankings"></div>
		<button id="rankingsrefresh" style="position: relative; left: -85px; top: 2px;" class="shinybutton" onclick="updateRankings()">Refresh</button>
	</div>

	<script type="text/javascript">
		function updateRankings()
		{
			$.ajax({
				url: 'updaterankings.php'
			   ,dataType: 'HTML'
			   ,success: function(data, status, xhr){
				   	$('#rankings').html($(data).html());
			   }
			});
   			$("#rankingsrefresh").attr('disabled','disabled');
			setTimeout(function(){$("#rankingsrefresh").removeAttr('disabled')},20000);
		}
	</script>
</div>
</body>