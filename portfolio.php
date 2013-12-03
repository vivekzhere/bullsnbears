<?php
require_once("includes/global.php"); 
require_once("includes/sanitize.php"); 
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}
	metadetails();
?>
</head>
<body>
	<?php require_once("includes/nav.php"); ?>
	<div id="content">
		<div id="portfolio">
			<h2 align="center" id="type">Bought Stocks</h2>
			<div style="height: 20px">
				<button id="showOther" class="btn btn-green" onclick="updatePortfolio(1)">Show Shorted Stocks</button>
				<button id="portfolioRefresh" class="btn btn-green" onclick="updatePortfolio(0)">Refresh</button>
			</div><br/>
			<table id="portfolioTable" class="box box1 boughtTable">
				<thead><tr>
					<th>Name</th><th>Amount</th><th>Avg. Bought Price</th><th>Live Price</th><th>Investment Value</th><th>Latest Value</th><th>Brokerage</th><th>Overall Gain</th><th></th>
				</tr></thead>
				<tbody>
				<?php
					$results = $mysqli->query("SELECT b.`id`, b.`symbol`, b.`amount`, b.`avg`, s.`name`, s.`value` FROM `bought_stock` b, `stocks` s WHERE b.`symbol` = s.`symbol` AND b.`id` = '{$_SESSION['id']}';");
					if ($results->num_rows != 0) while ($result = $results->fetch_assoc()) echo "<tr onclick=window.location.href='trade.php?type=Sell&symbol={$result['symbol']}'><td>{$result['name']}</td><td>{$result['amount']}</td><td>{$result['avg']}</td><td>{$result['value']}</td><td>".number_format($result['avg'] * $result['amount'], 2, '.', '')."</td><td>".number_format($result['value'] * $result['amount'], 2, '.', '')."</td><td>".number_format($result['avg'] * $result['amount'] * 0.002, 2, '.', '')."</td><td>".addarrow(number_format((($result['value'] * 0.998) - ($result['avg'] * 1.002)) * $result['amount'], 2, '.', ''))."</td><td class='btn-red table-btn'>Sell</td></tr>";
				?>
				</tbody>
			</table>
		</div>
	</div>

	<script>
	ReGet = 1;
	function updatePortfolio(a) {
		p = $('#type').innerHTML;
		if (a) {
			if (p == "Bought Stocks") AjaxGet('update/portfolio.php?t=Shorted');
			else if (p == "Shorted Stocks") AjaxGet('update/portfolio.php?t=Bought');
		} else {
			if (p == "Bought Stocks") AjaxGet('update/portfolio.php?t=Bought');
			else if (p == "Shorted Stocks") AjaxGet('update/portfolio.php?t=Shorted');
		}
		pr = $("#portfolioRefresh");
		if (pr) { pr.className = pr.className.replace(" btn-green",""); pr.disabled = true; }
	};

	function Ajax_Success(a, b, c) {
		Portfolio = JSON.parse(c.substring(5, c.indexOf("</div>")));
		pT = $('#portfolioTable');
		if (a == "update/portfolio.php?t=Bought") {
			$('#type').innerHTML = "Bought Stocks";
			$('#showOther').innerHTML = "Show Shorted Stocks";
			if (Portfolio.length == 0) data = 'You dont have any Bought Stocks!';
			else {
				data = "<thead><tr><th>Name</th><th>Amount</th><th>Avg. Bought Price</th><th>Live Price</th><th>Investment Value</th><th>Latest Value</th><th>Brokerage</th><th>Overall Gain</th><th></th></tr></thead><tbody>";
				for (i in Portfolio) data += "<tr onclick=window.location.href='lookup.php?symbol="+Portfolio[i]['symbol']+"'><td>"+Portfolio[i]['name']+'</td><td>'+Portfolio[i]['amount']+'</td><td>'+Portfolio[i]['avg']+"</td><td>"+Portfolio[i]['value']+"</td><td>"+Portfolio[i]['invested_value']+"</td><td>"+Portfolio[i]['present_value']+"</td><td>"+Portfolio[i]['brokerage']+"</td><td>"+Portfolio[i]['gain']+"</td><td onclick=window.location.href='trade.php?type=Sell&symbol="+Portfolio[i]['symbol']+"' class='btn-red table-btn'>Sell</td></tr>";
				data += "</tbody>";
			}
			pT.className = pT.className.replace(" shortedTable"," boughtTable");
			pr.className = pr.className.replace(" btn-green","");
		} else {
			$('#type').innerHTML = "Shorted Stocks";
			$('#showOther').innerHTML = "Show Bought Stocks";
			if (Portfolio.length == 0) data = 'You dont have any Shorted Stocks!';
			else {
				data = "<thead><tr><th>Name</th><th>Amount</th><th>Avg. Sold Price</th><th>Live Price</th><th>Total Sold Value</th><th>Brokerage</th><th>Overall Gain</th><th></th></tr></thead><tbody>";
				for (i in Portfolio) data += "<tr onclick=window.location.href='lookup.php?symbol="+Portfolio[i]['symbol']+"'><td>"+Portfolio[i]['name']+'</td><td>'+Portfolio[i]['amount']+'</td><td>'+Portfolio[i]['val']+"</td><td>"+Portfolio[i]['value']+"</td><td>"+Portfolio[i]['sold_value']+"</td><td>"+Portfolio[i]['brokerage']+"</td><td>"+Portfolio[i]['gain']+"</td><td onclick=window.location.href='trade.php?type=Cover&symbol="+Portfolio[i]['symbol']+"' class='btn-red table-btn'>Cover</td></tr>";
				data += "</tbody>";
			}
			pT.className = pT.className.replace(" boughtTable"," shortedTable");
		};
		pr = $("#portfolioRefresh");
		if (pr) {
			pr.className = pr.className.replace(" btn-green","");
			pr.disabled = true;
			setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, parseInt(c.substring(c.indexOf("</div>") + 6)) * 1000);
		}
		pT.innerHTML = data;
	};

	function Ajax_Failure(a, b, c) {
		alert("Something went wrong! Try Again Later");
		pr = $("#portfolioRefresh");
		if (pr) {
			pr.className = pr.className.replace(" btn-green","");
			pr.disabled = true;
			setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);
		}
	};
	</script>

	<?php require_once("includes/ticker.php"); AjaxGet(); Load_Anim(); ?>
</body>
</html>