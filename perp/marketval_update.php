<?php
require_once("../includes/config.php");
require_once("../includes/connection.php");
if ($_GET['key'] == $mainkey) {
	$mysqli->query("CREATE TEMPORARY TABLE MarketVal (id VARCHAR(15) NOT NULL, b_amount INT NOT NULL DEFAULT 0,  ss_amount INT NOT NULL DEFAULT 0, ss_value DECIMAL(15, 2) NOT NULL DEFAULT 0, value INT) ENGINE=MEMORY;");
	echo $mysqli->error;
	$mysqli->query("INSERT INTO MarketVal (SELECT b.id as ID, IFNULL((b.amount), 0) AS b_amount, IFNULL((ss.amount), 0), IFNULL((ss.val), 0) AS ss_value, s.value FROM short_sell AS ss RIGHT JOIN bought_stock AS b ON b.symbol = ss.symbol AND b.id = ss.id LEFT JOIN stocks AS s ON s.symbol = b.symbol) UNION (SELECT ss.id as ID, IFNULL((b.amount), 0) AS b_amount, IFNULL((ss.amount), 0), IFNULL((ss.val), 0) AS ss_value, s.value FROM short_sell AS ss LEFT JOIN bought_stock AS b ON ss.symbol = b.symbol AND ss.id = b.id LEFT JOIN stocks AS s ON s.symbol = ss.symbol)");
	echo $mysqli->error;
	$mysqli->query("UPDATE player SET market_val = (SELECT SUM(MarketVal.b_amount * MarketVal.value) + SUM(MarketVal.ss_amount * (MarketVal.ss_value - MarketVal.value)) from MarketVal WHERE MarketVal.id = player.id) WHERE rank = 1");
	echo $mysqli->error;
	echo(date("Y-m-d",time()));
} else header('Location: ../home.php')
?>