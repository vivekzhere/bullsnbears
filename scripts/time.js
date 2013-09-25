window.onload = function() { var timediv = document.createElement('div');
  	timediv.setAttribute('id','time');
 		 document.getElementById("navigation").appendChild(timediv);
  	timedisplay(); 
    }; 
setInterval( "timedisplay();", 1000 );

function timedisplay(){
	  var currentTime = new Date();
  var hours = currentTime.getHours();
  var minutes = currentTime.getMinutes();
  var seconds = currentTime.getSeconds();
  if(hours<12) var ap = " AM"; 
  else { var ap = " PM"; }
  
  if(hours%12==0) 
	var hours = 12;
  else
  	 hours = hours%12;
  if (minutes < 10)
  	minutes = "0" + minutes;
  if (seconds < 10)
  	seconds = "0" + seconds;
  var ans = hours + ":" + minutes + ":" + seconds + ap;
  
   
  document.getElementById('time').innerHTML = ans ;
};


