<?php require_once("includes/global.php");
	if(!isset($_SESSION['username']))
		header("Location: index.php");
		

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
.html-marquee {height:50px;width:1300px;font-family:Cursive;font-size:22pt;color:000000;border-width:1;border-style:solid;border-color:000000;}
</style>
<!--<marquee class="html-marquee" style="overflow:hidden;" direction="left" behavior="scroll" scrollamount="12" >The weekly winners are requested to call 9895602275 to collect thier prizes.....</marquee>-->
	
	
	<br/><br/>
	
	<h2 style="color:grey; width:90%; border-bottom:1px solid #ddd; font-size:20px; font-weight:normal; margin:0 auto; z-index:-1;" >Winners</h1>
	<table style="margin:0 150px; position:absolute; top:97px; ">
	
	<tbody>
	<tr style="font-family:sans; font-size:12px; font-weight:bold;">
	<td style="text-align:center;  width:155px; margin-right:10px"><img src="https://graph.facebook.com/1432241263/picture" style="width:30px;height:30;vertical-align:middle" /> Joju Joseph Zajo </td>
        <td style="text-align:center;  width:160px; margin-right:10px"><img src="https://graph.facebook.com/100000211800509/picture" style="width:30px;height:30;vertical-align:middle" /> Amrit Ratnabham </td>
        <td style="text-align:center;  width:160px; margin-right:10px"><img src="https://graph.facebook.com/765573046/picture" style="width:30px;height:30;vertical-align:middle" /> Haksar Farooq </td>
	<!--<td style="text-align:center;  width:150px; margin-right:10px"> alex george </td>
	
	<td style="text-align:center;  width:150px; margin-right:10px"> Alan Paul Joy </td>--!>
	</tr><tr></tr>
	</tbody>
	<tr ></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr>
	<td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px"> Week 1</th>
	<td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px"> Week 2</th>
        <td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px"> Week 3</th>
	<!--<td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px"> Week 4</th>	
	<td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px"> Final Winner</th>-->	
	
	</tr>
	
	<br/><br/>



	</table>
	<div id="rankings">
	
        </div>
	
	</div><!-- content_main -->
</div><!--content-->
</body>