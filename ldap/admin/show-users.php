<?php
define('PATH',"../");
require_once(PATH."conf/site.conf.php");
require_once(PATH."inlcudes/cls.inc.php");
require_once(PATH."classes/admin.class.php"); $admin = new admin();

if($SESSION->get_var('login')=='' || !$SESSION->get_var('login')){ header("location: logout.php"); }

$data = $admin->ldap->get_LDAP_usersentries($SESSION->get_var('login'),$admin->encrypt_decrypt('decrypt',''),'people');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Show All Users</title>
   <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container">
<br />
<br />
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li class="Active">
             <button type="button" class="btn btn-default btn-primary" onClick="window.close()">Close Window</button>
          </li>
        </ul>
      </div>
      
      
      <h3>All Users</h3>
      <ol>
      <?php for ($i=0; $i<$data["count"]; $i++) { echo "<li> {$data[$i]['cn'][0]} (<strong>{$data[$i]['uid'][0]}</strong>) </li>"; } ?>
      </ol>
      
      <h3>Dump all data</h3>
      <pre>
      <?php print_r($data); ?>   
      </pre>
      
   </div>

<br />
<p align="center">
   <button type="button" class="btn btn-default btn-primary" onClick="window.close()">Close Window</button>
</p>
<br />
<br />

</body>

</html>