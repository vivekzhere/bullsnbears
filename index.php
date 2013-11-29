<?php
require_once("includes/global.php");
require_once("includes/sanitize.php");
require_once("fb-sdk/facebook.php");

	if (session_id() == '') session_start();
	if (!(isset($_GET['key']) && $_GET['key'] == 'M1112AER') && !(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") or die();
		elseif (isset($_SESSION['id'])) header("Location: home.php") or die();
	} else header("Location: home.php");

	$facebook = new Facebook($fbArray);
	$user = $facebook->getUser();
	if ($user) {
  		try {
    		$user_profile = $facebook->api('/me');
  		} catch (FacebookApiException $e) {
    		error_log($e);
    		$user = null;
  		}
	}
	if ($user) {
		$fql = "select name, pic_square, email from user where uid='$user'";
		$access_token = $facebook->getAccessToken();
		$param  = array('method'     => 'fql.query', 'query'     => $fql, 'access_token' => $access_token , 'callback'    => '');
		$fqlResult = $facebook->api($param);
		$fbinfo = array('id' => $user, 'name' => $fqlResult[0]['name'], 'email' => $fqlResult[0]['email'], 'picurl' => $fqlResult[0]['pic_square']);
		$_SESSION['id'] = $fbinfo['id'];
		$_SESSION['name'] = $fbinfo['name'];
		$_SESSION['picurl'] = $fbinfo['picurl'];
		$result = $mysqli->query("SELECT * FROM `player` WHERE `id` = '{$fbinfo['id']}'");
		if (!$result) echo $mysqli->$error;
		if ($result->num_rows == 0) {
			$statement = $mysqli->prepare("INSERT INTO `player` (`id`, `name`, `liq_cash`, `market_val`, `rank`, `short_val`, `day_worth`, `week_worth`, `email`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$temp = 0;
			$statement->bind_param('dsddiddds', $fbinfo['id'], $fbinfo['name'], $start_money, $temp, $temp, $temp, $start_money, $start_money, $fbinfo['email']);
			$statement->execute();
			echo $mysqli->error;
			$_SESSION['liq_cash'] = $start_money;
			$_SESSION['market_val'] =0;
			$_SESSION['rank']= 0;
		} else {
			$row = $result->fetch_assoc();
			$_SESSION['liq_cash'] = $row['liq_cash'];
			$_SESSION['market_val'] =  $row['market_val'];
			$_SESSION['rank']= $row['rank'];
		}
		$mysqli->close();
		header("Location: home.php");
		die();
	} else $loginUrl = $facebook->getLoginUrl();
	metadetails("index");
?>

</head>

<body>
	<div id="fb-box-container" class="box box1"><div class="fb-like-box" data-href="https://www.facebook.com/bullsnbearscommunity" data-width="300" data-height="120" data-colorscheme="light" data-show-faces="false" data-header="false" data-stream="true" data-show-border="false"></div></div>
	<div id="login" class="center box box1">
		<img src="images/user.png" id="user-img" class="center" alt="User Image" width="150px" height="150px"></img>
		<div id="login-btn" onclick='window.location.replace("<?php echo $loginUrl ?>")'></div>
	</div>
	<div id="tathva-pic">
		<img id ="img-tathva" alt="Tathva 13" width="240px" height="90px" src="images/tathva_black.png"></img>
		<div id="fb-like" class="fb-like centerh" data-href="http://facebook.com/tathva" data-width="200px" data-height="20px" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="false" data-send="false"></div>
	</div>
	<?php FacebookJS($appId); ?>
</body>
</html>