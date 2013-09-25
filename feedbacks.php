<?php  require_once("includes/global.php");
if($_SESSION['playerid']!='100001351617375' && $_SESSION['playerid']!='100002423133536' && $_SESSION['playerid']!='100000534273222' && $_SESSION['playerid']!='1042067347'  )
		header("Location: index.php");
echo "<div id='feedback'>
	<form action='sendmsg.php' method='POST' id='showform'>
	<textarea style='resize: none;' name='feedback' cols=80 rows=2 maxlength=300></textarea>
	<input type='hidden' name='sendflg' value='r' style='float:right; width;30px; height:34' /> <input type='text' name='pid'/>		
	<input type='submit' name='msgsend' value='Send Feedback' style='float:right; width;30px; height:30px;'/></form></div>";	
	
echo "	<table border='1'> 
	<thead>
	<tr>
		<th>Tathva ID</th><th>Message</th><th>Time</th>
	</tr>
	</thead>	
	<tbody>";
	$sql="select id, message, time_stamp from feedback where flag='S' order by time_stamp desc";
	$fbs = mysql_query($sql);
	$out = "";
	$flag = 0;
	while($fb = mysql_fetch_array($fbs)){
			$ids = $fb['id'];
			$message = $fb['message'];
			$time = $fb['time_stamp'];
			$out .= "<tr><td>$ids</td><td>$message</td><td>$time</td></tr>";
		}
		echo $out;

	echo "</tbody></table>";	
	
?>
	

		
 
