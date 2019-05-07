<?php

session_start(); // Starting Session

$error=''; // Variable To Store Error Message
if (isset($_POST['submit'])) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error = "Username or Password is invalid.";
    }
    else {
        // Define $username and $password
        $username=$_POST['username'];
        $password=$_POST['password'];
        $clientid = '';
        $clientSecret = '';
        $orgid = "";
        $url = "https://".$orgid.".memberclicks.net/oauth/v1/token";
        $data = 'grant_type=password&scope=read&username='.$username.'&password='.$password;
        $headers = array (
            "POST /oauth/v1/token HTTP/1.1",
            "Host: ".$orgid.".memberclicks.net",
            "Authorization: Basic ".base64_encode($clientid.":".$clientSecret),
            "Content-Type: application/x-www-form-urlencoded",
            "Cache-Control: no-cache"
        );

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_RETRNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch,CURLOPT_HEADER, true);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responsetype = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $body = substr($result,$header_size);
        $jsonResult = json_decode($result);

        curl_close($ch);

        /*$aResult = array (
            $result,
            $header_size,
            $body,
            $jsonResult
        );*/  
        


        if ($responsetype >= 200 && $responsetype < 300) {
            
            $_SESSION['login_user'] = $jsonResult; // grab our auth code
            //header("location: http://forums.naacos.com/knowledgebase/index.php"); // Redirecting To Other Page
        } else {
            $_SESSION["login_user"] = null;
            $error = "Username or Password is invalid.";
        }
    }
}

if(isset($_SESSION['login_user'])){
    header("location: https://forums.naacos.com/knowledgebase/index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>NAACOS Knowledgebase Login</title>
<link href="css/loginstyle.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main" style="text-align:center;">
<div style="width:auto;text-align:center;">
          <img src="images/logo.png" alt="logo">
</div>
<h1>Knowledgebase Login</h1>

<div id="login">
<form action="login.php" method="post">
<label>Username :</label>
<input id="name" name="username" placeholder="username" type="text">
<label>Password :</label>
<input id="password" name="password" placeholder="**********" type="password">
<input name="submit" type="submit" value=" Login ">
<span><?php echo $error; ?></span>
</form>
</div>
</div>
</body>
</html>