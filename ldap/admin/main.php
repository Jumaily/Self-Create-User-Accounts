<?php
define('PATH',"../");
require_once(PATH."conf/site.conf.php");
require_once(PATH."inlcudes/cls.inc.php");
require_once(PATH."classes/admin.class.php"); $admin = new admin();


if($SESSION->get_var('login')=='' || !$SESSION->get_var('login')){ header("location: logout.php"); }
$users = $admin->get_allusers(); 
$LDAPgroups = @$admin->ldap->get_LDAP_Groups('groups');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>CESB Users</title>
   <link href="../css/bootstrap.min.css" rel="stylesheet">
   <link href="../css/jumbotron-narrow.css" rel="stylesheet">
   <script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
   <script src="../js/bootstrap.min.js"></script>
</head>

<body>

  <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li class="active">
            <button type="button" class="btn btn-default " onclick="window.open('show-users.php');return false;">Show All Users</button>
          </li>
          <li class="active">
             <button type="button" class="btn btn-default" onclick="window.location.href='logout.php'">Logout <?php echo $SESSION->get_var('login');?></button>
          </li>
        </ul>
        <div class="alert alert-info">CESB Lab Users</div>
      </div>
      
      
   <div class="bs-example">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Name</th>
          <th>Group Requested</th>
          <th>Activate User</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $v){ ?>
           <tr>
             <td>
                <button class="btn btn-info btn-xs" data-toggle="modal" data-target=".<?php echo $v['id']; ?>">
                  <?php echo "{$v['fname']} {$v['lname']}"; ?>
                </button>
             </td>
             <td>
                <button class="btn btn<?php echo ($v['labgroup'])?'-primary btn-xs':'-default btn-xs'; ?>" data-toggle="modal" data-target=".<?php echo "labgroup-{$v['id']}"; ?>">
                <?php echo ($v['labgroup'])?$v['labgroup']:'[No Group Selected]'; ?>
                </button>
             </td>
             <td>
               <form name="form11" method="post" action="<?php $_SERVER['PHP_SELF'] ?>" >
                 <input type="hidden" name="user-activate" value="<?php echo $v['uname']; ?>">
                 <button type="submit" class="btn btn-danger btn-xs" name="activateyn" value="delete">Delete Entry</button>
                 &nbsp; | &nbsp;
                 <button type="submit" class="btn btn-success btn-xs" name="activateyn" value="add">Add to LDAP</button>
               </form>
             </td>
            </tr>
         <?php } ?>
      </tbody>
    </table>
  </div>
  

   <?php  foreach ($users as $v){ ?>
       <div class="modal fade <?php echo "labgroup-{$v['id']}"; ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
             <div class="modal-content">
               <div class="panel panel-primary">
                 <div class="panel-heading">
                    <h3 class="panel-title"><?php echo "Active Groups in: {$v['labgroup']}"; ?></h3>
                 </div>
                 
                  <div class="panel-body">
                  <form name="form1" method="post" action="<?php $_SERVER['PHP_SELF'] ?>" role="form" >
                     <input type="hidden" name="user-savegroup" value="<?php echo $v['uname']; ?>">
                         <?php
                           $groups = explode(',',$v['labgroup']); 
				               for ($i=0; $i<(count($LDAPgroups)-1); $i++) {
				                  echo '<div class="checkbox">
                                       <label>
				                          '."<input type=\"checkbox\" value=\"{$LDAPgroups[$i]['cn']['0']}\" name='group[]' ";
				                  echo (in_array($LDAPgroups[$i]['cn']['0'],$groups))?'checked="checked" ':" ";
				                  echo "/> {$LDAPgroups[$i]['description']['0']}
				                  ".'</label>
                                </div>';
				                  }
                          ?>
                     <button type="submit" class="btn btn-primary">Save Requested Group</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
       </div>
       
   <?php } ?>


   <?php  foreach ($users as $v){ ?>
       <div class="modal fade <?php echo $v['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
             <div class="modal-content">
                       <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title"><?php echo "{$v['fname']} {$v['lname']}"; ?></h3>
            </div>
            <div class="panel-body">
                <table class="table table-striped">
                  <tr> <td> Username: <strong> <?php echo $v['uname']; ?> </strong></td> </tr>
                  <tr> <td> First Name: <strong> <?php echo $v['fname']; ?> </strong></td> </tr>
                  <tr> <td> Last Name: <strong><?php echo $v['lname']; ?> </strong></td> </tr>
                  <tr> <td> Email: <strong><a href="mailto:<?php echo $v['email']; ?>"><?php echo $v['email']; ?></a></strong> </td> </tr>
                  <tr> <td> Phone: <strong><?php echo $admin->misc->formatPhoneNumber($v['phone']); ?> </strong></td> </tr>
                  <tr> <td> Time Requested: <strong><?php echo $admin->misc->format_datetime($v['requested']); ?> </strong></td> </tr>
                  <tr> <td> Address Requested from: <strong><?php echo $v['iphost']; ?> </strong></td> </tr>
                  <tr> <td> Groups Requested: <strong><?php echo $v['labgroup']; ?> </strong></td> </tr>
                  <tr> <td> Active: <strong><?php echo $v['active']; ?> </strong></td> </tr>
                </table>
              </div>
             </div>
            </div>
          </div>
       </div>
       
   <?php } ?>




  </div> <!-- /container -->
</body>

</html>
