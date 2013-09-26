<?php
	if($_GET['key']=="Ti1lLSK65bds")
	{		
		require_once("../includes/global.php");
		$mt = strftime("%H", time());
		$mt_m = strftime("%M", time());
		$mt_d = strtolower(strftime("%A",time()));
		$json = file_get_contents("http://nseindia.com/live_market/dynaContent/live_watch/stock_watch/niftyStockWatch.json");
		$jsonobj = json_decode($json);
		$update_time = date('Y-m-d H:i:s', strtotime($jsonobj->{'time'}));
		echo $update_time;
		foreach($jsonobj->{'data'} as $data)
		{	
			$sql = "SELECT symbol FROM stockval WHERE symbol LIKE '".substr($data->{'symbol'}, 0, 6)."%';";
			$query = mysql_query($sql);
			$result = mysql_fetch_object($query);
			if(str_replace(",", "",$data->{'ltP'})!=0) 
			{
				$sql = "UPDATE stockval SET `time_stamp` = '$update_time', `value` = '".str_replace(",", "",$data->{'ltP'})."', `change` = '".str_replace(",", "",$data->{'ptsC'})."', `change_perc` = '".str_replace(",", "",$data->{'per'})."', `day_low` = '".str_replace(",", "",$data->{'low'})."', `day_high` = '".str_replace(",", "",$data->{'high'})."', `week_low` = '".str_replace(",", "",$data->{'wklo'})."', `week_high` = '".str_replace(",", "",$data->{'wkhi'})."' WHERE `symbol` = '{$result->symbol}';";
				$result=mysql_query($sql) or die(mysql_error());
			}
		}
 	}
?>
