<?php 
 require_once("includes/global.php");
            if(!isset($_SESSION['username']))
                header("Location: index.php");

	metadetails();
        if(isset($_GET['t']))
                $t=$_GET['t'];
        else
               $t="help";
 ?>

  
    
</head>
<body>
<div id="content">
	<?php 
	if(isset($_SESSION['username']))
		navigation("help");
	else
	echo "<div id=\"banner\">
		<h1>Bulls And Bears</h1>
	</div><!-- banner -->
	<div id=\"navigation\">
		<ul>
			<li><a href=\"index.php\">Login</a></li>
			<li class=\"page\"><span>Help</span></li>
		</ul>
	</div>";
	?>
<br/>
         <div id="portfolio">
         <form method="get" id="showform" action="help_out.php"><input type="hidden" value=
	<?php if($t=="help") echo "rules"; else echo "help"; ?> name="t"/><input type="submit" value=
	<?php if($t=="help") echo "\"Show Rules\""; else echo "\"Show Help\"" ?> /></a>
	</form></div>
	<!--navigation-->
	<div id="content_main">
	<div id="help">
<?php
     if($t=="help")
{    
	$out .= "<h2>Help</h2><ul>
	<li> What Are Stocks?
	<p>Plain and simple, stock is a share in the ownership of a company. Stock represents a claim on the company's assets and earnings. As you acquire more stock, your ownership stake in the company becomes greater. Whether you say shares, equity, or stock, it all means the same thing.<br />It must be emphasized that there are no guarantees when it comes to individual stocks. Some companies pay out dividends, but many others do not. And there is no obligation to pay out dividends even for those firms that have traditionally given them. Without dividends, an investor can make money on a stock only through its appreciation in the open market. On the downside, any stock may go bankrupt, in which case your investment is worth nothing.</p>
	</li>
	<li>Different Types Of Stocks
	<p>There are two main types of stocks: common stock and preferred stock.</p>

<h3>Common Stock</h3>
<p>Common stock is, well, common. When people talk about stocks they are usually referring to this type. In fact, the majority of stock is issued is in this form</p>
 <h3>Preferred Stock </h3>
<p>Preferred stock represents some degree of ownership in a company but usually doesn't come with the same voting rights. (This may vary depending on the company.) With preferred shares, investors are usually guaranteed a fixed dividend forever. This is different than common stock, which has variable dividends that are never guaranteed.</p>
	</li>
	<li>What Is Short Selling?
	<p>Short selling is the selling of a stock that the seller doesn't own. More specifically, a short sale is the sale of a security that isn't owned by the seller, but that is promised to be delivered. That may sound confusing, but it's actually a simple concept.When you short sell a stock, your broker will lend it to you. The stock will come from the brokerage's own inventory, from another one of the firm's customers, or from another brokerage firm. The shares are sold and the proceeds are credited to your account. Sooner or later, you must \"close\" the short by buying back the same number of shares (called covering) and returning them to your broker. If the price drops, you can buy back the stock at the lower price and make a profit on the difference. If the price of the stock rises, you have to buy it back at the higher price, and you lose money.</p>
        <p>In the game shorted stock are automatically covered at market close.</p>
	</li>
	<li>What Causes Stock Prices To Change?
	<p>Stock prices change every day as a result of market forces. By this we mean that share prices change because of supply and demand. If more people want to buy a stock (demand) than sell it (supply), then the price moves up. Conversely, if more people wanted to sell a stock than buy it, there would be greater supply than demand, and the price would fall.</p>
	</li>
	<li>The Bulls, The Bears And The Farm
	<h3>The Bulls</h3>
<p>A bull market is when everything in the economy is great, people are finding jobs, gross domestic product (GDP) is growing, and stocks are rising. Things are just plain rosy! Picking stocks during a bull market is easier because everything is going up. Bull markets cannot last forever though, and sometimes they can lead to dangerous situations if stocks become overvalued. If a person is optimistic and believes that stocks will go up, he or she is called a \"bull\" and is said to have a \"bullish outlook\".</p>
<h3>The Bears</h3>
<p>A bear market is when the economy is bad, recession is looming and stock prices are falling. Bear markets make it tough for investors to pick profitable stocks. One solution to this is to make money when stocks are falling using a technique called short selling. Another strategy is to wait on the sidelines until you feel that the bear market is nearing its end, only starting to buy in anticipation of a bull market. If a person is pessimistic, believing that stocks are going to drop, he or she is called a \"bear\" and said to have a \"bearish outlook\".</p>

<h4>The Other Animals on the Farm - Chickens and Pigs</h4>
<p>Chickens are afraid to lose anything. Their fear overrides their need to make profits and so they turn only to money-market securities or get out of the markets entirely. While it's true that you should never invest in something over which you lose sleep, you are also guaranteed never to see any return if you avoid the market completely and never take any risk,<br>Pigs are high-risk investors looking for the one big score in a short period of time. Pigs buy on hot tips and invest in companies without doing their due diligence. They get impatient, greedy, and emotional about their investments, and they are drawn to high-risk securities without putting in the proper time or money to learn about these investment vehicles. Professional traders love the pigs, as it's often from their losses that the bulls and bears reap their profits.</p>
	</li>
	</ul><br/><br/><br/>";
}
else
{
        $out .="<h2>Rules</h2><ul>
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
	
	</ul><br/><br/><br/>";

        

}
echo $out;
?>
	</div><!-- help -->
	
	</div><!-- content_main -->
</div><!--content-->
</body>
