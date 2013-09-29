<?php require_once("includes/global.php");
	if(!isset($_SESSION['username'])) header("Location: index.php");
	metadetails();
 ?>

</head>
<body onload="updateTable()">
	<div id="content">
	<?php navigation("markets"); ?>
		<br/>
		<button class="shinybutton" style="position: relative; top: 25px; left: 40px;" onclick="updateTable()">Refresh</button><br/>
		<div id="markets">
		</div>
	</div>


	<script type="text/javascript">
		function updateTable()
		{
			$.ajax({ url: 'updatemarkets.php', dataType: 'html',
				success: function(data, status, xhr){
					$('#markets').html($(data).html());
					$("#marketsTable").tablesorter({widgets: ['sortPersist']});	
					$("#marketsTable").trigger("update");				   
			   }
			});
		}
	</script>

	<script type="text/javascript">
		$(document).ready(function()
		{
			$.tablesorter.addWidget({
				  id: "sortPersist",
				  format: function(table) {
					  var COOKIE_NAME = 'MY_PERSISTENT_TABLE';
					  var cookie = $.cookie(COOKIE_NAME);
					  var options = {path: '/'};
					  var data = [];
					  var sortList = table.config.sortList;
					  var id = $(table).attr('id');
					  if (sortList.length > 0) {
						  if (typeof(cookie) == "undefined" || cookie == null) {
							  data = {id: sortList};
						  }
						  else {
							  data = $.evalJSON(cookie);
							  data[id] = sortList;
						  }
						  $.cookie(COOKIE_NAME, $.toJSON(data), options);
					  }
					  else {
						  if (typeof(cookie) != "undefined" && cookie != null) {
							  var data = $.evalJSON($.cookie(COOKIE_NAME));
							  if (typeof(data[id]) != "undefined" && data[id] != null) {
								  sortList = data[id];
								  if (sortList.length > 0) {
										$(table).trigger("sorton", [sortList]);
								  }
							   }
						  }
					  }
				  }
			  });
			$("#marketsTable").tablesorter({widgets: ['sortPersist']});
		});
	</script>

	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
   	<script src="scripts/jquery.tablesorter.min.js"></script>
 	<script src="scripts/jquery.cookie.js"></script>
 	<script src="scripts/jquery.json-2.2.min.js"></script>
</body>