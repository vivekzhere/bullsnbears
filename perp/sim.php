<?php
// To simulate game from history table

if($_GET['key']=="Ti1lLSK65bds")
{
	require_once("../includes/global.php");
	$sql="delete from bought_stock";
	echo $sql.";<br />";
	mysql_query($sql) or die(mysql_error());
	$sql="delete from short_sell";
	echo $sql.";<br />";
	mysql_query($sql) or die(mysql_error());
	$sql="update player set liq_cash=2500000, market_val=0, rank=0, day_worth=2500000, week_worth=2500000, short_val=0";
	echo $sql.";<br /><br />";
	mysql_query($sql) or die(mysql_error());
	
	//$sql= "select * from history WHERE p_id=100001180026427 ORDER BY t_time";
	$sql= "select * from history ORDER BY t_time";
	$results = mysql_query($sql) or die(mysql_error());
	$i=0;
	while($history = mysql_fetch_assoc($results))
	{
		$i++;
		//print_r($history);		
		//echo "<br />";
				
		if($history['t_type'] == 'b')
		{							
			$sql_player = "update player set liq_cash = liq_cash-({$history['amount']}*{$history['value']}*1.002), rank='1' where id = '{$history['p_id']}'";
			$sql_stock = "insert into bought_stock values( '{$history['p_id']}' , '{$history['symbol']}' , '{$history['amount']}', '{$history['value']}' ) on duplicate key update avg = ((avg*amount)+({$history['value']}*{$history['amount']}))/(amount+{$history['amount']}), amount = amount+{$history['amount']}";		
			
		}
		else if($history['t_type'] == 'ss')
		{
			$sql_player = "update player set liq_cash = liq_cash-({$history['amount']}*{$history['value']}*0.002), rank='1' where id = '{$history['p_id']}'";
			$sql_stock = "insert into short_sell values( '{$history['p_id']}' , '{$history['symbol']}' , '{$history['amount']}', '{$history['value']}' , '{$history['t_time']}' ) on duplicate key update val = ((val*amount)+({$history['value']}*{$history['amount']}))/(amount+{$history['amount']}), amount = amount+{$history['amount']}";	
		}
		else if($history['t_type'] == 's')
		{	
			$sql_player = "update player set liq_cash = liq_cash+({$history['amount']}*{$history['value']}*0.998), rank='1' where id = '{$history['p_id']}'";
			$sql_stock = "update bought_stock set amount=amount-'{$history['amount']}' where id='{$history['p_id']}' and symbol='{$history['symbol']}'";
		}
		else if($history['t_type'] == 'c')
		{
			$sql="select val from short_sell where id='{$history['p_id']}' and symbol='{$history['symbol']}'";
			$shortres=mysql_query($sql) or die(mysql_error());
			$shortval = mysql_fetch_assoc($shortres);
			//echo $shortval['val']." ";
			$liqchange = ($shortval['val']-$history['value'])*$history['amount'];
			$liqchange -= $history['amount']*$history['value']*0.002;
			//echo $liqchange;
			$sql_player = "update player set liq_cash =liq_cash+{$liqchange}, rank='1' where id = '{$history['p_id']}'";
			$sql_stock = "update short_sell set amount=amount-{$history['amount']} where id='{$history['p_id']}' and symbol='{$history['symbol']}'";
		}
		echo $sql_player.";<br />".$sql_stock.";<br /><br />";
		mysql_query($sql_player) or die(mysql_error());
		mysql_query($sql_stock) or die(mysql_error());
		$sql="delete from bought_stock where amount=0";
		echo $sql.";<br />";
		mysql_query($sql) or die(mysql_error());
		$sql="delete from short_sell where amount=0";
		echo $sql.";<br />";
		mysql_query($sql) or die(mysql_error());
		
		/*$sql ="select liq_cash from player where id =100001180026427";
		$temp=mysql_query($sql) or die(mysql_error());
		print_r(mysql_fetch_assoc($temp));
		echo "<br />";*/
	}
	
	echo $i;
}
?>