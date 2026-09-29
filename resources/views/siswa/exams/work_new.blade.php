<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->nama }} - SMART CBT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="text/x-mathjax-config">
        MathJax.Hub.Config({
            tex2jax: { inlineMath: [['\\(','\\)']], displayMath: [['$$','$$']], processEscapes: true }
        });
    </script>
    <script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.7/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    <style>
        /* Standalone fallback styles to guarantee flawless rendering across all devices & WebViews */
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        .option-label {
            word-break: break-word;
            overflow-wrap: anywhere;
            max-width: 100%;
        }

        .rich-text-content {
            word-break: break-word;
            overflow-wrap: anywhere;
            max-width: 100%;
            line-height: 1.7;
        }

        .rich-text-content p {
            margin-top: 0;
            margin-bottom: 0.625rem;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .rich-text-content p:last-child {
            margin-bottom: 0;
        }

        .option-content > p:only-child {
            display: inline;
            margin-bottom: 0;
        }

        .rich-text-content img {
            max-width: 100% !important;
            height: auto !important;
            object-fit: contain;
            border-radius: 0.5rem;
            display: block;
            margin: 0.5rem 0;
        }

        .rich-text-content table {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            border-collapse: collapse;
            margin: 0.75rem 0;
            background: #ffffff;
        }

        .rich-text-content table td,
        .rich-text-content table th {
            padding: 0.5rem 0.75rem;
            border: 1px solid #cbd5e1;
            font-size: 0.875rem;
            min-width: 80px;
            vertical-align: top;
        }

        .rich-text-content pre {
            max-width: 100% !important;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-size: 0.875rem;
        }

        .rich-text-content blockquote {
            border-left: 4px solid #3b82f6;
            background: #f8fafc;
            padding: 0.625rem 1rem;
            margin: 0.75rem 0;
            border-radius: 0 0.5rem 0.5rem 0;
            color: #334155;
            font-style: italic;
        }

        .rich-text-content ul {
            list-style-type: disc;
            margin-left: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .rich-text-content ol {
            list-style-type: decimal;
            margin-left: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .MathJax_Display, .MathJax_Preview, .MathJax {
            max-width: 100% !important;
            overflow-x: auto !important;
            overflow-y: hidden !important;
            -webkit-overflow-scrolling: touch;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased" 
      x-data="{ navOpen: false, zoomImage: null, showConfirmModal: false, unansweredCount: 0, pendingCount: 0, submitErrorMessage: null }"
      @keydown.escape.window="navOpen = false"
      @open-submit-modal.window="unansweredCount = $event.detail.unansweredCount; pendingCount = $event.detail.pendingCount; showConfirmModal = true;"
      @close-submit-modal.window="showConfirmModal = false;">
    <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur-md shadow-sm">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-3 py-2.5 sm:px-6 sm:py-3.5">
            <div class="min-w-0 flex-1">
                <p class="truncate text-[10px] font-bold uppercase tracking-wider text-blue-600 sm:text-xs">
                    {{ $exam->mataPelajaran?->nama }}
                </p>
                <h1 class="truncate text-sm font-black text-slate-900 sm:text-lg">
                    {{ $exam->nama }}
                </h1>
            </div>

            <div class="shrink-0 flex items-center gap-2 sm:gap-3">
                <!-- Save Status & Connection Status (Desktop) -->
                <div class="hidden flex-col items-end gap-0.5 sm:flex">
                    <div id="save-status" class="flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold">
                        <span id="save-status-text">--</span>
                        <span id="save-status-icon" class="h-3.5 w-3.5"></span>
                    </div>
                    <div id="connection-status" class="text-right text-[10px] font-semibold text-emerald-600">Online</div>
                </div>

                <!-- Timer Badge -->
                <div class="flex flex-col items-center rounded-xl bg-blue-50 px-2.5 py-1 text-center sm:px-4 sm:py-1.5 border border-blue-100">
                    <span class="block text-[8px] sm:text-[9px] font-bold uppercase tracking-wider text-blue-600">Sisa waktu</span>
                    <strong id="countdown" class="text-sm sm:text-lg font-black tabular-nums text-blue-800">--:--</strong>
                </div>

                <!-- Mobile Daftar Soal Button -->
                <button type="button" 
                        @click="navOpen = true"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800 active:scale-95 transition lg:hidden"
                        title="Buka Navigasi Soal">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                    <span>Soal</span>
                    <span id="mobile-progress-badge" class="rounded bg-white/20 px-1.5 py-0.5 text-[10px] font-bold text-white">0/{{ $exam->questions->count() }}</span>
                </button>
            </div>
        </div>

        <!-- Mobile Sub-Header Status Bar -->
        <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/90 px-3 py-1 text-[11px] sm:hidden">
            <div class="flex items-center gap-1.5">
                <span id="mobile-connection-dot" class="inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span id="mobile-connection-status" class="font-medium text-slate-600">Online</span>
            </div>
            <div class="flex items-center gap-1 font-semibold text-slate-600">
                <span id="mobile-save-status-text">--</span>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-3 py-4 sm:px-6 sm:py-6 lg:grid lg:grid-cols-[1fr_270px] lg:gap-6">
        <div class="min-w-0 flex-1">
            @if ($exam->deskripsi)
                <details class="group mb-5 rounded-2xl border border-amber-200 bg-amber-50/90 p-4 shadow-sm" open>
                    <summary class="flex cursor-pointer items-center justify-between font-bold text-xs sm:text-sm text-amber-900 list-none select-none">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Petunjuk Ujian</span>
                        </div>
                        <span class="text-xs text-amber-700 transition duration-200 group-open:rotate-180">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </summary>
                    <div class="mt-2 text-xs sm:text-sm text-amber-800 leading-relaxed whitespace-pre-wrap pl-7 border-t border-amber-200/60 pt-2">
                        {{ $exam->deskripsi }}
                    </div>
                </details>
            @endif

            <form id="exam-form" method="POST" action="{{ route('siswa.ujian.submit', $exam, false) }}" class="space-y-5">
            @csrf
            
            <!-- Hidden input to track pending state -->
            <input type="hidden" id="pending-indicator" value="0">
            
            @foreach ($exam->questions as $question)
                <section id="question-{{ $loop->iteration }}" 
                         class="question-panel rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-7 overflow-hidden" 
                         data-question="{{ $loop->iteration }}" 
                         data-question-id="{{ $question->id }}"
                         style="display: {{ $loop->first ? 'block' : 'none' }}">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 sm:pb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm sm:text-base font-extrabold text-slate-800">
                                Soal {{ $loop->iteration }}
                            </span>
                            <span class="text-xs font-semibold text-slate-400">
                                / {{ $exam->questions->count() }}
                            </span>
                        </div>
                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700 uppercase tracking-wide">
                            {{ $question->tipe === 'pg' ? 'Pilihan Ganda' : 'Essay' }}
                        </span>
                    </div>

                    @if($question->image)
                        <div class="my-4">
                            <img src="{{ Storage::url($question->image) }}" 
                                 class="max-h-80 w-auto max-w-full rounded-xl border border-slate-200 shadow-sm cursor-pointer hover:opacity-90 transition object-contain" 
                                 alt="Gambar Soal" 
                                 @click="zoomImage = '{{ Storage::url($question->image) }}'">
                        </div>
                    @endif

                    <div class="mt-4 text-slate-800">
                        <div class="rich-text-content question-content break-words text-[15px] sm:text-base leading-relaxed text-slate-800">
                            {!! $question->pertanyaan !!}
                        </div>
                    </div>

                    @if ($question->tipe === 'pg')
                        <div class="mt-6 space-y-3">
                            @foreach (['a','b','c','d','e'] as $option)
                                @if ($question->{'opsi_'.$option})
                                    <label class="option-label group relative flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 sm:p-4 transition-all hover:border-blue-400 hover:bg-blue-50/40 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/60 has-[:checked]:ring-1 has-[:checked]:ring-blue-600/30">
                                        <input type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ strtoupper($option) }}" 
                                               class="mt-1 h-4 w-4 shrink-0 text-blue-600 border-slate-300 focus:ring-blue-500 answer-input"
                                               data-question-id="{{ $question->id }}"
                                               @checked(($answers[$question->id] ?? '') === strtoupper($option))>
                                        
                                        <div class="min-w-0 flex-1 flex items-start gap-2.5 sm:gap-3">
                                            <span class="option-badge inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-700 transition group-hover:bg-blue-100 group-hover:text-blue-700 group-has-[:checked]:bg-blue-600 group-has-[:checked]:text-white select-none">
                                                {{ strtoupper($option) }}
                                            </span>
                                            <div class="rich-text-content option-content min-w-0 flex-1 break-words text-sm sm:text-base leading-relaxed text-slate-800">
                                                {!! $question->{'opsi_'.$option} !!}
                                            </div>
                                        </div>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <label class="mt-6 block">
                            <span class="mb-2 block text-xs sm:text-sm font-bold text-slate-700">Jawaban Anda</span>
                            <textarea name="answers[{{ $question->id }}]" 
                                      rows="5" 
                                      class="w-full max-w-full rounded-xl border-slate-300 p-3 sm:p-4 text-sm sm:text-base focus:border-blue-500 focus:ring-blue-500 answer-input shadow-inner transition-colors"
                                      data-question-id="{{ $question->id }}"
                                      placeholder="Tulis jawaban Anda di sini...">{{ $answers[$question->id] ?? '' }}</textarea>
                        </label>
                    @endif
                </section>
            @endforeach

            <div class="flex items-center justify-between gap-3 pt-2">
                <button type="button" id="previous" 
                        class="flex flex-1 sm:flex-none items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 min-h-[44px]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>Sebelumnya</span>
                </button>
                <button type="button" id="next" 
                        class="flex flex-1 sm:flex-none items-center justify-center gap-1.5 rounded-xl bg-blue-600 px-5 py-3 text-xs sm:text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95 min-h-[44px]">
                    <span>Berikutnya</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button type="button" id="finish" 
                        class="hidden flex-1 sm:flex-none items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-5 py-3 text-xs sm:text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 active:scale-95 min-h-[44px]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Kumpulkan Ujian</span>
                </button>
            </div>
            </form>
        </div>

        <!-- Desktop Sidebar Navigation -->
        <aside class="hidden lg:block h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sticky top-24">
            <h2 class="font-bold text-slate-900">Navigasi Soal</h2>
            <div class="mt-4">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                    <span>Progress jawaban</span>
                    <span id="answer-progress-text">0/{{ $exam->questions->count() }}</span>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                    <div id="answer-progress-bar" class="h-full w-0 rounded-full bg-blue-600 transition-all"></div>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-5 gap-2 max-h-[55vh] overflow-y-auto pr-1 custom-scrollbar">
                @foreach ($exam->questions as $question)
                    <button type="button" 
                            class="question-number h-9 rounded-lg border border-slate-200 text-sm font-bold text-slate-600 hover:border-blue-400 transition focus:outline-none" 
                            data-target="{{ $loop->iteration }}"
                            data-question-id="{{ $question->id }}">
                        {{ $loop->iteration }}
                    </button>
                @endforeach
            </div>
            <p class="mt-5 text-xs leading-relaxed text-slate-500">
                Jawaban akan tersimpan otomatis setiap 5 soal yang berubah. 
                <span id="pending-count" class="hidden font-semibold text-amber-600">
                    Ada <span id="pending-count-num">0</span> jawaban yang belum tersimpan.
                </span>
            </p>
        </aside>
    </main>

    <!-- Mobile Drawer Backdrop -->
    <div x-show="navOpen" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
         @click="navOpen = false"
         style="display: none;"></div>

    <!-- Mobile Drawer Content -->
    <aside x-show="navOpen"
           x-transition:enter="transition ease-out duration-300 transform"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           class="fixed inset-y-0 right-0 z-50 flex w-80 max-w-[85vw] flex-col bg-white shadow-2xl lg:hidden"
           style="display: none;">
        <div class="flex items-center justify-between border-b border-slate-100 p-4">
            <div>
                <h2 class="font-bold text-slate-900 text-base">Navigasi Soal</h2>
                <p class="text-xs text-slate-500">Pilih nomor untuk membuka soal</p>
            </div>
            <button type="button" @click="navOpen = false" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="border-b border-slate-100 p-4 bg-slate-50/60">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                <span>Progress Jawaban</span>
                <span id="mobile-drawer-progress-text">0/{{ $exam->questions->count() }}</span>
            </div>
            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                <div id="mobile-drawer-progress-bar" class="h-full w-0 rounded-full bg-blue-600 transition-all"></div>
            </div>
            <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500">
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Terjawab</span>
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-slate-200"></span> Belum</span>
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full border border-blue-600"></span> Aktif</span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
            <div class="grid grid-cols-5 gap-2">
                @foreach ($exam->questions as $question)
                    <button type="button" 
                            @click="navOpen = false"
                            class="question-number h-10 rounded-xl border border-slate-200 text-sm font-bold text-slate-600 hover:border-blue-400 active:scale-95 transition" 
                            data-target="{{ $loop->iteration }}"
                            data-question-id="{{ $question->id }}">
                        {{ $loop->iteration }}
                    </button>
                @endforeach
            </div>
        </div>
    </aside>

    <script>
        /**
         * ============================================================================
         * BATCH ANSWER SAVING SYSTEM WITH LOCALSTORAGE PERSISTENCE
         * ============================================================================
         * 
         * FITUR:
         * - Simpan pending answers ke localStorage sebagai persistent queue
         * - Batch save setiap 5 jawaban yang berubah
         * - Retry mechanism untuk failed requests
         * - Hapus hanya batch sukses dari localStorage
         * - Visual indicator status
         * - Prevent submit sebelum pending selesai
         * - Handle page leave & time up dengan pending answers
         * - Prevent duplikasi dengan update-or-create backend
         * - Resume ujian dengan merge localStorage + database
         */

        // ============================================================================
        // CONFIG & STATE MANAGEMENT
        // ============================================================================

        const CONFIG = {
            BATCH_SIZE: 5,                    // Save setiap 5 jawaban
            PERIODIC_INTERVAL: 30000,         // 30 detik untuk fallback save
            SAVE_TIMEOUT: 10000,              // 10 detik timeout untuk request
            MAX_RETRIES: 3,                   // Maksimal 3x retry
            RETRY_DELAY: 1000,                // Delay 1 detik sebelum retry
        };

        // Get unique storage key untuk exam attempt
        const ATTEMPT_ID = '{{ $attempt->id }}';
        const STORAGE_KEY = `cbt_pending_answers_${ATTEMPT_ID}`;

        // State tracking
        const state = {
            // Pending answers di memory (sync dengan localStorage)
            // Structure: { questionId: answer, ... }
            pendingAnswers: {},
            
            changedQuestions: new Set(),      // Set<questionId> - soal yang berubah, digunakan untuk batch logic
            savedAnswers: new Map(),          // Map<questionId, jawaban> - jawaban yang sudah tersimpan di server
            isSaving: false,                  // Prevent concurrent saves
            savingError: null,                // Error dari save terakhir
            retryCount: 0,                    // Jumlah retry attempt
            pageUnloading: false,             // Flag untuk page unload
            isSubmitting: false,              // Flag untuk submit
            submitStarted: false,             // Mutex global untuk submit/timer race condition
        };

        // DOM Elements
        const elements = {
            form: document.getElementById('exam-form'),
            previous: document.getElementById('previous'),
            next: document.getElementById('next'),
            finish: document.getElementById('finish'),
            panels: [...document.querySelectorAll('.question-panel')],
            numbers: [...document.querySelectorAll('.question-number')],
            answerInputs: [...document.querySelectorAll('.answer-input')],
            countdown: document.getElementById('countdown'),
            saveStatus: document.getElementById('save-status'),
            saveStatusText: document.getElementById('save-status-text'),
            saveStatusIcon: document.getElementById('save-status-icon'),
            pendingIndicator: document.getElementById('pending-indicator'),
            pendingCountEl: document.getElementById('pending-count'),
            pendingCountNum: document.getElementById('pending-count-num'),
            connectionStatus: document.getElementById('connection-status'),
            answerProgressText: document.getElementById('answer-progress-text'),
            answerProgressBar: document.getElementById('answer-progress-bar'),
        };

        let current = 1;
        let saveTimer = null;
        let periodicSaveTimer = null;
        let deadline = null;
        let countdownTimer = null;
        let activeSavePromise = null;

        // ============================================================================
        // LOCALSTORAGE MANAGEMENT
        // ============================================================================

        /**
         * Baca pending answers dari localStorage
         */
        function getPendingAnswersFromStorage() {
            try {
                const stored = localStorage.getItem(STORAGE_KEY);
                return stored ? JSON.parse(stored) : {};
            } catch (error) {
                console.error('Error reading from localStorage:', error);
                return {};
            }
        }

        /**
         * Simpan pending answers ke localStorage
         */
        function savePendingAnswersToStorage(answers) {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(answers));
            } catch (error) {
                console.error('Error writing to localStorage:', error);
            }
        }

        /**
         * Hapus answers tertentu dari localStorage (setelah batch berhasil disimpan)
         */
        function removeAnswersFromStorage(questionIds) {
            try {
                const pending = getPendingAnswersFromStorage();
                questionIds.forEach(qId => {
                    delete pending[String(qId)];
                });
                savePendingAnswersToStorage(pending);
            } catch (error) {
                console.error('Error removing from localStorage:', error);
            }
        }

        /**
         * Hapus semua pending answers dari localStorage (setelah submit berhasil)
         */
        function clearPendingAnswersStorage() {
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (error) {
                console.error('Error clearing localStorage:', error);
            }
        }

        // ============================================================================
        // UTILITY FUNCTIONS
        // ============================================================================

        /**
         * Update visual indicator untuk status penyimpanan
         */
        function updateSaveStatus() {
            // Hitung jumlah pending dari object
            const pendingCount = Object.keys(state.pendingAnswers).length;
            
            if (state.isSaving) {
                elements.saveStatus.className = 'flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-700';
                elements.saveStatusText.textContent = 'Menyimpan...';
                elements.saveStatusIcon.innerHTML = '<svg class="animate-spin h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            } else if (state.savingError) {
                elements.saveStatus.className = 'flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold bg-red-50 text-red-700';
                elements.saveStatusText.textContent = `Gagal (${state.retryCount}/${CONFIG.MAX_RETRIES})`;
                elements.saveStatusIcon.innerHTML = '<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            } else if (pendingCount > 0) {
                elements.saveStatus.className = 'flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold bg-blue-50 text-blue-700';
                elements.saveStatusText.textContent = `${pendingCount} pending`;
                elements.saveStatusIcon.innerHTML = '<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            } else {
                elements.saveStatus.className = 'flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700';
                elements.saveStatusText.textContent = 'Tersimpan';
                elements.saveStatusIcon.innerHTML = '<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            }

            // Sync with mobile status bar
            const mobileSaveStatus = document.getElementById('mobile-save-status-text');
            if (mobileSaveStatus) {
                if (state.isSaving) {
                    mobileSaveStatus.textContent = 'Menyimpan...';
                } else if (state.savingError) {
                    mobileSaveStatus.textContent = `Gagal (${state.retryCount}/${CONFIG.MAX_RETRIES})`;
                } else if (pendingCount > 0) {
                    mobileSaveStatus.textContent = `${pendingCount} pending`;
                } else {
                    mobileSaveStatus.textContent = 'Tersimpan';
                }
            }

            // Update pending count di sidebar
            if (elements.pendingCountEl && elements.pendingCountNum) {
                if (pendingCount > 0) {
                    elements.pendingCountEl.classList.remove('hidden');
                    elements.pendingCountNum.textContent = pendingCount;
                } else {
                    elements.pendingCountEl.classList.add('hidden');
                }
            }

            // Update hidden indicator
            if (elements.pendingIndicator) {
                elements.pendingIndicator.value = pendingCount;
            }
            updateAnswerProgress();
        }

        function updateAnswerProgress() {
            const answers = getCurrentAnswers();
            const answeredCount = Object.values(answers).filter(answer => String(answer).trim() !== '').length;
            const totalQuestions = elements.panels.length;
            const percentage = totalQuestions ? (answeredCount / totalQuestions) * 100 : 0;

            if (elements.answerProgressText) {
                elements.answerProgressText.textContent = `${answeredCount}/${totalQuestions}`;
            }
            if (elements.answerProgressBar) {
                elements.answerProgressBar.style.width = `${percentage}%`;
            }

            // Update mobile header badge
            const mobileBadge = document.getElementById('mobile-progress-badge');
            if (mobileBadge) {
                mobileBadge.textContent = `${answeredCount}/${totalQuestions}`;
            }

            // Update mobile drawer progress
            const drawerProgressText = document.getElementById('mobile-drawer-progress-text');
            if (drawerProgressText) {
                drawerProgressText.textContent = `${answeredCount}/${totalQuestions}`;
            }
            const drawerProgressBar = document.getElementById('mobile-drawer-progress-bar');
            if (drawerProgressBar) {
                drawerProgressBar.style.width = `${percentage}%`;
            }

            elements.numbers.forEach(button => {
                const questionId = button.dataset.questionId;
                const isAnswered = String(answers[questionId] ?? '').trim() !== '';
                button.classList.toggle('border-emerald-500', isAnswered);
                button.classList.toggle('bg-emerald-50', isAnswered);
                button.classList.toggle('text-emerald-700', isAnswered);
            });
        }

        function updateConnectionStatus() {
            const isOnline = navigator.onLine;
            if (elements.connectionStatus) {
                elements.connectionStatus.textContent = isOnline ? 'Online' : 'Offline - tersimpan di HP';
                elements.connectionStatus.className = isOnline
                    ? 'text-right text-[10px] font-semibold text-emerald-600'
                    : 'text-right text-[10px] font-semibold text-amber-600';
            }
            const mobileConn = document.getElementById('mobile-connection-status');
            if (mobileConn) {
                mobileConn.textContent = isOnline ? 'Online' : 'Offline';
            }
            const mobileDot = document.getElementById('mobile-connection-dot');
            if (mobileDot) {
                mobileDot.className = isOnline 
                    ? 'inline-block h-2 w-2 rounded-full bg-emerald-500 animate-pulse' 
                    : 'inline-block h-2 w-2 rounded-full bg-amber-500';
            }
        }

        /**
         * Get jawaban terkini dari form
         */
        function getCurrentAnswers() {
            const formData = new FormData(elements.form);
            const answers = {};
            
            for (const [key, value] of formData.entries()) {
                const match = key.match(/^answers\[(\d+)\]$/);
                if (match) {
                    answers[match[1]] = value;
                }
            }
            
            return answers;
        }

        /**
         * Track perubahan jawaban dan update pending queue + localStorage
         */
        function trackAnswerChange(questionId, newAnswer) {
            const questionIdStr = String(questionId);
            const savedAnswer = state.savedAnswers.get(questionIdStr);
            
            // Jika jawaban sama dengan yang sudah tersimpan di server, hapus dari pending
            if (savedAnswer === newAnswer) {
                delete state.pendingAnswers[questionIdStr];
                state.changedQuestions.delete(questionIdStr);
            } else {
                // Jika berbeda, masukkan ke pending
                state.pendingAnswers[questionIdStr] = newAnswer;
                state.changedQuestions.add(questionIdStr);
            }
            
            // Sync dengan localStorage
            savePendingAnswersToStorage(state.pendingAnswers);
            
            updateSaveStatus();
        }

        // ============================================================================
        // SAVE MECHANISM
        // ============================================================================

        /**
         * Save batch answers ke backend
         * Retry otomatis jika gagal
         * Hapus dari localStorage HANYA jika berhasil
         */
        async function saveBatchAnswers(answers, attempt = 0) {
            if (Object.keys(answers).length === 0) {
                return Promise.resolve(); // No answers to save
            }

            if (activeSavePromise) {
                return activeSavePromise.then(() => saveBatchAnswers(answers, attempt));
            }

            state.isSaving = true;
            updateSaveStatus();

            const sentAnswers = { ...answers };
            const savePromise = (async () => {
                let retryAttempt = attempt;

                while (true) {
                    try {
                        const response = await fetch('{{ route('siswa.ujian.answers', $exam, false) }}', {
                            method: 'POST',
                            credentials: 'same-origin',
                            
                            timeout: CONFIG.SAVE_TIMEOUT,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ answers: sentAnswers }),
                        });

                        if (!response.ok) {
                    if (response.status === 419 || response.status === 401 || response.status === 403) {
                        alert("Sesi Anda telah kadaluarsa atau Anda login di perangkat lain. Jawaban terakhir Anda sudah diamankan di browser.\n\nSilakan REFRESH / MUAT ULANG halaman ini sekarang.");
                        throw new Error("Session Expired");
                    }
                    throw new Error(`Server error: ${response.status}`);
                }

                        const data = await response.json();
                        
                        if (!data.saved) {
                            throw new Error('Save response invalid');
                        }

                        // Backend berhasil - mark answers sebagai tersimpan
                        Object.entries(sentAnswers).forEach(([qId, answer]) => {
                            state.savedAnswers.set(String(qId), answer);
                            const questionId = String(qId);
                            const hasPendingAnswer = Object.prototype.hasOwnProperty.call(state.pendingAnswers, questionId);

                            if (!hasPendingAnswer || state.pendingAnswers[questionId] === answer) {
                                delete state.pendingAnswers[questionId];
                                state.changedQuestions.delete(questionId);
                            }
                        });

                        // HAPUS hanya pending yang nilainya masih sama dengan request sukses
                        savePendingAnswersToStorage(state.pendingAnswers);

                        state.savingError = null;
                        state.retryCount = 0;
                        return true;
                    } catch (error) {
                        console.error('Save error:', error);
                        state.savingError = error.message;

                        if (retryAttempt < CONFIG.MAX_RETRIES) {
                            retryAttempt += 1;
                            state.retryCount = retryAttempt;
                            await new Promise(resolve => setTimeout(resolve, CONFIG.RETRY_DELAY * retryAttempt));
                            continue;
                        }

                        // Setelah retry maksimum: pending wajib tetap ada.
                        // Jangan menghapus localStorage dan jangan menyembunyikan error.
                        console.error('Max retries reached, answers kept in pending');
                        throw error;
                    }
                }
            })();

            activeSavePromise = savePromise;

            try {
                return await savePromise;
            } finally {
                if (activeSavePromise === savePromise) {
                    activeSavePromise = null;
                }
                state.isSaving = false;
                updateSaveStatus();
            }
        }

        /**
         * Check apakah batch sudah full (5 jawaban) dan save jika perlu
         */
        async function checkAndSaveBatch() {
            if (state.changedQuestions.size >= CONFIG.BATCH_SIZE) {
                // Convert Set to array dan ambil CONFIG.BATCH_SIZE items
                const itemsToSave = Array.from(state.changedQuestions).slice(0, CONFIG.BATCH_SIZE);
                const batchAnswers = {};
                
                itemsToSave.forEach(qId => {
                    // Get dari state.pendingAnswers object
                    batchAnswers[qId] = state.pendingAnswers[String(qId)];
                });

                await saveBatchAnswers(batchAnswers);
            }
        }

        /**
         * Save semua pending answers (digunakan saat submit & time up)
         */
        async function saveAllPendingAnswers() {
            if (Object.keys(state.pendingAnswers).length === 0) {
                return Promise.resolve();
            }

            // Copy semua pending answers
            const allAnswers = { ...state.pendingAnswers };
            return saveBatchAnswers(allAnswers);
        }

        /**
         * Queue save dengan debounce
         */
        function queueSave() {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(checkAndSaveBatch, 500);
        }

        // ============================================================================
        // NAVIGATION
        // ============================================================================

        function showQuestion(number) {
            current = Math.max(1, Math.min(number, elements.panels.length));
            elements.panels.forEach((panel, index) => {
                panel.style.display = index + 1 === current ? 'block' : 'none';
            });
            elements.numbers.forEach((button) => {
                const isCurrent = Number(button.dataset.target) === current;
                button.classList.toggle('border-blue-600', isCurrent);
                button.classList.toggle('ring-2', isCurrent);
                button.classList.toggle('ring-blue-500', isCurrent);
                button.classList.toggle('ring-offset-1', isCurrent);
            });
            
            elements.previous.disabled = current === 1;
            elements.next.classList.toggle('hidden', current === elements.panels.length);
            elements.finish.classList.toggle('hidden', current !== elements.panels.length);

            // Auto-scroll smooth to question top
            const activePanel = document.getElementById(`question-${current}`);
            if (activePanel) {
                const rect = activePanel.getBoundingClientRect();
                if (rect.top < 0 || rect.top > 160) {
                    window.scrollTo({
                        top: activePanel.offsetTop - 75,
                        behavior: 'smooth'
                    });
                }
            }
        }

        // ============================================================================
        // SUBMIT & TIME UP HANDLING
        // ============================================================================

        /**
         * Buka modal konfirmasi kumpulkan ujian (Kompatibel 100% dengan Safe Exam Browser & Android Exambro)
         */
        function submitExam() {
            if (state.submitStarted || state.isSubmitting) {
                return;
            }

            // Hitung jumlah soal yang belum dijawab
            const answers = getCurrentAnswers();
            const answeredCount = Object.values(answers).filter(a => String(a).trim() !== '').length;
            const unanswered = elements.panels.length - answeredCount;
            const pending = Object.keys(state.pendingAnswers).length;

            // Buka custom DOM modal melalui event Alpine
            window.dispatchEvent(new CustomEvent('open-submit-modal', {
                detail: {
                    unansweredCount: unanswered,
                    pendingCount: pending
                }
            }));
        }

        /**
         * Eksekusi kumpulkan ujian saat siswa menekan tombol "Ya, Kumpulkan" di dalam modal
         */
        async function executeFinalSubmit() {
            window.dispatchEvent(new CustomEvent('close-submit-modal'));

            if (state.submitStarted || state.isSubmitting) {
                return;
            }

            state.submitStarted = true;
            state.isSubmitting = true;

            // Disable buttons
            elements.previous.disabled = true;
            elements.next.disabled = true;
            elements.finish.disabled = true;
            elements.finish.textContent = 'Menyimpan jawaban...';

            // Sinkronkan seluruh pending answers ke input form sebelum submit
            if (state.pendingAnswers && typeof state.pendingAnswers === 'object') {
                Object.entries(state.pendingAnswers).forEach(([qId, val]) => {
                    const radio = elements.form.querySelector(`input[name="answers[${qId}]"][value="${val}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                    const textarea = elements.form.querySelector(`textarea[name="answers[${qId}]"]`);
                    if (textarea) {
                        textarea.value = val;
                    }
                });
            }

            try {
                await saveAllPendingAnswers();

                // Hanya clear localStorage setelah server mengkonfirmasi save sukses.
                clearPendingAnswersStorage();

                // Submit form hanya setelah semua pending berhasil disimpan.
                elements.form.submit();
            } catch (error) {
                console.error('Submit error:', error);
                elements.finish.disabled = false;
                elements.finish.textContent = 'Kumpulkan Ujian';
                state.isSubmitting = false;
                state.submitStarted = false;

                const errorMsg = error.message === "Session Expired"
                    ? "Sesi ujian Anda telah berakhir atau akun aktif di perangkat lain. Silakan MUAT ULANG / REFRESH halaman ini."
                    : `Gagal menyimpan jawaban ke server (${error.message || 'Koneksi error'}). Silakan periksa koneksi internet Anda lalu coba tekan Kumpulkan lagi.`;

                const bodyEl = document.querySelector('body');
                if (window.Alpine && bodyEl) {
                    const alpineData = Alpine.$data(bodyEl);
                    if (alpineData) {
                        alpineData.submitErrorMessage = errorMsg;
                    }
                }
                alert(errorMsg);
            }
        }

        /**
         * Auto submit ketika waktu habis
         */
        async function timeExpired() {
            console.log('Waktu ujian habis');
            state.pageUnloading = true;
            
            // Show full screen overlay
            const overlay = document.createElement('div');
            overlay.innerHTML = '<div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,0.9);color:white;flex-direction:column;gap:1rem;"><svg class="h-16 w-16 text-rose-500 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><h2 class="text-2xl font-bold text-center">Waktu Ujian Telah Habis!</h2><p class="text-slate-300 text-center">Sistem sedang mengumpulkan jawaban Anda...</p></div>';
            document.body.appendChild(overlay.firstChild);

            // Disable tombol navigasi (overlay sudah memblokir klik user, jangan disable answerInputs agar data tetap terkirim via form submit)
            elements.previous.disabled = true;
            elements.next.disabled = true;
            elements.finish.disabled = true;

            // Pastikan seluruh pending answers tersinkron ke input form
            if (state.pendingAnswers && typeof state.pendingAnswers === 'object') {
                Object.entries(state.pendingAnswers).forEach(([qId, val]) => {
                    const radio = elements.form.querySelector(`input[name="answers[${qId}]"][value="${val}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                    const textarea = elements.form.querySelector(`textarea[name="answers[${qId}]"]`);
                    if (textarea) {
                        textarea.value = val;
                    }
                });
            }

            try {
                await saveAllPendingAnswers();
                clearPendingAnswersStorage();
                await new Promise(resolve => setTimeout(resolve, 1000));
            } catch (error) {
                console.error('Error saving pending on time up:', error);
            }

            elements.form.submit();
        }

        // ============================================================================
        // PAGE UNLOAD HANDLING
        // ============================================================================

        /**
         * Jika ada pending answers saat page unload, coba save
         */
        window.addEventListener('pagehide', async () => {
            if (state.submitStarted || state.isSubmitting || state.pageUnloading) {
                return; // Sudah dalam proses submit
            }

            const pendingCount = Object.keys(state.pendingAnswers).length;
            if (pendingCount > 0) {
                state.pageUnloading = true;
                
                // Use fetch dengan keepalive untuk reliability pada page unload
                const answers = { ...state.pendingAnswers };

                fetch('{{ route('siswa.ujian.answers', $exam, false) }}', {
                    method: 'POST',
                            credentials: 'same-origin',
                    
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ answers }),
                }).catch(err => console.error('Error saving on page unload:', err));
            }

            // Leave session
            fetch('{{ route('siswa.ujian.leave', $exam, false) }}', {
                method: 'POST',
                            credentials: 'same-origin',
                
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            }).catch(err => console.error('Error on leave:', err));
        });

        /**
         * Prevent unload dialog jika ada pending
         */
        window.addEventListener('beforeunload', (e) => {
            const pendingCount = Object.keys(state.pendingAnswers).length;
            if (pendingCount > 0 && !state.submitStarted && !state.isSubmitting && !state.pageUnloading) {
                e.preventDefault();
                e.returnValue = 'Ada jawaban yang belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?';
            }
        });

        // ============================================================================
        // INITIALIZE
        // ============================================================================

        /**
         * Initialize dengan load dari localStorage dan database
         */
        function initialize() {
            // Load jawaban dari database yang sudah tersimpan
            const dbAnswers = getCurrentAnswers();
            Object.entries(dbAnswers).forEach(([qId, answer]) => {
                if (answer) {  // Hanya jika ada jawaban
                    state.savedAnswers.set(String(qId), answer);
                }
            });

            // Load pending answers dari localStorage (jawaban yang belum berhasil disimpan)
            const storedPending = getPendingAnswersFromStorage();
            state.pendingAnswers = { ...storedPending };

            // Reconstruct changedQuestions dari pending answers
            Object.keys(storedPending).forEach(qId => {
                state.changedQuestions.add(qId);
            });

            console.log('Initialized state:');
            console.log('- Saved answers from DB:', state.savedAnswers.size);
            console.log('- Pending answers from localStorage:', Object.keys(state.pendingAnswers).length);
            console.log('- Changed questions:', state.changedQuestions.size);

            // Restore UI from localStorage pending answers
            if (Object.keys(state.pendingAnswers).length > 0) {
                Object.entries(state.pendingAnswers).forEach(([qId, val]) => {
                    const inputs = elements.form.querySelectorAll(`[name="answers[${qId}]"]`);
                    inputs.forEach(input => {
                        if (input.type === 'radio') {
                            if (input.value === val) {
                                input.checked = true;
                            }
                        } else {
                            input.value = val;
                        }
                    });
                });
                // Update nav button colors based on restored answers
                updateAnswerProgress();
            }

            // Set deadline
            const deadlineStr = '{{ $attempt->started_at->copy()->addMinutes($exam->durasi_menit)->min($exam->tanggal_selesai ?? $attempt->started_at->copy()->addMinutes($exam->durasi_menit))->toIso8601String() }}';
            deadline = new Date(deadlineStr).getTime();

            // Setup event listeners
            elements.previous.addEventListener('click', () => showQuestion(current - 1));
            elements.next.addEventListener('click', () => showQuestion(current + 1));
            elements.finish.addEventListener('click', submitExam);

            elements.numbers.forEach(button => {
                button.addEventListener('click', () => showQuestion(Number(button.dataset.target)));
            });

            // Track answer changes
            elements.answerInputs.forEach(input => {
                input.addEventListener('input', (e) => {
                    const questionId = e.target.dataset.questionId;
                    
                    // Get current value (for textarea & radio)
                    let currentValue;
                    if (e.target.type === 'radio') {
                        const checkedRadio = elements.form.querySelector(`input[name="answers[${questionId}]"]:checked`);
                        currentValue = checkedRadio ? checkedRadio.value : '';
                    } else {
                        currentValue = e.target.value;
                    }
                    
                    trackAnswerChange(questionId, currentValue);
                    updateAnswerProgress();
                    queueSave();
                });

                input.addEventListener('change', (e) => {
                    const questionId = e.target.dataset.questionId;
                    
                    let currentValue;
                    if (e.target.type === 'radio') {
                        const checkedRadio = elements.form.querySelector(`input[name="answers[${questionId}]"]:checked`);
                        currentValue = checkedRadio ? checkedRadio.value : '';
                    } else {
                        currentValue = e.target.value;
                    }
                    
                    trackAnswerChange(questionId, currentValue);
                    updateAnswerProgress();
                    queueSave();
                });
            });

            // Periodic fallback save: simpan otomatis ke server jika ada pending answers (meskipun belum genap 5)
            periodicSaveTimer = setInterval(() => {
                const pendingCount = Object.keys(state.pendingAnswers).length;
                if (pendingCount > 0 && !state.isSaving && !state.isSubmitting) {
                    saveAllPendingAnswers().catch(err => console.warn('Periodic save warning:', err));
                }
            }, CONFIG.PERIODIC_INTERVAL);
            window.addEventListener('online', updateConnectionStatus);
            window.addEventListener('offline', updateConnectionStatus);
            updateConnectionStatus();

            // Countdown timer
            countdownTimer = setInterval(() => {
                const remaining = Math.max(0, deadline - Date.now());
                const minutes = Math.floor(remaining / 60000);
                const seconds = Math.floor((remaining % 60000) / 1000);

                elements.countdown.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

                if (remaining === 0) {
                    clearInterval(countdownTimer);
                    timeExpired();
                }
            }, 1000);

            // Show first question
            showQuestion(1);
            updateSaveStatus();
        }

        // Start initialization
        initialize();
    </script>
    <!-- Submit Confirmation Modal (Safe Exam Browser & Android Exambro Compatible) -->
    <template x-if="showConfirmModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 p-4 backdrop-blur-sm"
             x-transition.opacity>
            <div class="w-full max-w-md rounded-2xl bg-white p-5 sm:p-6 shadow-2xl max-h-[90vh] overflow-y-auto custom-scrollbar" @click.stop>
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-amber-100 p-2.5 text-amber-600 shrink-0">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">Kumpulkan Ujian?</h3>
                        <p class="text-xs text-slate-500">Konfirmasi penyelesaian ujian</p>
                    </div>
                </div>

                <div class="mt-4 space-y-3 text-xs sm:text-sm text-slate-600">
                    <template x-if="unansweredCount > 0">
                        <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 sm:p-3.5 text-rose-800">
                            <div class="flex items-center gap-2 font-bold text-xs sm:text-sm">
                                <svg class="h-4 w-4 shrink-0 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span>Perhatian: Masih ada soal kosong!</span>
                            </div>
                            <p class="mt-1 text-xs leading-relaxed text-rose-700">
                                Ada <strong class="font-extrabold text-rose-900" x-text="unansweredCount"></strong> soal yang belum Anda jawab. Anda tetap bisa mengumpulkan atau memeriksa kembali.
                            </p>
                        </div>
                    </template>

                    <template x-if="unansweredCount === 0">
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 sm:p-3.5 text-emerald-800">
                            <div class="flex items-center gap-2 font-bold text-xs sm:text-sm">
                                <svg class="h-4 w-4 shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Semua soal sudah dijawab</span>
                            </div>
                            <p class="mt-1 text-xs leading-relaxed text-emerald-700">
                                Luar biasa! Seluruh soal telah Anda jawab dengan lengkap.
                            </p>
                        </div>
                    </template>

                    <p class="text-xs leading-relaxed text-slate-500">
                        Jawaban yang sudah dikumpulkan tidak dapat diubah kembali. Pastikan Anda telah memeriksa seluruh jawaban Anda sebelum menyelesaikan ujian.
                    </p>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5 sm:gap-3">
                    <button type="button" 
                            class="flex-1 sm:flex-none rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 active:scale-95 transition"
                            @click="showConfirmModal = false">
                        Periksa Lagi
                    </button>
                    <button type="button" 
                            id="btn-confirm-submit"
                            class="flex-1 sm:flex-none rounded-xl bg-emerald-600 px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-emerald-700 active:scale-95 transition"
                            onclick="executeFinalSubmit()">
                        Ya, Kumpulkan
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Error Alert Modal (Fallback jika window.alert diblokir Exambro) -->
    <template x-if="submitErrorMessage">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 p-4 backdrop-blur-sm"
             x-transition.opacity>
            <div class="w-full max-w-md rounded-2xl bg-white p-5 sm:p-6 shadow-2xl max-h-[90vh] overflow-y-auto custom-scrollbar" @click.stop>
                <div class="flex items-center gap-3 text-rose-600">
                    <div class="rounded-full bg-rose-100 p-2.5 shrink-0">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">Gagal Mengumpulkan</h3>
                        <p class="text-xs text-slate-500">Terjadi kendala saat menyimpan jawaban</p>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-xs sm:text-sm leading-relaxed text-slate-600" x-text="submitErrorMessage"></p>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" 
                            class="rounded-xl bg-slate-800 px-5 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-slate-900 active:scale-95 transition"
                            @click="submitErrorMessage = null">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Image Zoom Modal -->
    <template x-if="zoomImage">
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/95 p-3 sm:p-4 backdrop-blur-sm transition-all" @click="zoomImage = null" x-transition.opacity>
            <div class="relative max-h-full max-w-full flex flex-col items-center">
                <button class="mb-2 self-end rounded-full bg-white/20 p-2 text-white hover:bg-white/40 active:scale-95" @click="zoomImage = null">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <img :src="zoomImage" class="max-h-[85vh] max-w-full rounded-xl object-contain shadow-2xl" @click.stop>
            </div>
        </div>
    </template>
</body>
</html>



