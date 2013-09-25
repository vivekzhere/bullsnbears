<?php require_once("includes/global.php");
	if(!isset($_SESSION['username']))
		header("Location: index.php");
		
	    $playerid = $_SESSION['playerid'];
	    if(isset($_POST['pid']))	
	    	$playerid = $_POST['pid'];
	    $feedback = mysql_escape_string(strip_tags($_POST['feedback']));
	    $flag = isset($_POST['sendflg'])?'R':'S';
	    $tme = date("Y-m-d H:i:s");
	    $sql = "insert into feedback values('{$playerid}', '$tme', '{$feedback}', '{$flag}')";
	     mysql_query($sql) or die(mysql_error());
	    if($flag=='S')
	    	header('Location:home.php');
            if($flag=='R')
                header('Location:feedbacks.php');
             mysql_close($connection);
	       
?>
