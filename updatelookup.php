<?php
require_once("includes/global.php");
	if (session_id() == '') session_start();
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}
?>
<?php	
	$results = $mysqli->query("SELECT * FROM `stocks` ORDER BY `symbol` ASC");
	$data = "";
	while ($result = $results->fetch_assoc()) {
		$data .= "<div id='{$result['symbol']}'>";
		$data .= "<h2 align='center'>{$result['name']}</h2>";
		$data .= "<div id='img-{$result['symbol']}' style='background-image: none;'></div>";
		$data .= "<table style='float: right; margin-top: 30px; margin-right: 20px;'>";
		$data .= "<tr><td>Value: </td><td>{$result['value']}</td></tr>";
		$data .= "<tr><td>Change: </td><td>".addarrow($result['change'])."</td></tr>";
		$data .= "<tr><td>Day High: </td><td>{$result['day_high']}</td></tr>";
		$data .= "<tr><td>Day Low: </td><td>{$result['day_low']}</td></tr>";
		$data .= "<tr><td>Year High: </td><td>{$result['week_high']}</td></tr>";
		$data .= "<tr><td>Year Low: </td><td>{$result['week_low']}</td></tr>";
		$data .= "</table></div>";
	}
	echo $data;
?>
