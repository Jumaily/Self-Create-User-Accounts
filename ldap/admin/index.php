<?php
define('PATH',"../");
require_once(PATH."conf/site.conf.php");
require_once(PATH."inlcudes/cls.inc.php");
require_once(PATH."classes/admin.class.php"); $admin = new admin();

if($SESSION->get_var('adminproceed')){ header('Location: main.php'); exit;}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>CESB Admin Login</title>
   <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/signin.css" rel="stylesheet">
</head>

<body>

    <div class="container">
    <h2 align="center" class="form-signin-heading">CESB Admin Login</h2>
			<form name="form1" method="post" action="<?php $_SERVER['PHP_SELF'] ?>" role="form" class="form-signin">
				<fieldset>
					<p>
						<input type="text" placeholder="Login" name="login" class="form-control" required autofocus />
					</p>

					<p>
					   <input type="password" placeholder="Password" name="pass" class="form-control" utocomplete="off" required/>
					</p>
               <p><?php if($SESSION->get_var('login')!='') echo "<font color=\"red\">{$SESSION->get_var('login')}</font>"; ?></p>
					<p>
						<button class="btn btn-lg btn-primary btn-block" />Login</button>
					</p>

  				</fieldset>
  			</form>
  		</div>

</body>
</html>
<?php $SESSION->destroy(); ?>
