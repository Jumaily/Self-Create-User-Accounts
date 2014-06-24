<?php
error_reporting(E_ALL); ini_set('display_errors', '1');
date_default_timezone_set('America/New_York');

# Define LDAP Server & Port
define('LDAP_Host',"ds01.cesb.uky.edu");
define('LDAP_Host_PORT',389);

# path to database file
define('sqliteDB',PATH."dbs/ldap_users.sqlite");
?>
