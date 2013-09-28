<?php 
 require_once("includes/global.php");
	if(!isset($_SESSION['username']))	header("Location: index.php");
	metadetails();
?>
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
</head>


<body>
<div id="content">
	<?php navigation("history"); ?>
	 <br/>
	<div id="portfolio">
	
	<h2>Transaction History</h2>
	<br/>
	<?php
		echo "<table id=\"marketsTable\" class=\"tablesorter\">
				<thead>
					<th>Time</th>
					<th>Type</th>
					<th>Symbol</th>
					<th>Amount</th>
					<th>Value</th>
					<th>Total</th>
					<th>Brokerage</th>
			  </thead>
			<tbody>";
			
	$sql = "select * from history where p_id = '{$_SESSION['player_id']}' order by t_time desc limit 100";
	$result = mysql_query($sql) or die(mysql_error());
	while ($transaction = mysql_fetch_array($result)) {
		$t_time = $transaction['t_time'];
		$t_time = date('j-M  H:i', strtotime($t_time));
		$t_type = $transaction['t_type'];
		if($t_type == 'b')
			$t_type = 'Buy';
		else if($t_type == 's')
			$t_type = 'Sell';
		else if($t_type == 'c')
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
		
	echo "</tbody></table><br/><br/>";

?>
	</div>
	
	</div><!-- content_main -->
</div><!--content-->

<script src="scripts/jquery.tablesorter.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {	$("#marketsTable").tablesorter();	} ); 
</script>	

</body>
</html>