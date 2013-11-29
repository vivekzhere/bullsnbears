<?php
require_once("includes/global.php");
//require_once("includes/sanitize.php");
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
	<?php require_once("includes/nav.php"); ?>

	<form id="transaction" class="box box1" action="" onsubmit="return false;" oninput="ChangeAmount();">
		<h2 id="transactionHeading" align="center"><?=$type?></h2><br/>
		<select id="type-select" onchange="ChangeType(this.item(this.selectedIndex).innerHTML)">
			<option value="Buy"<?php if ($type == "Buy") echo " selected=\"selected\""; ?>>Buy</option><option value="Sell"<?php if ($type == "Sell") echo " selected=\"selected\""; ?>>Sell</option><option value="Short"<?php if ($type == "Short") echo " selected=\"selected\""; ?>>Short Sell</option><option value="Cover"<?php if ($type == "Cover") echo " selected=\"selected\""; ?>>Cover</option>
		</select>
		<select id="stock-select" onchange="ShowValue(this.value)">
		<?php
			foreach ($symbols as $stock)
				if ($type == "Buy" || $type == "Short" || ($type == "Sell" && $stock['bought_amount'] != 0) || ($type == "Cover" && $stock['shorted_amount'] != 0)) {
					echo "<option value=\"{$stock['symbol']}\"";
					if ($stock['symbol'] == $symbol) echo " selected=\"selected\"";
					echo ">{$stock['name']}</option>";					
				}
		?>
		</select><br/>
		<table id="tradeTable">
			<thead><tr><th>Max Amount</th><th>Value</th><th>Total Cost</th></tr></thead>
			<tbody id="Trade-Symbol"></tbody>
		</table>
		<input id="transactionAmount" type="number" min=1 pattern="[0-9]+" placeholder="Enter Amount Here" required />
		<input id="scheduledPrice" type="number" step="any" min=1 placeholder="Enter Schedule Value Here" required />
		<input id="transactionSubmit" class="btn btn-green" onclick="DoSchedule();" value="Schedule" type="submit">
		<button id="schedule-ShowScheduled" style="position: absolute; width: 190px; left: 105px; bottom: 0px;" class="btn btn-green" onclick="ToggleView(1)">Show Scheduled Transactions</button> 
	</form>
	<div id="content" style="display: none;">
		<h2 align="center">Scheduled Transactions</h2>
		<button id="schedule-Schedule" style="float: right; margin-right: 20px;" class="btn btn-green" onclick="ToggleView()">Schedule</button> 
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
		
		dC = $('#content');
		dSP = $('#scheduledPrice');
		dS = $('#schedules');
		dSD = $('#stock-data');
		dSS = $('#stock-select');
		dTSy = $('#Trade-Symbol');
		dT = $('#transaction');
		dTA = $('#transactionAmount');
		dTH = $('#transactionHeading');
		dTS = $('#transactionSubmit');
		dTyS = $('#type-select');

		ShowValue();
		
		function ToggleView(a) {
			if (a) {
				dT.style.display = "none";
				dC.style.display = "block";
				if (ReGetSchedules) AjaxGet('update/schedule.php');
			} else {
				dT.style.display = "block";
				dC.style.display = "none";
			}
		};

		function ChangeType(t) {
			if (ReGet) AjaxGet('update/trade.php');
			else {
				dTH.innerHTML = t;
				ShowValue();
			}
		};

		function ShowValue(a) {
			if (ReGet) AjaxGet('update/trade.php');
			else {
				a = a || dSS.value;
				if (!a) {
					dTSy.innerHTML = "Nothing To Show Here!";
					dTA.disabled = true;
					dTS.disabled = true;
					return;
				}
				dTA.disabled = false;
				dTS.disabled = false;
				p = JSON.parse(dSD.innerHTML);
				t = dTyS.value;
				switch (t) {
					case "Buy":
					case "Sell":
						max_amount = p[a]['max_buy'];
						data = "<tr><td>"+p[a]['max_buy']+"</td>";
						break; 
					case "Short":
					case "Cover":
						max_amount = p[a]['max_short'];
						data = "<tr><td>"+p[a]['max_short']+"</td>";
						break;
				};
				data += "<td id='stock_value'>"+p[a]['value']+"</td><td id='sale_value'>"+(p[a]['value'] * 1.002).toFixed(2)+"</td>";
				dTA.value = 1;
				dTA.max = max_amount;
				dSP.value = p[a]['value'];
				dTSy.innerHTML = data; 
			}
		};

		function ChangeAmount() {
			t = (dTA.value) ? parseInt(dTA.value) : 1;
			if (t < dTA.min) t = 1;
			else if (t > dTA.max) t = dTA.max;
			if (dTA.value != "") dTA.value = t;
			$('#sale_value').innerHTML = (t * parseFloat($('#scheduledPrice').value) * 1.002).toFixed(2);
		};
		
		function DoSchedule() {
			if (!dT.checkValidity()) return;
			AjaxGet("doschedule.php?type=" + dTyS.value + "&symbol=" + dSS.value + "&amount=" + dTA.value + "&scheduledPrice=" + dSP.value);
		};

		function Ajax_Success(a, b, c) {
			if (a == 'update/trade.php') {
				dSD.innerHTML = c.substring(5, c.indexOf("</div>"));
				ReGet = 0;
				ReGetSchedules = 1;
				setTimeout(function() { ReGet = 1}, parseInt(c.substring(c.indexOf("</div>") + 6)) * 1000);
				ShowValue();
			} else if (a == 'update/schedule.php') {
				Schedules = JSON.parse(c.substring(5, c.indexOf("</div>")));
				data = "";
				ReGetSchedules = 0;
				if (!Schedules.length) data = "No Scheduled Transactions!";
				else for (i in Schedules) data += "<tr><td>" + Schedules[i]['symbol'] + "</td><td>" + Schedules[i]['name'] + "</td><td>" + Schedules[i]['transaction_type'] + "</td><td>" + Schedules[i]['scheduled_price'] + "</td><td>" + Schedules[i]['value'] + "</td><td>" + Schedules[i]['no_shares'] + "</td><td>" + Schedules[i]['pend_no_shares'] + "</td><td>" + (Schedules[i]['pend_no_shares'] == 0 ? 'Done' : 'Waiting' ) + "</td><td class=\"btn-red table-btn\" onclick='AjaxGet(\"update/schedule.php?skey=" + Schedules[i]['skey'] + "\")'>Cancel" + "</tr>";
				dS.innerHTML = data;
			} else {
				alert(c);
				ReGet = 1;
				ReGetSchedules = 1;
				if (dC.style.display == 'none') AjaxGet('update/trade.php');
				else AjaxGet('update/schedule.php');
			}
		};
	</script>
</body>
</html>
