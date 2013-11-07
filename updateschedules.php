<?php
require_once("includes/global.php");		
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0) || ($debug_status == 1 && $trade_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}
	if (isset($_REQUEST['skey'])) {
		if (is_numeric($_REQUEST['skey'])) $p = $mysqli->query("DELETE FROM `schedule` WHERE `skey` = ".$_REQUEST['skey']." AND `id` = '{$_SESSION['id']}'");
		if ($p) { echo "Success"; }
		else { echo "Failure"; }
	} else {
		$result_set = $mysqli->query("SELECT `s`.`symbol`, `s`.`value`, `s`.`name`, `sc`.`skey`, `sc`.`transaction_type`, `sc`.`scheduled_price`, `sc`.`no_shares`, `sc`.`pend_no_shares` FROM `schedule` AS `sc`, `stocks` as `s` WHERE `sc`.`id` = '{$_SESSION['id']}' AND `sc`.`symbol` = `s`.`symbol` ORDER BY `sc`.`skey` ASC");
		$schedules = array();
		while ($r = $result_set->fetch_assoc()) $schedules[] = $r;
		echo json_encode($schedules);		
	}
