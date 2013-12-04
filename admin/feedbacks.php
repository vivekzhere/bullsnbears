<?php  require_once("../includes/global.php");
	if (!in_array($_SESSION['id'], $admins)) header("Location: index.php") or die();
	metadetails();
?>
</head>

<body>
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
			$sql = "select * from `feedback`, player WHERE player.id = feedback.id";
			$result = $mysqli->query($sql) or die();
			while ($transaction = $result->fetch_assoc()) {
				$timestamp = $transaction['time_stamp'];
				$slno = $transaction['slno'];
				$user = $transaction['name'];
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