<?php
	require_once("config.php");
	$connection = mysql_connect($server,$sqlid,$sqlpass);
	if(!$connection){
		die("Database connection failed: " . mysql_error());
	}
			
			
	$db_select = mysql_select_db($bnbdbase,$connection);
	if(!$db_select){
		die("Database selection failed: " . mysql_error());
	}
	
?>
