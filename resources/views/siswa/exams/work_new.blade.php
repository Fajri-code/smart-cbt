<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->nama }} - SMART CBT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
    <div x-data="{ navOpen: false }" @keydown.escape.window="navOpen = false">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white shadow-sm">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
                <!-- Hamburger (Mobile Only) -->
                <button type="button" 
                        @click="navOpen = true"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-50 p-2.5 text-blue-700 hover:bg-blue-100 lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h8m-8 6h16"/></svg>
                </button>

                <div class="min-w-0 flex-1 lg:flex-none">
                    <p class="truncate text-[10px] font-bold uppercase tracking-wide text-blue-600 sm:text-xs">
                        {{ $exam->mataPelajaran?->nama }}
                    </p>
                    <h1 class="truncate text-base font-black text-slate-900 sm:text-lg">
                        {{ $exam->nama }}
                    </h1>
                </div>

                <div class="shrink-0 flex items-center gap-3">
                    <!-- Save Status Indicator -->
                    <div id="save-status" class="hidden items-center gap-2 rounded-lg px-2 py-1 text-xs font-semibold sm:flex">
                        <span id="save-status-text">--</span>
                        <span id="save-status-icon" class="h-4 w-4"></span>
                    </div>
                    <!-- Timer -->
                    <div class="rounded-xl bg-blue-50 px-3 py-1.5 text-center sm:px-4 sm:py-2">
                        <span class="block text-[9px] font-bold uppercase tracking-wider text-blue-600 sm:text-[10px]">Sisa waktu</span>
                        <strong id="countdown" class="text-lg font-black tabular-nums text-blue-800 sm:text-xl">--:--</strong>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:flex lg:gap-6">
            <div class="min-w-0 flex-1">
                <form id="exam-form" method="POST" action="{{ route('siswa.ujian.submit', $exam) }}" class="space-y-5">
                    @csrf
                    
                    <!-- Hidden input to track pending state -->
                    <input type="hidden" id="pending-indicator" value="0">
                    
                    @foreach ($exam->questions as $question)
                        <section id="question-{{ $loop->iteration }}" 
                                 class="question-panel rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7" 
                                 data-question="{{ $loop->iteration }}" 
                                 data-question-id="{{ $question->id }}"
                                 style="display: {{ $loop->first ? 'block' : 'none' }}">
                            
                            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                <span class="text-sm font-bold text-slate-500">
                                    Soal {{ $loop->iteration }} <span class="hidden sm:inline">dari {{ $exam->questions->count() }}</span>
                                </span>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                    {{ strtoupper($question->tipe) }}
                                </span>
                            </div>

                            <div class="prose prose-slate mt-5 max-w-none prose-img:max-w-full prose-img:h-auto prose-img:rounded-xl prose-table:block prose-table:overflow-x-auto prose-table:w-full prose-td:border prose-td:border-slate-200 prose-td:p-2">
                                <p class="whitespace-pre-line text-[15px] font-semibold leading-relaxed text-slate-900 sm:text-lg">
                                    {{ $question->pertanyaan }}
                                </p>
                            </div>

                            @if ($question->tipe === 'pg')
                                <div class="mt-7 space-y-3">
                                    @foreach (['a','b','c','d','e'] as $option)
                                        @if ($question->{'opsi_'.$option})
                                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3.5 transition-colors hover:border-blue-400 hover:bg-blue-50/40 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 sm:p-4">
                                                <input type="radio" 
                                                       name="answers[{{ $question->id }}]" 
                                                       value="{{ strtoupper($option) }}" 
                                                       class="mt-0.5 h-5 w-5 border-slate-300 text-blue-600 answer-input focus:ring-blue-500"
                                                       data-question-id="{{ $question->id }}"
                                                       @checked(($answers[$question->id] ?? '') === strtoupper($option))>
                                                <span class="text-[15px] sm:text-base">
                                                    <strong class="mr-2 text-blue-700">{{ strtoupper($option) }}.</strong>
                                                    {{ $question->{'opsi_'.$option} }}
                                                </span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <label class="mt-6 block">
                                    <span class="mb-2 block text-sm font-semibold text-slate-700">Jawaban Anda</span>
                                    <textarea name="answers[{{ $question->id }}]" 
                                              rows="5" 
                                              class="w-full rounded-xl border-slate-300 answer-input text-[15px] focus:border-blue-500 focus:ring-blue-500 sm:text-base"
                                              data-question-id="{{ $question->id }}"
                                              placeholder="Tulis jawaban Anda..."></textarea>
                                </label>
                            @endif
                        </section>
                    @endforeach

                    <div class="grid grid-cols-2 gap-3 sm:flex sm:items-center sm:justify-between">
                        <button type="button" id="previous" 
                                class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40 sm:w-auto">
                            Sebelumnya
                        </button>
                        <button type="button" id="next" 
                                class="flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 sm:w-auto">
                            Berikutnya
                        </button>
                        <button type="button" id="finish" 
                                class="hidden w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 sm:w-auto">
                            Kumpulkan Ujian
                        </button>
                    </div>
                </form>
            </div>

            <!-- Mobile Overlay -->
            <div x-show="navOpen" 
                 x-transition.opacity 
                 class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
                 @click="navOpen = false"
                 style="display: none;"></div>

            <!-- Navigation Sidebar / Drawer -->
            <aside class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 lg:static lg:w-72 lg:translate-x-0 lg:shrink-0 lg:rounded-2xl lg:border lg:border-slate-200 lg:bg-white lg:shadow-sm"
                   :class="{ 'translate-x-0': navOpen }">
                
                <div class="flex items-center justify-between border-b border-slate-100 p-5 lg:hidden">
                    <h2 class="font-bold text-slate-900">Navigasi Soal</h2>
                    <button type="button" @click="navOpen = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="hidden border-b border-slate-100 p-5 lg:block">
                    <h2 class="font-bold text-slate-900">Navigasi Soal</h2>
                </div>
                
                <div class="flex-1 overflow-y-auto p-5">
                    <div class="grid grid-cols-5 gap-2 sm:grid-cols-5">
                        @foreach ($exam->questions as $question)
                            <button type="button" 
                                    class="question-number h-10 w-full rounded-lg border border-slate-200 text-sm font-bold text-slate-600 transition-colors hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1" 
                                    data-target="{{ $loop->iteration }}"
                                    data-question-id="{{ $question->id }}">
                                {{ $loop->iteration }}
                            </button>
                        @endforeach
                    </div>
                    
                    <p class="mt-6 rounded-xl bg-slate-50 p-3 text-xs leading-relaxed text-slate-500">
                        Jawaban tersimpan otomatis. 
                        <span id="pending-count" class="hidden mt-1 block font-semibold text-amber-600">
                            Ada <span id="pending-count-num">0</span> jawaban belum tersimpan.
                        </span>
                    </p>
                </div>
            </aside>
        </main>
    </div>

    <script>
        /**
         * ============================================================================
         * BATCH ANSWER SAVING SYSTEM
         * ============================================================================
         * 
         * FITUR:
         * - Batch save setiap 5 jawaban yang berubah
         * - Pending queue di browser
         * - Retry mechanism untuk failed requests
         * - Visual indicator status
         * - Prevent submit sebelum pending selesai
         * - Handle page leave & time up dengan pending answers
         * - Prevent duplikasi dengan update-or-create backend
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

        // State tracking
        const state = {
            pendingAnswers: new Map(),        // Map<questionId, jawaban> - jawaban yang belum tersimpan
            changedQuestions: new Set(),      // Set<questionId> - soal yang berubah
            savedAnswers: new Map(),          // Map<questionId, jawaban> - jawaban yang sudah tersimpan
            isSaving: false,                  // Prevent concurrent saves
            savingError: null,                // Error dari save terakhir
            retryCount: 0,                    // Jumlah retry attempt
            pageUnloading: false,             // Flag untuk page unload
            isSubmitting: false,              // Flag untuk submit
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
        };

        let current = 1;
        let saveTimer = null;
        let periodicSaveTimer = null;
        let deadline = null;
        let countdownTimer = null;

        // ============================================================================
        // UTILITY FUNCTIONS
        // ============================================================================

        /**
         * Update visual indicator untuk status penyimpanan
         */
        function updateSaveStatus() {
            const pendingCount = state.pendingAnswers.size;
            
            if (state.isSaving) {
                elements.saveStatus.className = 'flex items-center gap-2 rounded-lg px-3 py-1 text-xs font-semibold bg-amber-50 text-amber-700';
                elements.saveStatusText.textContent = 'Menyimpan...';
                elements.saveStatusIcon.innerHTML = '<svg class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            } else if (state.savingError) {
                elements.saveStatus.className = 'flex items-center gap-2 rounded-lg px-3 py-1 text-xs font-semibold bg-red-50 text-red-700';
                elements.saveStatusText.textContent = `Gagal (${state.retryCount}/${CONFIG.MAX_RETRIES})`;
                elements.saveStatusIcon.innerHTML = '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            } else if (pendingCount > 0) {
                elements.saveStatus.className = 'flex items-center gap-2 rounded-lg px-3 py-1 text-xs font-semibold bg-blue-50 text-blue-700';
                elements.saveStatusText.textContent = `${pendingCount} jawaban pending`;
                elements.saveStatusIcon.innerHTML = '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            } else {
                elements.saveStatus.className = 'flex items-center gap-2 rounded-lg px-3 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700';
                elements.saveStatusText.textContent = 'Tersimpan';
                elements.saveStatusIcon.innerHTML = '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            }

            // Update pending count di sidebar
            if (pendingCount > 0) {
                elements.pendingCountEl.classList.remove('hidden');
                elements.pendingCountNum.textContent = pendingCount;
            } else {
                elements.pendingCountEl.classList.add('hidden');
            }

            // Update hidden indicator
            elements.pendingIndicator.value = pendingCount;
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
         * Track perubahan jawaban dan update pending queue
         */
        function trackAnswerChange(questionId, newAnswer) {
            const questionIdStr = String(questionId);
            const savedAnswer = state.savedAnswers.get(questionIdStr);
            
            // Jika jawaban sama dengan yang sudah tersimpan, hapus dari pending
            if (savedAnswer === newAnswer) {
                state.pendingAnswers.delete(questionIdStr);
                state.changedQuestions.delete(questionIdStr);
            } else {
                // Jika berbeda, masukkan ke pending
                state.pendingAnswers.set(questionIdStr, newAnswer);
                state.changedQuestions.add(questionIdStr);
            }
            
            updateSaveStatus();
        }

        // ============================================================================
        // SAVE MECHANISM
        // ============================================================================

        /**
         * Save batch answers ke backend
         * Retry otomatis jika gagal
         */
        async function saveBatchAnswers(answers, attempt = 0) {
            if (state.isSaving) {
                return Promise.resolve(); // Prevent concurrent saves
            }

            if (Object.keys(answers).length === 0) {
                return Promise.resolve(); // No answers to save
            }

            state.isSaving = true;
            updateSaveStatus();

            try {
                const response = await fetch('{{ route('siswa.ujian.answers', $exam) }}', {
                    method: 'POST',
                    keepalive: true,
                    timeout: CONFIG.SAVE_TIMEOUT,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ answers }),
                });

                if (!response.ok) {
                    throw new Error(`Server error: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.saved) {
                    // Mark answers sebagai tersimpan
                    Object.entries(answers).forEach(([qId, answer]) => {
                        state.savedAnswers.set(String(qId), answer);
                        state.pendingAnswers.delete(String(qId));
                        state.changedQuestions.delete(String(qId));
                    });

                    state.savingError = null;
                    state.retryCount = 0;
                } else {
                    throw new Error('Save response invalid');
                }
            } catch (error) {
                console.error('Save error:', error);
                state.savingError = error.message;

                // Retry dengan exponential backoff
                if (attempt < CONFIG.MAX_RETRIES) {
                    state.retryCount = attempt + 1;
                    await new Promise(resolve => setTimeout(resolve, CONFIG.RETRY_DELAY * (attempt + 1)));
                    return saveBatchAnswers(answers, attempt + 1);
                } else {
                    // Max retries reached, keep answers in pending untuk manual retry
                    console.error('Max retries reached, answers kept in pending');
                }
            } finally {
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
                    batchAnswers[qId] = state.pendingAnswers.get(qId);
                });

                await saveBatchAnswers(batchAnswers);
            }
        }

        /**
         * Save semua pending answers (digunakan saat submit & time up)
         */
        async function saveAllPendingAnswers() {
            if (state.pendingAnswers.size === 0) {
                return Promise.resolve();
            }

            const allAnswers = {};
            state.pendingAnswers.forEach((answer, qId) => {
                allAnswers[qId] = answer;
            });

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
            current = number;
            elements.panels.forEach((panel, index) => {
                panel.style.display = index + 1 === current ? 'block' : 'none';
            });
            elements.numbers.forEach((button, index) => {
                button.classList.toggle('border-blue-600', index + 1 === current);
            });
            
            elements.previous.disabled = current === 1;
            elements.next.classList.toggle('hidden', current === elements.panels.length);
            elements.finish.classList.toggle('hidden', current !== elements.panels.length);
        }

        // ============================================================================
        // SUBMIT & TIME UP HANDLING
        // ============================================================================

        /**
         * Submit exam dengan memastikan semua pending answers tersimpan dulu
         */
        async function submitExam() {
            if (!confirm('Kumpulkan ujian sekarang? Jawaban yang sudah dikirim tidak dapat diubah.')) {
                return;
            }

            state.isSubmitting = true;

            // Disable buttons
            elements.previous.disabled = true;
            elements.next.disabled = true;
            elements.finish.disabled = true;
            elements.finish.textContent = 'Menyimpan jawaban...';

            try {
                // Save semua pending answers dulu
                await saveAllPendingAnswers();

                // Wait a bit untuk memastikan state update
                await new Promise(resolve => setTimeout(resolve, 500));

                // Jika masih ada pending (setelah retries), tetap submit
                // Backend akan menggunakan jawaban yang sudah tersimpan
                if (state.pendingAnswers.size > 0) {
                    console.warn('Ada jawaban yang masih pending, tetap submit');
                }

                // Submit form
                elements.form.submit();
            } catch (error) {
                console.error('Submit error:', error);
                elements.finish.disabled = false;
                elements.finish.textContent = 'Kumpulkan Ujian';
                alert('Gagal menyimpan jawaban. Silakan coba lagi.');
                state.isSubmitting = false;
            }
        }

        /**
         * Auto submit ketika waktu habis
         */
        async function timeExpired() {
            console.log('Waktu ujian habis');

            state.pageUnloading = true;

            // Disable semua interaksi
            elements.previous.disabled = true;
            elements.next.disabled = true;
            elements.finish.disabled = true;
            elements.answerInputs.forEach(input => input.disabled = true);

            try {
                // Save semua pending answers
                await saveAllPendingAnswers();
                
                // Wait a bit
                await new Promise(resolve => setTimeout(resolve, 500));
            } catch (error) {
                console.error('Error saving pending on time up:', error);
            }

            // Auto submit form
            elements.form.submit();
        }

        // ============================================================================
        // PAGE UNLOAD HANDLING
        // ============================================================================

        /**
         * Jika ada pending answers saat page unload, coba save
         */
        window.addEventListener('pagehide', async () => {
            if (state.isSubmitting || state.pageUnloading) {
                return; // Sudah dalam proses submit
            }

            if (state.pendingAnswers.size > 0) {
                state.pageUnloading = true;
                
                // Use sendBeacon untuk reliability pada page unload
                const answers = {};
                state.pendingAnswers.forEach((answer, qId) => {
                    answers[qId] = answer;
                });

                const formData = new FormData();
                formData.append('answers', JSON.stringify(answers));
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                // Try fetch dengan keepalive (lebih reliable dari sendBeacon)
                fetch('{{ route('siswa.ujian.answers', $exam) }}', {
                    method: 'POST',
                    keepalive: true,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ answers }),
                }).catch(err => console.error('Error saving on page unload:', err));
            }

            // Leave session
            fetch('{{ route('siswa.ujian.leave', $exam) }}', {
                method: 'POST',
                keepalive: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            }).catch(err => console.error('Error on leave:', err));
        });

        /**
         * Prevent unload dialog jika ada pending (optional)
         */
        window.addEventListener('beforeunload', (e) => {
            if (state.pendingAnswers.size > 0 && !state.isSubmitting && !state.pageUnloading) {
                e.preventDefault();
                e.returnValue = 'Ada jawaban yang belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?';
            }
        });

        // ============================================================================
        // INITIALIZE
        // ============================================================================

        function initialize() {
            // Load initial answers sebagai "saved"
            const initialAnswers = getCurrentAnswers();
            Object.entries(initialAnswers).forEach(([qId, answer]) => {
                state.savedAnswers.set(String(qId), answer);
            });

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
                    queueSave();
                });
            });

            // Periodic fallback save (untuk case radio button yang tidak trigger input)
            periodicSaveTimer = setInterval(checkAndSaveBatch, CONFIG.PERIODIC_INTERVAL);

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
</body>
</html>
