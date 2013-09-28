<?php
 require_once("includes/global.php");
		if(!isset($_SESSION['username']))
				header("Location: index.php");

		metadetails();
?>


	<link rel="stylesheet" href="scripts/chosen.min.css">
	<script type="text/javascript" src="scripts/ticker.js"></script>
	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  
		
   <script type="text/javascript">
			function updateLookup()
			{
				   var targeturl = <?php  if(isset($_GET['symbol']))	
												{ echo "\"updatelookup.php?symbol=".urlencode($_GET['symbol'])."\""; }
										  else 
												{ echo "\"updatelookup.php\""; }?>;							
					$.ajax({
						url:targeturl
					   ,dataType: 'HTML'
					   ,success: function(data, status, xhr){
						   $('#lookup').html($(data).html());
					   }
					});
				}
		</script>
</head>
<body onload="updateLookup()">
<div id="content">
		<?php navigation("lookup"); ?>
<br/>
		<div id="lookupform">
				<form method="get" action="lookup.php" name="lookup" onsubmit="updateLookup()"> 
						
						<select data-placeholder="Choose a Stock..." name="symbol"  style="width:200px; text-align:left;" class="chosen-select">
						<option></option>
						<?php $out="";
								$sql = "select symbol from symbols";
								$result = mysql_query($sql) or die(mysql_error());
								while($sym = mysql_fetch_assoc($result)){
										$out .= "<option onclick=\"document.forms['lookup'].submit()\"";
										if($_GET['symbol']==$sym['symbol']) $out .= "selected   ";
										$out .= " value=\"{$sym['symbol']}\">{$sym['symbol']}</option>";
										
								}
								echo $out;
						?>
						</select>
						<input type="submit" style="float:right;" value="Go"/>
				<script type="text/javascript"> $(".chosen-select").chosen({no_results_text: "No stocks found"}).change(updateLookup()); </script>
				</form>

		</div><!-- lookup -->
		<div id="lookup">
		</div>
		
</div><!--content-->


  <script src="scripts/chosen.jquery.min.js"></script>
  <script type="text/javascript"> $(".chosen-select").chosen(); </script>

</body>
</html>