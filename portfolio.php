<?php require_once("includes/global.php"); 
	if(!isset($_SESSION['username'])) header("Location: index.php");
	metadetails();
?>
</head>
<body onload="updatePortfolio()">
	<div id="content">
		<?php navigation("portfolio"); ?>
		<br/><br/>
		<div id="portfolio">
		</div>
		<button id="portfoliorefresh" class="shinybutton" style="margin-left: 20px;" onclick="updatePortfolio()">Refresh</button>
	</div>

	<script type="text/javascript">
	 	    function updatePortfolio()
		    {
			   var targeturl = "updateportfolio.php?t=<?php echo $_GET['t'];?>";
			    $.ajax({
			        url:targeturl
			       ,dataType: 'HTML'
			       ,success: function(data, status, xhr){
			           $('#portfolio').html($(data).html());
			       }
			    });
   			$("#portfoliorefresh").attr('disabled','disabled');
			setTimeout(function(){$("#portfoliorefresh").removeAttr('disabled')},20000);
			}
	</script>
</div>
</body>