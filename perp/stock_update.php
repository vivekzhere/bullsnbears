<?php
require_once("../includes/config.php");
require_once("../includes/connection.php");
if ($_GET['key'] == $mainkey) {		
	$json = file_get_contents("http://nseindia.com/live_market/dynaContent/live_watch/stock_watch/niftyStockWatch.json");
	$jsonobj = json_decode($json);
	$update_time = date('Y-m-d H:i:s', strtotime($jsonobj->{'time'}));
	foreach($jsonobj->{'data'} as $data)
	{	
		if(str_replace(",", "",$data->{'ltP'})!=0) 
		{
			$sql = "UPDATE `stocks` SET `time_stamp` = '$update_time', `value` = '".str_replace(",", "",$data->{'ltP'})."', `change` = '".str_replace(",", "",$data->{'ptsC'})."', `change_perc` = '".str_replace(",", "",$data->{'per'})."', `day_low` = '".str_replace(",", "",$data->{'low'})."', `day_high` = '".str_replace(",", "",$data->{'high'})."', `week_low` = '".str_replace(",", "",$data->{'wklo'})."', `week_high` = '".str_replace(",", "",$data->{'wkhi'})."' WHERE `symbol` = '{$data->{'symbol'}}'";
			$result=$mysqli->query($sql) or die($mysqli->error);
		}
	}
	echo $update_time;
} else header("Location: ../home.php");
?>