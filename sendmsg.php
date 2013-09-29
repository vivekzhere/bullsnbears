<?php require_once("includes/global.php");
	if(!isset($_SESSION['username'])) header("Location: index.php");
		$playerid = $_SESSION['playerid'];
		if (isset($_POST['pid'])) $playerid = $_POST['pid'];
		$feedback = mysql_escape_string(strip_tags($_POST['feedback']));
		$flag = $_POST['sendflg'];
		$tme = date("Y-m-d H:i:s");
		$sql = "insert into `feedback` (`id` , `time_stamp`, `message`, `flag`) values('{$playerid}', '$tme', '{$feedback}', '{$flag}')";
		mysql_query($sql) or die(mysql_error());
		if ($flag=='S') header('Location:home.php');
		if ($flag=='R') {
		?>
<table id="feedbackTable" class="tablesorter">
		<thead><tr>
		   <th>Sl No.</th><th>User</th><th>Message</th><th>Time</th>
		</tr></thead>
		<tbody>
		<?php			
		$sql = "select * from `feedback`";
		$result = mysql_query($sql) or die(mysql_error());
		while ($transaction = mysql_fetch_array($result)) {
			$timestamp = $transaction['time_stamp'];
			$slno = $transaction['slno'];
			$user = $transaction['id'];
			$sql = "select `name` from player where id = \"".$user."\"";
			$user = mysql_query($sql);
			$user = mysql_result($user, 0, "name");
			$message = $transaction['message'];
			$flag = $transaction['flag'];
			if ($flag == "S") echo "<tr>";
			else echo "<tr style=\"background: #E9F2BC; \">";
			echo "<td>{$slno}</td><td>{$user}</td><td>{$message}</td><td>{$timestamp}</td></tr>";
		}
		?>
		</tbody>
	</table>

		<?php
		}
?>