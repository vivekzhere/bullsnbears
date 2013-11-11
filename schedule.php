<?php
require_once("includes/global.php");
require_once("includes/sanitize.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0) || ($debug_status == 1 && $schedule_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}

	$id = $_SESSION['id'];

	$mt = strftime("%H", time());
	$mt_m = strftime("%M", time());
	$mt_d = strtolower(strftime("%A",time()));
	
	$player = $mysqli->query("SELECT `liq_cash`, `market_val`, `short_val` FROM `player` WHERE `id` = '{$id}'");
	$player = $player->fetch_assoc();
	$player['total_val'] = $player['market_val'] + $player['liq_cash'];
	$type = (!isset($_GET['type'])) ? "Buy" : $_GET['type'];
	if (!in_array($type, array("Buy", "Sell", "Short", "Cover"))) $type = "Buy";
	$result_set = $mysqli->query("SELECT `s`.`symbol`, `s`.`value`, `s`.`name`, IFNULL((`b`.`amount`), 0) AS `bought_amount`, IFNULL((`ss`.`amount`), 0) AS `shorted_amount` FROM `stocks` AS `s` LEFT JOIN `bought_stock` AS `b` ON `s`.`symbol` = `b`.`symbol` AND `b`.`id` = '{$id}' LEFT JOIN `short_sell` AS `ss` ON `s`.`symbol` = `ss`.`symbol` AND `ss`.`id` = '{$id}' ORDER BY `name` ASC");
	$symbols = array();
	while ($result = $result_set->fetch_assoc()) {
		$result['max_buy'] = max(min(floor( ($player['liq_cash']- ($player['short_val'] / 4) ) / (1.002 * $result['value'] ) ), floor( ($player['liq_cash'] + $player['market_val']) / (6*1.002*$result['value']) ) - $result['bought_amount']), 0);
		$result['max_short'] = max(min(floor( ((4 * $player['liq_cash'] ) - $player['short_val'] ) / ( $result['value']*1.004 ) ), floor( ($player['liq_cash'] + $player['market_val'] - $player['short_val'] ) / (6*$result['value']*1.004) ) - $result['shorted_amount']), 0);
		$symbols[] = $result;
	}
	$symbol = (isset($_GET['symbol'])) ? $_GET['symbol'] : "";
	$i = 0;
	if ($symbol) for ($i = 0; $symbols[i] && $symbols[i]['symbol'] != $symbol; $i++);
	if ($symbols[$i] && $symbols[$i]['symbol'] == $symbol) {
		if ($type == "Sell" && $symbols[$i]['bought_amount'] == 0) $symbol = "";
		else if ($type == "Cover" && $symbols[$i]['shorted_amount'] == 0) $symbol = "";
	} else $symbol = "";
	metadetails();
?>

</head>
<body>
	<div id="banner"></div>
	<?php Menu(); ?>

	<form id="transaction" action="" onsubmit="return false;" oninput="ChangeAmount();">
		<h2 id="transactionHeading" align="center"><?=$type?></h2><br/>
		<select id="type-select" onchange="ChangeType(this.value, this.item(this.selectedIndex).innerHTML)">
			<option value="Buy"<?php if ($type == "Buy") echo " selected=\"selected\""; ?>>Buy</option><option value="Sell"<?php if ($type == "Sell") echo " selected=\"selected\""; ?>>Sell</option><option value="Short"<?php if ($type == "Short") echo " selected=\"selected\""; ?>>Short Sell</option><option value="Cover"<?php if ($type == "Cover") echo " selected=\"selected\""; ?>>Cover</option>
		</select><br/>
		<select id="stock-select" onchange="ShowValue(this.value)">
		<?php
			foreach ($symbols as $stock)
				if ($type == "Buy" || $type == "Short" || ($type == "Sell" && $stock['bought_amount'] != 0) || ($type == "Cover" && $stock['shorted_amount'] != 0)) {
					echo "<option value=\"{$stock['symbol']}\"";
					if ($stock['symbol'] == $symbol) echo " selected=\"selected\"";
					echo ">{$stock['name']}</option>";					
				}
		?>
		</select>
		<table id="tradeTable">
			<thead><tr><th>Max Amount</th><th>Value</th><th>Total Cost</th></tr></thead>
			<tbody id="Trade-Symbol"></tbody>
		</table>
		<input id="transactionAmount" type="number" min=1 pattern="[0-9]+" placeholder="Enter Amount Here" required />
		<input id="scheduledPrice" type="number" step="any" min=1 placeholder="Enter Schedule Value Here" required />
		<input id="transactionSubmit" class="button btn-green" onclick="DoSchedule();" value="Schedule" type="submit">
		<button id="schedule-ShowScheduled" style="position: absolute; width: 190px; left: 105px; bottom: 0px;" class="button btn-green" onclick="ToggleView(1)">Show Scheduled Transactions</button> 
	</form>
	<div id="content" style="display: none;">
		<h2 align="center">Scheduled Transactions</h2>
		<button id="schedule-Schedule" style="float: right; margin-right: 20px;" class="button btn-green" onclick="ToggleView(2)">Schedule</button> 
		<table id="scheduleTable">
			<thead><th>Symbol</th><th>Name</th><th>Transaction</th><th>Scheduled Price</th><th>Current Price</th><th>Amount</th><th>Pending</th><th>Status</th><th></th></thead>
			<tbody id="schedules"></tbody>
		</table>
	</div>

		
	<div id="stock-data" style="display: none;"></div>
	<div id="schedule-data" style="display: none;"></div>
	<?php AjaxGet(); Load_Anim(); ?>
	<script>
		ReGet = 1;
		ReGetSchedules = 1;
		max_amount = 0;
		setTimeout(function() { ReGet = 1; }, 15000);
		ShowValue();

		function ToggleView(a) {
			if (a == 1) {
				document.getElementById('transaction').style.display = "none";
				document.getElementById('content').style.display = "block";
				if (ReGetSchedules) AjaxGet('updateschedules.php');
			} else {
				document.getElementById('transaction').style.display = "block";
				document.getElementById('content').style.display = "none";
			}
		}
		function ChangeType(t, u) {
			stock = JSON.parse(document.getElementById('stock-data').innerHTML);
			data = "";
			for (i in stock) data += "<option value='"+stock[i]['symbol']+"'>"+stock[i]['name']+"</option>";
			document.getElementById('stock-select').innerHTML = data;
			document.getElementById('transactionHeading').innerHTML = u;
			ShowValue();
		};
		function ShowValue(a) {
			a = a || document.getElementById('stock-select').value;
			if (!a) {
				document.getElementById('Trade-Symbol').innerHTML = "Nothing To Show Here!";
				document.getElementById('transactionAmount').disabled = true;
				document.getElementById('transactionSubmit').disabled = true;
				return;
			}
			document.getElementById('transactionAmount').disabled = false;
			document.getElementById('transactionSubmit').disabled = false;
			if (ReGet) AjaxGet('updatetrade.php');
			else {
				p = JSON.parse(document.getElementById('stock-data').innerHTML);
				t = document.getElementById('type-select').value;
				for (i = 0; p[i] && p[i]['symbol'] != a; i++);
				switch (t) {
					case "Buy":
					case "Sell":
						max_amount = p[i]['max_buy'];
						data = "<tr><td>"+p[i]['max_buy']+"</td>";
						break; 
					case "Short":
					case "Cover":
						max_amount = p[i]['max_short'];
						data = "<tr><td>"+p[i]['max_short']+"</td>";
						break;
				};
				data += "<td id='stock_value'>"+p[i]['value']+"</td><td id='sale_value'>"+(p[i]['value'] * 1.002).toFixed(2)+"</td>";
				ta = document.getElementById('transactionAmount');
				ta.value = 1;
				ta.max = max_amount;
				document.getElementById('scheduledPrice').value = p[i]['value'];
				document.getElementById('Trade-Symbol').innerHTML = data; 
			}
		};

		function ChangeAmount() {
			p = document.getElementById('transactionAmount');
			t = (p.value == "") ? 1 : parseInt(p.value);
			document.getElementById('scheduledPrice').value = (p.value == "") ? 1 : document.getElementById('scheduledPrice').value;
			if (t < p.min) t = 1;
			else if (t > p.max) t = p.max;
			p.value = t;
			document.getElementById('sale_value').innerHTML = (t * parseFloat(document.getElementById('scheduledPrice').value) * 1.002).toFixed(2);
		};
		
		function DoSchedule() {
			type = document.getElementById("type-select").value;
			symbol = document.getElementById("stock-select").value;
			amount = document.getElementById("transactionAmount").value;
			scheduledPrice = document.getElementById("scheduledPrice").value;
			AjaxGet("doschedule.php?type=" + type + "&symbol=" + symbol + "&amount=" + amount + "&scheduledPrice=" + scheduledPrice);
		};

		function Ajax_Success(a, b, c) {
			if (a == 'updatetrade.php') {
				document.getElementById('stock-data').innerHTML = c;
				ReGet = 0;
				ReGetSchedules = 1;
				setTimeout(function() { ReGet = 1}, 15000);
				ShowValue();
			} else if (a == 'updateschedules.php') {
				Schedules = JSON.parse(c);
				data = "";
				ReGetSchedules = 0;
				if (!Schedules.length) data = "No Scheduled Transactions!";
				else for (i in Schedules) data += "<tr><td>" + Schedules[i]['symbol'] + "</td><td>" + Schedules[i]['name'] + "</td><td>" + Schedules[i]['transaction_type'] + "</td><td>" + Schedules[i]['scheduled_price'] + "</td><td>" + Schedules[i]['value'] + "</td><td>" + Schedules[i]['no_shares'] + "</td><td>" + Schedules[i]['pend_no_shares'] + "</td><td>" + (Schedules[i]['pend_no_shares'] == 0 ? 'Done' : 'Waiting' ) + "</td><td class=\"btn-red table-btn\" onclick='AjaxGet(\"updateschedules.php?skey=" + Schedules[i]['skey'] + "\")'>Cancel" + "</tr>";
				document.getElementById('schedules').innerHTML = data;
			} else {
				alert(c);
				ReGet = 1;
				ReGetSchedules = 1;
				if (document.getElementById('content').style.display == 'none') AjaxGet('updatetrade.php');
				else AjaxGet('updateschedules.php');
			}
		}
	</script>
</body>
</html>
