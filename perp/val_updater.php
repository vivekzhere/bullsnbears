<?php header('Refresh: 120');
	require_once("../includes/global.php");
	include("./stock_update.php");
	$mt = strftime("%H", time());
	$mt_m = strftime("%M", time());
	$mt_d = strtolower(strftime("%A",time())); 
	if($_GET['key']=="Ti1lLSK65bds")
	{
	if(($mt > $start_time ||($mt == $start_time && $mt_m >= $start_time_min)) && $mt_d != "sunday" && $mt_d != "saturday" && ($mt < $end_time || ($mt ==$end_time && $mt_m <= $end_time_min)))
	{	
		echo date("Y-m-d H:i:s");		
		//to update market_val
		//to be run after market closes
		$sql = "select symbol from symbols";
		$result = mysql_query($sql);
		$n = mysql_num_rows($result);
		//$sql = "select symbols.symbol, a.value from symbols, (select * from stockval order by time_stamp desc limit $n) as a where symbols.symbol = a.symbol";
		$sql="select stockval.symbol as symbol, value from stockval, (select symbol, max(time_stamp) as lt from stockval group  by symbol)  as u, symbols where stockval.symbol=u.symbol  and stockval.time_stamp=u.lt and stockval.symbol = symbols.symbol order by stockval.symbol";
		$stocks = mysql_query($sql);
		$value = array();
		while($stock = mysql_fetch_array($stocks))
		{
			$value[$stock['symbol']] = $stock['value'];
		}
		$sql = "select id, liq_cash from player";
		$players = mysql_query($sql) or die(mysql_error());
		$player = array();
		while($play = mysql_fetch_assoc($players))
		{
			$player[$play['id']] = $play['liq_cash'];
		}
		foreach($player as $id => $liq_cash)
		{
			$sql="select * from player where id='$id'";
			$pdetails = mysql_query($sql) or die(mysql_error());
			$pdetail = mysql_fetch_assoc($pdetails);
			$shrtval=$pdetail['short_val'];
			$money=$pdetail['liq_cash'];
			$mval=$pdetail['market_val'];
			$sql="select * from schedule where id='$id'";
			$pschedules = mysql_query($sql) or die(mysql_error());
			while($pschedule = mysql_fetch_assoc($pschedules))
			{
				$passet=$money+$mval;
				if( ( ($pschedule['scheduled_price'] >= $value[$pschedule['symbol']] && $pschedule['flag']=='l') || ($pschedule['scheduled_price'] <= $value[$pschedule['symbol']] && $pschedule['flag']=='g') ) && ($pschedule['pend_no_shares']!=0 ) )
				{
					if($pschedule['transaction_type']=="b")		//buy
					{
						$sql="select * from bought_stock where id = '$id' and symbol = '{$pschedule['symbol']}'";
						$bstocks = mysql_query($sql) or die(mysql_error());
						if(mysql_num_rows($bstocks)>0)
						{
							$bstock = mysql_fetch_array($bstocks);
							$n = $bstock['amount'];
						}
						else
						{
							$n=0;	
						}
						$max=min(floor(($money-($shrtval/4))/(1.002*$value[$pschedule['symbol']])),floor($passet/(6*(1.002*$value[$pschedule['symbol']]))) - $n);
						if($max>0)
						{
							if($max>$pschedule['pend_no_shares'])
							{
								$pendstock=0;
							}
							else
							{
								$pendstock=$pschedule['pend_no_shares']-$max;
							}
							$donestocks=$pschedule['pend_no_shares']-$pendstock;
							if(mysql_num_rows($bstocks) == 0)
							{
								$sql = "insert into bought_stock values( '$id' , '{$pschedule['symbol']}' , '{$donestocks}', '{$value[$pschedule['symbol']]}' )";
							}
							else
							{
								$newno = $bstock['amount'] + $donestocks;
								$avg = (($bstock['amount'] * $bstock['avg']) + ($donestocks * $value[$pschedule['symbol']]))/$newno;
								$sql = "update bought_stock set amount = '{$newno}', avg = '{$avg}' where id = '$id' and symbol = '{$pschedule['symbol']}'";
							}
							$resultset = mysql_query($sql) or die(mysql_error());
							if($resultset)
							{
								
								$tm = strftime("%Y-%m-%d %H:%M:%S", time());
								$hsql = "insert into history values('$tm', '$id', '{$pschedule['transaction_type']}', '{$pschedule['symbol']}', '{$pschedule['skey']}', '$donestocks',  '{$value[$pschedule['symbol']]}', '$mval', '$money')";
								mysql_query($hsql) or die(mysql_error());
								
								
								
								$mval = $mval + $donestocks * $value[$pschedule['symbol']];
								$money = $money - round(($donestocks * $value[$pschedule['symbol']] * 1.002));
								$sql = "update player set liq_cash = '{$money}', rank='1' where id = '$id'";
								$resultset = mysql_query($sql) or die(mysql_error());
								$sql="update schedule set pend_no_shares = '{$pendstock}' where skey='{$pschedule['skey']}'";
								mysql_query($sql) or die(mysql_error());				
							}					
						}
					}
					else if($pschedule['transaction_type']=="ss")	//short
					{
						$sql="select * from short_sell where id = '$id' and symbol = '{$pschedule['symbol']}'";
						$bstocks = mysql_query($sql) or die(mysql_error());
						if(mysql_num_rows($bstocks)>0)
						{
							$bstock = mysql_fetch_array($bstocks);
							$n = $bstock['amount'];
						}
						else
						{
							$n=0;	
						}
						$max=min(floor( ( (4*$money)-$shrtval) / ($value[$pschedule['symbol']]*1.004) ),floor( ($passet-$shrtval) / (6* ($value[$pschedule['symbol']]*1.004)) ) - $n);
						if($max>0)
						{
							if($max>$pschedule['pend_no_shares'])
							{
								$pendstock=0;
							}
							else
							{
								$pendstock=$pschedule['pend_no_shares']-$max;
							}
							$donestocks=$pschedule['pend_no_shares']-$pendstock;
							$tme = strftime("%Y-%m-%d", time());
							if(mysql_num_rows($bstocks) == 0)
							{
								$sql = "insert into short_sell values( '$id' , '{$pschedule['symbol']}' , '{$donestocks}', '{$value[$pschedule['symbol']]}', '{$tme}' )";
							}
							else
							{
								$newno = $bstock['amount'] + $donestocks;						
								$avg = (($bstock['amount'] * $bstock['val']) + ($donestocks * $value[$pschedule['symbol']]))/$newno;
								$sql = "update short_sell set amount = '{$newno}', val = '{$avg}' where id = '$id' and symbol = '{$pschedule['symbol']}' and day ='{$tme}'";						
							}
							$resultset = mysql_query($sql) or die(mysql_error());
							if($resultset)
							{
								$tm = strftime("%Y-%m-%d %H:%M:%S", time());
								$hsql = "insert into history values('$tm', '$id', '{$pschedule['transaction_type']}', '{$pschedule['symbol']}', '{$pschedule['skey']}', '$donestocks',  '{$value[$pschedule['symbol']]}', '$mval', '$money')";
								mysql_query($hsql) or die(mysql_error());
								
								
								
								
								$shrtval=$shrtval+$donestocks * $value[$pschedule['symbol']];
								$money = $money - round(($donestocks * $value[$pschedule['symbol']] * 0.002));
								$sql = "update player set liq_cash = '{$money}', short_val = '{$shrtval}', rank='1' where id = '$id'";
								$resultset = mysql_query($sql) or die(mysql_error());					
								$sql="update schedule set pend_no_shares = '{$pendstock}' where skey='{$pschedule['skey']}'";
								mysql_query($sql) or die(mysql_error());
							}					
						}
					}
					if($pschedule['transaction_type']=="s")		//sell
					{
$sql="select * from bought_stock where id = '$id' and symbol = '{$pschedule['symbol']}'";
						$bstocks = mysql_query($sql) or die(mysql_error());
						if(mysql_num_rows($bstocks)>0)
						{
							$bstock = mysql_fetch_array($bstocks);
							$max = $bstock['amount'];
						}
						else
						{
							$max=0;	
						}				
						if($max!=0)
						{
							if($max>=$pschedule['pend_no_shares'])
							{
								
                                                                $pendstock=0;
								$newno=$max-$pschedule['pend_no_shares'];
								$donestocks=$pschedule['pend_no_shares'];
							}
							else
							{
								$pendstock=$pschedule['pend_no_shares']-$max;
								$newno=0;
								$donestocks=$max;
							}
					
							if($newno == 0)
							{
								$sql = "delete from bought_stock where id = '$id' and symbol = '{$pschedule['symbol']}'";
							}
							else
							{
								$sql = "update bought_stock set amount = '$newno' where id = '$id' and symbol = '{$pschedule['symbol']}'";
							}
							$resultset = mysql_query($sql) or die(mysql_error());
							if($resultset)
							{
								
								$resultset = mysql_query($sql) or die(mysql_error());
								$tm = strftime("%Y-%m-%d %H:%M:%S", time());
								$hsql = "insert into history values('$tm', '$id', '{$pschedule['transaction_type']}', '{$pschedule['symbol']}', '{$pschedule['skey']}', '$donestocks',  '{$value[$pschedule['symbol']]}', '$mval', '$money')";
								mysql_query($hsql) or die(mysql_error());							
								
								$mval = $mval - ($donestocks * $value[$pschedule['symbol']]);
								$money = $money + round(($donestocks * $value[$pschedule['symbol']] * 0.998));
								$sql = "update player set liq_cash = '{$money}', rank='1' where id = '$id'";
								$resultset = mysql_query($sql) or die(mysql_error());
								$sql="update schedule set pend_no_shares = '{$pendstock}' where skey='{$pschedule['skey']}'";
								mysql_query($sql) or die(mysql_error());					
							}					
						}
					}
					else if($pschedule['transaction_type']=="c")	//cover
					{
						$sql="select * from short_sell where id = '$id' and symbol = '{$pschedule['symbol']}'";
						$bstocks = mysql_query($sql) or die(mysql_error());
						if(mysql_num_rows($bstocks)>0)
						{
							$bstock = mysql_fetch_array($bstocks);
							$max = $bstock['amount'];
						}
						else
						{
							$max = 0;
						}					
						if($max!=0)
						{
							if($max>$pschedule['pend_no_shares'])
							{
								$pendstock=0;
								$newno=$max-$pschedule['pend_no_shares'];
								$donestocks=$pschedule['pend_no_shares'];
							}
							else
							{
								$pendstock=$pschedule['pend_no_shares']-$max;
								$newno=0;
								$donestocks=$max;
							}
							$tme = strftime("%Y-%m-%d", time());
							if($newno == 0)
							{
								$sql = "delete from short_sell where id = '$id' and symbol = '{$pschedule['symbol']}'";
							}
							else
							{					
								$sql = "update short_sell set amount = '{$newno}' where id = '$id' and symbol = '{$pschedule['symbol']}'";						
							}
							$resultset = mysql_query($sql) or die(mysql_error());
							if($resultset)
							{
								$tm = strftime("%Y-%m-%d %H:%M:%S", time());
								$hsql = "insert into history values('$tm', '$id', '{$pschedule['transaction_type']}', '{$pschedule['symbol']}', '{$pschedule['skey']}', '$donestocks',  '{$value[$pschedule['symbol']]}', '$mval', '$money')";
								mysql_query($hsql) or die(mysql_error());	
								
								$shrtval=$shrtval-$donestocks * $bstock['val'];
								$shortprofit = ($bstock['val'] - $value[$pschedule['symbol']]) * $donestocks;
								$money = $money - round(($donestocks * $value[$pschedule['symbol']] * 0.002)) + $shortprofit;
								$sql = "update player set liq_cash = '{$money}', short_val = '{$shrtval}', rank='1' where id = '$id'";
								$resultset = mysql_query($sql) or die(mysql_error());	
								$sql="update schedule set pend_no_shares = '{$pendstock}' where skey='{$pschedule['skey']}'";
								mysql_query($sql) or die(mysql_error());				
							}
						}
					}			
				}
			}
								
			$market_val = 0;
			$sql = "select * from bought_stock where id = '$id'";
			$playerstocks = mysql_query($sql) or die(mysql_error());
			while($playerstock = mysql_fetch_assoc($playerstocks))
			{
				$market_val += $playerstock['amount'] * $value[$playerstock['symbol']];
			}
			$sql = "select * from short_sell where id = '$id'";
			$shrtstocks = mysql_query($sql) or die(mysql_error());
			while($shrtstock = mysql_fetch_assoc($shrtstocks))
			{
				$market_val += $shrtstock['amount'] * ( $shrtstock['val']-$value[$shrtstock['symbol']] );
			}
			$market_val = round($market_val);
	
			$sql = "update player set market_val= {$market_val} where id ='$id'";
			mysql_query($sql) or die(mysql_error());
		}
	}
	}
	else
		header('Location:../home.php');
?>
