<?php
$file = 'resources/views/guru/bank-soal/show.blade.php';
$content = file_get_contents($file);

$search = "name=\"pertanyaan\"";
$replace = "id=\"pertanyaan\" name=\"pertanyaan\"";
$content = str_replace($search, $replace, $content);

$search2 = "name=\"petunjuk_jawaban\"";
$replace2 = "id=\"petunjuk_jawaban\" name=\"petunjuk_jawaban\"";
$content = str_replace($search2, $replace2, $content);

foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
    $search3 = "name=\"opsi_$opt\"";
    $replace3 = "id=\"opsi_$opt\" name=\"opsi_$opt\"";
    $content = str_replace($search3, $replace3, $content);
}

$replaceScripts = "
    @push('scripts')
    <script src=\"https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js\"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ckeditorConfig = {
                extraPlugins: 'mathjax',
                mathJaxLib: 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-MML-AM_CHTML',
                height: 150,
                toolbar: [
                    { name: 'document', items: ['Source'] },
                    { name: 'clipboard', items: ['Undo', 'Redo'] },
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline'] },
                    { name: 'insert', items: ['Mathjax', 'SpecialChar'] },
                    { name: 'tools', items: ['Maximize'] }
                ],
                removeButtons: ''
            };

            if(document.getElementById('pertanyaan')) CKEDITOR.replace('pertanyaan', ckeditorConfig);
            
            const options = ['a', 'b', 'c', 'd', 'e'];
            options.forEach(function(opt) {
                if (document.getElementById('opsi_' + opt)) {
                    let config = Object.assign({}, ckeditorConfig);
                    config.height = 80;
                    CKEDITOR.replace('opsi_' + opt, config);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>";

$content = str_replace("</x-app-layout>", $replaceScripts, $content);
file_put_contents($file, $content);
