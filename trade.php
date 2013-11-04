<?php
require_once("includes/global.php");
require_once("includes/sanitize.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0) || ($debug_status == 1 && $trade_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
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
		if ($symbol) for ($i = 0; $symbols[i] && $symbols[i]['symbol'] != $symbol; $i++);
		if ($symbols[$i] && $symbols[$i]['symbol'] == $symbol) {
			if ($type == "Sell" && $symbols[$i]['bought_amount'] == 0) $symbol = "";
			else if ($type == "Cover" && $symbols[$i]['shorted_amount'] == 0) $symbol = "";
		} else $symbol = "";
		
	} else $mtime = false;
	metadetails();
?>

</head>
<body>
	<div id="banner"></div>
	<?php Menu(); if ($mtime) { ?>

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
			<input id="transactionSubmit" onclick="DoTrade();" type="submit" value="Trade" />
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
		setTimeout(function() { ReGet = 1; }, 15000);
		ShowValue();

		function ChangeType(t, u) {
			stock = JSON.parse(document.getElementById('stock-data').innerHTML);
			data = "";
			switch (t) {
				case "Buy":
				case "Short":
					for (i in stock) data += "<option value='"+stock[i]['symbol']+"'>"+stock[i]['name']+"</option>";
					break;
				case "Sell":	
					for (i in stock) if (stock[i]['bought_amount'] != 0) data += "<option value='"+stock[i]['symbol']+"'>"+stock[i]['name']+"</option>";
					break;
				case "Cover":
					for (i in stock) if (stock[i]['shorted_amount'] != 0) data += "<option value='"+stock[i]['symbol']+"'>"+stock[i]['name']+"</option>";
					break;
			}
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
						max_amount = p[i]['max_buy'];
						data = "<tr><td>"+p[i]['max_buy']+"</td>";
						break; 
					case "Short":
						max_amount = p[i]['max_short'];
						data = "<tr><td>"+p[i]['max_short']+"</td>";
						break;
					case "Sell":	
						max_amount = p[i]['bought_amount'];
						data = "<tr><td>"+p[i]['bought_amount']+"</td>";
						break;
					case "Cover":
						max_amount = p[i]['shorted_amount'];
						data = "<tr><td>"+p[i]['shorted_amount']+"</td>";
						break;
				};
				data += "<td id='stock_value'>"+p[i]['value']+"</td><td id='sale_value'>"+(p[i]['value'] * 1.002).toFixed(2)+"</td>";
				ta = document.getElementById('transactionAmount');
				ta.value = 1;
				ta.max = max_amount;
				document.getElementById('Trade-Symbol').innerHTML = data; 
			}
		};

		function ChangeAmount() {
			p = document.getElementById('transactionAmount');
			t = (p.value == "") ? 1 : parseInt(p.value);
			if (t < p.min) t = 1;
			else if (t > p.max) t = p.max;
			p.value = t;
			document.getElementById('sale_value').innerHTML = (t * parseFloat(document.getElementById('stock_value').innerHTML) * 1.002).toFixed(2);
		};
		
		function DoTrade() {
			type = document.getElementById("type-select").value;
			symbol = document.getElementById("stock-select").value;
			amount = document.getElementById("transactionAmount").value;
			AjaxGet("dotrade.php?type=" + type + "&symbol=" + symbol + "&amount=" + amount);
		};

		function Ajax_Success(a, b, c) {
			if (a == 'updatetrade.php') {
				document.getElementById('stock-data').innerHTML = c;
				ReGet = 0;
				setTimeout(function() { ReGet = 1}, 15000);
				ShowValue();				
			} else {
				alert(c);
				ReGet = 1;
				AjaxGet('updatetrade.php');
			}
		}
	</script>
</body>
</html>
