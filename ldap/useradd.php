<?php
define('PATH',"./");
require_once(PATH."conf/site.conf.php");
require_once(PATH."inlcudes/cls.inc.php");
require_once("classes/main.class.php"); $main = new main();

if($SESSION->get_var('proceed')){ $adduser = $main->create_user(); }
else{ header('Location: index.php'); exit; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>CESB Account Request Submitted</title>
   <link href="css/bootstrap.min.css" rel="stylesheet">
   <link href="css/jumbotron-narrow.css" rel="stylesheet">
</head>

<body>


    <div class="container">
      <div class="jumbotron">
       <h2><?php echo $adduser; ?></h2>
              
       <p>First Name: <?php echo $SESSION->get_var('firstname'); ?></p>
       <p>Last Name: <?php echo $SESSION->get_var('lastname'); ?></p>
       <p>Phone Number: <?php echo $SESSION->get_var('email'); ?></p>
       <p>E-Mail: <?php echo $SESSION->get_var('phone'); ?></p>
       <p>Username: <?php echo $SESSION->get_var('uname'); ?></p>
       <p>Group(s): <?php echo $SESSION->get_var('group'); ?></p>
       
       <p><a class="btn btn-lg btn-success" href="http://bioinformatics.cesb.uky.edu/" role="button">Continue</a></p>
       
   </div>
 </div>   
   
   
</body>
</html>
<?php $SESSION->destroy(); ?>