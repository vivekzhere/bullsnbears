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
	<div id="banner"></div>
	<?php Menu(); ?>
	<div id="content">
		<div id="markets">
			<button id='marketrefresh' style='float: right; margin-right: 0;' class='button btn-green' onclick='updateMarket()'>Refresh</button>
			<br/><br/>
			<table id='marketsTable'>
				<thead>
					<tr><th>Symbol</th><th>Name</th><th>Price</th><th>Change %</th><th>Day High</th><th>Day Low</th><th>Year High</th><th>Year Low</th></tr>
				</thead>
				<tbody id="marketsTableBody">
				<?php
					$results = $mysqli->query("SELECT * FROM `stocks` ORDER BY `symbol` ASC");
					while ($row = $results->fetch_assoc()) {
						echo "<tr onclick=window.location.href='lookup.php?symbol=".$row['symbol']."'>";
						echo "<td>".$row['symbol']."</td><td>".$row['name']."</td><td>".$row['value']."</td><td>".$row['change_perc']."</td><td>".$row['day_high']."</td><td>".$row['day_low']."</td><td>".$row['week_high']."</td><td>".$row['week_low']."</td></tr>";
					}
				?>
				</tbody>
			</table>
		</div>
	</div>


	<script>
		pr = document.getElementById("marketrefresh");
		if (pr) { pr.className = pr.className.replace(" btn-green",""); pr.disabled = true; }
		setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);

		function updateMarket() {
			AjaxGet('updatemarkets.php');
			pr = document.getElementById("marketrefresh");
			if (pr) { pr.className = pr.className.replace(" btn-green",""); pr.disabled = true; }
		}
		
		function Ajax_Success(a, b, c) {
			var stocks = JSON.parse(c);
			if (stocks.length > 0) {
				data = "";
				for (i in stocks) {
					data += "<tr onclick=window.location.href='lookup.php?symbol="+stocks[i]['symbol']+"'>";
					data += "<td>"+stocks[i]['symbol']+"</td><td>"+stocks[i]['name']+"</td><td>"+stocks[i]['value']+"</td><td>"+stocks[i]['change_perc']+"</td><td>"+stocks[i]['day_high']+"</td><td>"+stocks[i]['day_low']+"</td><td>"+stocks[i]['week_high']+"</td><td>"+stocks[i]['week_low']+"</td></tr>";
				}
				document.getElementById('marketsTableBody').innerHTML = data;
			}
			pr = document.getElementById("marketrefresh");
			if (pr) {
				pr.className = pr.className.replace(" btn-green","");
				pr.disabled = true;
				setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);
			}
		}

		function Ajax_Failure(a, b, c) {
			pr = document.getElementById("marketrefresh");
			if (pr) {
				pr.className = pr.className.replace(" btn-green","");
				pr.disabled = true;
				setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);
			}
			alert("Something went wrong! Try Again Later.");
		}
	</script>
	<?php require_once("includes/ticker.php"); AjaxGet(); Load_Anim(); ?>
</body>
</html>