@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('company.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Company
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Settings</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <x-alerts />

    <div class="p-5 max-w-5xl mx-auto">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $company->name }} Overview</h2>
        </div>

        <div class="border-b bg-white rounded-t-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 shadow-sm">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab"
                data-tabs-toggle="#default-tab-content" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-flex items-center gap-2 p-4 border-b-2 rounded-t-xl transition-all duration-200 text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500" id="general-tab"
                        data-tabs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 6H6m12 4H6m12 4H6m12 4H6" />
                        </svg>
                        General Settings
                    </button>
                </li>
                <li class="me-2" role="presentation">
                    <button
                        class="inline-flex items-center gap-2 p-4 border-b-2 border-transparent rounded-t-xl hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition-all duration-200"
                        id="restricted-tab" data-tabs-target="#restricted" type="button" role="tab"
                        aria-controls="restricted" aria-selected="false">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Danger Zone
                    </button>
                </li>
            </ul>
        </div>

        <div id="default-tab-content" class="shadow-sm rounded-b-xl border-gray-200 dark:border-gray-700">
            <div class="" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="py-6 px-8 bg-white dark:bg-gray-800 rounded-b-xl">
                    <form action="{{ route('company.update', $company->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name <span class="text-red-600 dark:text-red-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $company->name) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors"
                                    placeholder="e.g. Acme Corp" required />
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Alias</label>
                                <input type="text" name="alias" value="{{ old('alias', $company->alias) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors"
                                    placeholder="e.g. ACME" />
                                @error('alias')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Code</label>
                                <input type="text" name="code" value="{{ old('code', $company->code) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors"
                                    placeholder="e.g. C-001" />
                                @error('code')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Currency <span class="text-red-600 dark:text-red-400">*</span></label>
                                <select name="currency"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                                    <option value="" disabled {{ !$company->currency ? 'selected' : '' }}>Choose a Currency</option>
                                    <option value="USD" {{ $company->currency == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                    <option value="IDR" {{ $company->currency == 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                                </select>
                                @error('currency')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6 flex flex-col sm:flex-row items-start gap-6 border-t border-b border-gray-100 dark:border-gray-700 py-6">
                            @if($company->logo)
                                <div class="flex-shrink-0">
                                    <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">Current Logo</label>
                                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl dark:bg-gray-900 dark:border-gray-600 flex items-center justify-center">
                                        <img src="{{ Storage::url($company->logo) }}" alt="Company Logo" class="h-20 w-auto max-w-[150px] object-contain rounded">
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex-grow w-full">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="logo_file">Upload New Logo</label>
                                <input name="logo" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-200 transition-all" id="logo_file" type="file" accept="image/*">
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" id="file_input_help">SVG, PNG, JPG or GIF (MAX. 2MB). A square or wide aspect ratio works best.</p>
                                @error('logo')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                            <textarea name="address" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors resize-y"
                                placeholder="Full street address">{{ old('address', $company->address ?? '') }}</textarea>
                        </div>

                        <div class="grid gap-6 mb-8 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors"
                                    placeholder="+1 (555) 000-0000">
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fax</label>
                                <input type="text" name="fax" value="{{ old('fax', $company->fax ?? '') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 transition-colors">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit"
                                class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                                </svg>
                                Save Changes
                            </button>
                            <a href="{{ route('company.index') }}"
                                class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="hidden" id="restricted" role="tabpanel" aria-labelledby="restricted-tab">
                <div class="py-6 px-8 bg-white dark:bg-gray-800 rounded-b-xl border border-red-100 dark:border-red-900/30">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Danger Zone</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Irreversible and destructive actions for this company.</p>
                    </div>

                    @canany(['is-dev', 'is-owner'])
                        <div class="flex flex-col gap-5 border-t border-gray-200 dark:border-gray-700 pt-5">
                            <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                                <div>
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Transfer Ownership</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Transfer ownership of this company to another selected user.</p>
                                </div>
                                <button data-modal-target="transfer-modal" data-modal-toggle="transfer-modal"
                                    class="text-yellow-700 hover:text-white border border-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:border-yellow-500 dark:text-yellow-500 dark:hover:text-white dark:hover:bg-yellow-600 dark:focus:ring-yellow-800 transition-colors whitespace-nowrap">                                    
                                    <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16h13M4 16l4-4m-4 4 4 4M20 8H7m13 0-4 4m4-4-4-4"/>
                                    </svg>
                                    Transfer Ownership
                                </button>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center pt-5 border-t border-gray-100 dark:border-gray-700">
                                <div>
                                    <h4 class="text-base font-semibold text-gray-900 dark:text-white">Delete Company</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Permanently remove this company and all its configuration data.</p>
                                </div>
                                <button data-modal-target="delete-modal" data-modal-toggle="delete-modal"
                                    class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900 transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>
                                    Delete Company
                                </button>                                
                            </div>
                        </div>
                    @endcanany
                </div>
            </div>

            <!-- Transfer Ownership Modal -->
            <div id="transfer-modal" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-gray-900/50">
                <div class="relative p-4 w-full max-w-lg max-h-full">
                    <div class="relative bg-white rounded-xl shadow-lg dark:bg-gray-800 border dark:border-gray-700">
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-700">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16h13M4 16l4-4m-4 4 4 4M20 8H7m13 0-4 4m4-4-4-4"/>
                                </svg>
                                Transfer Ownership
                            </h3>
                            <button type="button"
                                class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white transition-colors"
                                data-modal-hide="transfer-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <div class="p-4 md:p-5">
                            <form action="{{ route('company.transfer', $company->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select a team member to transfer your ownership privileges to. They will become the primary owner.</p>

                                <div class="mb-5">
                                    <label for="new_owner_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select New Owner</label>
                                    <select id="new_owner_id" name="new_owner_id"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-500 dark:focus:border-yellow-500"
                                        required>
                                        <option value="" disabled selected>Choose a member...</option>
                                        @foreach($companyUsers as $member)
                                            @if($member->id !== auth()->id())
                                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="p-4 mb-5 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800" role="alert">
                                    <div class="flex items-center mb-1">
                                        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
                                        </svg>
                                        <span class="font-medium">Warning!</span>
                                    </div>
                                    <div class="mt-1">You will lose the Owner privileges immediately after transfer.</div>
                                </div>

                                @if(auth()->user()->password)
                                <div class="mb-5">
                                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm Password</label>
                                    <input type="password" name="password" id="password"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-yellow-500 dark:focus:border-yellow-500"
                                        required placeholder="Type your password to confirm">
                                </div>
                                @else
                                <div class="mb-5">
                                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Confirm Action</label>
                                    <input type="hidden" name="password" id="password" value="oauth-bypass">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Because you logged in using an external provider (like Google), you can continue or an OTP will be sent to your email (if configured).</p>
                                </div>
                                <div class="p-4 mb-5 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800" role="alert">
                                    As security verification, a confirmation might be required via your email.
                                </div>
                                @endif

                                <button type="submit"
                                    class="w-full text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-yellow-500 dark:hover:bg-yellow-600 dark:focus:ring-yellow-800 transition-colors">
                                    @if(auth()->user()->password)
                                        I understand, transfer ownership
                                    @else
                                        Continue Transfer Process
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div id="delete-modal" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full backdrop-blur-sm bg-gray-900/50">
                <div class="relative p-4 w-full max-w-lg max-h-full">
                    <div class="relative bg-white rounded-xl shadow-lg dark:bg-gray-800 border dark:border-gray-700">
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-700">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                </svg>
                                Delete Company
                            </h3>
                            <button type="button"
                                class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white transition-colors"
                                data-modal-hide="delete-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <div class="p-4 md:p-5">
                            <form action="{{ route('company.destroy', $company->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">This action cannot be undone. This will permanently delete the <strong>{{ $company->name }}</strong> company, its assets (if any process allowed), and remove all user associations.</p>

                                <div class="p-4 mb-5 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                                    <div class="flex items-center mb-1">
                                        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
                                        </svg>
                                        <span class="font-medium">Danger!</span>
                                    </div>
                                    <div class="mt-1">All data will be lost. Ensure you have no active assets.</div>
                                </div>

                                <div class="mb-5">
                                    <label for="delete_company" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Please type <strong>{{ $company->name }}</strong> to confirm</label>
                                    <input type="text" name="delete_company" id="delete_company"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500"
                                        required placeholder="Company name">
                                </div>

                                <button type="submit"
                                    class="w-full text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 transition-colors">
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