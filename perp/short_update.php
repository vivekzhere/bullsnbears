<?php
	if($_GET['key']=="Ti1lLSK65bds")
	{
		require_once("../includes/global.php");
		//to sell shorted stock
		//to be run after market closes
		$sql = "select symbol from symbols";
		$result = mysql_query($sql);
		$n = mysql_num_rows($result);
		//$sql = "select symbols.symbol, a.value from symbols, (select * from stockval order by time_stamp desc limit $n) as a where symbols.symbol = a.symbol";
		$sql="select stockval.symbol as symbol, value from stockval, (select symbol, max(time_stamp) as lt from stockval group  by symbol)  as u, symbols where stockval.symbol=u.symbol  and stockval.time_stamp=u.lt and stockval.symbol = symbols.symbol order by stockval.symbol";
	
		$stocks = mysql_query($sql);
		$value = array();
		while($stock = mysql_fetch_array($stocks)){
			$value[$stock['symbol']] = $stock['value'];
		}
		$day = strftime("%Y-%m-%d",strtotime("-".$short_sell_days." days"));
		$sql = "select id, liq_cash from player";
		$players = mysql_query($sql) or die(mysql_error());
		$player = array();
		while($play = mysql_fetch_assoc($players)){
			$player[$play['id']] = $play['liq_cash'];
		}
		foreach($player as $id => $cash){
			$money = $cash;
			$sql = "select symbol, day, amount, val from short_sell where id = '$id' and day <= '$day'";
			$shorts = mysql_query($sql) or die(mysql_error());
			
			$hsql = "select market_val from player where id='$id'"; 
			$hmvals = mysql_query($hsql) or die(mysql_error());
			$hmval = mysql_fetch_assoc($hmvals);
			$mval = $hmval['market_val'];
			
			while($short = mysql_fetch_assoc($shorts)){
			       	
			       	
			       	
			       	
			       	$tm = strftime("%Y-%m-%d %H:%M:%S", time());
				$hsql = "insert into history values('$tm', '$id', 'c', '{$short['symbol']}', '-1', '{$short['amount']}',  '{$value[$short['symbol']]}', '$mval', '$money')";
				mysql_query($hsql) or die(mysql_error());
			
				$sql = "delete from short_sell where id = '$id' and symbol = '".$short['symbol']."' and day = '".$short['day']."'";
				$resultset = mysql_query($sql) or die(mysql_error());
				if($resultset){
					$money = $money - ceil($short['amount'] * 0.002 *$value[$short['symbol']]) + $short['amount']*($short['val']-$value[$short['symbol']]);
					$mval = $mval - $short['amount']*($short['val']-$value[$short['symbol']]);
				}
			}
			
			
		$sql = "update player set liq_cash = '$money', short_val=0 where id = '$id'";
		$resultset = mysql_query($sql) or die(mysql_error());
		echo(date("Y-m-d",time()));
		}
	}
	else
	  header('Location:../home.php');
?>
