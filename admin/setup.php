<?php
require_once("../includes/config.php");

	if ($sqlid == '') {
		echo 'Do';
		//	Need to Set up from Scratch
	}
	else {
		require_once("../includes/connection.php");
		$sql = "select 1 from symbols";
		if (!$mysqli->query($sql)) {

			//	Initialize DB Tables
			$sql = "CREATE TABLE IF NOT EXISTS `bought_stock` (`id` varchar(30) NOT NULL, `symbol` varchar(20) NOT NULL DEFAULT '', `amount` int(11) NOT NULL, `avg` decimal(15,2) DEFAULT NULL,
					PRIMARY KEY (`id`, `symbol`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$mysqli->query($sql) or die($query.'<br />'.$mysqli->error);

			$sql = "CREATE TABLE IF NOT EXISTS `feedback` (`slno` bigint(20) NOT NULL AUTO_INCREMENT, `id` varchar(30) NOT NULL,
  					`time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `message` varchar(1000) NOT NULL,
  					PRIMARY KEY (`slno`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$mysqli->query($sql) or die($query.'<br />'.$mysqli->error);

			$sql = "CREATE TABLE IF NOT EXISTS `history` (`p_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
					`t_type` varchar(2) COLLATE utf8_unicode_ci NOT NULL, `symbol` varchar(20) COLLATE utf8_unicode_ci NOT NULL, 
					`skey` bigint(20) NOT NULL, `amount` int(11) NOT NULL, `value` decimal(15,2) NOT NULL,
					`t_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
			$mysqli->query($sql) or die($query.'<br />'.$mysqli->error);

			$sql = "CREATE TABLE IF NOT EXISTS `player` (`id` varchar(30) NOT NULL, `name` varchar(40) NOT NULL, `liq_cash` int(11) NOT NULL,
  					`market_val` int(11) NOT NULL, `rank` int(11) NOT NULL, `day_worth` int(11) NOT NULL, `week_worth` int(11) NOT NULL,
  					`short_val` int(11) NOT NULL, `email` varchar(64) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$mysqli->query($sql) or die($query.'<br />'.$mysqli->error);

			$sql = "CREATE TABLE IF NOT EXISTS `schedule` (`id` varchar(30) NOT NULL, `symbol` varchar(20) NOT NULL,
					`transaction_type` varchar(2) NOT NULL, `scheduled_price` decimal(15,2) NOT NULL, `no_shares` int(11) NOT NULL,
  					`pend_no_shares` int(11) NOT NULL, `flag` char(1) NOT NULL, `skey` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  					PRIMARY KEY (`skey`)) ENGINE=InnoDB  DEFAULT CHARSET=latin1";
			$mysqli->query($sql) or die($query.'<br />'.$mysqli->error);

			$sql = "CREATE TABLE IF NOT EXISTS `short_sell` (`id` varchar(30) NOT NULL, `symbol` varchar(20) NOT NULL,
					`amount` int(11) NOT NULL, `val` decimal(15,2) NOT NULL, PRIMARY KEY (`id`, `symbol`) )
					ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$mysqli->query($sql) or die($query.'<br />'.$mysqli->error);

			$sql = "CREATE TABLE IF NOT EXISTS `stocks` (`time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  					`name` varchar(100) NOT NULL, `symbol` varchar(20) NOT NULL DEFAULT '', `value` decimal(15,2) DEFAULT NULL,
  					`change` decimal(15,2) DEFAULT NULL, `day_low` decimal(15,2) DEFAULT NULL, `day_high` decimal(15,2) DEFAULT NULL,
  					`week_low` decimal(15,2) DEFAULT NULL, `week_high` decimal(15,2) DEFAULT NULL, `change_perc` decimal(15,2) NOT NULL,
  					PRIMARY KEY (`symbol`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
			$mysqli->query($sql) or die($query.'<br />'.$mysqli->error);
			echo "Database Tables Succesfully Added<br/><br/>";


			//	Populate Database with Top 50 Nifty
			$json = file_get_contents("http://nseindia.com/live_market/dynaContent/live_watch/stock_watch/niftyStockWatch.json");
			$jsonobj = json_decode($json);
			$update_time = date('Y-m-d H:i:s', strtotime($jsonobj->{'time'}));
			$sql = "INSERT INTO `stocks` (`name`, `symbol`, `time_stamp`, `value`, `change`, `change_perc`, `day_low`, `day_high`, `week_low`, `week_high`) VALUES ";
			$out = "";
			foreach($jsonobj->{'data'} as $data)
			{
		        $c = curl_init();
		        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			    curl_setopt($c,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		        curl_setopt($c, CURLOPT_URL, "http://www.nseindia.com/live_market/dynaContent/live_watch/get_quote/ajaxGetQuoteJSON.jsp?series=EQ&symbol=".$data->{'symbol'});
		        $contents = curl_exec($c);
		        curl_close($c);
				$symb = json_decode($contents);

				foreach ($symb->{'data'} as $data2)	$symbname = $data2->{'companyName'};
				$out .= "(\"".$symbname."\", \"".$data->{'symbol'}."\", '".$update_time."', \"".str_replace(",", "", $data->{'ltP'})."\", \"".str_replace(",", "", $data->{'ptsC'})."\", \"".$data->{'per'}."\", \"".str_replace(",", "", $data->{'low'})."\", \"".str_replace(",", "", $data->{'high'})."\", \"".str_replace(",", "", $data->{'wklo'})."\", \"".str_replace(",", "", $data->{'wkhi'})."\" ), ";
			}
			$sql .= substr($out, 0, strlen($out) - 2);
			$mysqli->query($sql);
			echo "Stocks Succesfully Updated.";
			echo "<br/><br/><a href=\"../index.php\">Index</a>";
			isset($_SESSION) && session_destroy();
		}
		else {
			header("Location: ../index.php");
		}
	}

?>