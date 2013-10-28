<?php require_once("includes/global.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}

	$results = $mysqli->query("SELECT * FROM `stocks` ORDER BY `symbol` ASC");
	echo "<h2 align='center'>Market</h2>";
?>
	<table id="marketsTable">
		<thead><tr>
			<th>Symbol</th><th>Name</th><th>Price</th><th>Change %</th><th>Day High</th><th>Day Low</th><th>Year High</th><th>Year Low</th><th></th><th></th>
		</tr></thead>
		<tbody>
		<?php
			while ($result = $results->fetch_assoc()) {
				$ptage = round(($result['change'] / ($result['value'] + $result['change'])) * 100, 2);
				echo "<tr onclick=\"window.location.href='lookup.php?symbol={$result['symbol']}'\"><td>{$result['symbol']}</td><td>{$result['name']}</td><td>{$result['value']}</td><td>".addarrow($result['change_perc'])."</td><td>{$result['day_high']}</td><td>{$result['day_low']}</td><td>{$result['week_high']}</td><td>{$result['week_low']}</td><td id=\"td-sell\" onclick=\"window.location.href = 'trade.php?type=buy&symbol={$result['symbol']}'\" class='btn-red'>Buy</td><td onclick=\"window.location.href = 'trade.php?type=short&symbol={$result['symbol']}'\" id=\"td-sell\" class='btn-red'>Short</td></tr>";
			}
		?>
		</tbody>
	</table>