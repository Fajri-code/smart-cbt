<?php
$files = [
    'resources/views/siswa/exams/work_new.blade.php',
    'resources/views/siswa/exams/work.blade.php',
    'resources/views/layouts/app.blade.php'
];

$headStr = "<script type=\"text/x-mathjax-config\">
        MathJax.Hub.Config({
            tex2jax: { inlineMath: [['\\\\(','\\\\)']], displayMath: [['\$\$','\$\$']], processEscapes: true }
        });
    </script>
    <script type=\"text/javascript\" async src=\"https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-MML-AM_CHTML\"></script>
</head>";

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace('</head>', $headStr, $content);
        file_put_contents($file, $content);
    }
}
