<div class="tickercontainer">
<div id="ticker_tape_content" class="mask">
	<ul id="ticker" class="newsticker">
 
            
            
            <?php
		$sql = "select symbol from symbols";
		$result = mysql_query($sql);
		$n = mysql_num_rows($result);
		$sql = "select symbols.symbol, a.value, a.change from symbols, (select * from stockval order by time_stamp desc limit $n) as a where symbols.symbol = a.symbol";
		$stocks = mysql_query($sql);
		$out = "";
		while($stock = mysql_fetch_array($stocks)){
			$symbol = $stock['symbol'];
			$value = $stock['value'];
			$change = $stock['change'];
			
			$out .= "<li> <span class = \"symbol\">{$symbol}</span>  <span class = \"value\">".$value."</span>  <span class = \"change\">".addarrow($change)."</span> </li>";
		}
		echo $out;
	?>

           
        </ul>
        
</div>
</div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="scripts/jquery.ticker.js" ></script>

<script type="text/javascript">
$(document).ready(function() {
    
    jQuery('#ticker').webTicker();
});
 
 
 
</script>

