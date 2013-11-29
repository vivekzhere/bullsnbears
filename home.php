<?php
require_once("includes/global.php");
require_once("includes/sanitize.php");

	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}
	metadetails();
?>
</head>
<body>
	<?php require_once("includes/nav.php"); ?>
	<div id="content">
		<div id="Stats" class="box box1"><div id="Notice" onclick="window.location.href = 'portfolio.php'"><?php echo strtoupper($_SESSION['name']); ?></div>
			<ul>
				<li><span class='data_name'>Cash At Hand:</span><span class='data' id="cashAtHand"><?=$Stats['liq_cash']?></span></li>
				<li><span class='data_name'>Market Value:</span><span class='data' id="marketValue"><?=$Stats['market_val']?></span></li>
				<li><span class='data_name'>Overall Rank:</span><span class='data' id="overallRank"><?=$Stats['overall_rank']?></span></li>
				<li><span class='data_name'>Daily Rank:</span><span class='data' id="dailyRank"><?=$Stats['daily_rank']?></span></li>
				<li><span class='data_name'>Weekly Rank:</span><span class='data' id="weeklyRank"><?=$Stats['weekly_rank']?></span></li>
			</ul>
			<div id="netWorth" class="button btn-red"><div>Net Worth</div><div><?=$Stats['net_worth']?></div></div>
			<ul>
				<li><span class='data_name'>Today's Gain:</span><span class='data' id="daysGain"><?=$Stats['day_gain']?></span></li>
				<li><span class='data_name'>Week's Gain:</span><span class='data' id="weeksGain"><?=$Stats['week_gain']?></span></li>
			</ul>
			<button id="homeRefresh" class="btn btn-green" onclick="RefreshHome();">Refresh</button><br/>
		</div>
		<div id="NiftyChart" class="box"></div>
		<form id="FeedbackForm" onsubmit="return false;">
  			<fieldset>
    			<input type="text" id="FeedbackMsg" required pattern=".{5,100}" oninput="if (this.checkValidity()) $('#FeedbackSubmit').hidden = false; else $('#FeedbackSubmit').hidden = true;" class="form-text" placeholder="We would love some feedback!">
				<input type="submit" id="FeedbackSubmit" onclick="if (this.checkValidity()) AjaxPost('sendmsg.php', 'msg=' + $('#FeedbackMsg').value);" class="btn btn-green" value="Submit">
  			</fieldset>
		</form>
	</div>
	<script>
		$('#FeedbackSubmit').hidden = true;
		function RefreshHome() {
			AjaxPost('update/home.php');
			pr = $("#homeRefresh");
			if (pr) { pr.className = pr.className.replace(" btn-green",""); pr.disabled = true; }
		}
		function Ajax_Success(a, b, c) {
			pr = "";
			if (a == "update/home.php") {	
				Stats = JSON.parse(c);
				i = 0;
				p = $('.data');
				for (key in Stats) {
					if (i == 7) {
						$('#netWorth').children[1].innerHTML = Stats[key];
					} else p[i++].innerHTML = Stats[key];
				}
				pr = $("#homeRefresh");
				
			} else {
				$('#FeedbackMsg').value = c;
				pr = $("#FeedbackSubmit");
			}
			if (pr) {
				pr.className = pr.className.replace(" btn-green","");
				pr.disabled = true;
				setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);
			}
		}
		function Ajax_Failure(a, b, c) {
			pr = "";
			if (a == "update/home.php") {
				pr = $("#homeRefresh");
				alert("Something went wrong! Try again later");
			} else {
				pr = $("#FeedbackSubmit");
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