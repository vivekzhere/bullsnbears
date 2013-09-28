<?php

require_once("includes/global.php");
if(!isset($_SESSION['username']))
		header("Location: index.php");
		
 $player_id = $_SESSION['player_id'];


function valupdate($userid)
	{
		$sql="select sum(bs) as totbs from (select stockval.symbol, stockval.value*bought_stock.amount as bs from stockval, bought_stock, (select symbol, max(time_stamp) as lt from stockval group  by symbol)  as u where stockval.symbol = bought_stock.symbol and id='$userid' and u.lt = stockval.time_stamp and u.symbol=stockval.symbol) as bstable";
		$stocks = mysql_query($sql);
		$bs = mysql_fetch_array($stocks);
		$sql="select sum(ss) as totss from (select stockval.symbol, (short_sell.val - stockval.value)*short_sell.amount as ss from stockval, short_sell, (select symbol, max(time_stamp) as lt from stockval group  by symbol)  as u where stockval.symbol = short_sell.symbol and id='$userid' and u.lt = stockval.time_stamp and u.symbol=stockval.symbol) as sstable";
		$stocks = mysql_query($sql);
		$ss = mysql_fetch_array($stocks);
		$market_val = round($bs['totbs'] + $ss['totss']);
		$sql = "update player set market_val= '{$market_val}' where id ='$userid'";
		mysql_query($sql) or die(mysql_error());
		
	}

	$mt = strftime("%H", time());
	$mt_m = strftime("%M", time());
	$mt_d = strtolower(strftime("%A",time()));
	//$mt = "10"; $mt_m = "00";
	//$mt_d = "monday";

	if((($mt > $start_time ||($mt == $start_time && $mt_m >= $start_time_min)) && $mt_d != "sunday" && $mt_d != "saturday" && ($mt < $end_time || ($mt ==$end_time && $mt_m <= $end_time_min)) ) || $player_id == '100000534273222' ) 
	{
	$mtime = true;
	$flag = 0;
	$tflag = 0;
	$errors = array();
	$sql= "select * from player where id = '{$player_id}'";
	$playerd = mysql_query($sql) or die(mysql_error());
	$pdetail = mysql_fetch_array($playerd);
	$passet=$pdetail['market_val']+$pdetail['liq_cash'];
	$mval = $pdetail['market_val'];
	$money=$pdetail['liq_cash'];
	
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
				$sql = "select symbol from bought_stock where id = '{$player_id}'";
				$symbol_set = mysql_query($sql);
				if(mysql_num_rows($symbol_set) == 0){
					$errors['sell'] = "nothing to sell";
				}
			break;
			case "Cover":
				$sql = "select distinct symbol from short_sell where id = '{$player_id}'";
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
			foreach($errors as $er_flag){
				$form2 = "<p class=\"wrong\">".$er_flag."</p>";
			}
			if(!isset($er_flag)){
				$form2 = "<div id=\"stocklist\"><form method=\"post\" action=\"trade.php?type=".$type."\" style=\"width:300px;\" id=\"showform\">";
				$form2 .= "\n<label for=\"symbol\"> 	<select data-placeholder=\"Choose a Stock...\" name=\"symbol\"  style=\"width:200px; text-align:left;\" class=\"chzn-select\">
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
				$form2 .="\n</select></label>\n&nbsp;&nbsp;<input type=\"submit\" style=\"float:right;\" value =\"{$type} \" name=\"Go\">
		
		<script type=\"text/javascript\"> $(\".chzn-select\").chosen({no_results_text: \"No stocks found\"}).change(updateLookup()); </script>
		</div></form>";
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
			$form3 = "<form method=\"post\" action=\"trade.php?type=".$type."\" >";
			$form3 .= "\n<input type=\"hidden\" name=\"symbol\" value=\"{$symbol}\">";
			$form3 .= "\n<input type=\"hidden\" name=\"type\" value=\"{$type}\">";
			switch($type){
				case "Buy":
					$sql = "select amount from bought_stock where id = '{$player_id}' and symbol = '{$symbol}' ";
					$resultset = mysql_query($sql);
					$n = 0;
					if(mysql_num_rows($resultset)>0){
						$result = mysql_fetch_array($resultset);
						$n = $result['amount'];
					}
					
					//$n = floor($max_stock/$value)-$n;
					$max=min(floor(($pdetail['liq_cash']-($pdetail['short_val']/4))/(1.002*$value)),floor($passet/(6*(1.002*$value))) - $n);			
					$max=max($max, 0);
					//$max = min(floor($_SESSION['liq_cash']/$value) , $n);
					$form3 .= "<div id=\"totvalm\"><table id=\"totvaltable\"><tr><th>Broker Charges</th><th>Total Value</th><th>Cash at Hand</th></tr><tr><td id=\"coltotval\"><div id=\"broker\">0</div></td><td id=\"coltotval\"><div id=\"totval\">0</div></td><td id=\"coltotval\"><div id=\"balbuy\">{$pdetail['liq_cash']}</div></td></tr></table></div>
					\n<label for=\"amount\">No. of Shares <input type=\"text\" onkeypress=\"return isNumberKey(event, $value,$max, {$pdetail['liq_cash']},1)\" name=\"amount\" value=\"\" id=\"txtamt\"></label><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span style=\"color:#777; font-size:10px;\">Maximum No. of Shares: {$max}</span>";
					break;
				case "Short":
					$sql = "select amount from short_sell where id = '{$player_id}' and symbol = '{$symbol}' ";
					$resultset = mysql_query($sql);
					if(mysql_num_rows($resultset)>0){
						$result = mysql_fetch_array($resultset);
						$n = $result['amount'];
						
					}else{
						$n = 0;
					}					
					//$max = $short_max - $n;
					$max=min(floor(((4*$pdetail['liq_cash'])-$pdetail['short_val'])/($value*1.004)),floor(($passet-$pdetail['short_val'])/(6*($value*1.004))) - $n);
					$max=max($max, 0);
					$form3 .= "<div id=\"totvalm\"><table id=\"totvaltable\"><tr><th>Broker Charges</th><th>Total Value</th><th>Cash at Hand</th></tr><tr><td id=\"coltotval\"><div id=\"broker\">0</div></td><td id=\"coltotval\"><div id=\"totval\">0</div></td><td id=\"coltotval\"><div id=\"balshort\">{$pdetail['liq_cash']}</div></td></tr></table></div>
					\n<label for=\"amount\">No. of Shares <input type=\"text\" onkeypress=\"return isNumberKey(event,$value,$max,{$pdetail['liq_cash']},2)\" name=\"amount\" value=\"\" id=\"txtamt\"></label><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"color:#777; font-size:10px;\">Maximum No. of Shares: {$max}</span>";
					break;
				case "Sell":
					$sql = "select amount,avg from bought_stock where id = '{$player_id}' and symbol = '{$symbol}' limit 1";
					$resultset = mysql_query($sql);
					$result = mysql_fetch_array($resultset);
					$max = floor($result['amount']);
					$form3 .= "<div id=\"totvalm\"><table id=\"totvaltable\"><tr><th>Gain</th><th>Broker Charges</th><th>Total Value</th><th>Cash at Hand</th></tr><tr><td id=\"coltotval\"><div id=\"sprofit\">0.00</div></td><td id=\"coltotval\"><div id=\"broker\">0</div></td><td id=\"coltotval\"><div id=\"totval\">0</div></td><td id=\"coltotval\"><div id=\"balsell\">{$pdetail['liq_cash']}</div></td></tr></table></div>
					<label for=\"amount\">No. of Shares <input type=\"text\"  onkeypress=\"return isNumberKey(event, $value,$max,{$pdetail['liq_cash']},3,{$result['avg']})\" name=\"amount\" value=\"\" id=\"txtamt\"></label><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"color:#777; font-size:10px;\">Maximum No. of Shares: {$max}</span>";
					break;
				case "Cover":
					$sql = "select amount,val from short_sell where id = '{$player_id}' and symbol = '{$symbol}' ";
					$resultset = mysql_query($sql);
					$result = mysql_fetch_array($resultset);
					$max = floor($result['amount']);
					$form3 .= "<div id=\"totvalm\"><table id=\"totvaltable\"><tr><th>Gain</th><th>Broker Charges</th><th>Total Value</th><th>Cash at Hand</th></tr><tr><td id=\"coltotval\"><div id=\"cprofit\">0.00</div></td><td id=\"coltotval\"><div id=\"broker\">0</div></td><td id=\"coltotval\"><div id=\"totval\">0</div></td><td id=\"coltotval\"><div id=\"balcover\">{$pdetail['liq_cash']}</div></td></tr></table></div>
					\n<label for=\"amount\">No. of Shares<input type=\"text\"  onkeypress=\"return isNumberKey(event, $value,$max,{$pdetail['liq_cash']},4,{$result['val']})\" name=\"amount\" value=\"\" id=\"txtamt\"></label><br/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style=\"color:#777; font-size:10px;\">Maximum No. of Shares: {$max}</span>";
					
					break;
			}
			$stock_details .= "
							</ul>
							</div>";
							
			if(isset($_POST['submit'])){
				$no = trim($_POST['amount']);
				if(isnum($no) != "" || $no == 0 || $no > $max){
					$form3 .= "\n<br/><p class=\"wrong\">Invalid number of shares</p>";
				}else{
					$type = $_POST['type'];
					switch($type){
						case "Buy":
							$sql = "select * from bought_stock where id = '{$player_id}' and symbol = '{$symbol}'";
							$resultset = mysql_query($sql);
							if(mysql_num_rows($resultset) == 0){
								$sql = "insert into bought_stock values( '{$player_id}' , '{$symbol}' , {$no}, {$value} )";
							}else{
								$result = mysql_fetch_array($resultset);
								$n = $result['amount'] + $no;
								$avg = (($result['amount'] * $result['avg']) + ($no * $value))/$n;
								$sql = "update bought_stock set amount = {$n}, avg = {$avg} where id = '{$player_id}' and symbol = '{$symbol}'";
							}
							$resultset = mysql_query($sql) or die(mysql_error());
							if($resultset){
								
								$tm = strftime("%Y-%m-%d %H:%M:%S", time());
								$hsql = "insert into history (`t_time`, `p_id`, `t_type`, `symbol`, `skey`, `amount`, `value`, `p_mval`, `p_liqcash`) values('$tm', '{$player_id}', 'b', '$symbol', '-1', '$no',  '$value', '$mval', '$money')";
								mysql_query($hsql) or die(mysql_error());		
								
								
								$money = $pdetail['liq_cash'] - round(($no * $value * 1.002));
								$sql = "update player set liq_cash = {$money}, rank='1' where id = '{$player_id}'";
								$resultset = mysql_query($sql);
								$_SESSION['liq_cash'] = $money;
								valupdate($player_id);
								$trade=1;
							}
							break;
						case "Sell":
							$sql = "select * from bought_stock where id = '{$player_id}' and symbol = '{$symbol}'";
							$resultset = mysql_query($sql);
							$result = mysql_fetch_array($resultset);
							$amt = $result['amount'];
							$amt = $amt - $no;
							if($amt == 0){
								$sql = "delete from bought_stock where id = '{$player_id}' and symbol = '{$symbol}'";
							}else{
								$sql = "update bought_stock set amount = {$amt} where id = '{$player_id}' and symbol = '{$symbol}'";
							}
							$resultset = mysql_query($sql);
							if($resultset){
							
								$tm = strftime("%Y-%m-%d %H:%M:%S", time());
								$hsql = "insert into history (`t_time`, `p_id`, `t_type`, `symbol`, `skey`, `amount`, `value`, `p_mval`, `p_liqcash`) values('$tm', '{$player_id}', 's', '$symbol', '-1', '$no',  '$value', '$mval', '$money')";
								mysql_query($hsql) or die(mysql_error());	
							
								$money = $pdetail['liq_cash'] + round(($no * $value)) - round(0.002*($no * $value));
								$sql = "update player set liq_cash = '{$money}' where id = '{$player_id}'";
								$resultset = mysql_query($sql);
								$_SESSION['liq_cash'] = $money;
								valupdate($player_id);
								$trade=1;
							}
							break;
						case "Short":
							$t = strftime("%Y-%m-%d", time());
							$sql = "select * from short_sell where id = '{$player_id}' and symbol = '{$symbol}' and day = '{$t}'";
							$resultset = mysql_query($sql);
							if(mysql_num_rows($resultset) == 0){
								$sql = "insert into short_sell values( '{$player_id}' , '{$symbol}' , {$no} , {$value}, '{$t}' )";
							}else{
								$result = mysql_fetch_array($resultset);
								$avg = $result['val'];
								$n = $result['amount'] + $no;
								$avg = (($result['amount'] * $avg) + ($no * $value))/$n;
								$sql = "update short_sell set amount = {$n}, val = {$avg} where id = '{$player_id}' and symbol = '{$symbol}' and day = '{$t}'";
							}
							$resultset = mysql_query($sql);
							if($resultset){
							
								$tm = strftime("%Y-%m-%d %H:%M:%S", time());
								$hsql = "insert into history (`t_time`, `p_id`, `t_type`, `symbol`, `skey`, `amount`, `value`, `p_mval`, `p_liqcash`) values('$tm', '{$player_id}', 'ss', '$symbol', '-1', '$no',  '$value', '$mval', '$money')";
								mysql_query($hsql) or die(mysql_error());	
							
								$money = $pdetail['liq_cash'] - round(($no * $value * 0.002));
								$shrtmoney=$pdetail['short_val'] + round($no * $value);
								$sql = "update player set liq_cash = '{$money}', short_val = '$shrtmoney', rank='1' where id = '{$player_id}'";
								$resultset = mysql_query($sql);
								$_SESSION['liq_cash'] = $money;
								valupdate($player_id);
								$trade=1;
							}
							break;
						case "Cover":
							$n = $no;
							$n = $n + 0;
							if($n==0)
							{
								$shortprofit=0;
								$coveramt=0;
							}
							while($n != 0){
								$sql = "select * from short_sell where (id = '{$player_id}' and symbol = '{$symbol}') order by day asc limit 1" ;
								$resultset = mysql_query($sql) ;
								$result = mysql_fetch_array($resultset);
								$amt = $result['amount'];
								$shortprofit=($result['val']-$value)*$n;
								$coveramt=$result['val']*$n;
								if($amt > $n){
									$amt = $amt - $n;
									$n = 0;
								}else{
									$n = $n - $amt;
									$amt = 0;
								}
								if($amt == 0){
									$sql = "delete from short_sell where id = '{$player_id}' and symbol = '{$symbol}' order by day asc limit 1";
								}else{
									$sql = "update short_sell set amount = {$amt} where id = '{$player_id}' and symbol = '{$symbol}' order by day asc limit 1";
								}
								$resultset = mysql_query($sql) or die(mysql_error());
							}
							if($resultset){
							
								$tm = strftime("%Y-%m-%d %H:%M:%S", time());
								$hsql = "insert into history (`t_time`, `p_id`, `t_type`, `symbol`, `skey`, `amount`, `value`, `p_mval`, `p_liqcash`) values('$tm', '{$player_id}', 'c', '$symbol', '-1', '$no',  '$value', '$mval', '$money')";
								mysql_query($hsql) or die(mysql_error());	
								
								$newshort_val=$pdetail['short_val']-$coveramt;
								$money = $pdetail['liq_cash'] + $shortprofit - round(0.002*($no * $value));
								$sql = "update player set liq_cash = {$money}, short_val='$newshort_val' where id = '{$player_id}'";
								$resultset = mysql_query($sql) or die(mysql_error());
								$_SESSION['liq_cash'] = $money;
								valupdate($player_id);
								$trade=1;
							}
							break;
					}
				}
			}
			$form3 .= "\n&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><input  type=\"submit\" class=\"redbutton\" name=\"submit\" value=\"Execute\"> \n </form>";
		}
	}
	}else{
		
		$mtime = false;
	}
?>

  <?php metadetails(); ?>
     <link rel="stylesheet" type="text/css" href="scripts/chosen.css" />
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="scripts/chosen.jquery.js" ></script>
  <script type="text/javascript">
$(document).ready(function() {
  if($.browser.msie)
   {
     $('#totvalm').hide();
    }

});
function displayval(val,num,bal,flag, oprice)
{

if(isNaN(num*val))
	{
	document.getElementById("totval").innerHTML=0;
	document.getElementById("broker").innerHTML=0;
	document.getElementById("cprofit").innerHTML=0;
	document.getElementById("sprofit").innerHTML=0;
	document.getElementById("balbuy").innerHTML=bal;
	document.getElementById("balshort").innerHTML=bal;
	document.getElementById("balcover").innerHTML=bal;
	document.getElementById("balsell").innerHTML=bal;
	}
else
	{

	document.getElementById("totval").innerHTML=(num*val*1.002).toFixed(2);
	
	if(flag==1)  document.getElementById("balbuy").innerHTML=(bal-((num*val)*1.002)).toFixed(2);
	if(flag==2) document.getElementById("balshort").innerHTML=(bal-((num*val)*0.002)).toFixed(2);
	if(flag==3) 
	{
		
		document.getElementById("balsell").innerHTML=(bal+((num*val)*0.998)).toFixed(2);
		document.getElementById("sprofit").innerHTML=((val-oprice)*num-(val*num*0.004)).toFixed(2);
	}
	if(flag==4)
	{	
	 	document.getElementById("balcover").innerHTML=(bal+(oprice-val)*num-((num*val)*0.002)).toFixed(2);
	 	document.getElementById("cprofit").innerHTML=((oprice-val)*num-(val*num*0.004)).toFixed(2);
	}
	document.getElementById("broker").innerHTML=(num*val*0.002).toFixed(2);
	
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
           else var num = document.getElementById("txtamt").value 
         if(num<=max)
         {
           displayval(val,num,bal,flag,oprice);
           return true; 
           }
         else 
           return false;
         }
      }
      
</script>
</head>
<body>
<div id="content">
	<?php navigation("trade"); ?>
<br/>
	<div id="trade">
		<?php
	   if($mtime == true){
			$form1= "<h2>Stock Order Form</h2>
			<form method=\"get\" action=\"trade.php\" class=\"first_form\" id=\"showform\" >
				<label for=\"transaction\"> <select name=\"type\" id=\"type\">
										<option value=\"Buy\"";

			if($tflag == 1) {if($type == "Buy")  $form1 .= "selected"; } else  $form1 .="selected";
			$form1 .= ".>Buy</option>
						<option value=\"Sell\"";
			if($tflag == 1) if($type == "Sell") $form1 .= "selected";
			$form1 .= ">Sell</option>
						<option value=\"Short\"";
			if($tflag == 1) if($type == "Short") $form1 .= "selected";
			$form1 .= ">Short Sell</option>
					<option value=\"Cover\"";
			if($tflag == 1) if($type == "Cover") $form1 .= "selected";
			$form1 .= ">Cover</option>
						</select></label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" value =\"Go\"></form>";
			
			echo $form1;
			if($trade==1)
				echo "<p class=\"big\">Traded Successfully</p>";
			else
			{
				if($tflag == 1){
					echo $form2;
				}
				if(isset($form3)){
					echo $form3;
					echo $stock_details;
				}
			}

		}
		
		else
		
		{
			
			echo "<p class=\"big\">Markets are closed.<br/>Open on weekdays from 9:00 am to 3:30 pm.</p>";
			
		}
		?>

	</div><!-- trade -->
	
	</div><!-- content_main -->
</div><!--content-->
</body>