<?php
//Database credentials
define('DB_SERVER', 'localhost');//Db Ip, default: localhost
define('DB_USERNAME', 'root');//Db username
define('DB_PASSWORD', '');//Db Password
define('DB_NAME', 'JuanFi');//Db name
define('DB_PORT', '3306');//Db port

// Mikrotik credentials
define('MT_SERVER', '10.15.0.1');//Mikrotik IP
define('MT_USERNAME', 'Development');//Mikrotik Username
define('MT_PASSWORD', 'Development'); //Mikrotik Password
define('MT_PORT', '8728'); //Mikrotik port
//Settings
$CURRENCY = '₱';
$INTERFACE = "ether1"; //Interface Data Usage
$REFRESH = 15000; //Topstats refreshh 15000 = 15sec
$SHARE = 70; //Max 100, examples: 70=70%, 50=50%


//Dont touch this
$mysql_db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
if (!$mysql_db) {
	die("Error: Unable to connect " . $mysql_db->connect_error);
}
