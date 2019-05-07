<?php
session_start();
session_destroy();
if (isset($_SESSION['login_user'])) {
    unset($_SESSION['login_user']);
}
header("location: https://forums.naacos.com/knowledgebase/login.php");
?>