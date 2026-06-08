<?php
echo "<pre>\n";
$prodRoot = '/home/sekelikn/gentix-apps.com';
$f = '/resources/views/organizer/gate/scan.blade.php';
$path = $prodRoot . $f;
if (file_exists($path)) {
    echo "$f hash: " . md5_file($path) . "\n";
} else {
    echo "$f does not exist!\n";
}
echo "</pre>";
@unlink(__FILE__);
