<?php require_once("includes/global.php");
	if(!isset($_SESSION['username'])) header("Location: index.php");
	echo <<<CONTENT
		<div>
			<h2>Market Shares</h2>
	 		<table id="marketsTable" class="tablesorter"> 
				<thead>
					<th>Symbol</th><th>Name</th><th>Price</th><th>Change%</th><th>Day High</th><th>Day Low</th><th>52 Wk High</th><th>52 Wk Low</th>
					<th></th>
				</thead>
				<tbody>
CONTENT;
	$sql = 'select stockval.`symbol`, symbols.`name`, stockval.`time_stamp`, stockval.`value`, stockval.`change`, stockval.`day_low`, stockval.`day_high`, stockval.`week_low`, stockval.`week_high`, stockval.`change_perc` from `stockval`, `symbols` where stockval.`symbol` = symbols.`symbol`';
	$stocks = mysql_query($sql);
	$out = "";
	$flag = 0;
	while ($stock = mysql_fetch_array($stocks)) {
		$name1 = $stock['name'];
		$symbol = $stock['symbol'];
		$value = $stock['value'];
		$change = addarrow($stock['change']);
		$ptage = round(($stock['change'] / ($stock['value'] + $stock['change'])) * 100, 2);
        $change = addarrow($stock['change']);
        $change_perc = addarrow($stock['change_perc']);
        $dhigh = $stock['day_high'];
		$dlow = $stock['day_low'];
		$whigh = $stock['week_high'];
		$wlow = $stock['week_low'];
		$out .= "<tr onclick=\"window.location.href='lookup.php?symbol=".$stock['symbol']."'\"";
		$out .= "  >\n<td>{$symbol}</td><td>{$name1}</td><td>{$value}</td><td>{$change_perc}%</td><td>{$dhigh}</td><td>{$dlow}</td><td>{$whigh}</td><td>{$wlow}</td>";
		$out .= "<td><form class=\"formbuy\" id=\"fb\" method=\"post\"   action=\"trade.php?type=Buy\"><input type=\"hidden\" name=\"symbol\" value=\"{$symbol}\"/><input type=\"submit\" value=\"Buy\" /></form><form class=\"formshort\"  method=\"post\" action=\"trade.php?type=Short\"><input type=\"hidden\" name=\"symbol\" value=\"{$symbol}\"/><input type=\"submit\" value=\"Short\"/></form></td>";
		$out .="\n</tr>\n";
	}
	echo $out;
	echo "</tbody></table>";
?>
</div>