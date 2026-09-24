<?php
$file = 'resources/views/guru/soal/form.blade.php';
$content = file_get_contents($file);

$replace = "
    @push('scripts')
    <script src=\"https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js\"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ckeditorConfig = {
                extraPlugins: 'mathjax',
                mathJaxLib: 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-MML-AM_CHTML',
                height: 250,
                toolbar: [
                    { name: 'document', items: ['Source'] },
                    { name: 'clipboard', items: ['Undo', 'Redo'] },
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Subscript', 'Superscript'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList'] },
                    { name: 'insert', items: ['Image', 'Table', 'SpecialChar', 'Mathjax'] },
                    { name: 'tools', items: ['Maximize'] }
                ],
                removeButtons: ''
            };

            // Init on pertanyaan
            CKEDITOR.replace('pertanyaan', ckeditorConfig);
            
            // Init on opsi_a to opsi_e
            const options = ['a', 'b', 'c', 'd', 'e'];
            options.forEach(function(opt) {
                if (document.getElementById('opsi_' + opt)) {
                    // clone config for smaller height
                    let config = Object.assign({}, ckeditorConfig);
                    config.height = 100;
                    CKEDITOR.replace('opsi_' + opt, config);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>";

$content = str_replace("</x-app-layout>", $replace, $content);
file_put_contents($file, $content);
