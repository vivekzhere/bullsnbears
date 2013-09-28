<?php
	if($_GET['key']=="Ti1lLSK65bds")
	{
		require_once("../includes/global.php");
		
		// To sell all relcapita shares at today's price 320.05
			// To remove from bought stock
			// To add to liq cash = liqcash + amount*320.05*1.002 ; marketval = markvetval -amount*320.05
		$sql = "select * from bought_stock where symbol='STER.NS'";
		$results = mysql_query($sql);
		$i=0;
		while($result = mysql_fetch_array($results))
		{
			echo $i++;
			$price = $result['avg'];
			$sql= "select * from player where id = '".$result['id']."'";
			$playerd = mysql_query($sql) or die(mysql_error());
			$pdetail = mysql_fetch_array($playerd);
			$passet=$pdetail['market_val']+$pdetail['liq_cash'];
			$mval = $pdetail['market_val'];
			$money=$pdetail['liq_cash'];
			$tm = strftime("%Y-%m-%d %H:%M:%S", time());
			$hsql = "insert into history (`t_time`, `p_id`, `t_type`, `symbol`, `skey`, `amount`, `value`, `p_mval`, `p_liqcash`) values('$tm', '".$result['id']."', 's', '".$result['symbol']."', '-1', '".$result['amount']."',  '$price*1.004', '$mval', '$money')";	
			echo $hsql."<br/>";
			mysql_query($hsql) or die(mysql_error());
			
			$usql = "update player set liq_cash = liq_cash+{$result['amount']}*$price*1.002 where id='{$result['id']}'";
			echo $usql."<br/>";
			mysql_query($usql) or die(mysql_error());
			
			$usql = "delete from bought_stock where symbol = 'STER.NS' and id = '".$result['id']."'";
			echo $usql."<br/>";
			mysql_query($usql) or die(mysql_error());
			
			
		}
		
		// To remove all relcapita schedules

		// To remove  ashorted share manually
		
		// To add coal india in symbol table

	
	}
	else
	  header('Location:../home.php');
?>