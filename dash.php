<?php require_once('admin/functions.php');
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on'){header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'404.php');exit;}
check_authenticity();
tw_header('twdash','dash');
if(isset($_POST['dashlogout'])){
    session_start();
    session_unset();
    session_destroy();
    unset($_SERVER['PHP_AUTH_USER']);
    unset($_SERVER['PHP_AUTH_PW']);
    header("Location:".home_url());
    exit();  
}
if(isset($_POST['addextrapass'])){
    $myid = $_SESSION['user_id'];
    $role = db_select("users","role","id=?",[$myid],"i")[0]['role'];
    $username = $_POST['extrausername'];
    $pass = $_POST['extrapassword'];
    $repass = $_POST['repeatpassword'];
    if($pass == $repass && $role == 0){
        db_update("variables","value=?","name=?",[password_hash($pass,PASSWORD_BCRYPT),"signpassword"],"ss");
        db_update("variables","value=?","name=?",[$username,"signusername"],"ss");
        db_update("variables","value=?","name=?",["1","signin"],"ss");
    }
}
if(isset($_POST['dlogin'])){
    $signin = db_select("variables","value","name=?",['signin'],"s")[0]['value'];
    $extralayer = 0;
    if(isset($_POST['signinextra'])){$extralayer=1;}
    if(!$extralayer && $signin){
        $myid = $_SESSION['user_id'];
        $role = db_select("users","role","id=?",[$myid],"i")[0]['role'];
        if($role==0){db_update("variables","value=?","name=?",["0","signin"],"ss");}
    } else if($extralayer){ ?>
        <form method="post">
            <label for="extrausername">Username</label>
            <input type="text" id="extrausername" name="extrausername">
            <label for="extrapassword">Password</label>
            <input type="password" id="extrapassword" name="extrapassword">
            <label for="repeatpassword">Repeat password</label>
            <input type="password" id="repeatpassword" name="repeatpassword">
            <input type="submit" name="addextrapass" value="submit">
        </form> <?php 
    }

}
$auth = db_select("users","auth")[0]['auth'];
$signin = db_select("variables","value","name=?",['signin'],"s")[0]['value'];?>
<form action="dash.php" method="post">
    <label for="signinextra">Sign in extra layer</label>
    <input type="checkbox" name="signinextra" id="signinextra" <?php if($signin==1){echo 'checked';}?>>
    <label for="auoth">auoth</label>
    <input type="checkbox" name="dashauoth" id="dashauoth" <?php if($auth==1){echo 'checked';}?>>
    <input type="submit" name="dlogin" value="save">
</form>
<form action="dash.php" method="post">
    <label for="dashlogout">logout</label>
    <input type="submit" id="dashlogout" name="dashlogout">
</form>
<?php 
tw_footer();