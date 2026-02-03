@extends('layouts.main')

@section('content')

    @push('styles')
        <style>
        /* Styling khusus video agar fill container reader */
        #reader video {
            object-fit: cover !important;
            width: 100% !important;
            height: 100% !important;
        }
        /* Kotak scan visual */
        #reader__scan_region { background: rgba(0,0,0,0.2); }
    </style>
    @endpush

    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <p class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Scan
                    </p>
                </li>
            </ol>
        </nav>
    </div>

    <x-alerts />

    <div class="p-5">
        <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="p-4 border-b dark:border-gray-700 text-center">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Scanner QR Code</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Arahkan kamera ke QR Code atau upload file gambar</p>
            </div>

            <div class="p-6 bg-gray-50 dark:bg-gray-900/50">
                <div id="reader" class="overflow-hidden rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-black aspect-video flex items-center justify-center relative">
                    <div id="camera-placeholder" class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Kamera Nonaktif</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <button id="start-btn" class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-blue-500/30 transition-all active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Buka Kamera
                    </button>
                    <button id="stop-btn" class="hidden flex items-center justify-center gap-2 w-full px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold transition-all">
                        Hentikan Kamera
                    </button>
                </div>

                <label class="flex items-center justify-center gap-2 w-full px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl font-semibold cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Upload Gambar
                    <input type="file" id="qr-input-file" accept="image/*" class="hidden">
                </label>
            </div>
        </div>
    </div>

    <form id="scan-form" action="{{ route('scan.process') }}" method="GET">
        <input type="hidden" name="code" id="code-input">
    </form>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const startBtn = document.getElementById('start-btn');
        const stopBtn = document.getElementById('stop-btn');
        const placeholder = document.getElementById('camera-placeholder');
        const fileInput = document.getElementById('qr-input-file');

        function onScanSuccess(decodedText) {
            // Hentikan scan jika berhasil agar tidak loop
            if(html5QrCode.isScanning) {
                html5QrCode.stop();
            }
            
            // Berikan feedback visual (Opsional: Bunyi Beep bisa ditambah di sini)
            document.getElementById('reader').classList.add('border-green-500');
            
            // Masukkan ke form dan submit
            document.getElementById('code-input').value = decodedText;
            document.getElementById('scan-form').submit();
        }

        // --- LOGIKA KAMERA ---
        startBtn.addEventListener('click', () => {
            placeholder.classList.add('hidden');
            html5QrCode.start(
                { facingMode: "environment" }, 
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess
            ).then(() => {
                startBtn.classList.add('hidden');
                stopBtn.classList.remove('hidden');
            }).catch(err => {
                alert("Gagal akses kamera: " + err);
                placeholder.classList.remove('hidden');
            });
        });

        stopBtn.addEventListener('click', () => {
            html5QrCode.stop().then(() => {
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
                placeholder.classList.remove('hidden');
            });
        });

        // --- LOGIKA UPLOAD FILE ---
        fileInput.addEventListener('change', e => {
            if (e.target.files.length == 0) return;
            const imageFile = e.target.files[0];
            html5QrCode.scanFile(imageFile, true)
                .then(onScanSuccess)
                .catch(err => alert("QR tidak ditemukan di gambar ini: " + err));
        });
    </script>
@endpush