<?php require_once("includes/global.php");
if(!isset($_SESSION['username']))
		header("Location: index.php");
   metadetails();
?>

      
</head>
<body>
<div id="content">
	<?php navigation("rules"); ?>
	<div id="help">
	<ul>
	<li> General
	<p>The listed stocks and prices are based on National Stock Exchange(NSE) Top 50 (NIFTY). Every new player is provided with a kitty of Rs. 25,00,000 at the start. The markets operate from 9:00AM IST to 3:30PM IST on all days of the week except Saturdays, Sundays and Offical Holidays declared by the exchange. Your Market Value is the aggregate of the current value of owned stocks and profit from shorted stocks. Your Net Worth is the sum of Market Value and Cash currently in hand </p>
	</li>
	<li>Trading Rules in the Game
	<p>For every transaction 0.2% of the total transaction value is charged as brokerage.</p>

<h3>Buying and Selling</h3>
<p>You can only invest 1/6th of your net worth in a SINGLE stock. You must have at least 25% of the total shorted value as liquid cash.</p>
<h3>Short Selling</h3>
<p>The maximum amount you can short sell any particualry share is limited by 1/6th of {Net Worth - Total shorted Value }. But at any point you cannot short for more than 4 times the cash at hand, as you must have the capability to cover the shorted stocks. </p>
<h3>Cover</h3>
<p>In the game, shorted stocks are automatically covered at market close every day. However you can cover at an earlier point of time also.</p>	
	</li>
	<li>Scheduling a Transaction
	<p>You can schedule a transaction to happen at any predefined value. The same rules for trading applies to scheduling. <br/>If you don't meet the criteria, your schedules may remain as pending till you have enough potential to complete the transaction.</p>
	<p>You may schedule a sell or cover shares scheduled to be bought or shorted also. </p>        
        </li>
	<li>Ranking & Winners
	<p>Players are ranked in 3 ways: Based on the daily gain, weekly gain, and the overall gain. </p>
	
	<p>A weekly winner will be declared based on their weekly gains at the end of each week.
	At the end of the game, an overall winner will be declared based on their overall gains.</p>
	</li> 
	
	</ul>
	</div><!-- help -->
	
	</div><!-- content_main -->
</div><!--content-->
</body>
