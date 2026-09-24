<?php
function replaceInFile($file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace("height: 150,", "height: 150,\n                versionCheck: false,", $content);
        file_put_contents($file, $content);
    }
}

replaceInFile("resources/views/guru/bank-soal/show.blade.php");
