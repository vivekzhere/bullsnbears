<?php
require_once("../includes/config.php");
require_once("../includes/connection.php");
if ($_GET['key'] == $mainkey && isset($_GET['symbol'])) {
	$r = $mysqli->query("SELECT value FROM stocks WHERE symbol = '{$_GET['symbol']}'");
	$r = $r->fetch_assoc();
	$r = $r['value'];
	$mysqli->query("INSERT INTO history ( p_id, t_type, symbol, amount, value, skey ) (SELECT id, 'S', '{$_GET['symbol']}', amount, '{$r}' as value, 0 FROM bought_stock WHERE bought_stock.symbol = '{$_GET['symbol']}')");
	$mysqli->query("INSERT INTO history ( p_id, t_type, symbol, amount, value, skey ) (SELECT id, 'C', '{$_GET['symbol']}', amount, '{$r}' as value, 0 FROM short_sell WHERE short_sell.symbol = '{$_GET['symbol']}')");
	$mysqli->query("UPDATE player, (SELECT id, amount, '{$r}' as value FROM bought_stock WHERE bought_stock.symbol = '{$_GET['symbol']}') B SET liq_cash = liq_cash + B.amount * B.value * 0.998, market_val = CASE WHEN market_val - B.amount * B.value < 0 THEN 0 ELSE market_val - B.amount * B.value END");
	$mysqli->query("UPDATE player, (SELECT id, amount, short_sell.val, '{$r}' as value FROM short_sell WHERE short_sell.symbol = '{$_GET['symbol']}') SS SET liq_cash = liq_cash + SS.amount * (SS.val - SS.value * 0.998), short_val = CASE WHEN short_val - SS.amount * SS.value < 0 THEN 0 ELSE short_val - SS.amount * SS.value END");
	$mysqli->query("DELETE FROM bought_stock WHERE symbol = '{$_GET['symbol']}'");
	$mysqli->query("DELETE FROM short_sell WHERE symbol = '{$_GET['symbol']}'");
	$mysqli->query("DELETE FROM schedule WHERE symbol = '{$_GET['symbol']}'");
	$mysqli->query("DELETE FROM stocks WHERE symbol = '{$_GET['symbol']}'");
} else header("Location: ../index.php");