<?php
$date = $_GET['date'] ?? '';
$dir = __DIR__ . "/tmp_exports/$date";
$zipName = "itms_export_$date.zip";
$zipPath = "$dir/$zipName";

$zip = new ZipArchive();
$zip->open($zipPath, ZipArchive::CREATE);

foreach (glob("$dir/chunk_*.csv") as $file) {
    $zip->addFile($file, basename($file));
}

$zip->close();

header('Content-Type: application/zip');
header("Content-Disposition: attachment; filename=$zipName");
readfile($zipPath);
exit;
