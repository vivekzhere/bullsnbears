<?php
require_once("includes/global.php");
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") or die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") or die();
	}
	if (isset($_POST['msg'])) {
		$statement = $mysqli->prepare("INSERT INTO `feedback` (`id`, `message`) VALUES (?, ?)");
		$statement->bind_param('ss', $_SESSION['id'], $_POST['msg']);
		$statement->execute();
		echo "Successfully Submitted!";
	}
?>