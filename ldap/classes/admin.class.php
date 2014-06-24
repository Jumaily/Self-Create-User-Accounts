<?php
class admin extends SQLite3{
   private $sqliteDB = sqliteDB;
   private $connect;
   public $misc;
   public $ldap;



   public function __construct(){
      $this->open($this->sqliteDB);
      $this->connect = new SQLite3($this->sqliteDB);

      $this->ldap = new ldap(); # include the ldap class
      $this->misc = new misc(); # include misc class, format, clean input, etc...

      # login
      if(isset($_POST['login'])){ $this->checkadmin_LDAP($_POST); }

      # modify group choices
      if(isset($_POST['user-savegroup'])){ $this->save_UserGroup(); }

      # modify user status
      if(isset($_POST['user-activate'])){ $this->save_UserStatus($_POST); }
      }



   public function __destruct(){
      $this->connect->close();
      unset($this->connect);
      }



   # add user to group then delete requests
   private function save_UserStatus($vars){
      if($vars['activateyn']=='add'){
         $linkb = $this->connect->query("SELECT * FROM users WHERE uname LIKE '{$vars['user-activate']}' LIMIT 1");
         $rows = array();
         while($row = $linkb->fetchArray(SQLITE3_ASSOC)){ array_push($rows,$row); }
         $this->add_to_LDAP($rows[0]);
         }
      $this->connect->query("DELETE FROM users WHERE uname LIKE '{$vars['user-activate']}'");
      header('Location: main.php');
      exit;
      }



   # save/modify group choices
   private function save_UserGroup($labgroup=''){
      if(isset($_POST['group']) && count($_POST['group'])){
         foreach($_POST['group'] as $g) { $labgroup .= "$g,"; }
         $labgroup = rtrim($labgroup,",");
         }
      $this->connect->query("UPDATE users SET labgroup='$labgroup' WHERE uname LIKE '{$_POST['user-savegroup']}'");
      header('Location: main.php');
      exit;
      }



   # Function to encrypt and decrypt password using 2 keys, one stored in DB & one stored in session key.
   # Encrypted password is stored in DB Temporarily
   function encrypt_decrypt($action, $string, $pw=''){
      global $SESSION;

      # Get TEMP encrypted pw & key from DB
      $linkb = $this->connect->query("SELECT * FROM admins WHERE uname LIKE '{$SESSION->get_var('login')}' LIMIT 1");
      $row = $linkb->fetchArray(SQLITE3_ASSOC);

      $encrypt_method = "AES-256-CBC";
      $secret_key = $row['ukey'];
      $secret_iv = $SESSION->get_var('token');

      # hash
      $key = hash('sha256',$secret_key);

      # iv - encrypt method AES-256-CBC expects 16 bytes
      $iv = substr(hash('sha256',$secret_iv),0,16);

      if($action=='encrypt'){ $pw = base64_encode(openssl_encrypt($string,$encrypt_method,$key,0,$iv)); }
      else if($action=='decrypt'){ $pw = openssl_decrypt(base64_decode($row["passwd"]),$encrypt_method,$key,0,$iv); }

      return $pw;
      }



   # login Admin update, write password key to DB
   private function UpdateLogin_Admin_DB($vars){
      global $SESSION;
      $u = $this->misc->cleanInput($vars["login"]);

      # set session & DB keys
      $SESSION->set_var('token',$SESSION->CreateTokenKey());
      $this->connect->query("UPDATE admins SET ukey='{$SESSION->Create_SessionKey()}' WHERE uname LIKE '$u'");

      # store password in DB temporarily
      $p = $this->encrypt_decrypt('encrypt', $vars["pass"]);
      $this->connect->query("UPDATE admins SET passwd='$p' WHERE uname LIKE '$u'");
      }



   # login check against LDAP
   private function checkadmin_LDAP($vars){
      global $SESSION;
	   $u = $this->misc->cleanInput($vars["login"]);
      $proceed = @$this->ldap->ldap_valiate_adminlogin($vars["login"],$vars["pass"]);
      if($proceed){
	      $SESSION->set_var('login',$u);
	      $SESSION->set_var('adminproceed',true);
	      $this->UpdateLogin_Admin_DB($vars);
         }
      else{ $SESSION->set_var('login',"Invalid User"); }
      }



   # add user to ldap directory
   private function add_to_LDAP($vars){
      global $SESSION;
      $uname  = "uid={$SESSION->get_var('login')},ou=special users,dc=cesb,dc=uky,dc=edu";
      $p = $this->encrypt_decrypt('decrypt','');

      $lc = ldap_connect($this->ldap->LDAP_Host);
      $ldapbind = ldap_bind($lc,$uname,$p);

      $info["givenname"][0]       = $vars['fname'];
      $info["sn"][0]              = $vars['lname'];
      $info["cn"][0]              = "{$vars['fname']} {$vars['lname']}";
      $info["telephonenumber"][0] = $vars['phone'];
      $info["mail"][0]            = $vars['email'];
      $info["userpassword"][0]    = $vars['passwd'];
      $info["objectclass"][0]     = $vars['labgroup'];
      $info["objectclass"][0]     = 'top';
      $info["objectclass"][1]     = 'person';
      $info["objectclass"][2]     = 'organizationalPerson';
      $info["objectclass"][3]     = 'inetorgperson';
      $info["objectclass"][4]     = 'inetuser';
      $info["objectclass"][4]     = 'inetuser';


      # add data to AD
      ldap_add($lc, "uid={$vars['uname']},ou=people,dc=cesb,dc=uky,dc=edu", $info);

      # add to groups
      if($vars['labgroup']!=''){
         $selected_groups = explode(',',$vars['labgroup']);
         $ldapgroups_req = $this->ldap->get_LDAP_Groups('requestable');

         $req_group = array();
         for ($i=0; $i<(count($ldapgroups_req)-1); $i++){ array_push($req_group, $ldapgroups_req[$i]['cn']['0']); }

		   for($i=0; $i<count($selected_groups); $i++){
		      #   Note: if it not in the requestable group, then take out "ou=requestable"
		      $ou_group = (in_array($selected_groups[$i], $req_group))?'requestable, ou=groups':'groups';
		      $dn = "cn={$selected_groups[$i]}, ou=$ou_group, dc=cesb,dc=uky,dc=edu";
            $entry['uniquemember'] = "uid={$vars['uname']}, ou=people, dc=cesb,dc=uky,dc=edu";

            # Add to group
            ldap_mod_add($lc, $dn, $entry);
		      }
         }
      ldap_close($lc);
      return 1;
      }



   # get requested users from DB
   function get_allusers(){
      $linkb = $this->connect->query("SELECT * FROM users WHERE 1 ORDER BY id DESC");
      $rows = array();
      while($row = $linkb->fetchArray(SQLITE3_ASSOC)){ array_push($rows,$row); }
      return $rows;
      }



   # Clear KEY & Password from DB upon logout request
   function logout_admin($u){ $this->connect->query("UPDATE admins SET passwd='', ukey='' WHERE uname LIKE '$u'"); }


   }
?>
