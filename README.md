Install Instructions
====================

1) Fill in Details in config.ini
2) Set permissions of config.ini to 500
2) Go to admin/setup.php?key=mainkey  (mainkey is the key you provided in config.ini)
3) Once database is setup, BnB should be functional. Just check to make sure all the functions are working.
4) Set up maintenance scripts in perp to run as follows :
	a) Val_update every 1 minute on working days during runtime.
	b) Short_update at the close of each Market day. (ie 3.30 PM by default)
	c) Day_update once a day before Market opens ( around 8.30 AM)
	d) Week_update once a week before Market opens ( Monday, 8.30 maybe)