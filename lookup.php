<?php
require_once("includes/global.php");
require_once("includes/sanitize.php");

	if (session_id() == '') session_start();
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}
	metadetails();
?>
</head>
<body>
	<div id="banner"></div>
	<?php Menu(); 

	?>
	<div id="lookup">
		<?php
			$results = $mysqli->query("SELECT * FROM `stocks` ORDER BY `name` ASC");
			$result = $results->fetch_assoc();
			if (isset($_GET['symbol'])) $t = $_GET['symbol']; else $t = "";
			$stock1 = $result;
			$options = "<option value=\"{$result['symbol']}\">{$result['name']}</option>";
			while ($result = $results->fetch_assoc()) {
				if ($t != "" && $result['symbol'] == $t) {
					$stock1 = $result;
					$options .= "<option value=\"{$result['symbol']}\" selected=\"selected\">{$result['name']}</option>";
				} else $options .= "<option value=\"{$result['symbol']}\">{$result['name']}</option>";
				
			}
			echo '<select id="symbol-select" onchange="ShowValue(this.value)" placeholder="Choose a Stock..." name="symbol" style="width:200px; text-align:left;">';
			echo $options;
			echo '</select>';
		?>	
		<div id="stock">
			<h2 align='center' id="stockName"><?=$stock1['name']?></h2>
			<div id='img-stock'></div>
			<table style='float: right; margin-top: 30px; margin-right: 20px;'>
			<tr><td>Value:</td><td id="value"><?=$stock1['value']?></td></tr>
			<tr><td>Change:</td><td id="change"><?=$stock1['change']?></td></tr>
			<tr><td>Day High:</td><td id="day_high"><?=$stock1['day_high']?></td></tr>
			<tr><td>Day Low:</td><td id="day_low"><?=$stock1['day_low']?></td></tr>
			<tr><td>Year High:</td><td id="year_high"><?=$stock1['week_high']?></td></tr>
			<tr><td>Year Low:</td><td id="year_low"><?=$stock1['week_low']?></td></tr>
			</table>
		</div>
	</div>
	<div id="data" style="display: none;">
	</div>

	<?php require_once("includes/ticker.php"); AjaxGet(); Load_Anim(); ?>
	<script>
		ReGet = 1;
		ShowValue();
		setTimeout(function() { ReGet = 1; }, 60000);

		function ShowValue(a) {
			a = a || document.getElementById('symbol-select').value;
			if (ReGet) AjaxGet('updatemarkets.php');
			else {
				Stocks = JSON.parse(document.getElementById('data').innerHTML);
				for (i = 0; Stocks[i] && Stocks[i]['symbol'] != a; i++);
				document.getElementById('stockName').innerHTML = Stocks[i]['name'];
				document.getElementById('value').innerHTML = Stocks[i]['value'];
				document.getElementById('change').innerHTML = Stocks[i]['change'];
				document.getElementById('day_high').innerHTML = Stocks[i]['day_high'];
				document.getElementById('day_low').innerHTML = Stocks[i]['day_low'];
				document.getElementById('year_high').innerHTML = Stocks[i]['week_high'];
				document.getElementById('year_low').innerHTML = Stocks[i]['week_low'];
				document.getElementById('img-stock').style.cssText = "background-image: url('http://ichart.finance.yahoo.com/z?s=" + a.substring(0, 9) + ".NS');\"";
			}
		}
		function Ajax_Success(a, b, c) {
			document.getElementById('data').innerHTML = c;
			ReGet = 0;
			ShowValue();
			setTimeout(function() { ReGet = 1; }, 60000);
		}
		function Ajax_Failure(a, b, c) {
			alert("Something went wrong! Could not sync Stock Data.")
		}
	</script>

</body>
</html>