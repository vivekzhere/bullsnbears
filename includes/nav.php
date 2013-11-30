<?
require_once("global.php");

	$Player = $mysqli->query("SELECT `name`, `liq_cash`, `day_worth`, `week_worth`, `market_val`, `rank` FROM `player` WHERE `id` = {$_SESSION['id']}");
	$Player = $Player->fetch_assoc();
	$Stats['liq_cash'] = ininr((int)$Player['liq_cash']);
	$Stats['market_val'] = ininr((int)$Player['market_val']);

	if ($Player['rank'] == 1) {
		$overall_rank = $mysqli->query("SELECT count(*) FROM `player` WHERE `rank` > 0 AND `liq_cash` + `market_val` > ".($Player['liq_cash'] + $Player['market_val']));
		$overall_rank = $overall_rank->fetch_array(MYSQLI_NUM); $Stats['overall_rank'] = $overall_rank[0] + 1;

		$daily_rank = $mysqli->query("SELECT count(*) FROM `player` WHERE `rank` > 0 AND `liq_cash` + `market_val` - `day_worth` > ".($Player['liq_cash'] + $Player['market_val'] - $Player['day_worth']));
		$daily_rank = $daily_rank->fetch_array(MYSQLI_NUM); $Stats['daily_rank'] = $daily_rank[0] + 1;

		$weekly_rank = $mysqli->query("SELECT count(*) FROM `player` WHERE `rank` > 0 AND `liq_cash` + `market_val` - `week_worth` > ".($Player['liq_cash'] + $Player['market_val'] - $Player['week_worth']));
		$weekly_rank = $weekly_rank->fetch_array(MYSQLI_NUM); $Stats['weekly_rank'] = $weekly_rank[0] + 1;
	} else $Stats['daily_rank'] = $Stats['weekly_rank'] = $Stats['overall_rank'] = "Not Ranked";

	$Stats['day_gain'] = addarrow($Player['liq_cash'] + $Player['market_val'] - (int)$Player['day_worth']);
	$Stats['week_gain'] = addarrow($Player['liq_cash'] + $Player['market_val'] - (int)$Player['week_worth']);
	$Stats['net_worth'] = ininr((int)$Player['liq_cash'] + (int)$Player['market_val']);
?>
	<script>
		<? require_once("js/global.js"); ?>
		tp = 0; side_nav = 0;
		function ToggleN(x) {
			if (x == 1) {
				if (!side_nav) side_nav = $('#nav_side');
					side_nav.style.left = '-0px';
			} else if (x == 2) {
				if (!side_nav) side_nav = $('#nav_side');
					side_nav.style.left = '-180px';
			} else {
				if (!tp) tp = $('#title_popdown');
				if (tp.style.height != '0px') {
					tp.style.height = '0px'; tp.style.width = '0px'; tp.style.borderWidth = '0px'; tp.style.lineHeight = '0em'; tp.style.overflow = 'hidden';	
				} else {
					tp.style.height = '170px'; tp.style.width = '250px'; tp.style.borderWidth = '1px'; tp.style.lineHeight = '1.5em'; tp.style.overflow = 'visible';
				}			
			}
		};
		<? require_once("js/hover.js"); ?>
	</script>


	<nav>
		<div id="nav_a">
			<div id="logo" class="centerh"></div>
			<a href="https://www.facebook.com/bullsnbearscommunity" id="title">Bulls &amp; Bears</a>
			<span id="name" onclick="ToggleN();"><?=$_SESSION['name']?></span>
			<div id="title_popdown" class="box">
				<span class="tp_name">Cash At Hand:</span><span class="tp_value"><?=$Stats['liq_cash']?></span><br/>
				<span class="tp_name">Market Value:</span><span class="tp_value"><?=$Stats['market_val']?></span><br/>
				<span class="tp_name">Overall Rank:</span><span class="tp_value"><?=$Stats['overall_rank']?></span><br/>
				<span class="tp_name">Daily Rank:</span><span class="tp_value"><?=$Stats['daily_rank']?></span><br/>
				<span class="tp_name">Weekly Rank:</span><span class="tp_value"><?=$Stats['weekly_rank']?></span><br/>
				<a href="logout.php" id="btn-logout" class="btn btn-blue">Logout</a>
			</div>
		</div>
		<div id="nav_b">
			<div id="nav_main-btn" class="btn" onclick="ToggleN(1);">Home</div>
			<div id="nav_side">
				<a id="nav_side_home" class="nav_side-btn" href="home.php"><div class="centerv">Home</div></a>
				<a id="nav_side_portfolio" class="nav_side-btn" href="portfolio.php"><div class="centerv">Portfolio</div></a>
				<a id="nav_side_trade" class="nav_side-btn" href="trade.php"><div class="centerv">Trade</div></a>
				<a id="nav_side_schedule" class="nav_side-btn" href="schedule.php"><div class="centerv">Schedule</div></a>
				<a id="nav_side_lookup" class="nav_side-btn" href="lookup.php"><div class="centerv">Lookup</div></a>
				<a id="nav_side_market" class="nav_side-btn" href="market.php"><div class="centerv">Market</div></a>
				<a id="nav_side_rankings" class="nav_side-btn" href="rankings.php"><div class="centerv">Rankings</div></a>
				<a id="nav_side_trade" class="nav_side-btn" href="history.php"><div class="centerv">History</div></a>
				<a id="nav_side_help" class="nav_side-btn" href="help.php"><div class="centerv">Help</div></a>
				<a id="nav_side_logout" class="nav_side-btn" href="logout.php"><div class="centerv">Logout</div></a>
			</div>
			<div id="nav_menu" class="centerh">
				<a id="nav_trade" href="trade.php" class="btn_menu">Trade</a>
				<a id="nav_schedule" href="schedule.php" class="btn_menu">Schedule</a>
				<a id="nav_lookup" href="lookup.php" class="btn_menu">Lookup</a>
				<a id="nav_market" href="market.php" class="btn_menu">Market</a>
				<a id="nav_rankings" href="rankings.php" class="btn_menu">Rankings</a>
			</div>
		</div>
	</nav>
	<script>
		t = location.pathname;
		t = t.substr(1, t.indexOf(".php") - 1);
		$('#nav_main-btn').innerHTML = t.charAt(0).toUpperCase() + t.substr(1);
		p = $('#nav_' + t);
		if (p) p.className += " btn_active";
		if (!side_nav) side_nav = $('#nav_side');
		if (!tp) tp = $('#title_popdown');
		msGesture = window.navigator && window.navigator.msPointerEnabled && window.MSGesture,
		touchSupport = (( "ontouchstart" in window ) || msGesture || window.DocumentTouch &&     document instanceof DocumentTouch);
		if (touchSupport) {
			q = $('body')[0];
			Hammer(q).on("dragright", function() { ToggleN(1); } );
			Hammer(q).on("dragleft", function() { ToggleN(2); } );
			Hammer($('#name')).on("dragdown", function() { ToggleN(); } );
			Hammer(tp).on("dragup", function() { ToggleN(); } );			
		}

	</script>