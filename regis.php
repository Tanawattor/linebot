Regis
<?php

if(isset($_SESSION['ses_login_userData_val']) && $_SESSION['ses_login_userData_val']!=""){
    // GET USER DATA FROM ID TOKEN
    $lineUserData = json_decode($_SESSION['ses_login_userData_val'],true);
    print_r($lineUserData); 
    echo "<hr>";
    echo "Line UserID: ".$lineUserData['sub']."<br>";
    echo "Line Display Name: ".$lineUserData['name']."<br>";
    echo '<img style="width:100px;" src="'.$lineUserData['picture'].'" /><br>';
}

?>
