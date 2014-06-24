<?php
class ldap{
   public $LDAP_Host = LDAP_Host;
   public $LDAP_Host_PORT = LDAP_Host_PORT;
   
   
   # this function is used to connect & search LDAP
   private function connectldap_request($ldap_search){
      $lc = ldap_connect($this->LDAP_Host, $this->LDAP_Host_PORT);
      ldap_set_option($lc, LDAP_OPT_NETWORK_TIMEOUT, 3); # 3 second timeout limit
      ldap_set_option ($lc, LDAP_OPT_REFERRALS, 0);
      ldap_set_option($lc, LDAP_OPT_PROTOCOL_VERSION, 3);
      ldap_bind($lc);
      
      $sr = ldap_search($lc,$ldap_search['basedn'],$ldap_search['filter']);
      $info = ldap_get_entries($lc, $sr);
            
      ldap_close($lc);
      return $info;
      }

   
   # get list of the groups available in the specific OU
   function get_LDAP_Groups($ou){
      $ou2 = ($ou=='requestable')?', ou=groups':'';
      $ldap_search=array("basedn"=>"ou=$ou $ou2, dc=cesb,dc=uky,dc=edu",
                         "filter"=>"objectclass=groupofuniquenames",
                         "attributes"=>""); 
      return $this->connectldap_request($ldap_search);
      }  
   
   
   
   # check if entry already exists in LDAP 
   function check_LDAP_User($u){
      $ldap_search=array("basedn"=>"ou=people,dc=cesb,dc=uky,dc=edu",
                         "filter"=>"uid=$u",
                         "attributes"=>""); 
      $info = $this->connectldap_request($ldap_search);      
      if($info['count']){ return true; }
      else{ return false; }
      } 
    
    
   
   # validate login via LDAP server authentication 
   function ldap_valiate_adminlogin($u,$password){      
      $uname  = "uid=$u,ou=special users,dc=cesb,dc=uky,dc=edu";
      $lc = ldap_connect($this->LDAP_Host);
      $ldapbind = ldap_bind($lc,$uname,$password);         
      if($ldapbind){ $v = true; }
      else{ $v = false; }
      
      ldap_close($lc);
      return $v;
      }
   


   # function list all users supplied the OU, username, and password
   function get_LDAP_usersentries($u,$p,$ou){
      $u = "uid=$u,ou=special users,dc=cesb,dc=uky,dc=edu";
      $ldaptree = "ou=$ou,dc=cesb,dc=uky,dc=edu";
      $lc = ldap_connect($this->LDAP_Host);
      $ldapbind = ldap_bind($lc,$u,$p);       
      $result = ldap_search($lc,$ldaptree, "(cn=*)");
      $data = ldap_get_entries($lc, $result);
      
      #echo "Number of entries found: " . ldap_count_entries($ldapconn, $result);
      ldap_close($lc);
      return $data;
      }

   }


?>