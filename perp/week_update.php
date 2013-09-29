<?php
	if($_GET['key']=="Ti1lLSK65bds")
	{
		require_once("../includes/global.php");			
		$sql = 'update `player` set `week_worth` = `liq_cash` + `market_val`';
		mysql_query($sql);
		echo(date("Y-m-d",time()));
	}
	else header('Location:../home.php');
?>
