<?php require_once("includes/global.php");
	if(!isset($_SESSION['username'])) header("Location: index.php");
	metadetails();
 ?>
	<link href="scripts/jquery.pnotify.default.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body onload="updateTable()">
	<div id="content">
	<?php navigation("markets"); ?><br/><br/>
		<button id="marketrefresh" class="shinybutton" onclick="updateTable()">Refresh</button>
		<div id="markets"></div>
	</div>

	<script type="text/javascript">
		function updateTable()
		{
			$.ajax({ url: 'updatemarkets.php', dataType: 'html',
				success: function(data, status, xhr){
					$('#markets').html($(data).html());
					$("#marketsTable").tablesorter({sortList: [[0,0], [1,0]]});	
					$("#marketsTable").trigger("update");
					$.pnotify({ title: 'Hello!', text: 'Page Loaded. You can Refresh the Page after 30s.', animation: 'show',
						delay: '3000', type: 'success' });
			   },
			   failure: function() { $.pnotify({ title: 'Uh Oh!', text: 'Something went wrong! Try again later.', animation: 'show',
					delay: '3000', type: 'error'  });
				}				   
			});
			$("#marketrefresh").attr('disabled','disabled');
			setTimeout(function(){$("#marketrefresh").removeAttr('disabled')},30000);
		}
	</script>

	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
   	<script src="scripts/jquery.tablesorter.min.js"></script>
 	<script src="scripts/jquery.pnotify.min.js"></script>
</body>