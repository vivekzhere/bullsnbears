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
	<?php require_once("includes/nav.php"); ?>
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
			echo '<select id="symbol-select" class="centerh" onchange="ShowValue(this.value)" placeholder="Choose a Stock...">';
			echo $options;
			echo '</select>';
		?>	
		<div id="stock" class="box box1">
			<h2 align='center' class="data" id="stockName"><?=$stock1['name']?></h2>
			<div id='img-stock'></div>
			<table style='float: right; margin-top: 30px; margin-right: 20px;'>
				<tr><td>Value:</td><td class="data" id="value"><?=$stock1['value']?></td></tr>
				<tr><td>Change:</td><td class="data" id="change"><?=$stock1['change']?></td></tr>
				<tr><td>Day High:</td><td class="data" id="day_high"><?=$stock1['day_high']?></td></tr>
				<tr><td>Day Low:</td><td class="data" id="day_low"><?=$stock1['day_low']?></td></tr>
				<tr><td>Year High:</td><td class="data" id="year_high"><?=$stock1['week_high']?></td></tr>
				<tr><td>Year Low:</td><td class="data" id="year_low"><?=$stock1['week_low']?></td></tr>
			</table>
		</div>
	</div>
	<div id="data" style="display: none;">
	</div>

	<?php require_once("includes/ticker.php"); AjaxGet(); Load_Anim(); ?>
	<script>
		ReGet = 1;
		ShowValue();

		function ShowValue(a) {
			a = a || $('#symbol-select').value;
			if (ReGet) AjaxGet('update/market.php');
			else {
				Stocks = JSON.parse($('#data').innerHTML);
				p = $('.data');
				i = 0;
				for (key in Stocks[a]) {
					if (key != 'symbol') p[i++].innerHTML = Stocks[a][key];
				}
				$('#img-stock').style.cssText = "background-image: url('http://ichart.finance.yahoo.com/z?s=" + a.substring(0, 9) + ".NS');\"";
			}
		}
		function Ajax_Success(a, b, c) {
			$('#data').innerHTML = c.substring(5, c.indexOf("</div>"));
			ReGet = 0;
			ShowValue();
			setTimeout(function() { ReGet = 1; },  parseInt(c.substring(c.indexOf("</div>") + 6)) * 1000);
		}
		function Ajax_Failure(a, b, c) {
			alert("Something went wrong! Could not sync Stock Data.")
		}
	</script>

</body>
</html>