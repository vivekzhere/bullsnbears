<?php
require_once("includes/global.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0) || ($debug_status == 1 && $trade_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}
	if (!(isset($_GET['type']) && isset($_GET['symbol']) && isset($_GET['amount']) && isset($_GET['scheduledPrice']))) header("Location: schedule.php") or die();
	$type = $_GET['type'];
	$symbol = $_GET['symbol'];
	$amount = $_GET['amount'];
	$scheduledPrice = $_GET['scheduledPrice'];
	$max_amount = 0;
	if (!in_array($type, array("Buy", "Sell", "Short", "Cover"))) header("Location: schedule.php") or die();

	$player = $mysqli->query("SELECT `liq_cash`, `short_val`, `market_val` FROM `player` WHERE `id` = {$_SESSION['id']}");
	$player = $player->fetch_assoc();
	$result_set = $mysqli->query("SELECT `s`.`value`, IFNULL((`b`.`amount`), 0) AS `bought_amount`, IFNULL((`ss`.`amount`), 0) AS `shorted_amount` FROM `stocks` AS `s` LEFT JOIN `bought_stock` AS `b` ON `s`.`symbol` = `b`.`symbol` AND `b`.`id` = '{$_SESSION['id']}' LEFT JOIN `short_sell` AS `ss` ON `s`.`symbol` = `ss`.`symbol` AND `ss`.`id` = '{$_SESSION['id']}' WHERE `s`.`symbol` = '{$symbol}' ORDER BY `name` ASC");
	$err_flag = true;
	$flag = "";
	if ($result_set->num_rows == 1)  {
		$result = $result_set->fetch_assoc();
		$err_flag = false;
		$max_amount = floor( ($player['liq_cash'] + $player['market_val']) / (6*1.002*$result['value']) );
		switch ($type) {
			case "Buy":
				$type = "B";		
				break;
			case "Sell":
				$type = "S";
				break;
			case "Short":
				$type = "SS";
				break;
			case "Cover":
				$type = "C";
				break;
		}

	}
	if ($scheduledPrice <= $result['value']) $flag = "l";
	else $flag = "g";
	if ($err_flag || !is_numeric($amount) || !is_numeric($scheduledPrice)) header("Location: schedule.php") or die();
	if ($amount > $max_amount) echo "The Amount you specified is too much. Try a lower value.";
	else if ($amount < 1) echo "Positive Amount Needed.";
	else {
		$mysqli->autocommit(FALSE);
		$p = $mysqli->query("INSERT INTO `schedule` ( `id`, `symbol`, `transaction_type`, `scheduled_price`, `no_shares`, `pend_no_shares`, `flag`) VALUES ( '{$_SESSION['id']}', '{$symbol}', '{$type}', '{$scheduledPrice}', '{$amount}', '{$amount}', '{$flag}' )");
		if ($p) { $mysqli->commit(); echo "Success!"; }
		else { $mysqli->rollback(); echo "Failure!"; } 
		$mysqli->autocommit(TRUE);
	}
?>