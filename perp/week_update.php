<?php
	if($_GET['key']=="Ti1lLSK65bds")
	{
		require_once("../includes/global.php");		
		//to update week_worth for each player. To be run when market opens on monday.
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
			$weekval=$result['liq_cash']+$result['market_val'];		
			$sql_array[] = "update player set week_worth ='$weekval' where id ='$resultid' limit 1";
		}
		foreach($sql_array as $sql){
			mysql_query($sql);
		}
		mysql_close($connection);
	}
	else
	  header('Location:../home.php');
?>
