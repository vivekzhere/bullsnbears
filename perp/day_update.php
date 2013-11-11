<?php
require_once("../includes/config.php");
require_once("../includes/connection.php");
if ($_GET['key'] == $mainkey) {
	$mysqli->query("UPDATE player SET day_worth = liq_cash + market_val");
	$mysqli->query("DELETE FROM schedule WHERE pend_no_shares = 0");
	echo(date("Y-m-d",time()));
} else header('Location: ../home.php');
?>
