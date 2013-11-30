<?php
require_once("includes/global.php");
require_once("includes/sanitize.php");

	metadetails();	
 ?>
</head>
<body>
<?php if (isset($_SESSION['id'])) require_once("includes/nav.php"); ?>
	<div id="content">
		<br/><button id="rankingsRefresh" style="float: right; margin-right: 10px;" class="btn btn-green" onclick="updateRankings()">Refresh</button><br/>
		<div id="rankings_col1">	
			<h2>Today's Leaders</h2><br/>
			<table id="daywinners">
				<thead><tr><th>Rank</th><th></th><th>Name</th><th>Today's Gain</th></tr></thead>
				<tbody id="daywinnersBody">
				<?php
					$sql = "SELECT `id`, `name`,  `market_val` + `liq_cash` - `day_worth` AS tot FROM `player` WHERE `rank` <> 0 ORDER BY `tot` DESC LIMIT 10";
					$resultset = $mysqli->query($sql) or die(mysql_error());
					$i = 0;
					while ($player = $resultset->fetch_assoc()){
						$i = $i+1;
						echo "<tr><td>".$i."</td><td><img src='https://graph.facebook.com/".$player['id']."/picture' style='width:30px; height:30px; vertical-align:middle' /></td><td>".$player['name']."</td><td>".addarrow($player['tot'])."</td></tr>";
					}
				?>
				</tbody>
			</table><br/><br/>
			<h2>Leaderboard</h2><br/>
			<table id="overallTable">
				<thead><tr><th>Rank</th><th></th><th>Name</th><th>Net Worth</th></tr></thead>
				<tbody id="overallTableBody">
				<?
					$sql = "SELECT `id`, `name`,  `market_val` + `liq_cash` AS tot FROM `player` WHERE `rank` <> 0 ORDER BY `tot` DESC LIMIT 20";
					$resultset = $mysqli->query($sql) or die(mysql_error());
					$i = 0;
					while ($player = $resultset->fetch_assoc()){
						$i = $i+1;
						echo "<tr><td>".$i."</td><td><img src='https://graph.facebook.com/".$player['id']."/picture' style='width:30px; height:30px; vertical-align:middle' /></td><td>".$player['name']."</td><td>".ininr($player['tot'])."</td></tr>";
					}
				?>	
				</tbody>
			</table>
		</div>
		<div id="rankings_col2">
			<h2>This Week's Leaders</h2><br/>
			<table id="weekwinners">
				<thead><tr><th>Rank</th><th></th><th>Name</th><th>Week's Gain</th></tr></thead>
				<tbody id="weekwinnersBody">
				<?php
					$sql = "SELECT `id`, `name`,  `market_val` + `liq_cash` - `week_worth` AS tot FROM `player` WHERE `rank` <> 0 ORDER BY `tot` DESC LIMIT 10";
					$resultset = $mysqli->query($sql) or die(mysql_error());
					$i = 0;
					while ($player = $resultset->fetch_assoc()){
						$i = $i+1;
						echo "<tr><td>".$i."</td><td><img src='https://graph.facebook.com/".$player['id']."/picture' style='width:30px; height:30px; vertical-align:middle' /></td><td>".$player['name']."</td><td>".addarrow($player['tot'])."</td></tr>";
					}						
				?>	
				</tbody>
			</table>
		</div>
	</div>

	<script>
		<? if (!isset($_SESSION['id']))require_once("js/global.js"); ?>
		pr = $("#rankingsRefresh");
		oTB = $('overallTableBody');
		dWB = $('#daywinnersBody');
		wWB = $('#weekwinnersBody');
		function updateRankings() {
    		AjaxGet('update/rankings.php');
			pr.className = pr.className.replace(" btn-green",""); pr.disabled = true;
	    	setTimeout(function() { pr.className = pr.className + " btn-green"; pr.disabled = false; }, 30000);		    		
		};
		function Ajax_Success(a, b, c) {
			overall_ranks = JSON.parse(c.substring(23, c.indexOf('</div')));
			data = "";
			if (overall_ranks.length) for (i in overall_ranks) data += "<tr><td>" + overall_ranks[i]['rank']+"</td><td><img src='https://graph.facebook.com/"+overall_ranks[i]['id']+"/picture' style='width:30px; height:30px; vertical-align:middle' /></td><td>"+overall_ranks[i]['name']+"</td><td>"+overall_ranks[i]['tot']+"</td></tr>";
			else data = "No Data Yet!";
			oTB.innerHTML = data;
			c = c.substring(c.indexOf("</div>") + 27, c.length);
			daily_ranks = JSON.parse(c.substring(0, c.indexOf('</div')));
			data = "";
			if (daily_ranks.length) for (i in daily_ranks) data += "<tr><td>" + daily_ranks[i]['rank']+"</td><td><img src='https://graph.facebook.com/"+daily_ranks[i]['id']+"/picture' style='width:30px; height:30px; vertical-align:middle' /></td><td>"+daily_ranks[i]['name']+"</td><td>"+daily_ranks[i]['tot']+"</td></tr>";
			else data = "No Data Yet!";
			dWB.innerHTML = data;
			c = c.substring(c.indexOf("</div>") + 28, c.length);
			weekly_ranks = JSON.parse(c.substring(0, c.indexOf('</div')));
			data = "";
			if (weekly_ranks.length) for (i in weekly_ranks) data += "<tr><td>" + weekly_ranks[i]['rank']+"</td><td><img src='https://graph.facebook.com/"+weekly_ranks[i]['id']+"/picture' style='width:30px; height:30px; vertical-align:middle' /></td><td>"+weekly_ranks[i]['name']+"</td><td>"+weekly_ranks[i]['tot']+"</td></tr>";
			else data = "No Data Yet!";
			wWB.innerHTML = data;
		};
	</script>
	<?php AjaxGet(); Load_Anim(); ?>
</div>
</body>
</html>