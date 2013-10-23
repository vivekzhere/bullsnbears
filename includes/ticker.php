<?php
require_once("connection.php");
	echo <<<CONTENT
		<div id="ticker" style="height: 25px;" onclick="ToggleTicker();"><marquee onmouseleave="this.start();" onmouseover="this.stop();" id="ticker-text"><ul style="float: left; margin: 0; padding-right: 15px;">
CONTENT;
	$sql = "select `symbol`, `value`, `change` from `stocks` order by `symbol`";
	$stocks = $mysqli->query($sql);
	$out = "";
	while ($stock = $stocks->fetch_assoc()) {
		$symbol = $stock['symbol'];
		$value = $stock['value'];
		$change = $stock['change'];
		$out .= '<li style="display: inline-block;"><span class="ticker-symbol">'.$symbol.'</span><span class="ticker-value">'.$value.'</span><span class="ticker-change">'.addarrow($change).'</span></li>';
	}
	echo $out;
	echo <<<CONTENT
		</ul></marquee></div>
			<script>
				function ToggleTicker() {
					var Ticker = document.getElementById('ticker'), x;
					x = Ticker.style.height;
					if (x == "25px") { Ticker.style.bottom = "-25px"; Ticker.style.height = "24px"; }
					else { Ticker.style.height = "25px"; Ticker.style.bottom = "0px"; }
				}
			</script>
CONTENT;
?>
