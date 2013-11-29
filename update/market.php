<?php
require_once("../includes/global.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}
	$results = $mysqli->query("SELECT name, value, `change_perc`, day_high, day_low, week_high, week_low, symbol FROM stocks ORDER BY symbol ASC");
	while (($row = $results->fetch_assoc()) && $row['change_perc'] = addarrow($row['change_perc']) && $stocks[$row['symbol']] = $row);
	$p = $mysqli->query("SELECT MAX(time_stamp) FROM stocks");
	$p = $p->fetch_array();
	$p = 250 - time() + strtotime(($p[0]));
	$p = ($p < 0) ? 30 : $p;
	$p = ($p < -300) ? 12000 : $p;
	echo "<div>".json_encode($stocks)."</div>".$p;
?>