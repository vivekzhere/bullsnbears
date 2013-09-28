<?php	require_once("../includes/config.php");

	if ($sqlid == '') {
		echo 'Do';
		//	Need to Set up from Scratch
	}
	else {
		require_once("../includes/connection.php");
		$sql = "select 1 from symbols";
		if (!mysql_query($sql)) {

			//	Initialize DB Tables
			$sql = "CREATE TABLE IF NOT EXISTS `bought_stock` (`id` varchar(30) NOT NULL, `symbol` varchar(20) NOT NULL DEFAULT '', `amount` int(11) NOT NULL, `avg` decimal(15,2) DEFAULT NULL,
					PRIMARY KEY (`id`, `symbol`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			mysql_query($sql);
			
			$sql = "CREATE TABLE IF NOT EXISTS `feedback` (`slno` bigint(20) NOT NULL AUTO_INCREMENT, `id` varchar(30) NOT NULL,
  					`time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `message` varchar(1000) NOT NULL,
  					`flag` char(1) NOT NULL, PRIMARY KEY (`slno`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			mysql_query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `history` (`t_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `p_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
					`t_type` varchar(2) COLLATE utf8_unicode_ci NOT NULL, `symbol` varchar(20) COLLATE utf8_unicode_ci NOT NULL, 
					`skey` bigint(20) NOT NULL, `amount` int(11) NOT NULL, `value` decimal(15,2) NOT NULL, `p_mval` int(11) NOT NULL,
  					`p_liqcash` int(11) NOT NULL, PRIMARY KEY (`skey`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			mysql_query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `player` (`id` varchar(30) NOT NULL, `name` varchar(40) NOT NULL, `liq_cash` int(11) NOT NULL,
  					`market_val` int(11) NOT NULL, `rank` int(11) NOT NULL, `day_worth` int(11) NOT NULL, `week_worth` int(11) NOT NULL,
  					`short_val` int(11) NOT NULL, `email` varchar(64) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			mysql_query($sql);


			$sql = "CREATE TABLE IF NOT EXISTS `schedule` (`id` varchar(30) NOT NULL, `symbol` varchar(20) NOT NULL, 
					`transaction_type` varchar(2) NOT NULL, `scheduled_price` decimal(15,2) NOT NULL, `no_shares` int(11) NOT NULL,
  					`pend_no_shares` int(11) NOT NULL, `flag` char(1) NOT NULL, `skey` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  					PRIMARY KEY (`skey`), UNIQUE KEY `skey` (`skey`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35";
			mysql_query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `short_sell` (`id` varchar(30) NOT NULL,
					`symbol` varchar(20) NOT NULL, `amount` int(11) NOT NULL, `val` decimal(15,2) NOT NULL, `day` datetime NOT NULL,
  					PRIMARY KEY (`id`, `symbol`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			mysql_query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `stockval` (`time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  					`symbol` varchar(20) NOT NULL DEFAULT '', `value` decimal(15,2) DEFAULT NULL, `change` decimal(15,2) DEFAULT NULL,
  					`day_low` decimal(15,2) DEFAULT NULL, `day_high` decimal(15,2) DEFAULT NULL, `week_low` decimal(15,2) DEFAULT NULL,
  					`week_high` decimal(15,2) DEFAULT NULL, `change_perc` decimal(15,2) NOT NULL, PRIMARY KEY (`symbol`) )
					ENGINE=MyISAM DEFAULT CHARSET=latin1";
			mysql_query($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `symbols` (`name` varchar(100) NOT NULL, `symbol` varchar(20) NOT NULL, PRIMARY KEY (`symbol`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			mysql_query($sql);
			echo "Database Tables Succesfully  Added";


			//	Populate Database with Top 50 Nifty
			$json = file_get_contents("http://nseindia.com/live_market/dynaContent/live_watch/stock_watch/niftyStockWatch.json");
			$jsonobj = json_decode($json);
			$update_time = date('Y-m-d H:i:s', strtotime($jsonobj->{'time'}));
			foreach($jsonobj->{'data'} as $data)
			{
				mysql_query("INSERT INTO `symbols` (`name` , `symbol`) VALUES (\"NotYetAvailable\", \"".substr($data->{'symbol'}, 0, 20)."\" )");
				mysql_query("INSERT INTO `stockval` (`time_stamp` , `symbol`, `value`, `change`, `change_perc`, `day_low`, `day_high`, `week_low`, `week_high`) VALUES ('$update_time', \"".str_replace(",", "",$data->{'symbol'})."\", \"".str_replace(",", "",$data->{'ltP'})."\", \"".str_replace(",", "",$data->{'ptsC'})."\", \"".str_replace(",", "",$data->{'per'})."\", \"".str_replace(",", "",$data->{'low'})."\", \"".str_replace(",", "",$data->{'high'})."\", \"".str_replace(",", "",$data->{'wklo'})."\", \"".str_replace(",", "",$data->{'wkhi'})."\" )");
			}
			echo "Stocks Succesfully Updated.";
			echo "<a href=\"../index.php\">Index</a>";
		}
		else {
			header("Location: ../index.php");
		}
	}


if (!in_array($_SESSION['playerid'], $admins))	echo 'No';
echo 'Yes';
?>