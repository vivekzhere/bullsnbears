<?php 
require_once("../includes/config.php");
require_once("../includes/connection.php");
require_once("../includes/transactions.php");
	if ($_GET['key'] == $mainkey) {		
		require_once("stock_update.php");
		$mysqli->query("CREATE TEMPORARY TABLE ScheduleProc (id VARCHAR(15) NOT NULL, p_liqcash INT NOT NULL DEFAULT 0, p_mval INT NOT NULL DEFAULT 0, p_sval INT NOT NULL DEFAULT 0, amount INT NOT NULL DEFAULT 0,  symbol VARCHAR(12) NOT NULL, skey BIGINT DEFAULT 0, type VARCHAR(2), value INT, bought_amount INT NOT NULL DEFAULT 0, shorted_amount INT NOT NULL DEFAULT 0) ENGINE=MEMORY;");
		$mysqli->query("INSERT INTO ScheduleProc (SELECT id, 0, 0, 0, pend_no_shares, schedule.symbol, skey, transaction_type, stocks.value, 0, 0 FROM schedule JOIN stocks ON schedule.symbol = stocks.symbol AND ( (schedule.flag = 'l' AND schedule.scheduled_price >= stocks.value) OR (schedule.flag = 'g' AND schedule.scheduled_price <= stocks.value) ))");
		$mysqli->query("UPDATE ScheduleProc, (SELECT id, liq_cash, market_val, short_val FROM player) P SET p_liqcash = P.liq_cash, p_mval = P.market_val, p_sval = P.short_val WHERE P.id = ScheduleProc.id");
		$mysqli->query("UPDATE ScheduleProc SET bought_amount = (SELECT amount FROM bought_stock WHERE bought_stock.id = ScheduleProc.id AND bought_stock.symbol = ScheduleProc.symbol)");
		$mysqli->query("UPDATE ScheduleProc SET shorted_amount = (SELECT amount FROM short_sell WHERE short_sell.id = ScheduleProc.id AND short_sell.symbol = ScheduleProc.symbol)");
		$mysqli->query("DELETE FROM ScheduleProc WHERE amount <= 0");
		$res = $mysqli->query("SELECT * from ScheduleProc");
		while ($r = $res->fetch_assoc()) $Schedules[] = $r;
		$res = $mysqli->query("SELECT * from player WHERE id IN (SELECT DISTINCT(id) FROM ScheduleProc)");
		while ($r = $res->fetch_assoc()) $Players[$r['id']] = $r;
		$res = $mysqli->query("SELECT id, symbol, amount from bought_stock WHERE id IN (SELECT DISTINCT(id) FROM ScheduleProc)");
		while ($r = $res->fetch_assoc()) $Players[$r['id']][$r['symbol']]['bought'] = $r['amount'];
		$res = $mysqli->query("SELECT id, symbol, amount, val from short_sell WHERE id IN (SELECT DISTINCT(id) FROM ScheduleProc)");
		while ($r = $res->fetch_assoc()) {
			$Players[$r['id']][$r['symbol']]['shorted'] = $r['amount'];
			$Players[$r['id']][$r['symbol']]['s_val'] = $r['val'];
		}
		if (!isset($Schedules)) die();
		foreach ($Schedules as &$Schedule) {
			$Players[$Schedule['id']][$Schedule['symbol']]['bought'] = (isset($Players[$Schedule['id']][$Schedule['symbol']]['bought'])) ? $Players[$Schedule['id']][$Schedule['symbol']]['bought'] : 0;
			$Players[$Schedule['id']][$Schedule['symbol']]['shorted'] = (isset($Players[$Schedule['id']][$Schedule['symbol']]['shorted'])) ? $Players[$Schedule['id']][$Schedule['symbol']]['shorted'] : 0;
			$Players[$Schedule['id']][$Schedule['symbol']]['s_val'] = (isset($Players[$Schedule['id']][$Schedule['symbol']]['s_val'])) ? $Players[$Schedule['id']][$Schedule['symbol']]['s_val'] : 0;
			$array = array( 'value' => $Schedule['value'], 'bought_amount' => $Players[$Schedule['id']][$Schedule['symbol']]['bought'], 'shorted_amount' => $Players[$Schedule['id']][$Schedule['symbol']]['shorted']);
			switch ($Schedule['type']) {
				case "B":
					$amount = max(min(floor( ($Players[$Schedule['id']]['liq_cash']- ($Players[$Schedule['id']]['short_val'] / 4) ) / (1.002 * $Schedule['value'] ) ), floor( ($Players[$Schedule['id']]['liq_cash'] + $Players[$Schedule['id']]['market_val']) / (6*1.002*$Schedule['value']) ) - $Players[$Schedule['id']][$Schedule['symbol']]['bought']), 0);
					$amount = max(min($Schedule['amount'], $amount), 0);
					if ($amount && Buy($Schedule['id'], $Schedule['symbol'], $amount, $array, $Schedule['skey'])) {
						$Players[$Schedule['id']]['liq_cash'] -= $amount * $Schedule['value'] * 1.002;
						$Players[$Schedule['id']]['market_val'] += $amount * $Schedule['value'];
						$Players[$Schedule['id']][$Schedule['symbol']]['bought'] += $amount;
					}
				break;
				case "S":					
					$amount = max(min($Schedule['amount'], $Players[$Schedule['id']][$Schedule['symbol']]['bought']), 0);
					if ($amount && Sell($Schedule['id'], $Schedule['symbol'], $amount, $array, $Schedule['skey'])) {
						$Players[$Schedule['id']]['liq_cash'] += $amount * $Schedule['value'] * 0.998;
						$Players[$Schedule['id']]['market_val'] -= $amount * $Schedule['value'];
						$Players[$Schedule['id']][$Schedule['symbol']]['bought'] -= $amount;
					}
				break;
				case "SS":
					$amount = max(min(floor( ((4 * $Players[$Schedule['id']]['liq_cash'] ) - $Players[$Schedule['id']]['short_val'] ) / ( $Schedule['value'] * 1.004 ) ), floor( ($Players[$Schedule['id']]['liq_cash'] + $Players[$Schedule['id']]['market_val'] - $Players[$Schedule['id']]['short_val'] ) / (6 * $Schedule['value'] * 1.004) )) - $Players[$Schedule['id']][$Schedule['symbol']]['shorted'], 0);
					$amount = max(min($Schedule['amount'], $amount), 0);
					if ($amount && Short($Schedule['id'], $Schedule['symbol'], $amount, $array, $Schedule['skey'])) {
						$Players[$Schedule['id']]['liq_cash'] -= $amount * $Schedule['value'] * .002;
						$Players[$Schedule['id']]['short_val'] += $amount * $Schedule['value'];
						$Players[$Schedule['id']][$Schedule['symbol']]['shorted'] += $amount;
					}
				break;
				case "C":
					$amount = max(min($Schedule['amount'], $Players[$Schedule['id']][$Schedule['symbol']]['shorted']), 0);
					if ($amount && Cover($Schedule['id'], $Schedule['symbol'], $amount, $array, $Schedule['skey'])) {
						$Players[$Schedule['id']]['liq_cash'] += ($Players[$Schedule['id']][$Schedule['symbol']]['s_val'] - $Schedule['value'] * 0.998) * $amount;
						$Players[$Schedule['id']]['short_val'] -= $amount * $Schedule['value'];
					}
				break;
			}
		}
		echo(date("Y-m-d",time()));
		require_once("marketval_update.php");
	} else header("Location: ../home.php");
?>