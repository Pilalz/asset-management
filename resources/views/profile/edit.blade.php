<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">

                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            Your Signature
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Draw your signature below. This will be used for approvals.
                        </p>
                    </header>

                    {{-- Menampilkan tanda tangan yang sudah ada --}}
                    @if (Auth::user()->signature)
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-700">Current Signature:</p>
                            <div class="border rounded-md p-2 mt-1 inline-block">
                                <img src="{{ Auth::user()->signature }}" alt="Your signature" class="h-16">
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 space-y-2">
                        <label for="signature">New Signature:</label>
                        <div class="border border-gray-400 rounded-md relative">
                            <canvas id="signature-pad" class="w-full h-48 relative z-10"></canvas>
                        </div>
                        <button type="button" id="clear-signature" class="text-sm text-blue-600 hover:underline">Clear</button>
                    </div>

                    <form id="signature-form" action="{{ route('profile.updateSignature') }}" method="POST" class="mt-6">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="signature" id="signature-data">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Save Signature</button>

                        @if (session('status') === 'signature-saved')
                            <p class="text-sm text-green-600 mt-2">Saved.</p>
                        @endif
                    </form>

                </div>
            </div>

            @if( Auth::user()->google_id === null )
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });

        // Ambil data tanda tangan yang sudah ada dari server (jika ada)
        const existingSignature = @json(Auth::user()->signature);

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);

            // JANGAN clear jika ada data, tapi GAMBAR ULANG
            if (existingSignature) {
                signaturePad.fromDataURL(existingSignature);
            } else {
                signaturePad.clear();
            }
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        document.getElementById('clear-signature').addEventListener('click', () => {
            signaturePad.clear();
        });

        document.getElementById('signature-form').addEventListener('submit', (event) => {
            if (signaturePad.isEmpty()) {
                alert("Please provide a signature first.");
                event.preventDefault();
            } else {
                document.getElementById('signature-data').value = signaturePad.toDataURL('image/svg+xml');
            }
        });
    });
</script>
@endpush