<?php
require_once("includes/global.php");

	$sql = "SELECT `id`, `name`,  `market_val` + `liq_cash` AS tot FROM `player` WHERE `rank` <> 0 ORDER BY `tot` DESC LIMIT 20";
	$resultset = $mysqli->query($sql) or die(mysql_error());
	$ranks = array();
	$i = 0;
	while ($player = $resultset->fetch_assoc()){
		$i = $i+1;
		$player['rank'] = $i;
		$player['tot'] = ininr($player['tot']);
		$ranks[] = $player;
	}
	echo "<div id=\"OverallRanks\">".json_encode($ranks)."</div>";

	$sql = "SELECT `id`, `name`,  `market_val` + `liq_cash` - `day_worth` AS tot FROM `player` WHERE `rank` <> 0 ORDER BY `tot` DESC LIMIT 10";
	$resultset = $mysqli->query($sql) or die(mysql_error());
	$i = 0;
	$ranks = array();
	while($player = $resultset->fetch_assoc()) {
		$i = $i+1;
		$player['rank'] = $i;
		$player['tot'] = addarrow($player['tot']);
		$ranks[] = $player;
	}
	echo "<div id=\"DailyRanks\">".json_encode($ranks)."</div>";	
	
	$sql = "SELECT `id`, `name`,  `market_val` + `liq_cash` - `week_worth` AS tot FROM `player` WHERE `rank` <> 0 ORDER BY `tot` DESC LIMIT 10";
	$resultset = $mysqli->query($sql) or die(mysql_error());
	$i = 0;
	$ranks = array();
	while($player = $resultset->fetch_assoc()){
		$i = $i+1;
		$player['rank'] = $i;
		$player['tot'] = addarrow($player['tot']);
		$ranks[] = $player;
	}
	echo "<div id=\"WeeklyRanks\">".json_encode($ranks)."</div>";	
?>