<?php
require_once("includes/global.php");
require_once("includes/sanitize.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0) || ($debug_status == 1 && $trade_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}

	$id = $_SESSION['id'];
	function marketValueUpdate($id) {
		global $mysqli;
		$result = $mysqli->query("SELECT SUM(`b`.`amount` * `s`.`value`) FROM `bought_stock` AS `b`, `stocks` as `s` WHERE `b`.`id` = {$_SESSION['id']} AND `b`.`symbol` = `s`.`symbol`");
		$result = $result->fetch_array();
		$market_val = $result[0];
		$result = $mysqli->query("SELECT SUM((`ss`.`val` - `s`.`value`) * `ss`.`amount`) FROM `short_sell` AS `ss`, `stocks` as `s` WHERE `ss`.`id` = {$_SESSION['id']} AND `ss`.`symbol` = `s`.`symbol`");
		$result = $result->fetch_array();
		$market_val += $result[0];
		$mysqli->query("UPDATE `player` SET `market_val` = $market_val WHERE `id` = $id");
	}

	$mt = strftime("%H", time());
	$mt_m = strftime("%M", time());
	$mt_d = strtolower(strftime("%A",time()));
	
	if (in_array($id, $admins) || ($mt_d != "sunday" && $mt_d != "saturday" && ($mt > $start_time || ($mt == $start_time && $mt_m >= $start_time_min)) && ($mt < $end_time || ($mt == $end_time && $mt_m <= $end_time_min)))) {
		$mtime = true;
		$error_flag = false;
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
		if ($symbol) for ($i = 0; $symbols[$i] && $symbols[$i]['symbol'] != $symbol; $i++);
		if ($symbols[$i] && $symbols[$i]['symbol'] == $symbol) {
			if ($type == "Sell" && $symbols[$i]['bought_amount'] == 0) $symbol = "";
			else if ($type == "Cover" && $symbols[$i]['shorted_amount'] == 0) $symbol = "";
		} else $symbol = "";
		$p = $mysqli->query("SELECT MAX(time_stamp) FROM stocks");
		$p = $p->fetch_array();
		$p = strtotime(($p[0])) - time();
		$p = ($p < 120) ? $p : 30;
		$p = ($p < 0) ? 12000 : $p;
	} else $mtime = false;
	metadetails();
?>

</head>
<body>
	<?php
		require_once("includes/nav.php");
		if ($mtime) { ?>
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
			<input id="transactionSubmit" class="btn btn-green" onclick="DoTrade();" type="submit" value="Trade" />
		</form>
		
		
			
		<div id="stock-data" style="display: none;"></div>
		<?php
	} else { ?>
		<div id="content" style="height: 10px;"><p style="text-align: center;">The Market is now closed. The Market is open from 9:30 AM - 3:30 PM on all weekdays except public holidays. Happy Trading!</p></div>
	<?php } ?>
	<?php AjaxGet(); Load_Anim(); ?>
	<script>
		ReGet = 1;
		max_amount = 0;

		dSD = $('#stock-data');
		dSS = $('#stock-select');
		dTSy = $('#Trade-Symbol');
		dT = $('#transaction');
		dTA = $('#transactionAmount');
		dTH = $('#transactionHeading');
		dTS = $('#transactionSubmit');
		dTyS = $('#type-select');

		ShowValue();
		function ChangeType(t) {
			stock = JSON.parse(dSD.innerHTML);
			data = "";
			switch (t) {
				case "Buy":
				case "Short Sell":
					for (i in stock) data += "<option value='"+stock[i]['symbol']+"'>"+stock[i]['name']+"</option>";
					break;
				case "Sell":	
					for (i in stock) if (stock[i]['bought_amount'] != 0) data += "<option value='"+stock[i]['symbol']+"'>"+stock[i]['name']+"</option>";
					break;
				case "Cover":
					for (i in stock) if (stock[i]['shorted_amount'] != 0) data += "<option value='"+stock[i]['symbol']+"'>"+stock[i]['name']+"</option>";
					break;
			}
			dSS.innerHTML = data;
			dTH.innerHTML = t;
			ShowValue();
		};
		function ShowValue(a) {
			a = a || dSS.value;
			if (!a) {
				dTSy.innerHTML = "Nothing To Show Here!";
				dTA.disabled = true;
				dTS.disabled = true;
				return;
			}
			dTA.disabled = false;
			dTS.disabled = false;
			if (ReGet) AjaxGet('update/trade.php');
			else {
				p = JSON.parse(dSD.innerHTML);
				t = dTyS.value;
				switch (t) {
					case "Buy":
						max_amount = p[a]['max_buy'];
						data = "<tr><td>"+p[a]['max_buy']+"</td>";
						break; 
					case "Short":
						max_amount = p[a]['max_short'];
						data = "<tr><td>"+p[a]['max_short']+"</td>";
						break;
					case "Sell":	
						max_amount = p[a]['bought_amount'];
						data = "<tr><td>"+p[a]['bought_amount']+"</td>";
						break;
					case "Cover":
						max_amount = p[a]['shorted_amount'];
						data = "<tr><td>"+p[a]['shorted_amount']+"</td>";
						break;
				};
				data += "<td id='stock_value'>"+p[a]['value']+"</td><td id='sale_value'>"+(p[a]['value'] * 1.002).toFixed(2)+"</td>";
				dTA.value = 1;
				dTA.max = max_amount;
				dTSy.innerHTML = data;
			}
		};

		function ChangeAmount() {
			t = (dTA.value) ? parseInt(dTA.value) : 1;
			if (t < dTA.min) t = 1;
			else if (t > dTA.max) t = dTA.max;
			if (dTA.value != "") dTA.value = t;
			$('#sale_value').innerHTML = (t * parseFloat($('#stock_value').innerHTML) * 1.002).toFixed(2);
		};
		
		function DoTrade() {
			if (!dT.checkValidity()) return;
			AjaxGet("dotrade.php?type=" + dTyS.value + "&symbol=" + dSS.value + "&amount=" + dTA.value);
		};

		function Ajax_Success(a, b, c) {
			if (a == 'update/trade.php') {
				dSD.innerHTML = c.substring(5, c.indexOf("</div>"));
				ReGet = 0;
				setTimeout(function() { ReGet = 1}, parseInt(c.substring(c.indexOf("</div>") + 6)) * 1000);
				ShowValue();				
			} else {
				alert(c);
				ReGet = 1;
				AjaxGet('update/trade.php');
			}
		};
	</script>
</body>
</html>