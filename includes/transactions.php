<?php	
require_once("config.php");

	function Buy($id, $symbol, $amount, $stock_data, $skey) {
		global $mysqli;
		$err_flag = TRUE;
		$mysqli->autocommit(FALSE);
		$p = $mysqli->query("INSERT INTO `bought_stock` VALUES( '{$id}', '{$symbol}', '{$amount}', '{$stock_data['value']}' ) ON DUPLICATE KEY UPDATE `avg` = ((`avg` * `amount`) + ".($amount * $stock_data['value'])." ) / (`amount` + ".$amount." ), `amount` = `amount` + ".$amount);
		if ($p) {
			$p = $mysqli->query("UPDATE `player` SET `rank` = 1, `liq_cash` = `liq_cash` - ".round($amount * $stock_data['value'] * 1.002).", `market_val` = `market_val` + ".round($amount * $stock_data['value'])." WHERE `id` = '{$id}'");
			if ($p) {
				$p = $mysqli->query("INSERT INTO `history` ( `p_id`, `t_type`, `symbol`, `amount`, `value`, `skey` ) VALUES ( '{$id}', 'B', '{$symbol}', '{$amount}', '{$stock_data['value']}', '{$skey}' )"); 
				if ($p) {
					if ($skey != -1) $p = $mysqli->query("UPDATE `schedule` SET `pend_no_shares` = `pend_no_shares` - {$amount} WHERE `skey` = '{$skey}'"); 
					if (!$p) $mysqli->rollback();
					else {
						$mysqli->commit();
						$err_flag = FALSE;
					}
				} else $mysqli->rollback();
			} else $mysqli->rollback(); 
		} else $mysqli->rollback();
		$mysqli->autocommit(TRUE);
		return !$err_flag;
	}


	function Sell($id, $symbol, $amount, $stock_data, $skey) {
		global $mysqli;
		$err_flag = TRUE;
		$mysqli->autocommit(FALSE);
		if ($amount != $stock_data['bought_amount']) $p = $mysqli->query("UPDATE `bought_stock` SET `avg` = ((`avg` * `amount`) - ".($amount * $stock_data['value'])." ) / (`amount` - ".$amount." ), `amount` = `amount` - ".$amount." WHERE `id` = '{$id}' AND `symbol` = '{$symbol}'");
		else $p = $mysqli->query("DELETE FROM `bought_stock` WHERE `id` = '{$id}' AND symbol = '{$symbol}'"); 
		if ($p) {
			$p = $mysqli->query("UPDATE `player` SET `liq_cash` = `liq_cash` + ".round($amount * $stock_data['value'] * 0.998).", `market_val` = Case When (`market_val` - ".ceil($amount*$stock_data['value']).") < 0 THEN 0 ELSE (`market_val` - ".ceil($amount*$stock_data['value']).") END WHERE `id` = '{$id}'");
			if ($p) {
				$p = $mysqli->query("INSERT INTO `history` ( `p_id`, `t_type`, `symbol`, `amount`, `value`, `skey` ) VALUES ( '{$id}', 'S', '{$symbol}', '{$amount}', '{$stock_data['value']}', '{$skey}' )"); 
				if ($p) {
					if ($skey != -1) $p = $mysqli->query("UPDATE `schedule` SET `pend_no_shares` = `pend_no_shares` - {$amount} WHERE `skey` = '{$skey}'"); 
					if (!$p) $mysqli->rollback();
					else {
						$mysqli->commit();
						$err_flag = FALSE;
					}
				} else $mysqli->rollback();
			} else $mysqli->rollback(); 
		} else $mysqli->rollback();
		$mysqli->autocommit(TRUE);
		return !$err_flag;
	}

	function Short($id, $symbol, $amount, $stock_data, $skey) {
		global $mysqli;
		$err_flag = TRUE;
		$mysqli->autocommit(FALSE);
		$p = $mysqli->query("INSERT INTO `short_sell` VALUES( '{$id}', '{$symbol}', '{$amount}', '{$stock_data['value']}' ) ON DUPLICATE KEY UPDATE `val` = ((`val` * `amount`) + ".($amount * $stock_data['value'])." ) / (`amount` + ".$amount." ), `amount` = `amount` + ".$amount);
		if ($p) {
			$p = $mysqli->query("UPDATE `player` SET `rank` = 1, `liq_cash` = `liq_cash` - ".round($amount * $stock_data['value'] * 0.002).", `short_val` = `short_val` + ".round($amount * $stock_data['value'] )." WHERE `id` = '{$id}'");
			if ($p) {
				$p = $mysqli->query("INSERT INTO `history` ( `p_id`, `t_type`, `symbol`, `amount`, `value`, `skey` ) VALUES ( '{$id}', 'SS', '{$symbol}', '{$amount}', '{$stock_data['value']}', '{$skey}' )"); 
				if ($p) {
					if ($skey != -1) $p = $mysqli->query("UPDATE `schedule` SET `pend_no_shares` = `pend_no_shares` - {$amount} WHERE `skey` = '{$skey}'"); 
					if (!$p) $mysqli->rollback();
					else {
						$mysqli->commit();
						$err_flag = FALSE;
					}
				} else $mysqli->rollback();
			} else $mysqli->rollback(); 
		} else $mysqli->rollback();
		$mysqli->autocommit(TRUE);
		return !$err_flag;
	}

	function Cover($id, $symbol, $amount, $stock_data, $skey) {
		global $mysqli;
		$err_flag = TRUE;
		$mysqli->autocommit(FALSE);
		$x = $mysqli->query("SELECT val FROM `short_sell` WHERE `id` = '{$id}' AND `symbol` = '{$symbol}'");
		if ($x) {
			$x = $x->fetch_assoc();
			if ($amount != $stock_data['shorted_amount']) $p = $mysqli->query("UPDATE `short_sell` SET `val` = ((`val` * `amount`) - ".($amount * $stock_data['value'])." ) / (`amount` - ".$amount." ), `amount` = `amount` - ".$amount." WHERE `id` = '{$id}' AND `symbol` = '{$symbol}'");
			else $p = $mysqli->query("DELETE FROM `short_sell` WHERE `id` = '{$id}' AND symbol = '{$symbol}'"); 
			if ($p) {
				$p = $mysqli->query("UPDATE `player` SET `liq_cash` = `liq_cash` + ".round(($x['val'] - $stock_data['value'] * 0.998) * $amount).", `short_val` = Case When (`short_val` - ".ceil($amount * $stock_data['value']).") < 0 THEN 0 ELSE (`short_val` - ".ceil($amount * $stock_data['value']).") END WHERE `id` = '{$id}'");
				if ($p) {
					$p = $mysqli->query("INSERT INTO `history` ( `p_id`, `t_type`, `symbol`, `amount`, `value`, `skey` ) VALUES ( '{$id}', 'C', '{$symbol}', '{$amount}', '{$stock_data['value']}', '{$skey}' )"); 
					if ($p) {
						if ($skey != -1) $p = $mysqli->query("UPDATE `schedule` SET `pend_no_shares` = `pend_no_shares` - {$amount} WHERE `skey` = '{$skey}'"); 
						if (!$p) $mysqli->rollback();
						else {
							$mysqli->commit();
							$err_flag = FALSE;
						}
					} else $mysqli->rollback();
				} else $mysqli->rollback(); 
			} else $mysqli->rollback();
		}
		$mysqli->autocommit(TRUE);
		return !$err_flag;
	}
?>
