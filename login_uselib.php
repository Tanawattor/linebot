<?php
class LineLoginLib
{
    private $_CLIENT_ID;
    private $_CLIENT_SECRET;
    private $_CALLBACK_URL;
    private $_STATE_KEY = 'random_state_str';
     
    public function __construct($_CLIENT_ID,$_CLIENT_SECRET,$_CALLBACK_URL)
    {
        $this->_CLIENT_ID = $_CLIENT_ID;
        $this->_CLIENT_SECRET = $_CLIENT_SECRET;
        $this->_CALLBACK_URL = $_CALLBACK_URL;
    }   
 
    public function authorize()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
         
        $_SESSION[$this->_STATE_KEY] = $this->randomToken();
 
        $url = "https://access.line.me/oauth2/v2.1/authorize?".
            http_build_query(array(
                'response_type' => 'code', // ไม่แก้ไขส่วนนี้
                'client_id' => $this->_CLIENT_ID,
                'bot_prompt' => 'aggressive',
                'redirect_uri' => $this->_CALLBACK_URL,
                'scope' => 'openid profile', // ไม่แก้ไขส่วนนี้
                'state' => $_SESSION[$this->_STATE_KEY]
            )
        );
        $this->redirect($url);
    }
     
    public function requestAccessToken($params, $returnResult = NULL, $ssl = NULL)
    {
        $_SSL_VERIFYHOST = (isset($ssl))?2:0;
        $_SSL_VERIFYPEER = (isset($ssl))?1:0;
        if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
             
        if(!isset($_SESSION[$this->_STATE_KEY]) || $params['state'] !== $_SESSION[$this->_STATE_KEY]){
            if(isset($_SESSION[$this->_STATE_KEY])){ unset($_SESSION[$this->_STATE_KEY]); }
            return false;
        }
         
        if(isset($_SESSION[$this->_STATE_KEY])){ unset($_SESSION[$this->_STATE_KEY]); }
         
        $code = $params['code'];
        $tokenURL = "https://api.line.me/oauth2/v2.1/token";
          
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
        $data = array(
            'grant_type' => 'authorization_code',
            'code' => (string)$code,
            'redirect_uri' => $this->_CALLBACK_URL,
            'client_id' => $this->_CLIENT_ID,
            'client_secret' => $this->_CLIENT_SECRET              
        );
         
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $tokenURL);
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, $_SSL_VERIFYHOST);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $_SSL_VERIFYPEER);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
        curl_close( $ch );
         
        $result = json_decode($result,TRUE);
 
        if($httpCode == 200){
            if(!is_null($result) && array_key_exists('access_token',$result)){
                if(is_null($returnResult)){
                    return $result['access_token'];                 
                }else{
                    if(array_key_exists('id_token',$result)){
                        $userData = explode(".",$result['id_token']);
                        list($alg,$data) = array_map('base64_decode',$userData);
                        $result['alg'] = $alg;
                        $result['user'] = $data;
                    }                   
                    return $result;     
                }
            }else{
                return NULL;    
            }                   
        }else{
            if(is_null($returnResult)){
                return NULL;
            }else{
                return $result;         
            }                           
        }
    }
 
    public function userProfile($accessToken, $returnResult = NULL, $ssl = NULL)
    {
        $_SSL_VERIFYHOST = (isset($ssl))?2:0;
        $_SSL_VERIFYPEER = (isset($ssl))?1:0;
        $accToken = $accessToken;
        $profileURL = "https://api.line.me/v2/profile";
         
        $headers = array(
            'Authorization: Bearer '.$accToken
        );
         
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $profileURL);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, $_SSL_VERIFYHOST);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $_SSL_VERIFYPEER);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
        curl_close( $ch );
 
        $result = json_decode($result,TRUE);
 
        if($httpCode == 200){
            if(!is_null($result) && array_key_exists('userId',$result)){
                if(is_null($returnResult)){
                    return $result['userId'];
                }else{
                    return $result;     
                }
            }else{
                return NULL;    
            }                   
        }else{
            if(is_null($returnResult)){
                return NULL;
            }else{
                return $result;         
            }                       
        }
    }
     
    public function verifyToken($accessToken, $returnResult = NULL, $ssl = NULL)
    {
        $_SSL_VERIFYHOST = (isset($ssl))?2:0;
        $_SSL_VERIFYPEER = (isset($ssl))?1:0;
        $accToken = $accessToken;
        $verifyURL = "https://api.line.me/oauth2/v2.1/verify";
         
        $headers = array();
         
        $data = array(
            'access_token' => $accToken
        );
         
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $verifyURL."?".http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, $_SSL_VERIFYHOST);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $_SSL_VERIFYPEER);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
        curl_close( $ch );
 
        $result = json_decode($result,TRUE);
 
        if($httpCode == 200){
            if(!is_null($result) && array_key_exists('scope',$result)){
                if(is_null($returnResult)){
                    return $result['scope'];
                }else{
                    return $result;     
                }
            }else{
                return NULL;    
            }                   
        }else{
            if(is_null($returnResult)){
                return NULL;
            }else{
                return $result;         
            }                           
        }
    }   
     
    public function refreshToken($refreshToken, $data, $returnResult = NULL, $ssl = NULL)
    {
        $_SSL_VERIFYHOST = (isset($ssl))?2:0;
        $_SSL_VERIFYPEER = (isset($ssl))?1:0;
        $tokenURL = "https://api.line.me/oauth2/v2.1/token";
         
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
         
        $data = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $this->_CLIENT_ID,
            'client_secret' => $this->_CLIENT_SECRET                  
        );      
             
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $tokenURL);
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, $_SSL_VERIFYHOST);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $_SSL_VERIFYPEER);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
        curl_close( $ch );
 
        $result = json_decode($result,TRUE);
 
        if($httpCode == 200){
            if(!is_null($result) && array_key_exists('access_token',$result)){
                if(is_null($returnResult)){
                    return $result['access_token'];
                }else{
                    return $result;     
                }
            }else{
                return NULL;    
            }                   
        }else{
            if(is_null($returnResult)){
                return NULL;
            }else{
                return $result;         
            }                           
        }
    }
 
    public function revokeToken($accessToken, $returnResult = NULL, $ssl = NULL)
    {
        $_SSL_VERIFYHOST = (isset($ssl))?2:0;
        $_SSL_VERIFYPEER = (isset($ssl))?1:0;
        $accToken = $accessToken;
        $revokeURL = "https://api.line.me/oauth2/v2.1/revoke";
         
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded'
        );
         
        $data = array(
            'access_token' => $accToken,
            'client_id' => $this->_CLIENT_ID,
            'client_secret' => $this->_CLIENT_SECRET              
        );      
 
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $revokeURL);
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, $_SSL_VERIFYHOST);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, $_SSL_VERIFYPEER);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec( $ch );
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
        curl_close( $ch );
 
        $result = json_decode($result,TRUE);
 
        if($httpCode == 200){
            return true;                
        }else{
            return NULL;                        
        }
    }
     
    public function redirect($url)
    {
        if(!header("Location: {$url}")){
            echo '<meta http-equiv="refresh" content="0;URL=$url">';
        }
        exit;       
    }
    public function setStateKey($stateKey)
    {
        $this->_STATE_KEY = $stateKey;   
    }
     
    public function randomToken($length = 32)
    {
        if(!isset($length) || intval($length) <= 8 ){
          $length = 32;
        }
        if(function_exists('random_bytes')) {
            return bin2hex(random_bytes($length));
        }
        if(function_exists('mcrypt_create_iv')) {
            return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
        } 
        if(function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($length));
        }
    }
     
}
?>


<?php


session_start();
//require_once("./lib/LineLoginLib.php");
//echo "tanawat";exit();
// กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
// กรณีมีการเชื่อมต่อกับฐานข้อมูล
//require_once("dbconnect.php");
 
/// ส่วนการกำหนดค่านี้สามารถทำเป็นไฟล์ include แทนได้
define('LINE_LOGIN_CHANNEL_ID','1613757830');
define('LINE_LOGIN_CHANNEL_SECRET','5c733c6a4057a64720b2c55e6141138a');
define('LINE_LOGIN_CALLBACK_URL','https://limitless-everglades-37945.herokuapp.com/login_uselib_callback.php');
 
$LineLogin = new LineLoginLib(
    LINE_LOGIN_CHANNEL_ID, LINE_LOGIN_CHANNEL_SECRET, LINE_LOGIN_CALLBACK_URL);
     
if(!isset($_SESSION['ses_login_accToken_val'])){    
    $LineLogin->authorize(); 
    exit;
}
 
$accToken = $_SESSION['ses_login_accToken_val'];
// Status Token Check
if($LineLogin->verifyToken($accToken)){
    // echo $accToken."<br><hr>";
    // echo "Token Status OK <br>";  
}
 
 
//echo "<pre>";
// Status Token Check with Result 
//$statusToken = $LineLogin->verifyToken($accToken, true);
//print_r($statusToken);
 
 
//////////////////////////
//echo "<hr>";
// GET LINE USERID FROM USER PROFILE
//$userID = $LineLogin->userProfile($accToken);
//echo $userID;
 
//////////////////////////
//echo "<hr>";
// GET LINE USER PROFILE 
$userInfo = $LineLogin->userProfile($accToken,true);
if(!is_null($userInfo) && is_array($userInfo) && array_key_exists('userId',$userInfo)){
    //print_r($userInfo);
}
 
//exit;
//echo "<hr>";
 
if(isset($_SESSION['ses_login_userData_val']) && $_SESSION['ses_login_userData_val']!=""){
    // GET USER DATA FROM ID TOKEN
    $lineUserData = json_decode($_SESSION['ses_login_userData_val'],true);
    // print_r($lineUserData); 
    // echo "<hr>";
    // echo "Line UserID: ".$lineUserData['sub']."<br>";
    // echo "Line Display Name: ".$lineUserData['name']."<br>";
    // echo '<img style="width:100px;" src="'.$lineUserData['picture'].'" /><br>';
}
 
 
//echo "<hr>";
if(isset($_SESSION['ses_login_refreshToken_val']) && $_SESSION['ses_login_refreshToken_val']!=""){
    // echo '
    // <form method="post">
    // <button type="submit" name="refreshToken">Refresh Access Token</button>
    // </form>   
    // ';  
}
if(isset($_SESSION['ses_login_refreshToken_val']) && $_SESSION['ses_login_refreshToken_val']!=""){
    if(isset($_POST['refreshToken'])){
        $refreshToken = $_SESSION['ses_login_refreshToken_val'];
        $new_accToken = $LineLogin->refreshToken($refreshToken); 
        if(isset($new_accToken) && is_string($new_accToken)){
            $_SESSION['ses_login_accToken_val'] = $new_accToken;
        }       
        $LineLogin->redirect("login_uselib.php");
    }
}
// Revoke Token
//if($LineLogin->revokeToken($accToken)){
//  echo "Logout Line Success<br>";   
//}
//
// Revoke Token with Result
//$statusRevoke = $LineLogin->revokeToken($accToken, true);
//print_r($statusRevoke);
?>
<?php
//echo "<hr>";
if($LineLogin->verifyToken($accToken)){
?>
<!-- <form method="post">
<button type="submit" name="lineLogout">LINE Logout</button>
</form> -->

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <!-- Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body style="font-size:14px">
    <div class="container" align="center">
      <br>          
      <img src="<?php echo $lineUserData['picture']; ?>" class="rounded-circle" alt="Cinque Terre" width="100" height="100"> 
      <br><br>
      <h5><?php echo $lineUserData['name']; ?></h5>
      <br>
      <form action="#" method="post">
      <div class="form-group">
          <!-- <label for="usr">เลขบัตรประชาชน:</label> -->
          <input type="number" style="text-align:center" class="form-control" name="cid" placeholder="เลขบัตรประชาชน (ไม่ต้องมี - )" required>
          <br><br>
          <button type="submit"
           style="background-color:#00C300;color:#FFFFFF"
           class="btn btn-block"
           name="register">ยืนยัน</button>
      </div>
      </form>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js" integrity="sha384-pjaaA8dDz/5BgdFUPX6M/9SUZv4d12SUPF0axWc+VRZkx5xU3daN+lYb49+Ax+Tl" crossorigin="anonymous"></script>
  </body>
</html>

<?php }else{ ?>
<form method="post">
<button type="submit" name="lineLogin">LINE Login</button>
</form>   
<?php } ?>
<?php
if(isset($_POST['register'])){
    //$LineLogin->authorize(); 
    exit;   
}
if(isset($_POST['lineLogin'])){
    $LineLogin->authorize(); 
    exit;   
}
if(isset($_POST['lineLogout'])){
    unset(
        $_SESSION['ses_login_accToken_val'],
        $_SESSION['ses_login_refreshToken_val'],
        $_SESSION['ses_login_userData_val']
    );  
    echo "<hr>";
    if($LineLogin->revokeToken($accToken)){
        echo "Logout Line Success<br>";   
    }
    echo '
    <form method="post">
    <button type="submit" name="lineLogin">LINE Login</button>
    </form>   
    ';
    $LineLogin->redirect("login_uselib.php");
}
?>