<?php
require_once("includes/global.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}
	if (isset($_POST['msg'])) {
		if (isset($_POST['flag'])) $flag = "G";
		else $flag = "F";
		$statement = $mysqli->prepare("INSERT INTO `feedback` (`id`, `message`, `flag`) VALUES (?, ?, ?)");
		$statement->bind_param('sss', $_SESSION['id'], $_POST['msg'], $flag);
		$statement->execute();
		echo "Successfully Submitted!";
	}
?>