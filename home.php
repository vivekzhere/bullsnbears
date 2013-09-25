<?php header("Location: rankings.php");
require_once("includes/global.php"); ?>
<?php

	if(!isset($_SESSION['username']))
		header("Location: index.php");
metadetails();
?>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
  
    <script type="text/javascript">
	  //  setInterval( "updateHome();", 30000 ); 
   
      function updateHome()
	    { 
	    if(!$.browser.msie)
   	          { 
		    $.ajax({
		        url: 'updatehome.php'
		       ,dataType: 'HTML'
		       ,success: function(data, status, xhr){
		           $('#home').html($(data).html());
                           $("#home").trigger("update");
		       }
		    });
                 }
	    }
   
  
	   
    </script>
</head>
<body onload="updateHome();">
<div id="content">
	<?php navigation("home"); ?>

<br/><button id="homerefresh" onclick="updateHome()">Refresh</button>

<div id="home">
		<div id='details' style='float:left;'>

			<h2><?php echo strtoupper($_SESSION['name']); 

				$username = $_SESSION['username'];
				$playerid = $_SESSION['playerid'];
				$connection = mysql_connect($server,$sqlid,$sqlpass) or die(mysql_error());
				$db_select = mysql_select_db($bnbdbase, $connection) or die(mysql_error());
				$sql = "select * from player where id = '$playerid'";
				$result_set = mysql_query($sql);
				$result = mysql_fetch_array($result_set);
				$name = $_SESSION['name'];
				$liq_cash =$result['liq_cash'];
				$sql_rank="select id, market_val+liq_cash as tot, rank from player order by tot desc";
				$sql_rank_result=mysql_query($sql_rank);
				$overall_rank=1;
				while($player=mysql_fetch_array($sql_rank_result)){
					if($playerid==$player['id'])
						break;
					else
					{
						if($player['rank']==0)
							continue;
							$overall_rank=$overall_rank+1;
						
					}
					}
				$sql_rank="select id, name, market_val+liq_cash-week_worth as tot, rank from player order by tot desc";
				$sql_rank_result=mysql_query($sql_rank);
				$weekly_rank=1;
				while($player=mysql_fetch_array($sql_rank_result)){
					if($playerid==$player['id'])
						break;
					else
					{
						if($player['rank']==0)
							continue;
							$weekly_rank=$weekly_rank+1;
						
					}
					}
				$market_val = (($result['market_val']==0)?0:$result['market_val']);
				$todays =  ($liq_cash + $market_val)-$result['day_worth'];
				$weeks = ($liq_cash + $market_val)- $result['week_worth'] ;
				$rank = $result['rank'];;
				
			?></h2>
			
			
			<ul>
				
				<li><span class='data_name'>Cash At Hand:</span><span class='data'><?php echo convertcash($liq_cash,"Rs. "); ?></span></li>
				<li><span class='data_name'>Market value:</span><span class='data'><?php echo convertcash($market_val,"Rs. "); ?></span></li>
				<li><span class='data_name'>Overall Rank:</span><span class='data'><?php if($rank==0) echo "Not Ranked"; else echo $overall_rank; ?></span>
				<span class='data_name'>Weekly Rank:</span><span class='data'><?php if($rank==0) echo "Not Ranked"; else echo $weekly_rank; ?></span></li>
			
			<ul id='networth'>
			<li><span class='data_name'>Net Worth</span><span class='data'><?php echo convertcash($liq_cash+$market_val,"Rs. "); ?></li>
			</ul>
			<li><span class='data_name'>Today's Gain:</span><span class='data'><?php echo addarrow($todays);?></span></li>
			<li><span class='data_name'>Week's Gain:</span><span class='data'><?php echo addarrow($weeks);?></span></li>
			</ul>
			<!--<input type='button' onclick='confirm_show()' value='Reset Account'>-->
		</div><!-- details -->
		<div id='nifty'>
			<img src='http://chart.finance.yahoo.com/t?s=^NSEI&lang=en-IN&region=IN&width=300&height=180'>
		
		</div>
		
	</div><!-- home -->
	<div id="feedback">
	<div id="adminmsg" ><b>Bulls n' Bears</b>:
	<?php $sql = "SELECT message from feedback where flag='R' and (id='$playerid' or id='TATALL') order by time_stamp desc limit 1 ";
		$result_set = mysql_query($sql);
		$result = mysql_fetch_array($result_set);
	      echo $result['message'];
	      mysql_close($connection);
	?>
	</div><br/>
	<form action="sendmsg.php" method="POST" id="showform">
	<textarea style="resize: none;" name="feedback" cols=80 rows=2 maxlength=300></textarea>
	<input type="submit" name="msgsend" value="Send Feedback" style="float:right; width;30px; height:30px;"/>
	
	
	</form>
	</div>
	
	</div><!-- content_main -->
</div><!--content-->
</body>
</html>