<?php
if($_GET['key']=="Ti1lLSK65bds")
{
	require_once("../includes/global.php");
	$sql="select `symbol`, `value` from stockval";
	$stocks = mysql_query($sql);
	$value = array();
	while($stock = mysql_fetch_array($stocks))
	{
		$value[$stock['symbol']] = $stock['value'];
	}
	$sql = "select * from `player`";
	$players = mysql_query($sql) or die(mysql_error());
	while($player = mysql_fetch_assoc($players))
	{
		$id=$player['id'];
		$market_val = 0;
		$short_val = 0;
		$sql = "select `amount`, `symbol` from `bought_stock` where `id` = '$id'";
		$playerstocks = mysql_query($sql) or die(mysql_error());
		while($playerstock = mysql_fetch_assoc($playerstocks))
		{
			$market_val += $playerstock['amount'] * $value[$playerstock['symbol']];
		}
		$sql = "select `symbol`, `amount`, `val` from `short_sell` where `id` = '$id'";
		$shrtstocks = mysql_query($sql) or die(mysql_error());
		
		while($shrtstock = mysql_fetch_assoc($shrtstocks))
		{	
			$market_val += $shrtstock['amount'] * ( $shrtstock['val']-$value[$shrtstock['symbol']]);		
			$short_val += $shrtstock['amount'] * $value[$shrtstock['symbol']];
		}
		$market_val = round($market_val);
	
		$sql = "update `player` set `market_val` = {$market_val}, `short_val` = {$short_val} where `id` ='$id'";
		mysql_query($sql) or die(mysql_error());
		echo(date("Y-m-d",time()));
	}
}
?>