<?php require_once("includes/global.php");
	if (!isset($_SESSION['username'])) header("Location: index.php");
	metadetails();
?>
  
</head>
<body>
	<div id="content">
		<?php navigation("home"); ?>
		<br/><button id="homerefresh" onclick="updateHome()">Refresh</button>

		<div id="home">
			<div id='details' style='float:left;'>
				<?php
					$name = $_SESSION['name'];
					$username = $_SESSION['username'];
					$playerid = $_SESSION['playerid'];
					$sql = 'select `day_worth`, `week_worth`, `liq_cash`, `market_val`, `rank` from `player` where id = "'.$playerid.'"';
					$result = mysql_fetch_array(mysql_query($sql));
					$liq_cash = $result['liq_cash'];
					$day_worth = $result['day_worth'];
					$week_worth = $result['week_worth'];
					$market_val = (int)$result['market_val'];
					$overall_rank = 'select count(`id`) from `player` where `market_val` + `liq_cash` > '.($liq_cash + $market_val);
					$overall_rank = (mysql_result(mysql_query($overall_rank), 0)) + 1;
					$weekly_rank = 'select count(`id`) from `player` where `market_val` + `liq_cash` - `week_worth` > '.($liq_cash + $market_val - $week_worth);
					$weekly_rank = (mysql_result(mysql_query($weekly_rank), 0)) + 1;
					$day_worth =  ($liq_cash + $market_val) - $day_worth;
					$week_worth = ($liq_cash + $market_val)- $week_worth;
					$rank = $result['rank'];
				?>	
				<h2><?php	echo strtoupper($_SESSION['name']);	?></h2>
				<ul>
					<li><span class='data_name'>Cash At Hand:</span><span class='data'><?php echo convertcash($liq_cash,"Rs. "); ?></span></li>
					<li><span class='data_name'>Market value:</span><span class='data'><?php echo convertcash($market_val,"Rs. "); ?></span></li>
					<li><span class='data_name'>Overall Rank:</span><span class='data'><?php if($rank==0) echo "Not Ranked"; else echo $overall_rank; ?></span></li>
					<li><span class='data_name'>Weekly Rank:</span><span class='data'><?php if($rank==0) echo "Not Ranked"; else echo $weekly_rank; ?></span></li>
				</ul>
				<ul id='networth'>
					<li><span class='data_name'>Net Worth</span><span class='data'><?php echo convertcash($liq_cash+$market_val,"Rs. "); ?></span></li>
				</ul>
				<ul>
					<li><span class='data_name'>Today's Gain:</span><span class='data'><?php echo addarrow($day_worth);?></span></li>
					<li><span class='data_name'>Week's Gain:</span><span class='data'><?php echo addarrow($week_worth);?></span></li>
				</ul>
			</div>
			<div id='nifty'><img style='width: 400px; height: 240px; position: relative; left: -50px;' src='http://chart.finance.yahoo.com/t?s=^NSEI&lang=en-IN&region=IN&width=600&height=360'></div>
		</div>
		
		<div id="feedback">
			<div id="adminmsg" ><b>Bulls n' Bears</b>:
				<?php
					$sql = 'SELECT `message` from `feedback` where `flag` = "R" order by `time_stamp` desc limit 1';
					$result = mysql_result(mysql_query($sql), 0);
					echo $result;
				?>
			</div><br/>
			<form action="sendmsg.php" method="POST" id="showform">
				<textarea style="resize: none;" name="feedback" cols=80 rows=2 maxlength=300></textarea>
				<input type="submit" name="msgsend" value="Send Feedback" style="float:right; width;30px; height:30px;"/>
			</form>
		</div>
		
	</div>
	<script type="text/javascript">
		function updateHome() { 
			$.ajax({ url: 'updatehome.php',
				dataType: 'HTML', 
				success: function(data, status, xhr){
					$('#home').html($(data).html());
					$("#home").trigger("update");
				}
			});
		}
	</script>
</body>
</html>