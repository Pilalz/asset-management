@extends('layouts.main')

@section('content')
    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <svg class="w-3 h-3 me-2.5 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Dashboard</span>
                </li>
            </ol>
        </nav>
    </div>
    
    <div class="p-5 w-full">
        <div class="w-full relative overflow-x-auto shadow-md sm:rounded-lg bg-white p-4 dark:bg-gray-800 dark:text-white">
            <h1 class="text-lg font-bold">Hello {{ Auth::user()->name }}</h1>
            <p class="text-md">Welcome To {{ Auth::user()->lastActiveCompany->name }}</p>
            <div class="flex gap-2 justify-around mt-2">
                <div class="p-4 bg-gray-200 w-full rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-900">testes</div>
                <div class="p-4 bg-gray-200 w-full rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-900">testes</div>
                <div class="p-4 bg-gray-200 w-full rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-900">testes</div>
                <div class="p-4 bg-gray-200 w-full rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-900">testes</div>
            </div>
            </div>
            </div>
        </div>
    </div>
    
@endsection