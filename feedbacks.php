<?php  require_once("includes/global.php");
	if (!in_array($_SESSION['id'], $admins)) header("Location: index.php") or die();
	metadetails();
?>
</head>

<style>

#tableDiv th {
	padding: 15px;
	width: 150px;
}
</style>
<body>
<? require_once("includes/nav.php"); ?>
<div id="content">

	<h2>Feedback</h2>
	<br/>

	<div id="tableDiv"><table id="feedbackTable">
		<thead><tr>
		   <th>Sl No.</th>
		   <th>User</th>
		   <th>Message</th>
		   <th>Time</th>
		</tr></thead>
		<tbody>
		<?php			
			$sql = "select * from `feedback`";
			$result = $mysqli->query($sql) or die();
			while ($transaction = $result->fetch_assoc()) {
				$timestamp = $transaction['time_stamp'];
				$slno = $transaction['slno'];
				$user = $transaction['id'];
				$sql = "select `name` from player where id = \"".$user."\"";
				$user = $mysqli->query($sql);
				$user = $user->fetch_assoc();
				$user = $user['name'];
				$message = $transaction['message'];
				echo "<tr>";
				echo "<td>{$slno}</td><td>{$user}</td><td>{$message}</td><td>{$timestamp}</td></tr>";
			}
		?>
		</tbody>
	</table></div>
	<br/><br/>
</div>


</body>
</html>