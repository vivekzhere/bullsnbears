<?php 

require_once("includes/global.php");
	if(!isset($_SESSION['username']))
		header("Location: index.php");

 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head> 

   

    <script type="text/javascript">
	//   setInterval( "updateTable();", 30000 ); 
	    function updateTable()
	    {
		    
		    $.ajax({
		        url: 'updatemarkets.php'
		       ,dataType: 'html'
		       ,success: function(data, status, xhr){
		           $('#markets').html($(data).html());
		  	$("#marketsTable").tablesorter({widgets: ['sortPersist']});	
			   $("#marketsTable").trigger("update");		           
		       
		       }
		    });

	    }
    </script>

  <title> Bulls n' Bears | Tathva '12</title>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta name="description" content="Stock simulator" />
  <meta name="keywords" content="tathva, bulls and bears" />
  <meta http-equiv="content-language" content="en"/>
  <link rel="stylesheet" type="text/css" href="stylesheets/global.css" />
  <link rel="stylesheet" href="scripts/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
  <link rel="shortcut icon"  href="images/logo.jpg" />
      <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
  <script src="scripts/jquery.tablesorter.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="scripts/jquery.fancybox-1.3.4.pack.js"></script>
   <script src="scripts/jquery.cookie.js" type="text/javascript"></script>
     <script src="scripts/jquery.json-2.2.min.js" type="text/javascript"></script>


    <script type="text/javascript">
    
    	$(document).ready(function()
	{
		
		     $(".nav").fancybox({
				'width'				: '60%',
				'height'			: '95%',
				'autoScale'			: true,
				'transitionIn'			: 'none',
				'transitionOut'			: 'none',
				'type'				: 'iframe'
			});

		   $('#pop').load(function() {
			 $('#popup').css('display','block');
		    });
		$.tablesorter.addWidget({
		      // give the widget a id
		      id: "sortPersist",
		      // format is called when the on init and when a sorting has finished
		      format: function(table) {

		          var COOKIE_NAME = 'MY_PERSISTENT_TABLE';
		          var cookie = $.cookie(COOKIE_NAME);
		          var options = {path: '/'};

		          var data = [];
		          var sortList = table.config.sortList;
		          var id = $(table).attr('id');
		                   // If the existing sortList isn't empty, set it into the cookie and get out
		          if (sortList.length > 0) {
		              if (typeof(cookie) == "undefined" || cookie == null) {
		                  data = {id: sortList};
		              }
		              else {
		                  data = $.evalJSON(cookie);
		                  data[id] = sortList;
		              }
		              $.cookie(COOKIE_NAME, $.toJSON(data), options);
		          }
		          // Otherwise...
		          else {
		              if (typeof(cookie) != "undefined" && cookie != null) {
		                  // Get the cookie data
		                  var data = $.evalJSON($.cookie(COOKIE_NAME));
		                  // If it exists
		                  if (typeof(data[id]) != "undefined" && data[id] != null) {
		                      // Get the list
		                      sortList = data[id];
		                      // And finally, if the list is NOT empty, trigger the sort with the new list
		                      if (sortList.length > 0) {
		                          //table.config.sortList = sortList;
		                            $(table).trigger("sorton", [sortList]);
		                      }
		                   }
		              }
		          }
		      }
		  });
	
	$("#marketsTable").tablesorter({widgets: ['sortPersist']});
    
    }
	);
</script>
</head>
<body onload="updateTable()">
<div id="content">
	<?php navigation("markets"); ?>
<br/>
		
		<button onclick="updateTable()">Refresh</button><br/>
		
	<div id="markets">

	</div><!-- markets -->

	
	</div><!-- content_main -->
</div><!--content-->
</body>