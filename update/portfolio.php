<?php
require_once("../includes/global.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}
	$t = (isset($_GET['t']) && $_GET['t'] == "Shorted")? "Shorted" : "Bought";
	$p = $mysqli->query("SELECT MAX(time_stamp) FROM stocks");
	$p = $p->fetch_array();
	$p = 250 - time() + strtotime(($p[0]));
	$p = ($p < 0) ? 30 : $p;
	$p = ($p < -300) ? 12000 : $p;
	if ($t == 'Bought') {
		$Portolio = array();
		$results = $mysqli->query("SELECT b.`symbol`, b.`amount`, b.`avg`, s.`name`, s.`value` FROM `bought_stock` b, `stocks` s WHERE b.`symbol` = s.`symbol` AND b.`id` = '{$_SESSION['id']}';");
		while ($result = $results->fetch_assoc()) {
			$result['invested_value'] = number_format($result['avg'] * $result['amount'], 2, '.', '');
			$result['present_value'] = number_format($result['value'] * $result['amount'], 2, '.', '');
			$result['brokerage'] = number_format($result['avg'] * $result['amount'] * 0.002, 2, '.', '');
			$result['gain'] = addarrow(number_format((($result['value'] * 0.998) - ($result['avg'] * 1.002)) * $result['amount'], 2, '.', ''));
			$Portolio[] = $result;
		}
		echo "<div>".json_encode($Portolio)."</div>".$p;
	} else {
		$Portolio = array();
		$results = $mysqli->query("SELECT b.`symbol`, b.`amount`, b.`val`, s.`name`, s.`value` FROM `short_sell` b, `stocks` s WHERE b.`symbol` = s.`symbol` AND b.`id` = '{$_SESSION['id']}';");
		while ($result = $results->fetch_assoc()) {
			$result['sold_value'] = number_format($result['val'] * $result['amount'], 2, '.', '');
			$result['brokerage'] = number_format($result['val'] * $result['amount'] * 0.02, 2, '.', '');
			$result['gain'] = addarrow(number_format((($result['value'] * 0.998) - ($result['val'] * 1.002)) * $result['amount'], 2, '.', ''));
			$Portolio[] = $result;
		}
	echo "<div>".json_encode($Portolio)."</div>".$p;
	}
?>