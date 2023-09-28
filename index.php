<?php require_once('admin/functions.php'); 
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on'){header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'404.php');exit;}
$messages = [];
$signin = db_select("variables","value","name=?",['signin'],"s")[0]['value'];
if($signin){
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Authentication required.';
        exit;
    }
    $signusername = db_select("variables","value","name=?",['signusername'],"s")[0]['value'];
    $signpassword = db_select("variables","value","name=?",['signpassword'],"s")[0]['value'];
    $entered_username = $_SERVER['PHP_AUTH_USER'];
    $entered_password = $_SERVER['PHP_AUTH_PW'];
    if ($entered_username !== $signusername || !password_verify($entered_password,$signpassword)){
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Invalid credentials.';
        exit;
    }
}
if(isset($_POST['loginsubmit'])){
    $email = $_POST['loginemail'];
    $loginpass = $_POST['loginpass'];
    $user = db_select("users","id,name,password,theme","email=?",[$email]);
    if($user && password_verify($loginpass, $user[0]['password'])){
        $id = $user[0]['id'];
        $timenow = date('Y-m-d H:i');
        $nosense = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 18);
        $update = db_update('users','nosense=?','id=?',[$nosense,$id],"si");
        if($update){
            $_SESSION['nosense'] = $nosense;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_id'] = $user[0]['id'];
            $_SESSION['user_name'] = $user[0]['name'];
            $_SESSION['theme'] = $user[0]['theme'];
            header("Location:".home_url()."dash.php");  
            exit();
        }
    } else { array_push($messages,'The email or the password isnt matching');}
} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="img/ittca_logo.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>login - twdash</title>
</head>
<body class="darktheme">
    <div class="container">
        <h1>tw Dash</h1>
        <form method="post">
            <label for="loginemail">Email</label><br>
            <input type="email" id="loginemail" name="loginemail"><br>
            <label for="loginpass">Password</label><br>
            <input type="password" id="loginpass" name="loginpass"><br>
            <input type="submit" name="loginsubmit" value="Enter">
        </form>
        <div id="messages"> <?php 
            if($messages){foreach($messages as $msg){echo '<span class="messages">'.$msg.'</span>';}} ?>
        </div>
    </div>
</body>
</html>
<script> var messagesArray=document.getElementsByClassName('messages');if(messagesArray.length>0){setTimeout(function(){for(var i=messagesArray.length-1;i>=0;i--){messagesArray[i].remove();}},5000);}</script>