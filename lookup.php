<?php
 require_once("includes/global.php");
        if(!isset($_SESSION['username']))
                header("Location: index.php");

        metadetails();
?>


    <link rel="stylesheet" type="text/css" href="scripts/chosen.css" />
 
  
  <script type="text/javascript" src="scripts/ticker.js"></script>
    
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js" type="text/javascript"></script>
  <script src="scripts/chosen.jquery.js" type="text/javascript"></script>
  <script type="text/javascript"> 
  $(".chzn-select").chosen(); </script>
  
        
   <script type="text/javascript">
            //setInterval( "updateLookup();", 3000 ); 

            
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
                        
                        <select data-placeholder="Choose a Stock..." name="symbol"  style="width:200px; text-align:left;" class="chzn-select">
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
                <script type="text/javascript"> $(".chzn-select").chosen({no_results_text: "No stocks found"}).change(updateLookup()); </script>
                </form>

        </div><!-- lookup -->
        <div id="lookup">
        </div>
        
        </div><!-- content_main -->
</div><!--content-->
</body>
</html>