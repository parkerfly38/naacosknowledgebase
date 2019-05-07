<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once($_SERVER["DOCUMENT_ROOT"].'/includes/memberClicks.inc.php');

define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/_system.config.php'); 
define('DB_HOST', NAACOS_DB_HOST);
define('DB_NAME', NAACOS_DB_NAME);
define('DB_USER', NAACOS_DB_USER);
define('DB_PASS', NAACOS_DB_PASS); 
define('DB_PORT', NAACOS_DB_PORT);
date_default_timezone_set('UTC'); // default time zone
require_once($_SERVER["DOCUMENT_ROOT"].'/includes/mysql.inc.php');

                $catsql = "SELECT idforum, subject, theText FROM forum ORDER BY postdate;";
                $getcategories = $db->query($catsql);

                $cats = $getcategories->fetchAll();

function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
  $url = 'https://www.gravatar.com/avatar/';
  $url .= md5( strtolower( trim( $email ) ) );
  $url .= "?s=$s&d=$d&r=$r";
  if ( $img ) {
      $url = '<img src="' . $url . '"';
      foreach ( $atts as $key => $val )
          $url .= ' ' . $key . '="' . $val . '"';
      $url .= ' />';
  }
  return $url;
}

if (isset($_GET["id"]))
{
  $topicsql = "SELECT a.*, b.subject as Category, count(c.idforumTopicResponse) as ResponseCount FROM forumTopic a 
  INNER JOIN forum b on a.idforum = b.idforum 
  LEFT JOIN forumTopicResponse as c USING(idForumTopic)
  WHERE idforumTopic = ".$_GET["id"]." 
  GROUP BY c.idForumTopic;";
  $gettopic = $db->query($topicsql);
                    
  $topic = $gettopic->fetch();
} else {
  header('Location: http://forums.naacos.com/knowledgebase/index.php');
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NAACOS Knowledgebase</title>
    <!-- LOADING STYLESHEETS -->
    <link href="css/bootstrap.css" rel="stylesheet">
	  <link href="css/font-awesome.min.css" rel="stylesheet" >
	  <link href="css/style.css" rel="stylesheet">
  </head>
  <body>
  <div class="container-fluid featured-area-white-border">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
        <div class="login-box border-right-1 border-left-1 ">
          <a href="logout.php"><i class="fa fa-key"></i> Logout</a>
        </div>
        </div>
      </div>
    </div>
  </div>
  <!-- LOGO -->
  <div class="container">
    <div class="row">
      <div class="header">
        <div class="logo">
          <img src="images/logo.png" alt="logo">
        </div>
      </div>
    </div>
  </div>
  <!-- END LOGO-->
  <!-- TOP NAVIGATION -->
  <div class="container-fluid">
    <div class="navigation">
      <div class="row">
        <ul class="topnav">
          <li></li>
          <li><a href="index.php"><i class="fa fa-home"></i>  Home</a></li>
          <!--<li><a href="knowledge-base.html"><i class="fa fa-book"></i> Knowledge Base</a></li>
          <li><a href="articles.html"><i class="fa fa-file-text-o"></i> Articles</a></li>
          <li><a href="faq.html"><i class="fa fa-lightbulb-o"></i> FAQ</a></li>
          <li class="icon">
            <a href="javascript:void(0);" onclick="myFunction()">&#9776;</a>
          </li>-->
       </ul>
      </div>
    </div>
  </div>
  <!-- END TOP NAVIGATION -->
  <!-- SEARCH FIELD AREA -->
  <div class="searchfield bg-hed-six">
    <div class="container" style="padding-top: 20px; padding-bottom: 20px;">
      <div class="row text-center margin-bottom-20">
        <h1 class="white"> Knowledge Base</h1>
        <span class="nested"> Search topics </span>
      </div>
      <br>
      <div class="row search-row">
        <form action="search-results.php" name="searchform" method="POST">
            <div class="form-group">
                <input type="text" id="searchfield" name="searchfield" class="search" placeholder="What do you need help with?" />
                <a type="submit" href="#" class="buttonsearch btn btn-info btn-lg" onclick="document.forms['searchform'].submit();">Search</a>
            </div>
        </form>
      </div>
    </div>
  </div>
  <!-- END SEARCH FIELD AREA -->
  <!-- MAIN SECTION -->
  <div class="container featured-area-default padding-30">
    <div class="row">
      <div class="col-md-8 padding-20">
        <div class="row">
          <!-- ARTICLE  -->
          <div class="panel panel-default">
            <div class="article-heading margin-bottom-5">
              <a href="#"><i class="fa fa-pencil-square-o"></i> <?php echo $topic["subject"]; ?></a>
            </div>
            <div class="article-info">
              <div class="art-date"><a href="#"><i class="fa fa-calendar-o"></i> <?php echo $topic["approvedDate"]; ?> </a></div>
              <div class="art-category"><a href="single-category.php?id=<?php echo $topic["idforum"]; ?>"><i class="fa fa-folder"></i> <?php echo $topic["Category"];?> </a></div>
              <div class="art-comments"><a href="#"><i class="fa fa-comments-o"></i> <?php echo $topic["ResponseCount"]; ?> Responses </a></div>
            </div>
            <div class="article-content">
              <p>
              <?php echo strip_tags($topic["theText"],"<p><br>"); ?>
              </p>
            </div>
          </div>
          <!-- END ARTICLE -->

          <!-- COMMENTS  -->
          <div class="panel panel-default">
            <div class="article-heading">
              <i class="fa fa-comments-o"></i> Responses (<?php echo $topic["ResponseCount"]; ?>)
            </div>

            <?php
              $responsesql = "SELECT a.*, b.* from forumTopicResponse a INNER JOIN member b on a.idmember = b.idmember WHERE idForumTopic = ".$_GET["id"].";";
              $responseresult = $db->query($responsesql);

              $responses = $responseresult->fetchAll();

              foreach($responses as $response)
              {
            ?>
            <!-- FIRST LEVEL COMMENT 1 -->
            <div class="article-content">
              <div class="article-comment-top">
                <div class="comments-user">
                  <img src="<?php echo get_gravatar($response['email'],70); ?>" alt="NAACOS user">
                  <div class="user-name"><?php echo $response["firstname"]." ".$response["lastname"]; ?></div>
                  <div class="comment-post-date">Posted On <span class="italics"><?php echo $response["approvedDate"]; ?></span></div>
                </div>
                <div class="comments-content">
                  <p>
                    <?php echo strip_tags($response["theText"],"<p><br>"); ?>
                  </p>
                </div>
              </div>
            </div>
            <!-- END FIRST LEVEL COMMENT 1 -->
              <?php } ?>
           
            <hr class="style-three">
          </div>
          <!-- END COMMENTS -->
        </div>

      </div>

      <!-- SIDEBAR STUFF -->
      <div class="col-md-4 padding-20">
        <div class="row margin-bottom-30">
          <div class="col-md-12 ">
            <div class="support-container">
              <h2 class="support-heading">Need more Support?</h2>
              If you cannot find an answer in the knowledgebase, you can <a href="mailto:admin@naacos.com">contact us</a> for further help, or search our forums at <a href="http://forums.naacos.com">forums.naacos.com</a>.
            </div>
          </div>
        </div>

        <?php
          $recentsql = "SELECT DISTINCT forumTopic.idForumTopic, subject FROM forumTopic INNER JOIN forumTopicResponse on forumTopic.idForumTopic = forumTopicResponse.idForumTopic
            WHERE forumTopic.isActive = 1 ORDER by forumTopicResponse.approvedDate DESC Limit 5;";
          $getRecents = $db->query($recentsql);

          $recents = $getRecents->fetchAll();
        ?>
        <div class="row margin-top-20">
          <div class="col-md-12">
            <div class="fb-heading-small">
              Recent Responses
            </div>
            <hr class="style-three">
            <div class="fat-content-small padding-left-10">
              <ul>
                <?php
                foreach ($recents as $recent)
                { ?>
                <li> <a href="single-article.php?id=<?php echo $recent['idForumTopic']; ?>"><i class="fa fa-file-text-o"></i> <?php echo $recent['subject']; ?></a> </li>
                <?php } ?>
              </ul>
            </div>
          </div>
        </div>

        
      </div>
      <!-- END SIDEBAR STUFF -->
    </div>
  </div>
  <!-- END MAIN SECTION -->

   <!-- FOOTER -->
   <div class="container-fluid footer marg30">
   <div class="container">
     <!-- FOOTER COLUMN ONE -->
     <div class="col-xs-12 col-sm-4 col-md-4 margin-top-20">
       <div class="panel-transparent">
         <div class="footer-heading">About NAACOS</div>
         <div class="footer-body">
           <p>National Association of ACOs (NAACOS )is a 501(c)6 non-profit organization that allows accountable care organizations (ACOs) to work together to increase quality of care, lower costs, and improve the health of their communities. Determined to create an environment for advocacy and shared learning, organizations representing more than 260 ACOs from all 50 states including the District of Columbia, have formed NAACOS.</p>
         </div>
       </div>
     </div>
     <!-- END FOOTER COLUMN ONE -->
     <!-- FOOTER COLUMN TWO -->
     <div class="col-xs-12 col-sm-4 col-md-4 margin-top-20">
       <div class="panel-transparent">
         <div class="footer-heading">Topics</div>
         <div class="footer-body">
           <ul>
           <?php foreach ($cats as $cat)
               { ?>
             <li> <a href="single-category.php?id=<?php echo $cat['idforum']; ?>"><?php echo $cat['subject']; ?></a> </li>
               <?php } ?>
           </ul>
         </div>
       </div>
     </div>
     <!-- END FOOTER COLUMN TWO -->
     
   </div>
 </div>
 <!-- END FOOTER -->

 <!-- COPYRIGHT INFO -->
 <div class="container-fluid footer-copyright marg30">
   <div class="container">
     <div class="pull-left">
       Copyright © 2018 NAACOS</a>
     </div>
     <div class="pull-right">
       <i class="fa fa-facebook"></i> &nbsp;
       <i class="fa fa-twitter"></i> &nbsp;
       <i class="fa fa-linkedin"></i>
     </div>
   </div>
 </div>
 <!-- END COPYRIGHT INFO -->


  <!-- LOADING MAIN JAVASCRIPT -->
  <script src="js/jquery-2.2.4.min.js"></script>
  <script src="js/main.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src='https://cdn.rawgit.com/VPenkov/okayNav/master/app/js/jquery.okayNav.js'></script>
  </body>
</html>
