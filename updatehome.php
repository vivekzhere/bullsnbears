<?php require_once("includes/global.php"); ?>
<?php
	if(!isset($_SESSION['username']))
		header("Location: index.php");

?>
<div>
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
				$sql_rank="select id, name, market_val+liq_cash as tot, rank from player order by tot desc";
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
				mysql_close($connection);
			
			?></h2>
			
			<ul>
				
				<li><span class='data_name'>Cash At Hand:</span><span class='data'><?php echo convertcash($liq_cash,"Rs. "); ?></span></li>
				<li><span class='data_name'>Market value:</span><span class='data'><?php echo convertcash($market_val,"Rs. "); ?></span></li>
				<li><span class='data_name'>Overall Rank:</span><span class='data'><?php if($rank==0) echo "Not Ranked"; else echo $overall_rank; ?></span></li>
				<li><span class='data_name'>Weekly Rank:</span><span class='data'><?php if($rank==0) echo "Not Ranked"; else echo $weekly_rank; ?></span></li>
			
			<ul id='networth'>
			<li><span class='data_name'>Net Worth</span><span class='data'><?php echo convertcash($liq_cash+$market_val,"Rs. "); ?></li>
			</ul>
			<li><span class='data_name'>Today's Gain:</span><span class='data'><?php echo addarrow($todays);?></span></li>
			<li><span class='data_name'>Week's Gain:</span><span class='data'><?php echo addarrow($weeks);?></span></li>
			</ul>
			<!--<input type='button' onclick='confirm_show()' value='Reset Account'>-->
		</div><!-- details -->
		<div id='nifty'>
			<img style='width: 400px; height: 240px; position: relative; left: -50px;' src='http://chart.finance.yahoo.com/t?s=^NSEI&lang=en-IN&region=IN&width=600&height=360'>
		</div>
	</div>
