<?php
if ($_GET['key'] == 'M1112AER') {
	session_start();
	$_SESSION['id'] = 100001074618086;
	$_SESSION['name'] = "Gautham Warrier";
	$_SESSION['market_val'] = 0;
	$_SESSION['liq_cash'] = 2500000;
	$_SESSION['rank'] = 0;
	header("Location: home.php");
	die();
}
header("Location: index.php");
?>