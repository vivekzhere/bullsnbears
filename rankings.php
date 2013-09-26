<?php require_once("includes/global.php");
	//if(!isset($_SESSION['username']))
	//	header("Location: index.php");
		

	metadetails();	
 ?>

  <script type="text/javascript" src="scripts/ticker.js"></script>
    
     <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
    
        <script type="text/javascript">
	//    setInterval( "updateRankings();", 12000 ); 

	    
	    function updateRankings()
	    {
		   
		    $.ajax({
		        url: 'updaterankings.php'
		       ,dataType: 'HTML'
		       ,success: function(data, status, xhr){
		           $('#rankings').html($(data).html());
		       }
		    });

	    }
    </script>
</head>
<body onload="updateRankings()">
<div id="content">
	<?php navigation("rankings"); ?>
<br/><button onclick="updateRankings()">Refresh</button>
<style type="text/css">
.html-marquee {height:40px;width:955px;font-family:Cursive;font-size:18pt;color:000000;border-width:1;border-style:solid;border-color:000000; margin:10px 0px 10px 0px}
</style>
	<div id="rankings">
	
        </div>
	
	</div><!-- content_main -->
</div><!--content-->
</body>