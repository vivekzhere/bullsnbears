<?php	require_once("../includes/config.php");
	if ($sqlid == '') header("Location: setup.php");
	else {
		require_once("../includes/global.php");
		if (!in_array($_SESSION['playerid'], $admins))	header("Location: ../index.php");
		}
?>