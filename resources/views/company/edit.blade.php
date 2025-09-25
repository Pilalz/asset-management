@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('company.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        company
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg bg-white dark:bg-gray-900">
            <form class="max-w mx-auto" action="{{ route('company.update', $company->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name <span class="text-red-900">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="HRGA" required />
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Alias</label>
                    <input type="text" name="alias" value="{{ old('alias', $company->alias) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="" />
                    @error('alias')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Company Code</label>
                    <input type="text" name="code" value="{{ old('code', $company->code) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="" />
                    @error('code')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                @if($company->logo)
                    <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium">Current Logo</label>
                        <img src="{{ Storage::url($company->logo) }}" alt="Company Logo" class="h-20 w-auto rounded">
                    </div>
                @endif

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium" for="logo_file">Upload New Logo</label>
                    <input name="logo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg" id="logo_file" type="file">
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium">Address</label>
                    <textarea name="address" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ old('address', $company->address ?? '') }}</textarea>
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium">Fax</label>
                    <input type="text" name="fax" value="{{ old('fax', $company->fax ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>

                @if ($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                        <span class="font-medium">Validasi Gagal!</span> Mohon periksa error di bawah ini:
                        <ul class="mt-1.5 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Update</button>
                <a href="{{ route('company.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Cancel</a>
            </form>
        </div>

    @can('is-dev')
        <div class="relative overflow-x-auto shadow-md mt-4 py-5 px-6 sm:rounded-lg bg-white dark:bg-gray-900">
            <form action="{{ route('company.destroy', $company->id) }}" method="POST" class="group">
                @csrf
                @method('DELETE')
                <button type="submit"
                    onclick="return confirm('Terdapat {{$countAsset}} Asset di company ini, Apakah Anda yakin ingin menghapus data ini?')" 
                    class="text-red-700 group-hover:text-white border border-red-700 group-hover:bg-red-800 font-medium rounded-lg text-xs px-5 py-2.5 text-center inline-flex items-center me-2 dark:border-red-500 dark:text-red-500 dark:group-hover:text-white dark:group-hover:bg-red-600">
                    <svg class="w-3.5 h-3.5 me-2 text-red-700 dark:text-white group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    @endcan
    </div>
@endsection