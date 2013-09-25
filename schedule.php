<?php header("Location: rankings.php");

require_once("includes/global.php");
	if(!isset($_SESSION['username']))
		header("Location: index.php");


$sql= "select * from player where id = '{$_SESSION['player_id']}'";
$playerd = mysql_query($sql) or die(mysql_error());
$pdetail = mysql_fetch_array($playerd);
$passet=$pdetail['market_val']+$pdetail['liq_cash'];

if(isset($_GET['type'])){
		$type = $_GET['type'];
		$tflag = 1;
		switch($type){
			case "Buy":
			case "Short":
				$sql = "select symbol from symbols";
				$symbol_set = mysql_query($sql);
			break;
			case "Sell":
				$sql = "select symbol from bought_stock where id = '{$_SESSION['player_id']}' UNION select symbol from schedule where id='{$_SESSION['player_id']}' and transaction_type='b'";
				$symbol_set = mysql_query($sql);
				if(mysql_num_rows($symbol_set) == 0){
					$errors['sell'] = "nothing to sell";
				}
			break;
			case "Cover":
				$sql = "select distinct symbol from short_sell where id = '{$_SESSION['player_id']}' UNION select symbol from schedule where id='{$_SESSION['player_id']}' and transaction_type='ss'";
				$symbol_set = mysql_query($sql);
				if(mysql_num_rows($symbol_set) == 0){
					$errors['cover'] = "nothing to cover";
				}
			break;
			default:
				$tflag = 0;
		}
		if($tflag == 1){
			if(isset($_POST['symbol'])){ 
				$symbol = $_POST['symbol'];
			}
			if(isset($errors))
			 {	foreach($errors as $er_flag){
					$form2 = "<p class=\"wrong\">".$er_flag."</p>";
				}
			  }
			if(!isset($er_flag)){
				$form2 = "<div id=\"stocklist\"><form method=\"post\" action=\"schedule.php?type=".$type."\" id=\"showform\">";
				$form2 .= "\n<label for=\"symbol\"> <select data-placeholder=\"Choose a Stock...\" name=\"symbol\"  style=\"width:200px; text-align:left;\" class=\"chzn-select\">
			<option></option>";
				$symbol_isset =0;
				while($ss = mysql_fetch_array($symbol_set)){
					$s = $ss['symbol'];
					$form2 .= "\n<option value=\"{$s}\"";
					if(isset($symbol))
					if($s == $symbol){
						$form2 .= "selected";
						$symbol_isset =1;
					}
					$form2 .= ">{$s}</option>";
				}
				$form2 .="\n</select></label>\n&nbsp;&nbsp;<input type=\"submit\" value=\"$type\" style=\"float:right;\" name=\"Go\"/>
			<script type=\"text/javascript\"> $(\".chzn-select\").chosen({no_results_text: \"No stocks found\"}).change(updateLookup()); </script>
		
		</form></div>";
			}
		}
		if(isset($symbol) && $symbol_isset == 1){ 

			$sql = "select * from stockval where (symbol = '{$symbol}') order by time_stamp desc limit 1";
			$valueset = mysql_query($sql) or die(mysql_error());
			$values = mysql_fetch_array($valueset);
			$value = $values['value'];
			$dhigh = $values['day_high'];
			$dlow = $values['day_low'];
			$whigh = $values['week_high'];
			$wlow = $values['week_low'];
			$change = $values['change'];
			$sq = "select name from symbols where symbol = '{$symbol}'";
			$set = mysql_query($sq);
			$stoc = mysql_fetch_assoc($set);
			$stock_name = $stoc['name'];
			$stock_details ="<div id=\"stock_details\">
							<h2>{$stock_name}</h2>
							<ul>
							<li>Day high: {$dhigh}</li>
							<li>Day low: {$dlow}</li>
							<li>52 Week High: {$whigh}</li>
							<li>52 Week Low: {$wlow}</li>
							<li>Price: {$value}</li>
							<li>Change: ".addarrow($change)."</li>";
			$form3 = "<form method=\"post\" action=\"schedule.php?type=".$type."\" >";
			$form3 .= "\n<input type=\"hidden\" name=\"symbol\" value=\"{$symbol}\">";
			$form3 .= "\n<input type=\"hidden\" name=\"type\" value=\"{$type}\">";
			
			
			
			
			switch($type){
				case "Buy": 
					$sql = "select amount from bought_stock where id = '{$_SESSION['player_id']}' and symbol = '{$symbol}' ";
					$resultset = mysql_query($sql);
					$n = 0;
					if(mysql_num_rows($resultset)>0){
						$result = mysql_fetch_array($resultset);
						$n = $result['amount'];
					}
					
					//$n = floor($max_stock/$value)-$n;
					$max=floor($passet/ (6* (1.002*$value) ) );
					$max=max($max, 0);
					//$max = min(floor($_SESSION['liq_cash']/$value) , $n);
					$form3 .= "<div id=\"totvalm\"><table id=\"totvaltable\"><tr><th>Broker Charges</th><th>Total Value</th></tr><tr><td id=\"coltotval\"><div id=\"broker\">0</div></td><td id=\"coltotval\"><div id=\"totval\">0</div></td></tr></table></div><br/><label for=\"sch_price\">Scheduled Price<input type=\"text\" name=\"sch_price\" value=\"\" id=\"txtval\" onkeypress=\"return isValueCorrect(event,$value);\"></label></br><br/>
					\n<label for=\"amount\">No. of Shares <input type=\"text\" onkeypress=\"return isNumberKey(event, $value,$max, {$pdetail['liq_cash']},1)\" name=\"amount\" value=\"\" id=\"txtamt\"></label><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style=\"color:#777; font-size:10px;\">Maximum No. of Shares: {$max}</span>"; 
					break;
				case "Short":
					$sql = "select amount from short_sell where id = '{$_SESSION['player_id']}' and symbol = '{$symbol}' ";
					$resultset = mysql_query($sql);
					if(mysql_num_rows($resultset)>0){
						$result = mysql_fetch_array($resultset);
						$n = $result['amount'];
						
					}else{
						$n = 0;
					}					
					//$max = $short_max - $n;
					$max=floor( ($passet-$pdetail['short_val']) / (6* ($value*1.004) ) );
					$max=max($max, 0);
					$form3 .= "<div id=\"totvalm\"><table id=\"totvaltable\"><tr><th>Broker Charges</th><th>Total Value</th></tr><tr><td id=\"coltotval\"><div id=\"broker\">0</div></td><td id=\"coltotval\"><div id=\"totval\">0</div></td></tr></table></div><br/><label for=\"sch_price\">Scheduled Price<input type=\"text\" name=\"sch_price\" value=\"\" id=\"txtval\" onkeypress=\"return isValueCorrect(event,$value);\"></label></br><br/>
					\n<label for=\"amount\">No. of Shares <input type=\"text\" onkeypress=\"return isNumberKey(event,$value,$max,{$pdetail['liq_cash']},2)\" name=\"amount\" value=\"\" id=\"txtamt\"></label><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"color:#777; font-size:10px;\">Maximum No. of Shares: {$max} </span>";
					break;
				case "Sell":
					$sql = "select amount,avg from bought_stock where id = '{$_SESSION['player_id']}' and symbol = '{$symbol}' limit 1";
					$resultset = mysql_query($sql);
					$result = mysql_fetch_array($resultset);
					if(mysql_num_rows($resultset)>0)
					{
						$boughtamt=$result['amount'];

					}
					else
					{
						$boughtamt=0;
					}	
				        $n=$boughtamt;
					$max=floor($passet/ (6* (1.002*$value) ) );			
					$max=max($max, $n);
					$form3 .= "<div id=\"totvalm\"><table id=\"totvaltable\"><tr><th>Broker Charges</th><th>Total Value</th></tr><tr><td id=\"coltotval\"><div id=\"broker\">0</div></td><td id=\"coltotval\"><div id=\"totval\">0</div></td></tr></table></div><br/><label for=\"sch_price\">Scheduled Price<input type=\"text\" name=\"sch_price\" value=\"\" id=\"txtval\" onkeypress=\"return isValueCorrect(event,$value);\"></label></br><br/>
					\n<label for=\"amount\">No. of Shares <input type=\"text\"  onkeypress=\"return isNumberKey(event, $value,$max,{$pdetail['liq_cash']},3);\" name=\"amount\" value=\"\" id=\"txtamt\"></label><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"color:#777; font-size:10px;\">Maximum No. of Shares: {$max} &nbsp;&nbsp; (Owned Shares: {$boughtamt})</span>";
					
					break;
				case "Cover":
					$sql = "select amount,val from short_sell where id = '{$_SESSION['player_id']}' and symbol = '{$symbol}' ";
					$resultset = mysql_query($sql);
					$result = mysql_fetch_array($resultset);
					if(mysql_num_rows($resultset)>0)
					{
						$boughtamt=$result['amount'];
					}
					else
					{
						$boughtamt=0;
					}
					$n=$boughtamt;
					$max=floor( ($passet-$pdetail['short_val']) / (6* ($value*1.004) ) );
					$max=max($max, $n);
					$form3 .= "<div id=\"totvalm\"><table id=\"totvaltable\"><tr><th>Broker Charges</th><th>Total Value</th></tr><tr><td id=\"coltotval\"><div id=\"broker\">0</div></td><td id=\"coltotval\"><div id=\"totval\">0</div></td></tr></table></div><br/><label for=\"sch_price\">Scheduled Price<input type=\"text\" name=\"sch_price\" value=\"\" id=\"txtval\" onkeypress=\"return isValueCorrect(event,$value);\"></label></br><br/>
					\n<label for=\"amount\">No. of Shares<input type=\"text\"  onkeypress=\"return isNumberKey(event, $value,$max,{$pdetail['liq_cash']},4)\" name=\"amount\" value=\"\" id=\"txtamt\"></label><br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"color:#777; font-size:10px;\">Maximum No. of Shares: {$max} &nbsp;&nbsp; (Shorted Shares: {$boughtamt})</span>";
					
					break;
			}
			$stock_details .= "
							</ul>
							</div>";
							
			if(isset($_POST['submit'])){
				$no = trim($_POST['amount']);
				$mo = $_POST['sch_price'];
				
				if(!($mo < 10000 && $mo>0))
					$form3 .= "\n<p class=\"wrong\">Scheduled Price must be between 0 and 10000</p>";
				else if(isnum($no) != "" || $no == 0 || $no > $max){
					$form3 .= "\n<p class=\"wrong\">Invalid number of shares </p>";
				}
				else{
					$type = $_POST['type'];
					$sql1="select value from stockval where symbol='{$symbol}'";
					$res=mysql_query($sql1) or die(mysql_error());
					$r=mysql_fetch_assoc($res);
					if($mo <= $r['value'])
						$fl='l';
					else
						$fl='g';
					switch($type){
						case "Buy":
							
							$sql="insert into schedule values('{$_SESSION['player_id']}','{$symbol}','b','{$mo}','{$no}','{$no}','{$fl}','0' )";
							$resultset = mysql_query($sql) or die(mysql_error());
							if($resultset){
								$trade=1;
							}
							break;
						case "Sell":
							$sql="insert into schedule values('{$_SESSION['player_id']}','{$symbol}','s','{$mo}','{$no}','{$no}','{$fl}','0' )";
							$resultset = mysql_query($sql) or die(mysql_error());
							if($resultset){
								$trade=1;
							}
							break;
						case "Short":
							$sql="insert into schedule values('{$_SESSION['player_id']}','{$symbol}','ss','{$mo}','{$no}','{$no}','{$fl}','0' )";
							$resultset = mysql_query($sql) or die(mysql_error());
							if($resultset){
								$trade=1;
							}
							break;
						case "Cover":
							$sql="insert into schedule values('{$_SESSION['player_id']}','{$symbol}','c','{$mo}','{$no}','{$no}','{$fl}','0' )";
							$resultset = mysql_query($sql) or die(mysql_error());
							if($resultset){
								$trade=1;
							}
							break;
					}
				}
			}
			$form3 .= "\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><input  type=\"submit\" class=\"redbutton\" name=\"submit\" value=\"Execute\"> \n </form>";
		}
		
	}
?>




<?php metadetails(); ?>
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
      <link rel="stylesheet" type="text/css" href="scripts/chosen.css" />
  <script src="scripts/chosen.jquery.js" type="text/javascript"></script>
   <script src="scripts/jquery.tablesorter.min.js" type="text/javascript"></script>
   <script type="text/javascript">
$(document).ready(function() {
  if($.browser.msie)
   {
     $('#totvalm').hide();
    }

});

     function displayval(val,num,bal,flag, oprice)
	{
	 val = document.getElementById("txtval").value;


	 if(isNaN(num*val))
		{
		document.getElementById("totval").innerHTML=0;
		document.getElementById("broker").innerHTML=0;
		return true;
		}
	else
		{
		
		document.getElementById("totval").innerHTML=(num*val*1.002).toFixed(2);
		document.getElementById("broker").innerHTML=(num*val*0.002).toFixed(2);
		return true;
		}
	
}

      function isNumberKey(evt,val,max,bal,flag,oprice)
      {
         if($.browser.msie)
         {
         return true;
         }
          
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
	else
	{
	  
	  var recentChar = String.fromCharCode(evt.which);
          if(charCode>=48 && charCode<=57) var num = document.getElementById("txtamt").value + recentChar;
          else if (charCode==8) { var x = document.getElementById("txtamt").value; var num = x.substring(0, x.length-1);}
           else var num = document.getElementById("txtamt").value ;
         if(num<=max)
         {  
           displayval(val,num,bal,flag,oprice);
           return true; 
           }
         else 
           return false;
         }
      }
      
      function displayval2(val)
	{
	 var num = document.getElementById("txtamt").value;
	
	 if(isNaN(num*val))
		{
		document.getElementById("totval").innerHTML=0;
		document.getElementById("broker").innerHTML=0;
		return true;
		}
	else
		{
		
		document.getElementById("totval").innerHTML=(num*val*1.002).toFixed(2);
		document.getElementById("broker").innerHTML=(num*val*0.002).toFixed(2);
		return true;
		}
	
}

      function isValueCorrect(evt,price)
      {
         if($.browser.msie)
         {
         return true;
         }
         
        var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode!=46 && charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
         else 
	{
	  
	  var recentChar = String.fromCharCode(evt.which);
          if((charCode>=48 && charCode<=57) || charCode==46) 
          	var val = document.getElementById("txtval").value + recentChar;
          else if (charCode==8) { var x = document.getElementById("txtval").value; var val = x.substring(0, x.length-1);}
          else 
           {	var val = document.getElementById("txtval").value ; }
          
  	 if(val<=10000)
         {  	
            
           displayval2(val);
           return true; 
           }
         else 
         {
           alert("Price must be between 0 and 10000");
           return false;
         }

      }
     }
</script>
    <script type="text/javascript">
   $(document).ready(function() 
    { 
        $("#marketsTable").tablesorter(); 
    } 
); </script>
</head>
<body>
<div id="content">
	<?php navigation("schedule"); ?>
<br/>
	<div id="schedule">
	
	<?php
	
	if(isset($_GET['t']))
		$t=$_GET['t'];
	?>
	<form method="get" id="showscheduleform" action="schedule.php"><input type="hidden" 
	<?php if($t!="display") echo "value=\"display\""; ?> name="t"/><input type="submit" value=
	<?php if($t=="display") echo "\"Schedule Transaction\">"; else echo "\"Show scheduled stocks\">" ?> 
	</form>
	
	
	
	
	<?php
	
	if(isset($_GET['type'])){ 
		$type = $_GET['type'];
		$tflag = 1;
		switch($type){
			case "Buy":
			case "Short":
				$sql = "select symbol from symbols";
				$symbol_set = mysql_query($sql);
			break;
			case "Sell":
				$sql = "select symbol from bought_stock where id = '{$_SESSION['player_id']}'";
				$symbol_set = mysql_query($sql);
				if(mysql_num_rows($symbol_set) == 0){
					$errors['sell'] = "nothing to sell";
				}
			break;
			case "Cover":
				$sql = "select distinct symbol from short_sell where id = '{$_SESSION['player_id']}'";
				$symbol_set = mysql_query($sql);
				if(mysql_num_rows($symbol_set) == 0){
					$errors['cover'] = "nothing to cover";
				}
			break;
			default:
				$tflag = 0;
		}
	}
	
	
	
	
	if($t=="display")
	{
		if(isset($_POST['skey']))
		{
			$sql1="delete from schedule where skey='{$_POST['skey']}'";
			$res=mysql_query($sql1) or die(mysql_error());
		}
	
		echo "<h2>Schedule Details</h2>";
		$flag=0;
		$out = "";
		$out = "<table id=\"scheduletable\">\n<tr>\n<th>Symbol</th><th>Name</th><th>Transaction</th><th>Scheduled Price</th><th>Current Price</th><th>Amount</th><th>Pending</th><th>Status</th><th></th>\n</tr>";
		$sql="select schedule.id,schedule.symbol,transaction_type,scheduled_price,no_shares,pend_no_shares, symbols.name,skey from schedule,symbols where schedule.symbol=symbols.symbol and schedule.id='{$_SESSION['player_id']}'";
		$resultset = mysql_query($sql) or die(mysql_error());
		while($result = mysql_fetch_assoc($resultset))
		{
			$flag=1;
			$op=$result['transaction_type'];
			$sql2="select stockval.symbol as symbol, value from stockval, (select symbol, max(time_stamp) as lt from stockval group  by symbol)  as u, symbols where stockval.symbol='{$result['symbol']}' and stockval.symbol=u.symbol  and stockval.time_stamp=u.lt and stockval.symbol = symbols.symbol";
			$r=mysql_query($sql2) or die(mysql_error());
			$s=mysql_fetch_assoc($r);
			
			$out.="<tr onclick=\"window.location.href='lookup.php?symbol=".$result['symbol']."'\">\n";
			$out.="<td>{$result['symbol']}</td><td>{$result['name']}</td><td>";
			if($op=='b')
				$out.="Buy</td>";
			else if($op=='s')
				$out.="Sell</td>";
			else if($op=='ss')
				$out.="Short Sell</td>";
			else if($op=='c')
				$out.="Cover</td>";
			$out.="<td>{$result['scheduled_price']}</td><td>{$s['value']}</td><td>{$result['no_shares']}</td><td>{$result['pend_no_shares']}</td>";
			if($result['pend_no_shares']==0)
			  { $status = "Processed";
			  $out.="<td style=\"color:#00AA00; font-weight:bold;\">$status</td><td> </td>";
			 }
			else
			 { $status = "Pending";
			  $out.="<td style=\"color:red; font-weight:bold;\">$status</td><td><form method=\"post\" action=\"schedule.php?t=display\"> <input type=\"hidden\" name=\"skey\" value=".$result['skey']."> <input type=\"submit\" name=\"cancel\" value=\"Cancel\"></form></td>";
			 }
				


			$out.="</tr>";
		}
			$out.="</table>";
		if($flag == 1){
			echo $out;
		}else{
			echo "<p class=\"big\">No scheduled transactions.</p>";
		}
	}
	
	
	else
	{
		
		$tflag=1;
		$form1= "<h2>Schedule Transactions</h2><form method=\"get\" action=\"schedule.php\" class=\"first_form\" id=\"showform\"  ><label for=\"transaction\"> <select name=\"type\" id=\"type\"><option value=\"Buy\"";

		if($tflag == 1) {if($type == "Buy")  $form1 .= "selected"; } else  $form1 .="selected";
		$form1 .= ".>Buy</option><option value=\"Sell\"";
		if($tflag == 1) if($type == "Sell") $form1 .= "selected";
		$form1 .= ">Sell</option><option value=\"Short\"";
		if($tflag == 1) if($type == "Short") $form1 .= "selected";
		$form1 .= ">Short Sell</option><option value=\"Cover\"";
		if($tflag == 1) if($type == "Cover") $form1 .= "selected";
		$form1 .= ">Cover</option></select></label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"Go\"></form>";
		echo $form1;
		if($trade==1)
			echo "<p class=\"big\">Scheduled Successfully</p>";
		else
	          { if($tflag == 1){
				 echo $form2; 
			}
			if(isset($form3)){
				echo $form3;
				echo $stock_details;
			}
		  }
	}
	
	?>
	</div>
	
	
	</div><!-- content_main -->
</div><!--content-->
</body>