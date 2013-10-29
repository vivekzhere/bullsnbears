<?php
require_once("includes/global.php");
?>

<div>
<div id="leaderboard">
<?php		  // Total Ranking
			$sql = "select id, name,  market_val+liq_cash as tot from player where rank <> 0 order by tot desc limit 20";
			$resultset = $mysqli->query($sql) or die(mysql_error());
			$out = "<div style=\"display: inline-block;\"><h2>Leaderboard</h2><br/><table id=\"overallTable\">
		<tr>
			<th>Rank</th><th></th><th>Name</th><th>Net Worth</th>
		</tr>"; $i = 0;
			while($player = $resultset->fetch_assoc()){
				$i = $i+1;
				$out .= "<tr>";
				$out .="<td>{$i}</td>";
				$player['tot'] = ininr($player['tot']);
				$out .= "<td><img src=\"https://graph.facebook.com/{$player['id']}/picture\" style=\"width:30px;height:30px;vertical-align:middle\" /> &nbsp;</td><td style=\"text-align:left\"> {$player['name']}</td>";
				$out .= "<td>{$player['tot']}</td>";
				$out .= "</tr>";
			}
			$out .= "</table></div><br/><br/>";
			echo $out;
		?>
	
	</div><!-- Rankings -->
        <div id="winners" >
		<?php   // Today's Winners
			$sql = "select id, name,  market_val+liq_cash-day_worth as tot from player where rank <> 0 order by tot desc limit 10";
			$resultset = $mysqli->query($sql) or die(mysql_error());
			$out = "<h2>Today's Leaders</h2><br/><table id=\"daywinners\">
		<tr>
			<th>Rank</th><th></th><th>Name</th><th>Today's Gain</th>
		</tr>"; $i = 0;
			while($player = $resultset->fetch_assoc()) {
				$i = $i+1;
				$out .= "<tr>";
				$out .="<td>{$i}</td>";
				$player['tot'] = ($player['tot']==0)?addarrow(0) : addarrow($player['tot']);
				$out .= "<td><img src=\"https://graph.facebook.com/{$player['id']}/picture\" style=\"width:30px;height:30;vertical-align:middle\" /> &nbsp;</td><td style=\"text-align:left\">  {$player['name']}</td>";
				$out .= "<td>{$player['tot']}</td>";
				$out .= "</tr>";
			}
			$out .= "</table><br/><br/>";
			echo $out;
		?>
		
				<?php  // Weekly Winners
			$sql = "select id, name,  market_val+liq_cash-week_worth as tot from player where rank <> 0 order by tot desc limit 10";
			$resultset = $mysqli->query($sql) or die(mysql_error());
			$out = "<h2>This Week's Leaders</h2><br/><table id=\"weekwinners\">
		<tr>
			<th>Rank</th><th></th><th>Name</th><th>Week's Gain</th>
		</tr>"; $i = 0;
			while($player = $resultset->fetch_assoc()){
				$i = $i+1;
				$out .= "<tr>";
				$out .="<td>{$i}</td>";
				$player['tot'] = ($player['tot']==0)?addarrow(0) : addarrow($player['tot']);			
				$out .= "<td><img src=\"https://graph.facebook.com/{$player['id']}/picture\" style=\"width:30px;height:30;vertical-align:middle\" /> &nbsp;</td><td style=\"text-align:left\"> {$player['name']}</td>";
				$out .= "<td>{$player['tot']}</td>";
				$out .= "</tr>";
			}
			$out .= "</table>";
			echo $out;
		?>
        </div><!-- winners -->
</div>
