<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        renderMathInElement(document.body, {
            delimiters: [
                {left: '', right: '', display: true},
                {left: '$', right: '$', display: false},
                {left: '\\(', right: '\\)', display: false},
                {left: '\\[', right: '\\]', display: true}
            ],
            throwOnError : false
        });
        
        // Also render Quill formulas if they aren't pre-rendered
        document.querySelectorAll('.ql-formula').forEach(el => {
            if (el.getAttribute('data-value') && !el.querySelector('.katex')) {
                katex.render(el.getAttribute('data-value'), el, { throwOnError: false });
            }
        });
    });
</script>
<style>
    .prose img { max-width: 100%; height: auto; border-radius: 0.375rem; }
</style>
