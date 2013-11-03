<?php
require_once("includes/global.php");
	if (!(isset($_GET['key']) && $_GET['key'] == 'M1112AER') && !(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
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
	echo json_encode($Stats);
?>