
<p id="ticker" class="marquee" onclick="ToggleTicker();">
	<?php
		require_once("connection.php");
		$sql = "SELECT message FROM `notice` WHERE player_id = 'g' OR player_id = '{$_SESSION['id']}'";		
		$messages = $mysqli->query($sql);
		$out = "";
		if ($messages->num_rows) while ($message = $messages->fetch_assoc()) $out .= "<span class='ticker-data'>".$message['message']."</span>";
		else $out = "<span class='ticker-data'>Welcome to Bulls N Bears! Hints & Expert Stock Advice might be made available on our Community Page. (fb.com/bullsnbearscommunity) Happy Trading! </span>";
		echo $out;
	?>
</p>

<script>
	Ticker = $('#ticker');
	function ToggleTicker() {
		Ticker.style.bottom = (Ticker.style.bottom == "-20px") ? "0" : "-20px";
	}
</script>


<?
/*
	echo <<<CONTENT
		<div id="ticker" onclick="ToggleTicker();"><marquee onmouseleave="this.start();" onmouseover="this.stop();" id="ticker-text"><ul style="float: left; margin: 0; padding-right: 15px;">
CONTENT;

	$sql = "select `symbol`, `value`, `change` from `stocks` order by `symbol`";
	$stocks = $mysqli->query($sql);
	$out = "";
	while ($stock = $stocks->fetch_assoc()) {
		$symbol = $stock['symbol'];
		$value = $stock['value'];
		$change = $stock['change'];
		$out .= '<li style="display: inline-block;"><span class="ticker-symbol">'.htmlspecialchars($symbol).'</span><span class="ticker-value">'.$value.'</span><span class="ticker-change">'.addarrow($change).'</span></li>';
	}
	echo $out;
	echo <<<CONTENT
		</ul></marquee></div>
			
CONTENT;

*/


?>
