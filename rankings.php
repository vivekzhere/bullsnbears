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
<marquee class="html-marquee" style="overflow:hidden;" direction="left" behavior="scroll" scrollamount="12" >Winners contact Tathva'12 Registration Desk to collect your prizes. Call 9400807648 for clarifications....</marquee>
	
	
	<br/><br/>
<!--	
	<h2 style="color:grey; width:90%; border-bottom:1px solid #ddd; font-size:20px; font-weight:normal; margin:0 auto; z-index:-1;" >Winners</h1>
	<table style="margin: -77px 0px 20px 150px; top:97px; ">
	
	<tbody>
	<tr>
        <td style="text-align:center;  width:160px; margin-right:10px"><img src="https://graph.facebook.com/765573046/picture" style="width:30px;height:30;vertical-align:middle" /> Haksar Farooq </td>
        <td style="text-align:center;  width:160px; margin-right:10px"><img src="https://graph.facebook.com/100001477938256/picture" style="width:30px;height:30;vertical-align:middle" /> Durga Rao </td>
        <td style="text-align:center;  width:160px; margin-right:10px"><img src="https://graph.facebook.com/1432241263/picture" style="width:30px;height:30;vertical-align:middle" /> Joju Joseph Zajo </td>
	
</tr><tr></tr>
	</tbody>
	<tr></tr><tr></tr><tr></tr><tr>
        <td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px"> Final Winner 1</th>
        <td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px"> Final Winner 2</th>
        <td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px"> Final Winner 3</th>
	
        </tr>
        </tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr>
		<tr style="margin-bottom:10px">
	<td style="text-align:center;  width:155px; margin-right:10px"><img src="https://graph.facebook.com/1432241263/picture" style="width:30px;height:30;vertical-align:middle" /> Joju Joseph Zajo </td>
        <td style="text-align:center;  width:160px; margin-right:10px"><img src="https://graph.facebook.com/100000211800509/picture" style="width:30px;height:30;vertical-align:middle" /> Amrit Ratnabham </td>
        <td style="text-align:center;  width:160px; margin-right:10px"><img src="https://graph.facebook.com/765573046/picture" style="width:30px;height:30;vertical-align:middle" /> Haksar Farooq </td>
	<td style="text-align:center;  width:160px; margin-right:10px"><img src="https://graph.facebook.com/765573046/picture" style="width:30px;height:30;vertical-align:middle" /> Haksar Farooq </td>
		
	
	</tr><tr></tr><tr></tr><tr>
<td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px; border-top:1px solid #ddd;"> Week 1</th>
	<td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px; border-top:1px solid #ddd;"> Week 2</th>
        <td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px; border-top:1px solid #ddd;""> Week 3</th>
	<td style="text-transform:uppercase;  font-size:9px; color:#666; letter-spacing:3px; text-align:center; width:150px; margin-right:10px; border-top:1px solid #ddd;""> Week 4</th>
	</tr>
	<br/><br/>



	</table>
	-->
	<div id="rankings">
	
        </div>
	
	</div><!-- content_main -->
</div><!--content-->
</body>