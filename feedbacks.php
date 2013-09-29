<?php  require_once("includes/global.php");
	if (!in_array($_SESSION['playerid'], $admins)) header("Location: index.php");
	metadetails();
?>
<link rel="stylesheet" href="scripts/chosen.min.css">
</head>


<body>
<div id="content">
	<?php navigation("feedback"); ?><br/>
	<div id="portfolio">
	
	<h3>Global Announcement</h3>
	<div id="announce">
		<form action='' id='showform'>
			<textarea style='resize: none;' id='f' name='feedback' cols=124 rows=5 maxlength=500></textarea>
			<select data-placeholder="Choose User Id" class="chosen-select" id='pid' name='pid'>
				<?php
					$players = mysql_query("select `id`, `name` from `player`");
					while ($player = mysql_fetch_array($players)) echo '<option value="'.$player['id'].'">'.$player['name'].'</option>';
				?>
			</select>
			<input type='submit' id='m' name='msgsend' value='Send Feedback' style='float:right; width;30px; height:30px;'/>
		</form>
	</div>
	<br/><br/><br/>

	<h2>Feedback</h2>
	<br/>

	<div id="tableDiv"><table id="feedbackTable" class="tablesorter">
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
	</table></div>
	<br/><br/>
	</div>
</div>

<script src="scripts/jquery.tablesorter.min.js"></script>
<script src="scripts/chosen.jquery.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() { 
		$("#feedbackTable").tablesorter();
		$(".chosen-select").chosen({width: "30%"});
	}  );
	
	$("#m").click(function() {
		$.ajax({  
			type: "POST", url: "sendmsg.php", data: 'feedback='  + $("textarea#f").val() + '&sendflg=R&pid=' + $("#pid").val(),  
			success: function(data) {
		    	$('#tableDiv').html(data);
			}  
		});
		$("textarea#f").val('');
		return false;  
	} );
</script>	

</body>
</html>