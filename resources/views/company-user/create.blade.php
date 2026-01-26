@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('company-user.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Company Users
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Create</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <x-alerts />

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md py-5 px-6 rounded-lg bg-white dark:bg-gray-800">
            <form class="max-w mx-auto" action="{{ route('company-user.store') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="select-user" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Search
                        User by Email or Name <span class="text-red-900 dark:text-red-400">*</span></label>
                    <select id="select-user" placeholder="Ketik minimal 3 huruf..."
                        data-search-url="{{ route('api.users.search') }}">
                    </select>
                    <input type="hidden" name="user_id" id="user_id">
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role <span
                            class="text-red-900 dark:text-red-400">*</span></label>
                    <select name="role"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option selected value="">Choose a Class</option>
                        <option value="User">User</option>
                        <option value="User Manager">User Manager</option>
                        <option value="Site Director">Site Director</option>
                        <option value="Asset Management">Asset Management</option>
                        <option value="CFO">CFO</option>
                        <option value="Director">Director</option>
                    </select>
                    @error('role')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="company_id" value="{{ Auth::user()->last_active_company_id }}" required />

                @if ($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400"
                        role="alert">
                        <span class="font-medium">Validasi Gagal!</span> Mohon periksa error di bawah ini:
                        <ul class="mt-1.5 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Create</button>
                    <a href="{{ route('company-user.index') }}"
                        class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/userSearch.js')
@endpush