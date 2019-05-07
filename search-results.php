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

if (isset($_POST["searchfield"]))
{
  $searchtopicssql = "SELECT a.*, b.subject as Category, COUNT(c.idForumTopic) as responseCount FROM forumTopic a
                      INNER JOIN forum b on a.idforum = b.idforum 
                      LEFT JOIN forumTopicResponse as c USING(idForumTopic)
                      WHERE (a.theText LIKE '%".$_POST["searchfield"]."%' or
                        a.theText Like '%".$_POST["searchfield"]."' OR a.theText LIKE '".$_POST["searchfield"]."%')
                        OR (a.subject LIKE '%".$_POST["searchfield"]."%' or
                        a.subject Like '%".$_POST["searchfield"]."' OR a.subject LIKE '".$_POST["searchfield"]."%') AND a.isActive = 1
                      GROUP BY c.idForumTopic
                      HAVING responseCount > 0
                      ORDER BY approvedDate DESC;";
  $gettopics = $db->query($searchtopicssql);

  $topics = $gettopics->fetchAll();
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
          <?php if (isset($_POST["searchfield"])) { ?>
      <!-- ARTICLE OVERVIEW SECTION -->
      <div class="col-md-8 padding-20">
        <div class="row">
          <!-- ARTICLES -->
          <div class="fb-heading">
            <i class="fa fa-search"></i> Search Results for: <strong><?php echo $_POST['searchfield']; ?></strong>
            <h4 class="padding-left-35"><small><?php echo count($topics) ?> results were found using the search term provided</small></h4>
          </div>
          <hr class="style-three">
          <?php foreach($topics as $topic) { ?>
          <div class="panel panel-default">
            <div class="article-heading-abb">
              <a href="single-article.php?id=<?php echo $topic['idforumTopic']; ?>"><i class="fa fa-pencil-square-o"></i> <?php echo $topic['subject']; ?></a>
            </div>
            <div class="article-info">
              <div class="art-date"><a href="#"><i class="fa fa-calendar-o"></i> <?php $exd = date_create($topic["approvedDate"]); echo date_format($exd,"d M Y"); ?></a></div>
              <div class="art-category"><a href="single-category.php?id=<?php echo $topic["idforum"]; ?>"><i class="fa fa-folder"></i> <?php echo $topic["Category"]; ?> </a></div>
              <?php $countsql = "SELECT COUNT(*) as responses FROM forumTopicResponse WHERE idForumTopic = ".$topic["idforumTopic"].";";
                $getcount = $db->query($countsql);
                $counter = $getcount->fetchAll(); ?>
              <div class="art-comments"><a href="#"><i class="fa fa-comments-o"></i> <?php echo $counter[0]["responses"]; ?> responses </a></div>
            </div>
            <div class="article-content">
              <p><?php echo strip_tags($topic["theText"],"<p><br>"); ?></p>
            </div>
            <div class="article-read-more">
              <a href="single-article.php?id=<?php echo $topic["idforumTopic"]; ?>" class="btn btn-default btn-wide">Read more...</a>
            </div>
          </div>
          <?php } ?>
          
          <!-- END ARTICLES -->

          <!-- PAGINATION -->
          <!--<nav class="text-center">
            <ul class="pagination">
              <li class="disabled">
                <a href="#" aria-label="Previous"><span aria-hidden="true"><i class="fa fa-arrow-circle-left"></i> Previous</span></a>
              </li>
              <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
              <li class="enabled"><a href="#">2 <span class="sr-only">(current)</span></a></li>
              <li class="enabled"><a href="#">3 <span class="sr-only">(current)</span></a></li>
              <li class="enabled">
                <a href="#" aria-label="Previous"><span aria-hidden="true">Next <i class="fa fa-arrow-circle-right"></i></span></a>
              </li>
            </ul>
          </nav>-->
          <!-- END PAGINATION -->
        </div>
      </div>
      <!-- END ARTICLES OVERVIEW SECTION-->
          <?php } ?>
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
        Copyright © 2017 NAACOS</a>
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
