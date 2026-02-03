@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('company.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        company
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <x-alerts />

    <div class="p-5">
        <div class="border-b bg-white rounded-t-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab"
                data-tabs-toggle="#default-tab-content" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg" id="general-tab"
                        data-tabs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="false">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 6H6m12 4H6m12 4H6m12 4H6" />
                        </svg>
                        General
                    </button>
                </li>
                <li class="me-2" role="presentation">
                    <button
                        class="inline-flex items-center gap-2 p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                        id="restricted-tab" data-tabs-target="#restricted" type="button" role="tab"
                        aria-controls="restricted" aria-selected="false">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Restricted access
                    </button>
                </li>
            </ul>
        </div>

        <div id="default-tab-content">
            <div class="hidden" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="relative overflow-x-auto shadow-md py-5 px-6 rounded-b-lg bg-white dark:bg-gray-800">
                    <form class="max-w mx-auto" action="{{ route('company.update', $company->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name <span
                                    class="text-red-900 dark:text-red-400">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $company->name) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="HRGA" required />
                            @error('name')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company
                                Alias</label>
                            <input type="text" name="alias" value="{{ old('alias', $company->alias) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="" />
                            @error('alias')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Code</label>
                            <input type="text" name="code" value="{{ old('code', $company->code) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="" />
                            @error('code')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Currency
                                <span class="text-red-900 dark:text-red-400">*</span></label>
                            <select name="currency"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option selected value="">Choose a Currency</option>
                                <option value="USD" {{ $company->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="IDR" {{ $company->currency == 'IDR' ? 'selected' : '' }}>IDR</option>
                            </select>
                            @error('currency')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($company->logo)
                            <div class="mb-5">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Current Logo</label>
                                <img src="{{ Storage::url($company->logo) }}" alt="Company Logo" class="h-20 w-auto rounded">
                            </div>
                        @endif

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                for="logo_file">Upload New Logo</label>
                            <input name="logo"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg"
                                id="logo_file" type="file">
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                            <textarea name="address" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ old('address', $company->address ?? '') }}</textarea>
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fax</label>
                            <input type="text" name="fax" value="{{ old('fax', $company->fax ?? '') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>

                        <div class="flex flex-col gap-2 sm:flex-row">
                            <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Update</button>
                            <a href="{{ route('company.index') }}"
                                class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="hidden" id="restricted" role="tabpanel" aria-labelledby="restricted-tab">
                <div class="relative overflow-x-auto py-5 px-6 rounded-b-lg bg-white dark:bg-gray-800">
                    @canany(['is-dev', 'is-owner'])
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-row gap-2 justify-between items-center">
                                <button data-modal-target="transfer-modal" data-modal-toggle="transfer-modal"
                                    class="text-yellow-700 hover:text-white border border-yellow-700 hover:bg-yellow-800 font-medium rounded-lg text-xs px-5 py-2.5 text-center inline-flex items-center me-2 dark:border-yellow-500 dark:text-yellow-500 dark:hover:text-white dark:hover:bg-yellow-600">                                    
                                    <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16h13M4 16l4-4m-4 4 4 4M20 8H7m13 0-4 4m4-4-4-4"/>
                                    </svg>
                                    Transfer Ownership
                                </button>

                                <p class="text-xs text-gray-500 dark:text-gray-400"><span class="text-red-400">* </span>Transfer
                                    ownership to another user</p>
                            </div>

                            <div class="flex flex-row gap-2 justify-between items-center">
                                <button data-modal-target="delete-modal" data-modal-toggle="delete-modal"
                                    class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 font-medium rounded-lg text-xs px-5 py-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600">
                                    <svg class="w-3.5 h-3.5 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>
                                    Delete Company
                                </button>                                

                                <p class="text-xs text-gray-500 dark:text-gray-400"><span class="text-red-400">* </span>Delete
                                    company</p>
                            </div>
                        </div>
                    @endcanany
                </div>
            </div>

            <!-- Transfer Ownership Modal -->
            <div id="transfer-modal" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-lg max-h-full">
                    <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                        <div
                            class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Transfer Ownership
                            </h3>
                            <button type="button"
                                class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="transfer-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 md:p-5">
                            <form action="{{ route('company.transfer', $company->id) }}" method="POST" class="p-4 md:p-5">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label for="new_owner_id" class="block mb-2 text-sm font-medium text-gray-900">Select
                                        New Owner</label>
                                    <select id="new_owner_id" name="new_owner_id"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
                                        required>
                                        <option value="">Choose a member...</option>
                                        @foreach($companyUsers as $member)
                                            @if($member->id !== auth()->id())
                                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                                    <span class="font-medium">Warning!</span> You will lose the Owner privileges
                                    immediately.
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Confirm
                                        Password</label>
                                    <input type="password" name="password" id="password"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
                                        required placeholder="Type your password">
                                </div>

                                <button type="submit"
                                    class="w-full text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    I understand, transfer ownership
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div id="delete-modal" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-lg max-h-full">
                    <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                        <div
                            class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Delete Company
                            </h3>
                            <button type="button"
                                class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="delete-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 md:p-5">
                            <form action="{{ route('company.destroy', $company->id) }}" method="POST" class="p-4 md:p-5">
                                @csrf
                                @method('DELETE')

                                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                                    <span class="font-medium">Warning!</span> You will lose the company.
                                </div>

                                <div class="mb-4">
                                    <label for="delete_company" class="block mb-2 text-sm font-medium text-gray-900">Confirm
                                        Delete</label>
                                    <input type="text" name="delete_company" id="delete_company"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5"
                                        required placeholder="Type your company name">
                                </div>

                                <button type="submit"
                                    class="w-full text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    I understand, delete company
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection