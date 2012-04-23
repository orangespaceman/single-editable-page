<?php 
/*
 * database connection and calls
 */
class DB {
  
  // privates
  protected $conf;
  protected $server;
  protected $db;
  protected $table;
  protected $username;
  protected $password;
  protected $conn;
  
  /**
   * The constructor
   */
  function __construct() {

    global $conf;
    $this->conf = $conf;

    foreach ($this->conf['db'] as $key => $value) {
     $this->$key = $value;
    }
    
    $this->conn = mysqli_connect($this->server, $this->username, $this->password);
    mysqli_select_db($this->conn, $this->db);
  }

  
  /**
   * Generic MySQL select query 
   */
  function select($sql, $selectOne = false) {
    
    //run the initial query
    if ($result = mysqli_query($this->conn, $sql)) {

      //condition : if it is a single value, return it
      if ($selectOne && mysqli_num_fields($result) === 1 && mysqli_num_rows($result) === 1) {
        list($return) = mysqli_fetch_row($result);
      
      // it is a single row so return object...
      } else if ($selectOne && mysqli_num_fields($result) > 1 && mysqli_num_rows($result) === 1) {
        $return = mysqli_fetch_object($result);

      // it is more than a single row, start an array to contain each object...
      } else {
        
        //start the var to return
        $return = array();
      
        //for each row in the result, start a new object
        while ($row = mysqli_fetch_object($result)) {
          $return[] = $row;
        }
      }

    // if there was an error with the SQL syntax 
    } else {
      $return = "SQL error: " . mysqli_error($this->conn);
    }
      
    return $return;
  }


  /*
   * Select just one result and return it directly (rather than an array)
   */
  function selectOne($sql) {
    return $this->select($sql, true);
  }


  /**
   * Generic MySQL update query 
   */
  function update($sql) {
    
    //run the initial query
    if ($result = mysqli_query($this->conn, $sql)) {

      if (mysqli_affected_rows($this->conn) > 0) {
        $return = true;
      } else {
        $return = false;
      }

    // if there was an error with the SQL syntax
    } else {
      $return = "SQL error: " . mysqli_error($this->conn);
    }
      
    return $return;
  }
  
  

  /**
   * Generic MySQL add query 
   */
  function add($sql) {
    
    //run the initial query
    if ($result = mysqli_query($this->conn, $sql)) {

      if (mysqli_affected_rows($this->conn) > 0) {
        $return = mysqli_insert_id($this->conn);
      } else {
        $return = false;
      }

    // if there was an error with the SQL syntax
    } else {
      $return = "SQL error: " . mysqli_error($this->conn);
    }
      
    return $return;
  }
  


  /*
   * Sanitise content
   */
  function sanitise($val) {
    return strip_tags(mysqli_real_escape_string($this->conn, $val));
  }
  

  /*
   * Sanitise content
   */
  function escape($val) {
    return mysqli_real_escape_string($this->conn, $val);
  }

  
  /*
   *
   * Site-specific calls
   *
   */
  
  /*
   * get latest page content
   */
  function getLatest() {
    $sql = "SELECT *, date_format(modified_date, '%D %M %Y, %k:%i') as date_added from `".$this->table."` order by modified_date desc limit 0,1";
    return $this->selectOne($sql);
  }


  /*
   * get specific version page content
   */
  function getVersion($id) {
    $sql = "SELECT *, date_format(modified_date, '%D %M %Y, %k:%i') as date_added from `".$this->table."` WHERE id = ".$id." limit 0,1";
    $return = $this->selectOne($sql);
    if (count($return) < 1) {
      return $this->getLatest();
    } else {
      return $return;
    }
  }
  

  /*
   * get a list of all versions of page content
   */
  function getAll() {
    $sql = "SELECT *, date_format(modified_date, '%D %M %Y, %k:%i') as date_added from `".$this->table."` order by modified_date desc";
    return $this->select($sql);
  }
  
  
  
  /*
   * save new page content
   */
  function save() {

    $post = $_POST;
    
    // sanitise
    foreach($post as $key => $postitem) {
      if ($key === "content") {
        $post[$key] = $this->escape($postitem);
      } else {
        $post[$key] = $this->sanitise($postitem);
      }
    }
        
    //insert 
    $sql = "INSERT into `".$this->table."` 
      (
        content,
        author,
        comment,
        ip,
        modified_date
      ) values (
        '".$post['content']."', 
        '".$post['author']."', 
        '".$post['comment']."', 
        '".$_SERVER['REMOTE_ADDR']."',  
         NOW()
      )";

    $id = $this->add($sql);
    
    if ($id) {    
      $return = array( 
        'success' => true,
        'id'      => $id,
        'details' => $post
      );
    } else {
      $return = array( 
        'success' => false,
        'error'   => $id,
        'details' => $post
      );
    }
    
    return $return;
  } 



  /*
   * restore an old page content
   */
  function restore() {
    
    // get details from existing record
    $post = $_POST;
    
    // sanitise
    foreach($post as $key => $postitem) {
      $post[$key] = $this->sanitise($postitem);
    }

    // get content
    $contentSQL = "SELECT `content` FROM `".$this->table."` WHERE id = ".$post['restore'];
    $content = $this->selectOne($contentSQL);
    $content = $this->escape($content);
        
    //insert 
    $sql = "INSERT into `".$this->table."` 
      (
        content,
        author,
        comment,
        ip,
        modified_date
      ) values (
        '".$content."', 
        '".$post['author']."', 
        '".$post['comment']."', 
        '".$_SERVER['REMOTE_ADDR']."',  
         NOW()
      )";

    $id = $this->add($sql);
    
    if ($id) {    
      $return = array( 
        'success' => true,
        'id'      => $id,
        'details' => $post
      );
    } else {
      $return = array( 
        'success' => false,
        'error'   => $id,
        'details' => $post
      );
    }
    
    return $return;
  } 
}