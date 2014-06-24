<?php
define('PATH',"../");
require_once(PATH."conf/site.conf.php");
require_once(PATH."inlcudes/cls.inc.php");
require_once(PATH."classes/admin.class.php"); $admin = new admin();

# Kill Session & set password and key value in database to NULL
if($SESSION->get_var('login')!='' || $SESSION->get_var('login')){ $admin->logout_admin($SESSION->get_var('login')); }

$SESSION->destroy();
header("location: index.php");
exit;
?>
