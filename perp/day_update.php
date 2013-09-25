<?php
/* To update day_worth for each player. To be run when market opens.
Day Worth of a player is the Net Worth before the market begins. Used to calculate Daily Gain */ 


	if($_GET['key']=="Ti1lLSK65bds")
	{
		require_once("../includes/global.php");			
		$sql = "select * from player";
		$resultset = mysql_query($sql) or die(mysql_error());
		$sql_array = array();
		while($result = mysql_fetch_assoc($resultset))
		{  
			if($tot != $result['tot_val']){
				$rank++;
				$tot = $result['tot_val'];
			}
		
			$resultid = $result['id'];		
			$dayval=$result['liq_cash']+$result['market_val'];		
			$sql_array[] = "update player set day_worth ='$dayval' where id ='$resultid' limit 1";
		}
		foreach($sql_array as $sql){
			mysql_query($sql) or die(mysql_error());
		}
		$sql = "delete from schedule where pend_no_shares='0'";
		mysql_query($sql) or die(mysql_error());		
		mysql_close($connection);
	}
	else
	  header('Location:../home.php');
?>
