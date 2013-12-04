<?php
require_once("../includes/global.php");
	if (!in_array($_SESSION['id'], $admins)) header("Location: ../index.php") or die();
	metadetails();
?>
	<link href="/stylesheets/admin.css" media="screen, projection" rel="stylesheet" type="text/css" />
</head>
<?php
$data = array();
$t = $mysqli->query("SELECT COUNT(*) FROM player");
$t = $t->fetch_array();
$data['total_players'] = $t[0];
$t = $mysqli->query("SELECT COUNT(*) FROM player WHERE rank = 1");
$t = $t->fetch_array();
$data['active_players'] = $t[0];
$t = $mysqli->query("SELECT MAX(time_stamp) FROM stocks");
$t = $t->fetch_array();
$data['last_stock_update'] = $t[0];
$t = $mysqli->query("SELECT COUNT(*) FROM stocks WHERE time_stamp = '{$data['last_stock_update']}'");
$t = $t->fetch_array();
$data['stock_count'] = $t[0];

?>

<body>
	<h2>Admin Panel</h2>
	<div id="Overall" class="box box1 centerh">
		<h3>Overall Stats</h3>
		<br/><ul>
			<li><span class="data">Market Open Time : </span><span class="value"><? echo $start_time.":".$start_time_min; ?></span></li>
			<li><span class="data">Market Close Time : </span><span class="value"><? echo $end_time.":".$end_time_min; ?></span></li>
			<li><span class="data">No. Of Players : </span><span class="value"><?=$data['total_players']?></span></li>
			<li><span class="data">No. Of Active Players : </span><span class="value"><?=$data['active_players']?></span></li>
			<li><span class="data">No. Of Active Stocks : </span><span class="value"><?=$data['stock_count']?></span></li>
			<li><span class="data">Last Update : </span><span class="value"><?=$data['last_stock_update']?></span></li>
		</ul>
		<a href="feedbacks.php" class="btn btn-green centerh">Feedback</a>
	</div>

</body>
</html>