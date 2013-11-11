<?php
require_once("includes/global.php");
require_once("includes/transactions.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0) || ($debug_status == 1 && $trade_status == 0)) { header("Location: testing.html"); die(); }
		elseif (!isset($_SESSION['id'])) { header("Location: index.php"); die(); }
	}
	if (!(isset($_GET['type']) && isset($_GET['symbol']) && isset($_GET['amount']))) { header("Location: trade.php"); die(); }
	$type = $_GET['type'];
	$symbol = $_GET['symbol'];
	$amount = $_GET['amount'];
	$max_amount = 0;
	if (!in_array($type, array("Buy", "Sell", "Short", "Cover"))) { header("Location: trade.php"); die(); }

	$player = $mysqli->query("SELECT `liq_cash`, `short_val`, `market_val` FROM `player` WHERE `id` = {$_SESSION['id']}");
	$player = $player->fetch_assoc();
	$result_set = $mysqli->query("SELECT `s`.`value`, IFNULL((`b`.`amount`), 0) AS `bought_amount`, IFNULL((`ss`.`amount`), 0) AS `shorted_amount` FROM `stocks` AS `s` LEFT JOIN `bought_stock` AS `b` ON `s`.`symbol` = `b`.`symbol` AND `b`.`id` = '{$_SESSION['id']}' LEFT JOIN `short_sell` AS `ss` ON `s`.`symbol` = `ss`.`symbol` AND `ss`.`id` = '{$_SESSION['id']}' WHERE `s`.`symbol` = '{$symbol}' ORDER BY `name` ASC");
	$err_flag = true;
	if ($result_set->num_rows == 1)  {
		$result = $result_set->fetch_assoc();
		$err_flag = false;
		switch ($type) {
			case "Buy":
				$max_amount = max(min(floor( ($player['liq_cash']- ($player['short_val'] / 4) ) / (1.002 * $result['value'] ) ), floor( ($player['liq_cash'] + $player['market_val']) / (6*1.002*$result['value']) ) - $result['bought_amount']), 0);
				break;
			case "Sell":
				$max_amount = $result['bought_amount'];
				break;
			case "Short":
				$max_amount = max(min(floor( ((4 * $player['liq_cash'] ) - $player['short_val'] ) / ( $result['value']*1.004 ) ), floor( ($player['liq_cash'] + $player['market_val'] - $player['short_val'] ) / (6*$result['value']*1.004) ) - $result['shorted_amount']), 0);
				break;
			case "Cover":
				$max_amount = $result['shorted_amount'];
				break;
		}
	}
	if ($err_flag || !is_numeric($amount)) { header("Location: trade.php"); die(); }
	if ($amount > $max_amount) echo "The Amount you specified is too much. Try a lower value.";
	else if ($amount < 1) echo "Positive Amount Needed.";
	else if ($type == "Buy") if (Buy($_SESSION['id'], $symbol, $amount, $result, -1)) echo "Failure"; else echo "Success";
	else  if ($type == "Sell") if (Sell($_SESSION['id'], $symbol, $amount, $result, -1)) echo "Failure"; else echo "Success";
	else if ($type == "Short") if (Short($_SESSION['id'], $symbol, $amount, $result, -1)) echo "Failure"; else echo "Success";
	else  if ($type == "Cover") if (Cover($_SESSION['id'], $symbol, $amount, $result, -1)) echo "Failure"; else echo "Success";
?>