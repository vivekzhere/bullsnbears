<div class="tickercontainer" style="z-index: 10100;">
	<div id="ticker_tape_content" class="mask">
		<ul id="ticker" class="newsticker">
 			<?php
				$sql = "select `symbol`, `value`, `change` from `stockval` order by `time_stamp` desc";
				$stocks = mysql_query($sql);
				$out = "";
				while ($stock = mysql_fetch_array($stocks)) {
					$symbol = $stock['symbol'];
					$value = $stock['value'];
					$change = $stock['change'];
					$out .= '<li><span class="symbol">'.$symbol.'</span>  <span class="value">'.$value.'</span>  <span class="change">'.addarrow($change).'</span></li>';
				}
				echo $out;
			?>
        </ul>
	</div>
</div>

<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="scripts/jquery.ticker.js" ></script>

<script type="text/javascript">
$(document).ready(function() {
	jQuery('#ticker').webTicker();
});
</script>