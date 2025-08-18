@extends('layouts.main')

@section('content')
    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Dashboard</span>
                </li>
            </ol>
        </nav>
    </div>
    
    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white p-4">
            <h1>Hii {{ Auth::user()->name }}</h1>
            <h1>Welcome to </h1>
            <div class="flex gap-2 justify-around">
                <div class="p-4 bg-gray-500 w-full rounded-lg">testes</div>
                <div class="p-4 bg-gray-500 w-full rounded-lg">testes</div>
                <div class="p-4 bg-gray-500 w-full rounded-lg">testes</div>
                <div class="p-4 bg-gray-500 w-full rounded-lg">testes</div>
            </div>
        </div>
    </div>
    
@endsection