@props(['restoreUrl', 'model'])

<div class="flex">
@can('is-admin')
    <form action="{{ $restoreUrl }}" method="POST">
        @csrf
        @method('PUT')
        <div class="text-gray-700 hover:text-white">
            <button type="submit"
                onclick="return confirm('Apakah Anda yakin ingin memulihkan data ini?')" 
                class="border border-gray-700 hover:bg-gray-800 font-medium rounded-lg text-xs px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-gray-900 dark:border-gray-500 dark:text-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                <svg class="w-3.5 h-3.5 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16h13M4 16l4-4m-4 4 4 4M20 8H7m13 0-4 4m4-4-4-4"/>
                </svg>
                Restore
            </button>
        </div>
    </form>
@endcan
</div>