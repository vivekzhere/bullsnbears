<?php require_once("includes/global.php"); 
	if(!isset($_SESSION['username'])) header("Location: index.php");
	metadetails();
	$t =  $_GET['t'];
?>
	<link href="scripts/jquery.pnotify.default.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body onload="updatePortfolio()">
	<div id="content">
		<?php navigation("portfolio"); ?><br/><br/>
		<button id="portfoliorefresh" class="shinybutton" onclick="updatePortfolio({$t})">Refresh</button>
		<div id="portfolio"></div>
	</div>

	<script type="text/javascript">
	 	    function updatePortfolio(a)
		    {
			   var targeturl = "updateportfolio.php?t=" + a;
			    $.ajax({
			        url:targeturl
			       ,dataType: 'HTML'
			       ,success: function(data, status, xhr){
			           $('#portfolio').html($(data).html());
			           $.pnotify({ title: 'Hello!', text: 'Page Loaded. You can Refresh the Page after 30s.', animation: 'show',
						delay: '3000', type: 'success' });
			       },
					failure: function() { $.pnotify({ title: 'Uh Oh!', text: 'Something went wrong! Try again later.', animation: 'show',
						delay: '3000', type: 'error'  });
					}
			    });
   			$("#portfoliorefresh").attr('disabled','disabled');
			setTimeout(function(){$("#portfoliorefresh").removeAttr('disabled')},30000);
			}
	</script>
	<script src="scripts/jquery.pnotify.min.js"></script>
</div>
</body>