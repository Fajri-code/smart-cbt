@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<style>
    .ql-editor {
        min-height: 120px;
        font-family: inherit;
        font-size: 0.875rem;
    }
    .ql-toolbar {
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        border-color: #cbd5e1 !important;
        background-color: #f8fafc;
    }
    .ql-container {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        border-color: #cbd5e1 !important;
    }
    .ql-editor img {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
    }
    #mathlive-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, 0.7);
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    #mathlive-modal.open {
        display: flex;
    }
    math-field {
        width: 100%;
        font-size: 1.25rem;
        padding: 0.5rem;
        border-radius: 0.375rem;
        border: 1px solid #cbd5e1;
        background: white;
    }
</style>
@endpush

@push('scripts')
<script defer src="https://unpkg.com/mathlive"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<div id="mathlive-modal">
    <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl">
        <div class="border-b border-slate-200 px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-slate-900">Equation Editor</h3>
            <button type="button" id="close-math-modal" class="text-slate-400 hover:text-slate-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-sm text-slate-500 mb-3">Ketik rumus atau gunakan virtual keyboard yang muncul.</p>
            <math-field id="math-field-input" virtual-keyboard-mode="manual"></math-field>
        </div>
        <div class="border-t border-slate-200 px-6 py-4 flex justify-end gap-3 bg-slate-50 rounded-b-2xl">
            <button type="button" id="cancel-math" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-200">Batal</button>
            <button type="button" id="insert-math" class="rounded-xl bg-blue-600 px-6 py-2 text-sm font-bold text-white hover:bg-blue-700">Sisipkan Rumus</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textareas = document.querySelectorAll('textarea');
        const mathModal = document.getElementById('mathlive-modal');
        const mathField = document.getElementById('math-field-input');
        const closeMathModal = document.getElementById('close-math-modal');
        const cancelMath = document.getElementById('cancel-math');
        const insertMath = document.getElementById('insert-math');
        
        let activeQuill = null;
        let activeRange = null;

        function closeModal() {
            mathModal.classList.remove('open');
            mathField.setValue('');
        }

        closeMathModal.addEventListener('click', closeModal);
        cancelMath.addEventListener('click', closeModal);

        insertMath.addEventListener('click', () => {
            if (activeQuill && activeRange) {
                const latex = mathField.getValue('latex');
                if (latex) {
                    activeQuill.insertEmbed(activeRange.index, 'formula', latex, Quill.sources.USER);
                    activeQuill.setSelection(activeRange.index + 1, Quill.sources.SILENT);
                }
            }
            closeModal();
        });

        textareas.forEach(ta => {
            // Hide the original textarea
            ta.style.display = 'none';

            // Create a container for Quill
            const container = document.createElement('div');
            ta.parentNode.insertBefore(container, ta.nextSibling);

            // Initialize Quill
            const quill = new Quill(container, {
                theme: 'snow',
                modules: {
                    formula: true,
                    toolbar: {
                        container: [
                            ['bold', 'italic', 'underline'],
                            ['link', 'image', 'formula'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }]
                        ],
                        handlers: {
                            'image': function() {
                                const input = document.createElement('input');
                                input.setAttribute('type', 'file');
                                input.setAttribute('accept', 'image/png, image/jpeg, image/jpg, image/webp');
                                input.click();
                                input.onchange = async () => {
                                    const file = input.files[0];
                                    if (file) {
                                        if (file.size > 2 * 1024 * 1024) {
                                            alert('Ukuran gambar maksimal 2MB.');
                                            return;
                                        }
                                        const formData = new FormData();
                                        formData.append('image', file);
                                        try {
                                            const response = await fetch('{{ route('guru.upload.image') }}', {
                                                method: 'POST',
                                                body: formData,
                                                headers: {
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                                }
                                            });
                                            const result = await response.json();
                                            if (result.url) {
                                                const range = this.quill.getSelection();
                                                this.quill.insertEmbed(range.index, 'image', result.url);
                                            }
                                        } catch (error) {
                                            alert('Gagal mengupload gambar.');
                                        }
                                    }
                                };
                            },
                            'formula': function() {
                                activeQuill = this.quill;
                                activeRange = this.quill.getSelection() || { index: this.quill.getLength() };
                                mathModal.classList.add('open');
                                setTimeout(() => mathField.focus(), 100);
                            }
                        }
                    }
                }
            });

            // Set initial content
            quill.root.innerHTML = ta.value;

            // Sync back to textarea on form submit or text change
            quill.on('text-change', function() {
                ta.value = quill.root.innerHTML;
                // Dispatch input event for AlpineJS if necessary
                ta.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });
    });
</script>
@endpush
