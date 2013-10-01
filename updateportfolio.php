<?php require_once("includes/global.php");
 if(!isset($_SESSION['username'])) header("Location: index.php");
  if(isset($_GET['t'])) $t = $_GET['t']; 
  else $t = "buy";
?>
<div>
	<h2><?php if($t=="short") echo "Shorted"; else echo "Bought"; ?> Stocks</h2>
	<button id="portfolioShow" class="shinybutton" onclick="updatePortfolio('<?php if ($t=="short") echo("bought"); else echo("short"); ?>')">Show <?php if ($t=="short") echo("Bought"); else echo("Shorted"); ?> Stocks</button>
	<br/>
<?php
				$stock_value = present_value();
				$flag = 0;
				$tr = 0;
				if($t != "short"){
				$out = "";
				$out = "<table id=\"portfolioTable\">\n<tr>\n<th>Name</th><th>Amount</th><th>Avg. Bought Price</th><th>Live Price</th><th>Inv. Value</th><th>Latest Value</th><th>Brokerage</th><th>Overall Gain</th><th></th>\n</tr>";
				$sql = "select symbols.symbol, name, avg, amount from bought_stock, symbols where symbols.symbol = bought_stock.symbol and id='{$_SESSION['player_id']}'";
				$resultset = mysql_query($sql) or die(mysql_error());
				while($result = mysql_fetch_assoc($resultset)){
					$flag = 1;
					$symbol = $result['symbol'];
					$out .= "<tr onclick=\"window.location.href='lookup.php?symbol=".$result['symbol']."'\"";
					if($tr == 0){
						$out .= " class=\"altr\"";
						$tr = 1;
					}else{
						$tr = 0;
					}
					$out .= ">\n";
					//foreach($result as $r){
						$out .= "<td>{$result['name']}</td><td>{$result['amount']}</td><td>{$result['avg']}</td><td>{$stock_value[$symbol]}</td>";
					//}
					$out .= "<td>".number_format($result['avg']*$result['amount'],2,'.','')."   </td><td>".number_format($stock_value[$symbol]*$result['amount'],2,'.','')."</td><td>".number_format($result['avg']*$result['amount']*0.002,2,'.','')."</td><td>".addarrow(($stock_value[$symbol]*$result['amount']-$result['avg']*$result['amount']-$result['avg']*$result['amount']*0.002))."</td><td><form method=\"post\" action=\"trade.php?type=Sell\"><input type=\"hidden\" name=\"symbol\" value=\"{$symbol}\"><input type=\"submit\" value=\"Sell\"></form></td>";
					$out .= "\n</tr>\n";
				}
				$out .= "</table>";
				}
				if($t == "short"){
				$out = "<table>\n<tr>\n<th>Name</th><th>Amount</th><th>Avg. sold Price</th><th>Live Price</th><th>Total Sold Value</th><th>Brokerage</th><th>Profit</th><th></th>\n</tr>";
				$sql = "select short_sell.symbol, name, amount, val, day from short_sell, symbols where symbols.symbol = short_sell.symbol and id='{$_SESSION['player_id']}' order by short_sell.symbol, day asc";
				$resultset = mysql_query($sql) or die(mysql_error());
				$checkarray = array();
				while($result = mysql_fetch_assoc($resultset)){
					$flag = 1;
					$symbol = $result['symbol'];
					if(isset($checkarray[$symbol]))
					$checkarray[$symbol] += 1;
					else
					$checkarray[$symbol] = 1;
					$out .= "\t\t\t<tr onclick=\"window.location.href='lookup.php?symbol=".$result['symbol']."'\"";
					if($tr == 0){
						$out .= " class=\"altr\"";
						$tr = 1;
					}else{
						$tr = 0;
					}
					$out .= ">\n\t\t\t\t";
					//foreach($result as $r){
						$out .= "<td>{$result['name']}</td><td>{$result['amount']}</td><td>{$result['val']}</td><td>{$stock_value[$symbol]}</td>";
					//}
					if($checkarray[$symbol] == 1)
					$out .= "<td>". number_format( $result['amount']*$result['val'],2,'.','')."</td><td>".number_format( $result['amount']*$result['val']*0.002,2,'.','')."</td><td>". addarrow( ($result['val'] - $stock_value[$symbol])*$result['amount']-($result['val']*$result['amount']*0.002))."</td><td><form method=\"post\" action=\"trade.php?type=Cover\"><input type=\"hidden\" name=\"symbol\" value=\"{$symbol}\"><input type=\"submit\" value=\"Cover\"></form></td>";
					else
					$out .= "<td></td><td></td>";
					$out .= "\n\t\t\t</tr>\n";
				}
				$out .= "</table>";
				}
				if($flag == 1){
					echo $out;
				}else{
					echo "<p class=\"big\">No Stocks owned</p>";
				}
			?>
		</div>
