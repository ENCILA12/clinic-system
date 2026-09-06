<?php
$files = glob('api/save_*.php');
foreach($files as $f) {
    $c = file_get_contents($f);
    if (preg_match('/INSERT INTO/i', $c)) {
        echo "\n--- $f ---\n";
        preg_match('/.*?(INSERT INTO.*?\)).*/is', $c, $m);
        echo isset($m[1]) ? $m[1] : 'Found insert but regex failed';
        echo "\n";
    }
}
?>
