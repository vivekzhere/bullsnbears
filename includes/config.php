<?php
	$config = parse_ini_file('config.ini');
	$server = $config['server'];
	$sqlid = $config['sqlid'];
	$sqlpass = $config['sqlpass'];
	$bnbdbase = $config['bnbdbase'];
	$short_max = $config['short_max'];
	$start_time = $config['start_time'];
	$start_time_min = $config['start_time_min'];
	$end_time = $config['end_time'];
	$end_time_min = $config['end_time_min'];
	$start_money = $config['start_money'];
	$max_stock = $config['max_stock'];
	$short_sell_days = $config['short_sell_days'];
	$admins = explode(' ', $config['admin_ids']);
?>
