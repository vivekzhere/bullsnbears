<?php
require_once("includes/global.php");		
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0) || ($debug_status == 1 && $trade_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}

	$player = $mysqli->query("SELECT `liq_cash`, `market_val`, `short_val` FROM `player` WHERE `id` = '{$_SESSION['id']}'");
	$player = $player->fetch_assoc();
	$result_set = $mysqli->query("SELECT `s`.`symbol`, `s`.`value`, `s`.`name`, IFNULL((`b`.`amount`), 0) AS `bought_amount`, IFNULL((`ss`.`amount`), 0) AS `shorted_amount` FROM `stocks` AS `s` LEFT JOIN `bought_stock` AS `b` ON `s`.`symbol` = `b`.`symbol` AND `b`.`id` = '{$_SESSION['id']}' LEFT JOIN `short_sell` AS `ss` ON `s`.`symbol` = `ss`.`symbol` AND `ss`.`id` = '{$_SESSION['id']}' ORDER BY `name` ASC");
	$symbols = array();
	while ($result = $result_set->fetch_assoc()) {
		$result['max_buy'] = max(min(floor( ($player['liq_cash']- ($player['short_val'] / 4) ) / (1.002 * $result['value'] ) ), floor( ($player['liq_cash'] + $player['market_val']) / (6*1.002*$result['value']) ) - $result['bought_amount']), 0);
		$result['max_short'] = max(min(floor( ((4 * $player['liq_cash'] ) - $player['short_val'] ) / ( $result['value']*1.004 ) ), floor( ($player['liq_cash'] + $player['market_val'] - $player['short_val'] ) / (6*$result['value']*1.004) ) - $result['shorted_amount']), 0);
		$symbols[] = $result;
	}
	echo json_encode($symbols);
?>