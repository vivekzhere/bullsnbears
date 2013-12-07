<?php
require_once("../includes/config.php");
require_once("../includes/connection.php");

	session_start();
	if (!in_array($_SESSION['id'], $admins) || $_GET['key'] != $mainkey) header("Location: ../index.php") or die();
	$err_flag = FALSE;
	$mysqli->autocommit(FALSE);
	$mysqli->query("DELETE FROM `bought_stock` WHERE 1") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("DELETE FROM `short_sell` WHERE 1") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("UPDATE `player` SET liq_cash = '{$start_money}', market_val = 0 WHERE rank = 1") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("UPDATE `player` SET liq_cash = liq_cash - (SELECT SUM(amount * value * 1.002) FROM history WHERE history.p_id = player.id and t_type = 'B' GROUP BY history.p_id)") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("UPDATE `player` SET liq_cash = liq_cash + (SELECT SUM(amount * value * 0.998) FROM history WHERE history.p_id = player.id and t_type = 'S' GROUP BY history.p_id)") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("UPDATE `player` SET liq_cash = liq_cash - (SELECT SUM(amount * value * 0.002) FROM history WHERE history.p_id = player.id and (t_type = 'C' OR t_type = 'SS') GROUP BY history.p_id)") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("INSERT INTO bought_stock (SELECT p_id, symbol, SUM(amount) as amt, (SUM(amount * value) / SUM(amount)) as value FROM history WHERE t_type = 'B' GROUP BY p_id, symbol)") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("UPDATE bought_stock dest, (SELECT p_id, symbol, SUM(amount) as amt, (SUM(amount * value) / SUM(amount)) as value FROM history WHERE t_type = 'S' GROUP BY p_id, symbol) src SET dest.amount = dest.amount - src.amt, dest.avg = (dest.amount * dest.avg - src.amt * src.value) / (dest.amount - src.amt) WHERE dest.id = src.p_id AND dest.symbol = src.symbol") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("INSERT INTO short_sell (SELECT p_id, symbol, SUM(amount) as amt, (SUM(amount * value) / SUM(amount)) as value FROM history WHERE t_type = 'SS' GROUP BY p_id, symbol)") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("UPDATE player dest, (SELECT p_id, symbol, SUM(amount) as amt, (SUM(amount * value) / SUM(amount)) as value FROM history WHERE t_type = 'C' GROUP BY p_id, symbol) src, short_sell srcb SET dest.liq_cash = dest.liq_cash + (srcb.val - src.value) * src.amt WHERE dest.id = src.p_id AND src.symbol = srcb.symbol AND dest.id = srcb.id") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("UPDATE short_sell dest, (SELECT p_id, symbol, SUM(amount) as amt, (SUM(amount * value) / SUM(amount)) as value FROM history WHERE t_type = 'C' GROUP BY p_id, symbol) src SET dest.amount = dest.amount - src.amt, dest.val = (dest.amount * dest.val - src.amt * src.value) / (dest.amount - src.amt) WHERE dest.id = src.p_id AND dest.symbol = src.symbol") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("DELETE FROM `bought_stock` WHERE amount = 0") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("DELETE FROM `short_sell` WHERE amount = 0") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("UPDATE `player` SET short_val = (SELECT SUM(amount * val) FROM short_sell WHERE id = player.id GROUP BY id)") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("CREATE TEMPORARY TABLE MarketVal (id VARCHAR(15) NOT NULL, b_amount INT NOT NULL DEFAULT 0,  ss_amount INT NOT NULL DEFAULT 0, ss_value DECIMAL(15, 2) NOT NULL DEFAULT 0, value INT) ENGINE=MEMORY;") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("INSERT INTO MarketVal (SELECT b.id as ID, IFNULL((b.amount), 0) AS b_amount, IFNULL((ss.amount), 0), IFNULL((ss.val), 0) AS ss_value, s.value FROM short_sell AS ss RIGHT JOIN bought_stock AS b ON b.symbol = ss.symbol AND b.id = ss.id LEFT JOIN stocks AS s ON s.symbol = b.symbol) UNION (SELECT ss.id as ID, IFNULL((b.amount), 0) AS b_amount, IFNULL((ss.amount), 0), IFNULL((ss.val), 0) AS ss_value, s.value FROM short_sell AS ss LEFT JOIN bought_stock AS b ON ss.symbol = b.symbol AND ss.id = b.id LEFT JOIN stocks AS s ON s.symbol = ss.symbol)") or $err_flag = TRUE;
	if (!$err_flag) $mysqli->query("UPDATE player SET market_val = (SELECT SUM(MarketVal.b_amount * MarketVal.value) + SUM(MarketVal.ss_amount * (MarketVal.ss_value - MarketVal.value)) from MarketVal WHERE MarketVal.id = player.id) WHERE rank = 1") or $err_flag = TRUE;
	if (!$err_flag) {
		$mysqli->commit();
		echo "Success!";
	} else {
		$mysqli->rollback();
		echo "Failure!";
	}
	$mysqli->autocommit(TRUE);
?>