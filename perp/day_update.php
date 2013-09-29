<?php
/* To update day_worth for each player. To be run when market opens.
Day Worth of a player is the Net Worth before the market begins. Used to calculate Daily Gain */ 


	if($_GET['key']=="Ti1lLSK65bds")
	{
		require_once("../includes/global.php");			
		$sql = 'update `player` set `day_worth` = `liq_cash` + `market_val`';
		mysql_query($sql);
		$sql = 'delete from `schedule` where `pend_no_shares` = 0';
		mysql_query($sql);
		echo(date("Y-m-d",time()));
	}
	else header('Location:../home.php');
?>
