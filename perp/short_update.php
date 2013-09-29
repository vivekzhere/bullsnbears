<?php
	if($_GET['key']=="Ti1lLSK65bds")
	{
		require_once("../includes/global.php");
		$sql = "select `symbol` from symbols";
		$result = mysql_query($sql);
		$n = mysql_num_rows($result);
		$sql="select `symbol`, `value` from `stockval`";
	
		$stocks = mysql_query($sql);
		$value = array();
		while ($stock = mysql_fetch_array($stocks)) {
			$value[$stock['symbol']] = $stock['value'];
		}
		$day = strftime("%Y-%m-%d",strtotime("-".$short_sell_days." days"));
		echo $day;
		$sql = "select `id`, `liq_cash` from player";
		$players = mysql_query($sql) or die(mysql_error());
		$player = array();
		while($play = mysql_fetch_assoc($players)){
			$player[$play['id']] = $play['liq_cash'];
		}
		foreach($player as $id => $cash){
			$money = $cash;
			$sql = "select `symbol`, `day`, `amount`, `val` from `short_sell` where `id` = '$id' and `day` <= '$day'";
			$shorts = mysql_query($sql) or die(mysql_error());
			
			$hsql = "select `market_val` from player where id='$id'"; 
			$hmvals = mysql_query($hsql) or die(mysql_error());
			$hmval = mysql_fetch_assoc($hmvals);
			$mval = $hmval['market_val'];
			
			while($short = mysql_fetch_assoc($shorts)){
			       	
			       	
			       	
			       	
			       	$tm = strftime("%Y-%m-%d %H:%M:%S", time());
				$hsql = "insert into history (`t_time`, `p_id`, `t_type`, `symbol`, `skey`, `amount`, `value`, `p_mval`, `p_liqcash`) values('$tm', '$id', 'c', '{$short['symbol']}', '-1', '{$short['amount']}',  '{$value[$short['symbol']]}', '$mval', '$money')";
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
