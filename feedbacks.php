<?php  require_once("includes/global.php");
	if (!in_array($_SESSION['playerid'], $admins)) header("Location: index.php");
	metadetails();
?>
<link rel="stylesheet" href="scripts/chosen.min.css">
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
</head>


<body>
<div id="content">
	<?php navigation("feedback"); ?><br/>
	<div id="portfolio">
	
	<h3>Global Announcement</h3>
	<div id="announce">
		<form action='sendmsg.php' method='POST' id='showform'>
			<textarea style='resize: none;' name='feedback' cols=124 rows=5 maxlength=500></textarea>
			<input type='hidden' name='sendflg' value='r' style='float:right; width;30px; height:34' />
			<select data-placeholder="Choose User Id" class="chosen-select" name='pid'>
				<?php
					$players = mysql_query("select * from `player`");
					while ($player = mysql_fetch_array($players)) {
						echo "<option value=\"".$player['id']."\">".$player['name']."</option>";
					}
				?>
			</select>
			<input type='submit' name='msgsend' value='Send Feedback' style='float:right; width;30px; height:30px;'/>
		</form>
	</div>
	<br/><br/><br/>

	<h2>Feedback</h2>
	<br/>

	<table id="feedbackTable" class="tablesorter">
		<thead><tr>
		   <th>Sl No.</th>
		   <th>User</th>
		   <th>Message</th>
		   <th>Time</th>
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
	<br/><br/>
	</div>
</div><!--content-->

<script src="scripts/jquery.tablesorter.min.js"></script>
<script src="scripts/chosen.jquery.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() { $("#feedbackTable").tablesorter(); }  );
	$(".chosen-select").chosen();
</script>	

</body>
</html>