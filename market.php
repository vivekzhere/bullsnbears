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
		<div id="Market">
			<button id='marketRefresh' class='btn btn-green' onclick='updateMarket()'>Refresh</button><br/>
			<table id='marketTable' class="box box1">
				<thead>
					<tr><th>Symbol</th><th>Name</th><th>Price</th><th>Change %</th><th>Day High</th><th>Day Low</th><th>Year High</th><th>Year Low</th></tr>
				</thead>
				<tbody id="marketTableBody">
				<?php
					$results = $mysqli->query("SELECT * FROM `stocks` ORDER BY `symbol` ASC");
					while ($row = $results->fetch_assoc()) {
						echo "<tr onclick=window.location.href='lookup.php?symbol=".$row['symbol']."'>";
						echo "<td>".$row['symbol']."</td><td>".$row['name']."</td><td>".$row['value']."</td><td>".addarrow($row['change_perc'])."</td><td>".$row['day_high']."</td><td>".$row['day_low']."</td><td>".$row['week_high']."</td><td>".$row['week_low']."</td></tr>";
					}
				?>
				</tbody>
			</table>
		</div>
	</div>


	<script>
		pr = $("#marketRefresh");
		if (pr) { pr.className = pr.className.replace(" btn-green",""); pr.disabled = true; }
		setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);

		function updateMarket() {
			AjaxGet('update/market.php');
			pr = document.getElementById("marketRefresh");
			if (pr) { pr.className = pr.className.replace(" btn-green",""); pr.disabled = true; }
		}
		
		function Ajax_Success(a, b, c) {
			var stocks = JSON.parse(c.substring(5, c.indexOf("</div>")));
			if (stocks.length > 0) {
				data = "";
				for (i in stocks) {
					data += "<tr onclick=window.location.href='lookup.php?symbol="+stocks[i]['symbol']+"'>";
					data += "<td>"+stocks[i]['symbol']+"</td><td>"+stocks[i]['name']+"</td><td>"+stocks[i]['value']+"</td><td>"+stocks[i]['change_perc']+"</td><td>"+stocks[i]['day_high']+"</td><td>"+stocks[i]['day_low']+"</td><td>"+stocks[i]['week_high']+"</td><td>"+stocks[i]['week_low']+"</td></tr>";
				}
				document.getElementById('marketTableBody').innerHTML = data;
			}
			pr = document.getElementById("marketRefresh");
			if (pr) {
				pr.className = pr.className.replace(" btn-green","");
				pr.disabled = true;
				setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, parseInt(c.substring(c.indexOf("</div>") + 6)) * 1000);
			}
		}

		function Ajax_Failure(a, b, c) {
			pr = document.getElementById("marketRefresh");
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