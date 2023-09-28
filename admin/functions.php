<?php define('TWmL','weGH3bCogsfRlTLF');
define('TWA','aes-256-cbc');session_start();
function tw_header($a,$b){$_SESSION['css']=$b;$_SESSION['page_name']=$a;require_once('header.php');}
function tw_footer(){require_once('footer.php');}

function conndb(){require_once('admin/conn.php');$a=twml();$b=new mysqli(twd($a[0]),twd($a[1]),twd($a[2]),twd($a[3]));if($b->connect_error){ die("Connection failed: ".$b->connect_error);} return $b;}
function home_url(){
    $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http");$base_url .= "://".$_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);return $base_url;
}
function db_select($table, $columns = "*", $where = null, $bindings = null, $bindTypes = null) {
    $conn = conndb();$query = "SELECT $columns FROM dash_$table";if ($where){$query .= " WHERE $where";}$stmt = $conn->prepare($query);
    if ($bindings){$paramTypes = $bindTypes ?: str_repeat('s',count($bindings));$stmt->bind_param($paramTypes, ...$bindings);}$stmt->execute();
    $result = $stmt->get_result();$results = [];while ($row = $result->fetch_assoc()){$results[] = $row;}$stmt->close();$conn->close();return $results;
}
function db_update($update, $set = "*", $where = null, $bindings = null, $bindTypes = null) {
    $conn = conndb();$query = "UPDATE dash_$update SET $set";if($where){ $query .= " WHERE $where";}$stmt = $conn->prepare($query);if(!$stmt){ return 0;}
    if($bindings){$paramTypes = $bindTypes ?: str_repeat('s',count($bindings));$stmt->bind_param($paramTypes, ...$bindings);}
    $stmt->execute();$affectedRows = $stmt->affected_rows;$stmt->close();$conn->close();return $affectedRows ? 1 : 0;
}
function db_insert($table,$columns,$bindings=null,$bindTypes=null) {
    $conn=conndb();$values="";$count = count(explode(",",$columns));
    for($i=1;$i<=$count;$i++){if($i==$count){$values.="?";}else{$values.="?,";}}$query="INSERT INTO dash_$table ($columns) values($values)";$stmt=$conn->prepare($query);
    if(!$stmt){return 0;}if($bindings){$paramTypes = $bindTypes ?: str_repeat('s',count($bindings));$stmt->bind_param($paramTypes, ...$bindings);}
    $stmt->execute();$affectedRows = $stmt->affected_rows;$stmt->close();$conn->close();return $affectedRows ? 1 : 0;
}
function twd($a){ return openssl_decrypt($a,TWA,TWmL,0,TWmL);}
function twe($a){ return openssl_encrypt($a,TWA,TWmL,0,TWmL);}
function devprint($a){echo '<pre>';print_r($a);echo '</pre>';}
function devprintexit($a){echo '<pre>';print_r($a);echo '</pre>';exit();}
function check_authenticity(){
    $usernosense = isset($_SESSION['nosense']) ? $_SESSION['nosense'] : 0;
    $userid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $check = db_select("users","id","id=? and nosense=?",[$userid,$usernosense],"is");
    if(!$check){session_start();
        session_unset();
        session_destroy();
        unset($_SERVER['PHP_AUTH_USER']);
        unset($_SERVER['PHP_AUTH_PW']);
        header("Location:".home_url());
        exit(); 
    }
    return 1;
}

function tw2faencode($input, $padding = true) {
    $map=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','2','3','4','5','6','7','='];
    if(empty($input)) return "";$input = str_split($input);$binaryString = "";
    for($i = 0; $i < count($input); $i++){$binaryString .= str_pad(base_convert(ord($input[$i]), 10, 2), 8, '0', STR_PAD_LEFT);}
    $fiveBitBinaryArray = str_split($binaryString, 5);$base32 = "";$i=0;
    while($i < count($fiveBitBinaryArray)){$base32 .= $map[base_convert(str_pad($fiveBitBinaryArray[$i], 5,'0'), 2, 10)];$i++;}
    if($padding && ($x = strlen($binaryString) % 40) != 0) {
        if($x == 8) $base32 .= str_repeat($map[32], 6);
        else if($x == 16) $base32 .= str_repeat($map[32], 4);
        else if($x == 24) $base32 .= str_repeat($map[32], 3);
        else if($x == 32) $base32 .= $map[32];
    }
    return $base32;
}
     
function tw2fadecode($input) {
    $map=['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','2','3','4','5','6','7','='];
    $flippedMap=['A'=>'0','B'=>'1','C'=>'2','D'=>'3','E'=>'4','F'=>'5','G'=>'6','H'=>'7','I'=>'8','J'=>'9','K'=>'10','L'=>'11','M'=>'12','N'=>'13','O'=>'14','P'=>'15','Q'=>'16','R'=>'17','S'=>'18','T'=>'19','U'=>'20','V'=>'21','W'=>'22','X'=>'23','Y'=>'24','Z'=>'25','2'=>'26','3'=>'27','4'=>'28','5'=>'29','6'=>'30','7'=>'31'];
    if(empty($input)) return;
    $paddingCharCount = substr_count($input, $map[32]);
    $allowedValues = array(6,4,3,1,0);
    if(!in_array($paddingCharCount, $allowedValues)) return false;
    for($i=0; $i<4; $i++){if($paddingCharCount == $allowedValues[$i] && substr($input, -($allowedValues[$i])) != str_repeat($map[32], $allowedValues[$i])) return false;}
    $input = str_replace('=','', $input);
    $input = str_split($input);
    $binaryString = "";
    for($i=0; $i < count($input); $i = $i+8) {
        $x = "";
        if(!in_array($input[$i], $map)) return false;
        for($j=0; $j < 8; $j++){$x .= str_pad(base_convert(@$flippedMap[@$input[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);}
        $eightBits = str_split($x, 8);
        for($z=0;$z<count($eightBits);$z++){$binaryString .=(($y=chr(base_convert($eightBits[$z],2,10))) || ord($y)==48) ? $y:"";}
    }
    return $binaryString;
}

function generateTOTP($secret, $timeSlice = null, $codeLength = 6){
    if($timeSlice === null) {$timeSlice = floor(time() / 30);}
    $hash = hash_hmac('sha1', pack('N*', 0) . pack('N*', $timeSlice), tw2fadecode($secret), true);
    $offset = ord(substr($hash, -1)) & 0x0F;
    $binary = (ord($hash[$offset]) & 0x7F) << 24 | (ord($hash[$offset + 1]) & 0xFF) << 16 | (ord($hash[$offset + 2]) & 0xFF) << 8 | (ord($hash[$offset + 3]) & 0xFF);
    $otp = $binary % pow(10, $codeLength);
    return str_pad($otp, $codeLength, '0', STR_PAD_LEFT);
 }