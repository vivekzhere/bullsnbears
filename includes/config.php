<?php
	$config = parse_ini_file('config.ini');
	
	$server = $config['server'];

	$sqlid = $config['sqlid'];
	$sqlpass = $config['sqlpass'];
	$bnbdbase = $config['bnbdbase'];
	$mainkey = $config['mainkey'];
	
	$start_time = $config['start_time'];
	$start_time_min = $config['start_time_min'];
	$end_time = $config['end_time'];
	$end_time_min = $config['end_time_min'];
	$start_money = $config['start_money'];
	$max_stock = $config['max_stock'];
	$time_offset = $config['time_offset'];
	$admins = explode(' ', $config['admin_ids']);
	
	$debug_status = $config['debug'];
	$access_status = $config['access'];
	$trade_status = $config['trade'];
	$schedule_status = $config['schedule'];
	$appId = $config['appid'];
	$secretKey = $config['secretkey'];
	$fbArray = array('appId' => $appId, 'secret' => $secretKey, 'fileUpload' => false);
?>
