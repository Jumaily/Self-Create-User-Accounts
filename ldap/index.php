<?php
define('PATH',"./");
require_once(PATH."conf/site.conf.php");
require_once(PATH."inlcudes/cls.inc.php");
require_once(PATH."classes/main.class.php"); $main = new main();
require_once("simple-php-captcha.php"); $SESSION->set_var('captcha', simple_php_captcha());

if($SESSION->get_var('proceed')){ header('Location: useradd.php'); exit;}
$LDAPgroups = @$main->ldap->get_LDAP_Groups('requestable'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>CESB Account Request</title>
   <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/signin.css" rel="stylesheet">
</head>

<body>

    <div class="container">
    <h2 align="center" class="form-signin-heading">CESB Account Request</h2>    
			<form name="form1" method="post" action="<?php $_SERVER['PHP_SELF'] ?>" role="form" class="form-signin">
				<fieldset>
					<p>
						<input type="text" placeholder="First Name" name="firstname" class="form-control" value="<?php if(isset($_POST["firstname"])) echo $_POST["firstname"] ?>" required autofocus />
					</p>
					
					<p>
						<input type="text" placeholder="Last Name" name="lastname" class="form-control" value="<?php if(isset($_POST["lastname"])) echo $_POST["lastname"] ?>" required/>
					</p>
					
					<p>
						<input type="email" placeholder="Email" name="email" class="form-control" value="<?php if(isset($_POST["email"])) echo $_POST["email"] ?>" required/>
						<?php if($SESSION->get_var('email')!='') echo "<font color=\"red\">{$SESSION->get_var('email')}</font>"; ?>
					</p>
					
					<p>
						<input type="text" placeholder="Phone Number" name="phone" class="form-control" value="<?php if(isset($_POST["phone"])) echo $_POST["phone"] ?>" required/>
						<?php if($SESSION->get_var('phone')!='') echo "<font color=\"red\">{$SESSION->get_var('phone')}</font>"; ?>
					</p>
					
					<p>
						<input type="password" placeholder="Password" name="pass1" class="form-control" utocomplete="off" required/>
					</p>
					
					<p>
						<input type="password" placeholder="Password (Confirm)" name="pass2" class="form-control" utocomplete="off" required/>
						<?php if($SESSION->get_var('password')!='') echo "<font color=\"red\">{$SESSION->get_var('password')}</font>"; ?>
					</p>
					
					<p>
						<input type="text" placeholder="Username (UK LinkBlue Preferred)" name="uname" class="form-control" value="<?php if(isset($_POST["uname"])) echo $_POST["uname"] ?>" required />
						<?php if($SESSION->get_var('uname')!='') echo "<font color=\"red\">{$SESSION->get_var('uname')}</font>"; ?>
					</p>
					
					<p align="center">
   				   <?php echo '<img src="'.$_SESSION['captcha']['image_src'].'" alt="CAPTCHA code">'; ?>
   				   <input type="text" placeholder="Type Security Code (Case Sensitive)" name="captcha" class="form-control" required />
   				   <?php if($SESSION->get_var('captchacode')!='') echo "<font color=\"red\">{$SESSION->get_var('captchacode')}</font>"; ?>
					</p>
					
				   <p>

				   <?php
				   for ($i=0; $i<(count($LDAPgroups)-1); $i++) {
				     echo "<input type=\"checkbox\" value=\"{$LDAPgroups[$i]['cn']['0']}\" name='group[]'> {$LDAPgroups[$i]['description']['0']} <br/>";
				     }
               if($SESSION->get_var('group')!='') echo "<font color=\"red\">{$SESSION->get_var('group')}</font>"; 
               ?>
               
				   </p>
					<p>
						<button class="btn btn-lg btn-primary btn-block" />Submit Request</button>
					</p>
  				</fieldset>
  			</form>
  		</div>

</body>

</html>

<?php 
 $SESSION->var_unset("firstname");
 $SESSION->var_unset("lastname");
 $SESSION->var_unset("email");
 $SESSION->var_unset("phone");
 $SESSION->var_unset("password");
 $SESSION->var_unset("uname");
 $SESSION->var_unset("captchacode");
 $SESSION->var_unset("group");
?>

