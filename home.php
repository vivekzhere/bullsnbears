<?php
require_once("includes/global.php");
require_once("includes/sanitize.php");


	if (session_id() == '') session_start();
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}

	$player_details = $mysqli->query("SELECT `name`, `liq_cash`, `day_worth`, `week_worth`, `market_val`, `rank` FROM `player` WHERE `id` = {$_SESSION['id']}");
	$player_details = $player_details->fetch_assoc();

	$overall_rank = $mysqli->query("SELECT count(*) FROM `player` WHERE `rank` > 0 AND `liq_cash` + `market_val` > ".($player_details['liq_cash'] + $player_details['market_val']));
	if ($overall_rank->num_rows > 0) { $overall_rank = $overall_rank->fetch_array(MYSQLI_NUM); $Stats['overall_rank'] = $overall_rank[0] + 1; }
	else $Stats['overall_rank'] = 1;

	$daily_rank = $mysqli->query("SELECT count(*) FROM `player` WHERE `rank` > 0 AND `liq_cash` + `market_val` - `day_worth` > ".($player_details['liq_cash'] + $player_details['market_val'] - $player_details['day_worth']));
	if ($daily_rank->num_rows > 0) { $daily_rank = $daily_rank->fetch_array(MYSQLI_NUM); $Stats['daily_rank'] = $daily_rank[0] + 1; }
	else $Stats['daily_rank'] = 1;

	$weekly_rank = $mysqli->query("SELECT count(*) FROM `player` WHERE `rank` > 0 AND `liq_cash` + `market_val` - `week_worth` > ".($player_details['liq_cash'] + $player_details['market_val'] - $player_details['week_worth']));
	if ($weekly_rank->num_rows > 0) { $weekly_rank = $weekly_rank->fetch_array(MYSQLI_NUM); $Stats['weekly_rank'] = $weekly_rank[0] + 1; }
	else $Stats['weekly_rank'] = 1;

	$Stats['liq_cash'] = ininr((int)$player_details['liq_cash']);
	$Stats['market_val'] = ininr((int)$player_details['market_val']);
	$Stats['net_worth'] = ininr((int)$player_details['liq_cash'] + (int)$player_details['market_val']);
	$Stats['day_gain'] = addarrow($player_details['liq_cash'] + $player_details['market_val'] - (int)$player_details['day_worth']);
	$Stats['week_gain'] = addarrow($player_details['liq_cash'] + $player_details['market_val'] - (int)$player_details['week_worth']);
	if ($player_details['rank'] == 0) {
		$Stats['daily_rank'] = "Not Ranked";
		$Stats['weekly_rank'] = "Not Ranked";
		$Stats['overall_rank'] = "Not Ranked";
	}
	metadetails();
?>
</head>
<body>
	<div id="banner"></div>
	<?php Menu(); ?>
	<div id="content">
		<div id="Stats"><div id="Notice" onclick="window.location.href = 'portfolio.php'"><?php echo strtoupper($_SESSION['name']); ?></div>
			<ul>
				<li><span class='data_name'>Cash At Hand:</span><span class='data' id="cashAtHand"><?=$Stats['liq_cash']?></span></li>
				<li><span class='data_name'>Market Value:</span><span class='data' id="marketValue"><?=$Stats['market_val']?></span></li>
				<li><span class='data_name'>Overall Rank:</span><span class='data' id="overallRank"><?=$Stats['overall_rank']?></span></li>
				<li><span class='data_name'>Daily Rank:</span><span class='data' id="dailyRank"><?=$Stats['daily_rank']?></span></li>
				<li><span class='data_name'>Weekly Rank:</span><span class='data' id="weeklyRank"><?=$Stats['weekly_rank']?></span></li>
			</ul>
			<br/>
			<div id="networth" class="button btn-red"><span style="width: 100%; display: block; text-align:center;">Net Worth</span><span style="width: 100%; text-align:center; display: block;" id="netWorth"><?=$Stats['net_worth']?></span></div>
			<br/>
			<ul>
				<li><span class='data_name'>Today's Gain:</span><span class='data' id="daysGain"><?=$Stats['day_gain']?></span></li>
				<li><span class='data_name'>Week's Gain:</span><span class='data' id="weeksGain"><?=$Stats['week_gain']?></span></li>
			</ul>
		</div>
		<div id="niftychart"></div>
		<button id="Refresh" class="button btn-green" onclick="RefreshHome();">Refresh</button><br/><br/>
		<form id="feedbackform" onsubmit="return false;">
  			<fieldset>
    			<input type="text" id="feedbackmsg" onchange="if (this.value.length > 5) document.getElementById('feedbacksubmit').hidden = false; else document.getElementById('feedbacksubmit').hidden = true;" class="form-text"  style="left: 5%; height: 1.5em;" placeholder="Any Feedback ? :) Latest News & Hints available on our Facebook Page!">
				<button id="feedbacksubmit" onclick="AjaxPost('sendmsg.php', 'msg=' + document.getElementById('feedbackmsg').value);" style="height: 1.5em;" class="button btn-green" >Submit</button>
  			</fieldset>
		</form>
	</div>
	<script>
		function RefreshHome() {
			AjaxPost('updatehome.php', '', '');
			pr = document.getElementById("Refresh");
			if (pr) { pr.className = pr.className.replace(" btn-green",""); pr.disabled = true; }
		}
		function Ajax_Success(a, b, c) {
			pr = "";
			if (a == "updatehome.php") {
				Stats = JSON.parse(c);
				document.getElementById('cashAtHand').innerHTML = Stats['liq_cash'];
				document.getElementById('marketValue').innerHTML = Stats['market_val'];
				document.getElementById('overallRank').innerHTML = Stats['overall_rank'];
				document.getElementById('dailyRank').innerHTML = Stats['daily_rank'];
				document.getElementById('weeklyRank').innerHTML = Stats['weekly_rank'];
				document.getElementById('daysGain').innerHTML = Stats['day_gain'];
				document.getElementById('weeksGain').innerHTML = Stats['week_gain'];
				document.getElementById('netWorth').innerHTML = Stats['net_worth'];
				pr = document.getElementById("Refresh");
			} else {
				document.getElementById('feedbackmsg').value = c;
				pr = document.getElementById("feedbacksubmit");
			}
			if (pr) {
				pr.className = pr.className.replace(" btn-green","");
				pr.disabled = true;
				setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);
			}
		}
		function Ajax_Failure(a, b, c) {
			pr = "";
			if (a == "updatehome.php") {
				pr = document.getElementById("Refresh");
				alert("Something went wrong! Try again later");
			} else {
				pr = document.getElementById("feedbacksubmit");
				alert("Something went wrong! Try again later");
			}	
			if (pr) {
				pr.className = pr.className.replace(" btn-green","");
				pr.disabled = true;
				setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);
			}
		}
	</script>
	<?php require_once("includes/ticker.php");	AjaxPost(); Load_Anim(); ?>
</body>
</html>