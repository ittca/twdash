<!DOCTYPE html>
<html>
    <head>
        <title>Meetinglink - Database connection</title>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" href="../../img/favicon.ico" type="image/x-icon"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            *{padding:0;margin:0;}
            html{
                background:#25282F;
            }
            .body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: calc(100vh - 30px);
                margin: 0;
            }
            .container {
                background-color: #f5f5f5;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
                width: 300px;
                text-align: center;
            }
            input[type="text"], input[type="password"] {
                width: 280px;
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
            input[type="submit"] {
                background-color: #007bff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
            }
            .website-name {
                margin-bottom: 20px;
            }
        </style>
    </head> <?php
    if(isset($_POST['server'])){
        $servername = $_POST['server'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $dbname = $_POST['dbname'];
        try {
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) { echo "Connection failed: " . $conn->connect_error;}
            else {
                $encryptionKey = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 16);
                $servername = openssl_encrypt($servername, 'aes-256-cbc', $encryptionKey, 0, $encryptionKey);
                $username = openssl_encrypt($username, 'aes-256-cbc', $encryptionKey, 0, $encryptionKey);
                $password = openssl_encrypt($password, 'aes-256-cbc', $encryptionKey, 0, $encryptionKey);
                $dbname = openssl_encrypt($dbname, 'aes-256-cbc', $encryptionKey, 0, $encryptionKey);
                $data = "<?php function twml(){ return ['$servername','$username','$password','$dbname']; }";
                $file = fopen('conn.php', 'w');
                if ($file) {
                    fwrite($file, $data);
                    fclose($file);
                    $fileContents = file('functions.php');
                    $newFirstLine = "<?php define('TWmL','$encryptionKey');";
                    if ($fileContents) {
                        $fileContents[0] = $newFirstLine . "\n";
                        $file = fopen('functions.php', 'w');
                        if ($file) {
                            fwrite($file, implode('', $fileContents));
                            fclose($file);
                            $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http");
                            $base_url .= "://".$_SERVER['HTTP_HOST'];
                            $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
                            $conn->query("CREATE TABLE IF NOT EXISTS dash_users (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                             name VARCHAR(40) NOT NULL,email VARCHAR(40) UNIQUE NOT NULL, password VARCHAR(80) NOT NULL,
                              theme varchar(20) default 'dark', role int(6) default 2, nosense VARCHAR(20) default 'nosense',created TIMESTAMP)");
                            $conn->query("CREATE TABLE IF NOT EXISTS dash_variables (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                              name VARCHAR(40) NOT NULL,value VARCHAR(80) NOT NULL)");
                            $conn->query("insert into dash_variables values (default, 'signin','0')");
                            $conn->query("insert into dash_variables values (default, 'signusername','')");
                            $conn->query("insert into dash_variables values (default, 'signpassword','')");
                            echo "Connection succeful. Go to <a href='$base_url'>home page</a>";
                        } else { echo "Unable to open mainFunctions.php";}
                    } else { echo "Unable to read the file.";}
                } else { echo "Unable to open the file.";}
            }
        } catch (mysqli_sql_exception $e){
            if(isset($conn)){
                echo "An error occurred: " . $conn->error;
            } else {
                echo "Issues in connecting to database";
            }
        }
    }  
    try {
        require_once('functions.php');
        require_once('conn.php');
        $a = twml();
        $b = new mysqli(twd($a[0]),twd($a[1]),twd($a[2]),twd($a[3]));
        if (!$b->connect_error){ 
            header("Location:".home_url()."404.php");
            exit();
        } 
    } catch (mysqli_sql_exception $e){ ?> 
        <body> 
            <div class="body">
            <div class="container">
                <div class="website-name">
                    <h1>tw Dash</h1>
                </div>
                <form method="post">
                    <label for="server">Server Name:</label>
                    <input type="text" id="server" name="server" required><br>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required><br>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required><br>
                    <label for="dbname">Database Name:</label>
                    <input type="text" id="dbname" name="dbname" required><br>
                    <input type="submit" value="Connect">
                </form>
            </div>
            </div>
        </body><?php 
    }  ?> 
</html>


                            