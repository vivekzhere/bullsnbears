<?php require_once("includes/global.php");
	if(!isset($_SESSION['username']))
		header("Location: index.php");
?>
<?php	
	if(isset($_GET['symbol'])){
		$flag = 1;
		$symbol = $_GET['symbol'];
		$sql="select stockval.symbol, symbols.name, time_stamp, value, stockval.change, day_low, day_high, week_low, week_high from stockval, (select symbol, max(time_stamp) as lt from stockval group  by symbol)  as u, symbols where stockval.symbol=u.symbol  and stockval.time_stamp=u.lt and stockval.symbol = symbols.symbol and stockval.symbol='$symbol' order by stockval.symbol";		
		$valueset = mysql_query($sql) or die(mysql_error());
		$values = mysql_fetch_array($valueset);
		
		if(mysql_num_rows($valueset)==0) $flag=0;
		$stock_name = $values['name'];
		$value = $values['value'];
		$dhigh = $values['day_high'];
		$dlow = $values['day_low'];
		$whigh = $values['week_high'];
		$wlow = $values['week_low'];
		$change = $values['change'];
		$stock_details = "<div id=\"graph\"><img src=\"http://ichart.finance.yahoo.com/z?s=".substr(urlencode($symbol), 0, 9).".NS\"></div><br/>";
		$stock_details .="<div id=\"stock_details\">
						<h2>$stock_name</h2>
						<ul>
						<li>Day high: {$dhigh}</li>
						<li>Day low: {$dlow}</li>
						<li>52 Week High: {$whigh}</li>
						<li>52 Week Low: {$wlow}</li>
						<li>Price: {$value}</li>
						<li>Change: ".addarrow($change)."</li></ul>
						<form method=\"post\" action=\"trade.php?type=Buy\"><input type=\"hidden\" name=\"symbol\" value=\"{$symbol}\"><input type=\"submit\" value=\"Buy\"></form><form method=\"post\" action=\"trade.php?type=Short\"><input type=\"hidden\" name=\"symbol\" value=\"{$symbol}\"><input type=\"submit\" value=\"Short\"></form></div>";
		//$stock_details .= "<div id=\"graph\"><img src=\"graph.php?symbol={$symbol}\"></div>";
		  
		
	
	}
	else 
	 { $flag=0;
	   echo "<div></div>";
	 }
	 
	 if($flag==1) echo "<div>$stock_details</div>";
	 echo "$symbol hai";

?>
