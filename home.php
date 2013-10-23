<?php
require_once("includes/global.php");		

	if (session_id() == '') session_start();
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}

	$player_details = $mysqli->query("SELECT `name`, `liq_cash`, `day_worth`, `week_worth`, `market_val`, `rank` FROM `player` WHERE `id` = {$_SESSION['id']}");
	$player_details = $player_details->fetch_assoc();

	$overall_rank = $mysqli->query("SELECT count(*) FROM `player` WHERE `rank` > 0 AND `liq_cash` + `market_val` > ".($player_details['liq_cash'] + $player_details['market_val']));
	if ($overall_rank->num_rows > 0) { $overall_rank = $overall_rank->fetch_array(MYSQLI_NUM); $overall_rank = $overall_rank[0] + 1; }
	else $overall_rank = 1;
	
	$daily_rank = $mysqli->query("SELECT count(*) FROM `player` WHERE `rank` > 0 AND `liq_cash` + `market_val` - `day_worth` > ".($player_details['liq_cash'] + $player_details['market_val'] - $player_details['day_worth']));
	if ($daily_rank->num_rows > 0) { $daily_rank = $daily_rank->fetch_array(MYSQLI_NUM); $daily_rank = $daily_rank[0] + 1; }
	else $daily_rank = 1;

	$weekly_rank = $mysqli->query("SELECT count(*) FROM `player` WHERE `rank` > 0 AND `liq_cash` + `market_val` - `week_worth` > ".($player_details['liq_cash'] + $player_details['market_val'] - $player_details['week_worth']));
	if ($weekly_rank->num_rows > 0) { $weekly_rank = $weekly_rank->fetch_array(MYSQLI_NUM); $weekly_rank = $weekly_rank[0] + 1; }
	else $weekly_rank = 1;
	
	$liq_cash = (int)$player_details['liq_cash'];
	$market_val = (int)$player_details['market_val'];
	$day_worth = $liq_cash + $market_val - (int)$player_details['day_worth'];
	$week_worth = $liq_cash + $market_val - (int)$player_details['week_worth'];
	$rank = $player_details['rank'];
	metadetails();
?>
</head>
<body onload="document.getElementById('feedbacksubmit').hidden = true;">
	<div id="banner"></div>
	<?php Menu(); ?>
	<div id="content">
		<div id="Stats"><div id="Notice" onclick="window.location.href = 'portfolio.php'"><?php echo strtoupper($_SESSION['name']); ?></div>
			<ul>
				<li><span class='data_name'>Cash At Hand:</span><span class='data'><?php echo ininr($liq_cash); ?></span></li>
				<li><span class='data_name'>Market Value:</span><span class='data'><?php echo ininr($market_val); ?></span></li>
				<li><span class='data_name'>Overall Rank:</span><span class='data'><?php if($rank==0) echo "Not Ranked"; else echo $overall_rank; ?></span></li>
				<li><span class='data_name'>Weekly Rank:</span><span class='data'><?php if($rank==0) echo "Not Ranked"; else echo $weekly_rank; ?></span></li>
			</ul>
			<br/>
			<ul>
				<li><div id="networth" class="button btn-red"><span style="width: 100%; display: block; text-align:center;">Net Worth</span><span style="width: 100%; text-align:center; display: block;"><?php echo ininr($liq_cash+$market_val); ?></span></div></li>
				<br/>
				<li><span class='data_name'>Today's Gain:</span><span class='data'><?php echo addarrow($day_worth);?></span></li>
				<li><span class='data_name'>Week's Gain:</span><span class='data'><?php echo addarrow($week_worth);?></span></li>
			</ul>
		</div>
		<div id="niftychart"></div>
		<button id="Refresh" class="button btn-green" onclick="AjaxPost('updatehome.php', 'Stats', '');">Refresh</button>
		<form id="feedbackform" onsubmit="return false;">
  			<fieldset>
    			<input type="text" id="feedbackmsg" onchange="if (this.value.length > 5) document.getElementById('feedbacksubmit').hidden = false; else document.getElementById('feedbacksubmit').hidden = true;" class="form-text"  style="left: 5%; height: 1.5em;" placeholder="Would you like to give us a lil feedback ? Latest News & Hints available on our Facebook Page!">
				<button id="feedbacksubmit" onclick="AjaxPost('sendmsg.php', 'feedbackmsg', 'msg=' + document.getElementById('feedbackmsg').value);" style="height: 1.5em;" class="button btn-green" >Submit</button>
  			</fieldset>
		</form>
	</div>
	<?php require_once("includes/ticker.php"); AjaxPost(); Load_Anim(); ?>
</body>
</html>