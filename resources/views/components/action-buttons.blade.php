@can('is-admin')
    <div class="flex">
        <a href="{{ $editUrl }}" type="button" class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-xs px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700">
            <svg class="w-3.5 h-3.5 me-2 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 21">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
            </svg>
            Edit
        </a>

        <form action="{{ $deleteUrl }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="text-red-700 hover:text-white">
                <button type="submit"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" 
                    class="border border-red-700 hover:bg-red-800 font-medium rounded-lg text-xs px-5 py-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:bg-red-600 dark:hover:text-white">
                    <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                    </svg>
                    Delete
                </button>
            </div>
        </form>
    </div>
@endcan