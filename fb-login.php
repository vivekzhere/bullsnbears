<?php 
	require_once("fb-php-sdk/facebook.php");
	require_once("includes/global.php");
	
	if(isset($_SESSION['username']))
		header("Location: home.php");

	
	// Setting APP Details and initializing API
	$app_id = '106257179526701';
	$app_secret = '9ffd564e1becb3b9691be3c9b13a3e26';
	$app_namespace = 'bullsnbears';
	$app_url = 'https://apps.facebook.com/' . $app_namespace . '/';
	$scope = 'email';
	
	$facebook = new Facebook( array(
		                   'appId' => $app_id,
		                   'secret' => $app_secret,
		                 ));
           
	// Get the current user
	$user = $facebook->getUser();
	
	// If the user has installed and logged in the app
	if($user)
	{
		$fql    =   "select name, hometown_location, sex, pic_square, email, education from user where uid='$user'";
		$param  =   array(
			'method'     => 'fql.query',
			'query'     => $fql,
			'access_token' => $access_token ,
			'callback'    => ''
			);
		$fqlResult   =   $facebook->api($param);
		$fbinfo = array(
			'id' => $user,
			'name' => $fqlResult[0]['name'],
			'email' => $fqlResult[0]['email'],
			'picurl' => $fqlResult[0]['pic_square']
			);

	
		$sql = "select * from player where id = '{$fbinfo['id']}'";
		$result_set = mysql_query($sql);		
		
		// If the user logs in for the first time, add details to dB and set session
		if(mysql_num_rows($result_set) == 0){
			$sql = "insert into player(id, name, liq_cash, market_val, rank, short_val, day_worth, week_worth, email) values('{$fbinfo['id']}', '{$fbinfo['name']}', {$start_money}, 0, 0, 0, {$start_money}, {$start_money}, '{$fbinfo['email']}')";
			mysql_query($sql) or die(mysql_error());
			$_SESSION['playerid'] = $fbinfo['id'];
			$_SESSION['username'] = $fbinfo['id'];
			$_SESSION['name'] = $fbinfo['name'];
			$_SESSION['liq_cash'] = $start_money;
			$_SESSION['market_val'] =0;
			$_SESSION['rank']= 0;
			$_SESSION['player_id']= $fbinfo['id'];
			mysql_close($connection); 
			header("Location: home.php");
		}
		// set the session variables
		else{
			$result = mysql_fetch_array($result_set);
			$_SESSION['playerid'] = $fbinfo['id'];
			$_SESSION['username'] = $fbinfo['id'];
			$_SESSION['name'] = $fbinfo['name'];
			$_SESSION['liq_cash'] = $result['liq_cash'];
			$_SESSION['market_val'] = (($result['market_val']==0)?0:$result['market_val']);
			$_SESSION['rank']= $result['rank'];
			$_SESSION['player_id']= $fbinfo['id'];
			mysql_close($connection); 
			header("Location: home.php");
		}		
	}
	
	// If it is within CANVAS page , then redirect it to login.
	else if( isset($_REQUEST['signed_request']) ) {
			
		$loginUrl = $facebook->getLoginUrl(array(
			'scope' => $scope,
			'redirect_uri' => $app_url,
			));
		print('<script> top.location.href=\'' . $loginUrl . '\'</script>');	
	}
	
	else {
			$loginUrl = $facebook->getLoginUrl(array(
			'scope' => $scope,
			'redirect_uri' => "http://bullsnbears.tathva.org/fb-login.php",
			));
			print('<script> top.location.href=\'' . $loginUrl . '\'</script>');		
	}	 	

   
?>


