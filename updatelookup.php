<?php
require_once("includes/global.php");
	if (session_id() == '') session_start();
	if (!(isset($_SESSION['id']) && in_array($_SESSION['id'], $admins))) {
		if (($debug_status == 2) || ($debug_status == 1 && $access_status == 0)) header("Location: testing.html") && die();
		elseif (!isset($_SESSION['id'])) header("Location: index.php") && die();
	}
?>
<?php	
	$results = $mysqli->query("SELECT * FROM `stocks` ORDER BY `symbol` ASC");
	$data = array();
	while ($result = $results->fetch_assoc()) $data[] = $result;
	echo json_encode($data);
?>
