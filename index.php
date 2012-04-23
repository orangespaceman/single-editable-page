<?php

  // import lib files
  require_once("_includes/php/DB.php");
  require_once("_includes/php/PageBuilder.php");
  require_once("_includes/php/Login.php");  

  // set config file
  $configFile = is_file("config.php") ? "config.php" : "config.sample.php";

  // import config
  $conf = parse_ini_file($configFile, true);

  // start the app
  $db           = new DB;
  $pageBuilder  = new PageBuilder;
  $login        = new Login;


  // calculate what page we're on
  $page = (isset($_GET['page'])) ? $_GET['page'] : "";
  switch ($page) {
    
    // history page
    case 'history':

      // condition : log-in required?
      if ($conf['options']['doLoginForView'] || $conf['options']['doLoginForEdit']) {
        session_start();

        // condition : if not currently logged in, show form
        $loggedin = $login->doLogin();
        if (!$loggedin) {
          echo $pageBuilder->buildLoginPage();
          exit;
        }
      }

      $update = null; 

      // condition : save content?
      if (isset($_POST) && isset($_POST['restore']) && !empty($_POST['restore'])) {
        $update = $db->restore();
      }

      // generate page
      echo $pageBuilder->buildHistoryPage($update);
      
      break;
    


    // edit page
    case 'edit':

      // condition : log-in required?
      if ($conf['options']['doLoginForView'] || $conf['options']['doLoginForEdit']) {
        session_start();

        // condition : if not currently logged in, show form
        $loggedin = $login->doLogin();
        if (!$loggedin) {
          echo $page->buildLoginPage();
          exit;
        }
      }

      $update = null; 

      // condition : save content?
      if (isset($_POST) && isset($_POST['content']) && !empty($_POST['content'])) {
        $update = $db->save();
      }

      // generate page
      echo $pageBuilder->buildEditPage($update);

      break;



    // home page
    case 'home':
    case '':
    default:

      // condition : log-in required?
      if ($conf['options']['doLoginForView']) {
        session_start();

        // condition : if not currently logged in, show form
        $loggedin = $login->doLogin();
        if (!$loggedin) {
          echo $pageBuilder->buildLoginPage();
          exit;
        }
      } 

      // generate page
      echo $pageBuilder->buildIndexPage();
      
      break;
  }
