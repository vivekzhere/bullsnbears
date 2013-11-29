<?php
require_once("includes/global.php");
require_once("includes/sanitize.php");
if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}
	metadetails();
	$results = $mysqli->query("SELECT `t_time`, `symbol`, `t_type`, `amount`, `value` from `history` WHERE `p_id` = '{$_SESSION['id']}' ORDER BY `t_time` DESC LIMIT 100");
?>
</head>
<body>
	<?php require_once("includes/nav.php"); ?>
	<div id="content">
		<h2 align="center">Transaction History</h2>
		<?php 
		if ($results->num_rows != 0)  {
		?>
		<br/><br/><table id="historyTable" class="box box1">
			<thead><tr>
				<th>Time</th><th>Type</th><th>Stock</th><th>Amount</th><th>Stock Price</th><th>Value</th><th>Brokerage</th>
			</tr></thead>
			<tbody>
<?php			
	
	while ($transaction = $results->fetch_assoc()) {
		$t_time = $transaction['t_time'];
		$t_time = date('j-M  H:i', strtotime($t_time));
		$t_type = $transaction['t_type'];
		if($t_type == 'B')
			$t_type = 'Buy';
		else if($t_type == 'S')
			$t_type = 'Sell';
		else if($t_type == 'C')
			$t_type = 'Cover';
		else
			$t_type = 'Short Sell';			
		$symbol = $transaction['symbol'];
		$amount = $transaction['amount'];
		$value = $transaction['value'];
		$total = number_format($value*$amount, 2, '.', '');
		$brokerage = number_format(0.002*$total, 2, '.', '');
		echo "<tr><td>{$t_time}</td><td>{$t_type}</td><td>{$symbol}</td><td>{$amount}</td><td>{$value}</td><td>{$total}</td><td>{$brokerage}</td></tr>";
	}
?>	
			</tbody>
		</table>
		<?php
			} else echo "<br/><br/><div style='text-align: center;'>No Records Found.</div>";
		?>
		<br/><br/>
	</div>
	<?php require_once("includes/ticker.php"); ?>
</body>
</html>