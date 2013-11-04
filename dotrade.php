<?php
require_once("includes/global.php");
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
	else if ($type == "Buy") {
		$err_flag = TRUE;
		$mysqli->autocommit(FALSE);
		$p = $mysqli->query("INSERT INTO `bought_stock` VALUES( '{$_SESSION['id']}', '{$symbol}', '{$amount}', '{$result['value']}' ) ON DUPLICATE KEY UPDATE `avg` = ((`avg` * `amount`) + ".($amount * $result['value'])." ) / (`amount` + ".$amount." ), `amount` = `amount` + ".$amount);
		if ($p) {
			$p = $mysqli->query("UPDATE `player` SET `rank` = 1, `liq_cash` = `liq_cash` - ".round($amount * $result['value'] * 1.002).", `market_val` = `market_val` + ".round($amount * $result['value'])." WHERE `id` = '{$_SESSION['id']}'");
			if ($p) {
				$p = $mysqli->query("INSERT INTO `history` ( `p_id`, `t_type`, `symbol`, `amount`, `value` ) VALUES ( '{$_SESSION['id']}', 'B', '{$symbol}', '{$amount}', '{$result['value']}' )"); 
				if (!$p) $mysqli->rollback();
				else {
					$mysqli->commit();
					$err_flag = FALSE;
				}
			} else $mysqli->rollback(); 
		} else $mysqli->rollback();
		if ($err_flag) echo "Failure!"; else echo "Success!";
		$mysqli->autocommit(TRUE);
	} else  if ($type == "Sell") {
		$err_flag = TRUE;
		$mysqli->autocommit(FALSE);
		if ($amount != $max_amount) $p = $mysqli->query("UPDATE `bought_stock` SET `avg` = ((`avg` * `amount`) - ".($amount * $result['value'])." ) / (`amount` - ".$amount." ), `amount` = `amount` - ".$amount." WHERE `id` = '{$_SESSION['id']}' AND `symbol` = '{$symbol}'");
		else $p = $mysqli->query("DELETE FROM `bought_stock` WHERE `id` = '{$_SESSION['id']}' AND symbol = '{$symbol}'"); 
		if ($p) {
			$p = $mysqli->query("UPDATE `player` SET `liq_cash` = `liq_cash` + ".round($amount * $result['value'] * 0.998).", `market_val` = Case When (`market_val` - ".ceil($amount*$result['value']).") < 0 THEN 0 ELSE (`market_val` - ".ceil($amount*$result['value']).") END WHERE `id` = '{$_SESSION['id']}'");
			if ($p) {
				$p = $mysqli->query("INSERT INTO `history` ( `p_id`, `t_type`, `symbol`, `amount`, `value` ) VALUES ( '{$_SESSION['id']}', 'S', '{$symbol}', '{$amount}', '{$result['value']}' )"); 
				if (!$p) $mysqli->rollback();
				else {
					$mysqli->commit();
					$err_flag = FALSE;
				}
			} else $mysqli->rollback(); 
		} else $mysqli->rollback();
		if ($err_flag) echo "Failure!"; else echo "Success!";
		$mysqli->autocommit(TRUE);
	} else if ($type == "Short") {
		$err_flag = TRUE;
		$mysqli->autocommit(FALSE);
		$p = $mysqli->query("INSERT INTO `short_sell` VALUES( '{$_SESSION['id']}', '{$symbol}', '{$amount}', '{$result['value']}' ) ON DUPLICATE KEY UPDATE `val` = ((`val` * `amount`) + ".($amount * $result['value'])." ) / (`amount` + ".$amount." ), `amount` = `amount` + ".$amount);
		if ($p) {
			$p = $mysqli->query("UPDATE `player` SET `rank` = 1, `liq_cash` = `liq_cash` - ".round($amount * $result['value'] * 0.002).", `short_val` = `short_val` + ".round($amount * $result['value'] )." WHERE `id` = '{$_SESSION['id']}'");
			if ($p) {
				$p = $mysqli->query("INSERT INTO `history` ( `p_id`, `t_type`, `symbol`, `amount`, `value` ) VALUES ( '{$_SESSION['id']}', 'SS', '{$symbol}', '{$amount}', '{$result['value']}' )"); 
				if (!$p) $mysqli->rollback();
				else {
					$mysqli->commit();
					$err_flag = FALSE;
				}
			} else $mysqli->rollback(); 
		} else $mysqli->rollback();
		if ($err_flag) echo "Failure!"; else echo "Success!";
		$mysqli->autocommit(TRUE);
	} else  if ($type == "Cover") {
		$err_flag = TRUE;
		$mysqli->autocommit(FALSE);
		$x = $mysqli->query("SELECT (amount * val) AS `old` FROM `short_sell` WHERE `id` = '{$_SESSION['id']}' AND `symbol` = '{$symbol}'");
		if ($x) {
			$x = $x->fetch_assoc();
			if ($amount != $max_amount) $p = $mysqli->query("UPDATE `short_sell` SET `val` = ((`val` * `amount`) - ".($amount * $result['value'])." ) / (`amount` - ".$amount." ), `amount` = `amount` - ".$amount." WHERE `id` = '{$_SESSION['id']}' AND `symbol` = '{$symbol}'");
			else $p = $mysqli->query("DELETE FROM `short_sell` WHERE `id` = '{$_SESSION['id']}' AND symbol = '{$symbol}'"); 
			if ($p) {
				$p = $mysqli->query("UPDATE `player` SET `liq_cash` = `liq_cash` + ".round($x['old'] - $amount * $result['value'] * 0.998).", `short_val` = Case When (`short_val` - ".ceil($amount * $result['value']).") < 0 THEN 0 ELSE (`short_val` - ".ceil($amount * $result['value']).") END WHERE `id` = '{$_SESSION['id']}'");
				if ($p) {
					$p = $mysqli->query("INSERT INTO `history` ( `p_id`, `t_type`, `symbol`, `amount`, `value` ) VALUES ( '{$_SESSION['id']}', 'C', '{$symbol}', '{$amount}', '{$result['value']}' )"); 
					if (!$p) $mysqli->rollback();
					else {
						$mysqli->commit();
						$err_flag = FALSE;
					}
				} else $mysqli->rollback(); 
			} else $mysqli->rollback();
		}
		if ($err_flag) echo "Failure!"; else echo "Success!";
		$mysqli->autocommit(TRUE);
	}
?>