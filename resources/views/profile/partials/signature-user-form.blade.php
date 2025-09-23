<header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
        Your Signature
    </h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-100">
        Draw your signature below. This will be used for approvals.
    </p>
</header>

{{-- Menampilkan tanda tangan yang sudah ada --}}
@if (Auth::user()->signature)
    <div class="mt-4">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-100">Current Signature:</p>
        <div class="border rounded-md p-2 mt-1 inline-block">
            <img src="{{ Auth::user()->signature }}" alt="Your signature" class="h-16">
        </div>
    </div>
@endif

<div class="mt-6 space-y-2">
    <label for="signature" class="dark:text-white">New Signature:</label>
    <div class="border border-gray-400 rounded-md relative">
        <canvas id="signature-pad" class="w-full h-48 relative z-10" data-signature="{{ Auth::user()->signature ? json_encode(Auth::user()->signature) : 'null' }}"></canvas>
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

@push('scripts')
    @vite('resources/js/pages/profileSignature.js')
@endpush