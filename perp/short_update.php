<?php
require_once("../includes/config.php");
require_once("../includes/connection.php");
if ($_GET['key'] == $mainkey) {
	$mysqli->query("INSERT INTO history (p_id, t_type, symbol, amount, value) SELECT id, 'c', symbol, amount, val FROM short_sell");
	echo $mysqli->error;
	$mysqli->query("CREATE TEMPORARY TABLE ShortSell (id VARCHAR(15) NOT NULL, value DECIMAL(15, 2) NOT NULL DEFAULT 0) ENGINE=MEMORY;");
	echo $mysqli->error;
	$mysqli->query("INSERT INTO ShortSell (SELECT id, SUM(value * 0.998 * amount - val * amount) as S FROM short_sell LEFT JOIN stocks ON short_sell.symbol = stocks.symbol)");	
	echo $mysqli->error;
	$mysqli->query("UPDATE player, ShortSell SET player.liq_cash = player.liq_cash + ShortSell.value, short_val = 0 WHERE player.id = ShortSell.id");
	echo $mysqli->error;
	$mysqli->query("DELETE FROM short_sell WHERE 1");
	echo $mysqli->error;
	echo(date("Y-m-d",time()));
} else header('Location: home.php');
?>
