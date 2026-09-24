<?php
function replaceInFile($file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace("height: 250,", "height: 250,\n                versionCheck: false,", $content);
        $content = str_replace("height: 120,", "height: 120,\n                versionCheck: false,", $content);
        file_put_contents($file, $content);
    }
}

replaceInFile("resources/views/guru/soal/form.blade.php");
replaceInFile("resources/views/guru/bank-soal/show.blade.php");
