<?php require_once("includes/global.php");
	metadetails();	
 ?>
	<link href="scripts/jquery.pnotify.default.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body onload="updateRankings()">
	<div id="content">
		<?php navigation("rankings"); ?>
		<br/><br/></br/>
		<div id="rankings"></div>
		<button id="rankingsrefresh" class="shinybutton" onclick="updateRankings()">Refresh</button>
	</div>

	<script type="text/javascript">
		function updateRankings()
		{
			$.ajax({
				url: 'updaterankings.php'
			   ,dataType: 'HTML'
			   ,success: function(data, status, xhr){
				   	$('#rankings').html($(data).html());
				   	$.pnotify({ title: 'Hello!', text: 'Page Loaded. You can Refresh the Page after 30s.', animation: 'show',
						delay: '3000', type: 'success' });
			   },
			   failure: function() { $.pnotify({ title: 'Uh Oh!', text: 'Something went wrong! Try again later.', animation: 'show',
					delay: '3000', type: 'error'  });
				}
			});
   			$("#rankingsrefresh").attr('disabled','disabled');
			setTimeout(function(){$("#rankingsrefresh").removeAttr('disabled')},30000);
		}
	</script>
	<script src="scripts/jquery.pnotify.min.js"></script>
</div>
</body>