<?php
require_once("includes/global.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}
	$t = ($_GET['t'] == "Shorted")? "Shorted" : "Bought";
	if ($t == 'Bought') {
		echo "<h2 align='center'>Bought Stocks</h2>";
		echo '<div style="height: 20px"><button id="showother" style="float: right; margin-left: 0; border-radius: 0px 10px 10px 0px;" class="button btn-green" onclick="updatePortfolio(\'Shorted\', \'NULL\')">Show Shorted Stocks</button>';
		echo '<button id="portfoliorefresh" style="float: right; margin-right: 0; border-right: 3px solid #ccc; border-radius: 10px 0px 0px 10px;" class="button btn-green" onclick="updatePortfolio(\'Bought\')">Refresh</button></div>';
		$results = $mysqli->query("SELECT b.`id`, b.`symbol`, b.`amount`, b.`avg`, s.`name`, s.`value` FROM `bought_stock` b, `stocks` s WHERE b.`symbol` = s.`symbol` AND b.`id` = '{$_SESSION['id']}';");
		if ($results->num_rows == 0) echo "You dont have any Bought stocks!";
		else {
?>
	<table id="portfolioTable">		
		<thead><tr>
			<th>Name</th><th>Amount</th><th>Avg. Bought Price</th><th>Live Price</th><th>Inv. Value</th><th>Latest Value</th><th>Brokerage</th><th>Overall Gain</th><th></th>
		</tr></thead>
		<tbody>
		<?php
			while ($result = $results->fetch_assoc()) {
				echo "<tr><td>{$result['name']}</td><td>{$result['amount']}</td><td>{$result['avg']}</td><td>{$result['value']}</td><td>".number_format($result['avg'] * $result['amount'], 2, '.', '')."</td><td>".number_format($result['value'] * $result['amount'], 2, '.', '')."</td><td>".number_format($result['avg'] * $result['amount'] * 0.002, 2, '.', '')."</td><td>".addarrow(number_format((($result['value'] * 0.998) - ($result['avg'] * 1.002)) * $result['amount'], 2, '.', ''))."</td><td onclick=\"window.location.href = 'trade.php?op=sell&stock={$result['symbol']}'\" class='btn-red'>Sell</td></tr>";
			}
		?>
		</tbody>
	</table>
<?php
		} 
	} else {
		echo "<h2 align='center'>Shorted Stocks</h2>";
		echo '<div style="height: 20px"><button id="showother" style="float: right; margin-left: 0; border-radius: 0px 10px 10px 0px;" class="button btn-green" onclick="updatePortfolio(\'Bought\', \'NULL\')">Show Bought Stocks</button>';
		echo '<button id="portfoliorefresh" style="float: right; margin-right: 0; border-right: 3px solid #ccc; border-radius: 10px 0px 0px 10px;" class="button btn-green" onclick="updatePortfolio(\'Shorted\')">Refresh</button></div>';
		$results = $mysqli->query("SELECT b.`id`, b.`symbol`, b.`amount`, b.`val`, s.`name`, s.`value` FROM `short_sell` b, `stocks` s WHERE b.`symbol` = s.`symbol` AND b.`id` = '{$_SESSION['id']}';");
		if ($results->num_rows == 0) echo "You dont have any Bought stocks!";
		else {
		?>
	<table id="portfolioTable">		
		<thead><tr>
			<th>Name</th><th>Amount</th><th>Avg. Sold Price</th><th>Live Price</th><th>Total Sold Value</th><th>Brokerage</th><th>Profit</th><th></th>		
		</tr></thead>
		<tbody>
		<?php
			while ($result = $results->fetch_assoc()) {
				echo "<tr><td>{$result['name']}</td><td>{$result['amount']}</td><td>{$result['val']}</td><td>{$result['value']}</td><td>".number_format($result['val'] * $result['amount'], 2, '.', '')."</td><td>".number_format($result['val'] * $result['amount'] * 0.02, 2, '.', '')."</td><td>".addarrow(number_format((($result['value'] * 0.998) - ($result['val'] * 1.002)) * $result['amount'], 2, '.', ''))."</td><td onclick=\"window.location.href = 'trade.php?op=cover&stock={$result['symbol']}'\" class='btn-red'>Cover</td></tr>";
			}
		?>
		</tbody>
	</table>
		<?php
		}
	}
?>