<?php

require_once("includes/global.php");
	if(!isset($_SESSION['username']))
		header("Location: index.php");
		

	metadetails();
?>

   
   <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
   

<script type="text/javascript" src="scripts/jquery.ticker.js" ></script>

<script type="text/javascript">

 jQuery('#ticker').webTicker()
 
 
</script>
   
      
   <script type="text/javascript">
	 //   setInterval( "updatePortfolio();", 600000 ); 

	    
	    function updatePortfolio()
	    {
		   var targeturl = <?php echo "\"updateportfolio.php?t=".$_GET['t']."\""; ?>;
	    		
		    $.ajax({
		        url:targeturl
		       ,dataType: 'HTML'
		       ,success: function(data, status, xhr){
		           $('#portfolio').html($(data).html());
		       }
		    });
		}
	</script>
</head>
<body onload="updatePortfolio()">
<div id="content">
	<?php navigation("portfolio"); ?>
	<br/><button onclick="updatePortfolio()">Refresh</button>
	<br/>
	<div id="portfolio">
        



	</div>
	
	</div><!-- content_main -->
</div><!--content-->
</body>