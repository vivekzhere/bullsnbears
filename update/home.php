<?php
require_once("../includes/global.php");
	if (!(isset($_GET['key']) && $_GET['key'] == 'M1112AER') && !(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}
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
	echo json_encode($Stats);
?>