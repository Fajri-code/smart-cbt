<?php
$file = 'resources/views/guru/soal/form.blade.php';
$content = file_get_contents($file);

$modalHtml = "
    <!-- Modal MathLive -->
    <div id=\"mathlive-modal\" style=\"display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.6); z-index:99999; backdrop-filter: blur(4px);\">
        <div style=\"background:#fff; width:90%; max-width:600px; margin:10vh auto; padding:24px; border-radius:16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);\">
            <h3 class=\"text-lg font-bold text-slate-900 mb-2\">Editor Rumus Matematika (Visual)</h3>
            <p class=\"text-sm text-slate-500 mb-4\">Gunakan keyboard virtual di bawah untuk membuat pecahan, akar, pangkat, dll.</p>
            
            <div style=\"border: 1px solid #cbd5e1; border-radius: 8px; padding: 12px; background: #f8fafc;\">
                <math-field id=\"mathfield\" style=\"width: 100%; font-size: 24px; outline: none; border: none; background: transparent;\"></math-field>
            </div>
            
            <div class=\"mt-6 flex justify-end gap-3\">
                <button type=\"button\" onclick=\"document.getElementById('mathlive-modal').style.display='none'\" class=\"rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50\">Batal</button>
                <button type=\"button\" onclick=\"insertMath()\" class=\"rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700\">Sisipkan Rumus</button>
            </div>
        </div>
    </div>
    
    @push('scripts')";

$content = str_replace("@push('scripts')", $modalHtml, $content);

$scriptAdd = "<script src=\"https://unpkg.com/mathlive\"></script>
    <script>
        // Registrasi plugin custom untuk CKEditor
        CKEDITOR.plugins.add('visualmath', {
            init: function(editor) {
                editor.addCommand('openVisualMath', {
                    exec: function(editor) {
                        window.currentMathEditor = editor;
                        document.getElementById('mathlive-modal').style.display = 'block';
                        // clear field
                        document.getElementById('mathfield').setValue('');
                        setTimeout(() => document.getElementById('mathfield').focus(), 100);
                    }
                });
                editor.ui.addButton('VisualMath', {
                    label: 'Editor Rumus Visual (Pecahan, Akar, dll)',
                    command: 'openVisualMath',
                    toolbar: 'insert',
                    icon: 'https://cdn-icons-png.flaticon.com/512/1046/1046399.png'
                });
            }
        });

        function insertMath() {
            var latex = document.getElementById('mathfield').getValue();
            if (latex) {
                window.currentMathEditor.insertHtml('\\\\(' + latex + '\\\\)');
            }
            document.getElementById('mathlive-modal').style.display = 'none';
        }
    </script>";

$content = str_replace("<script src=\"https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js\"></script>", "<script src=\"https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js\"></script>\n" . $scriptAdd, $content);

$content = str_replace("extraPlugins: 'mathjax',", "extraPlugins: 'mathjax,visualmath',", $content);
$content = str_replace("{ name: 'insert', items: ['Image', 'Table', 'SpecialChar', 'Mathjax'] },", "{ name: 'insert', items: ['Image', 'Table', 'SpecialChar', 'Mathjax', 'VisualMath'] },", $content);

file_put_contents($file, $content);
