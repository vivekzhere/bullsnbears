<?php
require_once("includes/global.php");
	if (session_id() == '') session_start();
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}
	metadetails();
?>
</head>
<body onload="Init()">
	<div id="banner"></div>
	<?php Menu(); 

	?>
	<div id="lookup">
		<?php
			$results = $mysqli->query("SELECT * FROM `stocks` ORDER BY `name` ASC");
			$options = "";
			$data = "";
			while ($result = $results->fetch_assoc()) {
				$options .= "<option value=\"{$result['symbol']}\">{$result['name']}</option>";
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
			echo '<select id="symbol-select" onchange="ShowValue(this.value)" placeholder="Choose a Stock..." name="symbol" style="width:200px; text-align:left;">';
			echo $options;
			echo '</select>';
		?>	
		
		<div id="symbol" class="box stylebg">
		</div>
	</div>
	<div id="data" style="display: none;">
	<?php
		echo $data;
	?>
	</div>

	<?php require_once("includes/ticker.php"); AjaxGet(); Load_Anim(); ?>
	<script>
		var ReGet = 0;
		ShowValue(document.getElementById('symbol-select').value);
		function Init() {
			setTimeout(function() { ReGet = 1; }, 60000);
		}

		function ShowValue(a) {
			if (ReGet == 1) {
				AjaxGet('updatelookup.php', 'data');
				ReGet = 2;
				setTimeout(function() { ReGet = 0; ShowValue(a); Init(); }, 5000);
			} else {
				p = document.getElementById('symbol');
				q = document.getElementById(a);
				imgS = "style=\"background-image: url('http://ichart.finance.yahoo.com/z?s=" + a.substring(0, 9) + ".NS');\"";
				q.innerHTML = q.innerHTML.replace("style=\"background-image: none;\"", imgS);
				p.innerHTML = q.innerHTML;
				p.innerHTML = p.innerHTML.replace("img-" + a, "img-symbol");
			}
		}
	</script>

</body>
</html>