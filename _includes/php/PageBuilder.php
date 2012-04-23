<?php 
/**
 * page builder
 * build pages
 *
*/
class PageBuilder {

  // import globals
  protected $db;
  protected $conf;
  

  /**
   * The constructor
   */
  public function __construct() {
    global $db, $conf;
    $this->db = $db;
    $this->conf = $conf;
  }

  
  /**
   * Build the login page
   */
  public function buildLoginPage() {

    $user = (isset($_POST['u'])) ? $this->db->sanitise($_POST['u']) : "";

    // create content
    $title = "Login";
    $content = '
        <div id="login-form">

            <h2>Please log in&hellip;</h2>

            <form method="post" action="">
              <fieldset>
                <legend>Login</legend>
                <div class="input-container clearfix">
                  <label for="u">Username</label>
                  <input type="text" class="text" id="u" name="u" value="'.$user.'" />
                </div>
                <div class="input-container clearfix">
                  <label for="p">Password</label>
                  <input type="password" class="text" id="p" name="p" />
                  <input class="button" type="submit" name="login" id="button" value="Log in" />
                </div>
              </fieldset>
            </form>
          ';

    if (isset($_POST) && count($_POST) > 0) {
      $content .= '
            <p class="error">There was an error with your log-in details, please try again!</p>
      ';
    }
      
    $content .= '
        </div>
    ';

    // build page
    return $this->buildPage($title, $content);
  }



  /**
   * Build the login page
   */
  public function buildIndexPage() {

    if (isset($_GET['v'])) {
      $v = $this->db->sanitise($_GET['v']);
      $page = $this->db->getVersion($v);
      $preview = true;
    } else {
      $page = $this->db->getLatest();
      $preview = false;
    }

    // calculate sidebar links from content - h2 & h3 headings
    $page = $this->createLinksFromContent($page);
  
    // create content
    $title = "Home";
    $content = '';
    
    // don't output sidebar for preview
    if (!$preview) {
      $content .= '
        <aside>
          <h1>'.$this->conf['details']['title'].'</h1>
          '.$page->sidebar.'
          <div id="meta">
            <p><a href="./edit">Edit</a></p>
            <p>Version: <strong>'.$page->id.'</strong><br/>
            Last Updated: <strong>'.$page->date_added.'</strong><br/>
            By: <strong>'.$page->author.'</strong></p>
          </div>  
        </aside>
      ';
    } 

    $content .= '
        <article>
          '.$page->content.'
        </article>
    ';

    // build page
    return $this->buildPage($title, $content);
  }



  /**
   * Build the login page
   */
  public function buildEditPage($update = false) {

    if (is_array($update)) {
      if ($update['success']) {
        $message = '
          <div class="message">
            <h3>Content updated</h3>
            <p><a href="./">Back to site</a></p>
          </div>
        ';
      } else {
        $message = $update['error'];
      }
    } else {
      $message = "";
    }

    $latest = $this->db->getLatest();
    
    // create content
    $title = "Edit";
    $content = '
        <aside>
          <h1>'.$this->conf['details']['title'].'</h1>
          <p><a href="./">Back to site</a></p>
          <p><a href="./history">View or restore an older version of this page</a></p>
        </aside>
        <article>
          <h2>Edit</h2>
          '.$message.'
            <form method="post" action="">
              <fieldset>
                <legend>Edit page</legend>
                <div class="input-container clearfix">
                  <label for="author">Your name</label>
                  <input type="text" class="text" id="author" name="author" value="" />
                </div>
                <div class="input-container clearfix">
                  <label for="comment">Update description <em>(optional)</em></label>
                  <textarea name="comment" id="comment" rows="3" cols="30"></textarea>
                </div>
                <label>Make your changes below</label>
                <div class="input-container clearfix">
                  <!-- <label for="content">Content</label> -->
                  <textarea name="content" id="content" rows="30" cols="40">'.$latest->content.'</textarea>
                </div>
                <div class="input-container clearfix">
                  <input class="button" type="submit" name="login" id="button" value="Save" />
                  <p><a class="button" href="./">Cancel</a></p>
                </div>
              </fieldset>
            </form>
        </article>
    ';

    // build page
    return $this->buildPage($title, $content);
  }



  /**
   * Build the login page
   */
  public function buildHistoryPage($update = false) {

    if (is_array($update)) {
      if ($update['success']) {
        $message = '
          <div class="message">
            <h3>Content updated</h3>
            <p><a href="./">Back to site</a></p>
          </div>
        ';
      } else {
        $message = $update['error'];
      }
    } else {
      $message = "";
    }

    $history = $this->db->getAll();
    
    // create content
    $title = "History";
    $content = '
        <aside>
          <h1>'.$this->conf['details']['title'].'</h1>
          <p><a href="./">Back to site</a></p>
          <p><a href="./edit">Edit current page content</a></p>
        </aside>
        <article>
          <h2>History</h2>
          '.$message.'
          <p>Preview or restore an old version of the page</p>
          <p>If you are going to restore an old version, please add your details into the form below</p>
          <form method="post" action="">
            <fieldset>
              <legend>Restore page</legend>
              <div class="input-container clearfix">
                <label for="author">Your name</label>
                <input type="text" class="text" id="author" name="author" value="" />
              </div>
              <div class="input-container clearfix">
                <label for="comment">Update description <em>(optional)</em></label>
                <textarea name="comment" id="comment" rows="5" cols="20"></textarea>
              </div>
    
              <div class="input-container clearfix">
                <table>
                  <thead>
                    <tr>
                      <th>Version</th>
                      <th>Date</th>
                      <th>Note</th>
                      <th>Author</th>
                      <th colspan="2">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
    ';

    foreach ($history as $key => $version) {
      $latestFlag = ($key === 0) ? " (latest)" : "";
      $content .= '
                    <tr>
                      <td>'.$version->id.$latestFlag.'</td>
                      <td>'.$version->date_added.'</td>
                      <td>'.$version->author.'</td>
                      <td>'.$version->comment.'</td>
                      <td>
                        <a class="button preview" href="./?v='.$version->id.'">Preview</a>
                      </td>
                      <td>
                        <button class="button restore" type="submit" name="restore" id="restore-'.$version->id.'" value="'.$version->id.'">Restore</button>
                      </td>
                    </tr>
      ';
    }


    $content .= '
                  </tbody>
                </table>
              </div>
            </fieldset>
          </form>
        </article>
    ';

    // build page
    return $this->buildPage($title, $content);
  }



  /**
   * Build the page
   */
  private function buildPage($title, $content) {

    $return = '<!doctype html>
    <html>
    <head>
      <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width,initial-scale=1" />
      <title>'.$title.' | '.$this->conf['details']['title'].'</title>
      <link media="screen" rel="stylesheet" href="_includes/css/screen.css" />
      <link media="print" rel="stylesheet" href="_includes/css/print.css" />
      <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
      <script src="_includes/tinymce/tiny_mce.js"></script>
      <script src="_includes/js/printlinks.js"></script>
      <script src="_includes/js/init.js"></script>
    </head>
    <body id="'.str_replace(" ", "-", strtolower($title)).'">
      <div id="wrapper">
        '.$content.'
      </div>

      <!-- this script fixes a layout issue with iOS devices when rotated -->
      <script src="https://raw.github.com/gist/901295/bf9a44b636a522e608bba11a91b8298acd081f50/ios-viewport-scaling-bug-fix.js"></script>
    </body>
    </html>';

    return $return;
  }


  /*
   *
   */
  private function createLinksFromContent($content) {

    $content->sidebar = "";

    // detect h2s
    $h2s = preg_match_all("/<h2>.+<\/h2>/", $content->content, $matches, PREG_PATTERN_ORDER);
    if ($h2s > 0) {

      // start the sidebar link collection
      $content->sidebar = '<ul id="section-select">';

      // calculate titles, generate sidebar
      foreach($matches[0] as $match) {
        $originalTag = $match;
        $titleText = strip_tags($originalTag);
        $link = str_replace(" ", "-", strtolower($titleText));
        $newTag = str_replace('<h2>', '<h2 id="'.$link.'">', $match);
        $content->content = str_replace($originalTag, $newTag, $content->content);
        $content->sidebar .= '<li><a href="#'.$link.'">'.$titleText.'</a></li>';
      }

      $content->sidebar .= "</ul>";
    } 

    return $content;
  }
}