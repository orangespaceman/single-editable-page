<?php  
/**
 * This class is responsible for the main login functionality
 *
 */
class Login {

  // import globals
  protected $db;
  protected $conf;

  /*
   *
   */
  function __construct() {
    global $db, $conf;
    $this->db = $db;
    $this->conf = $conf;
  }


  /*
   * Simple login 
   */
  function doLogin() {

    $logins = $this->conf['users'];

    // condition : if form is posted, assess its validity
    if (isset($_POST) && isset($_POST['u']) && isset($_POST['p'])) {

      $u = $this->db->sanitise($_POST['u']);
      $p = $this->db->sanitise($_POST['p']);

      foreach($logins as $username => $password) {
        if ($u === $username && $p === $password) {
          $_SESSION['u'] = $u;
          $_SESSION['p'] = $p;
          return true;
        }
      }
    }


    // condition : if logged in already, double-check...
    if (isset($_SESSION) && isset($_SESSION['u']) && isset($_SESSION['p'])) {
      foreach($logins as $username => $password) {
        if ($_SESSION['u'] === $username && $_SESSION['p'] === $password) {
          return true;
        }
      }
    }
    
    return false;
  }
}