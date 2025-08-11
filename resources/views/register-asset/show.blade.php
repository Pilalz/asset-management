@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('register-asset.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Action
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('register-asset.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Register Asset</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Create</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    @if (session('success'))
        <div id="alert-3" class="auto-dismiss-alert flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div id="alert-2" class="auto-dismiss-alert flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('error') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-2" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session('info'))
        <div id="alert-1" class="auto-dismiss-alert flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('info') }}
            </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-1" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg m-5 bg-white dark:bg-gray-900">
        <div class="mb-5 flex">
            <label class="flex w-40 text-sm font-medium text-gray-900 dark:text-white">Nomor Formulir <span class="text-red-900">*</span></label>
            <span> : </span>
            <p class="flex ml-1 text-sm text-gray-900">{{ $register_asset->form_no }}</p>
        </div>

        <div class="mb-5 flex content-center">
            <label class="w-40 text-sm font-medium text-gray-900 dark:text-white">Select Department <span class="text-red-900">*</span></label>
            <span> : </span>
            <p class="ml-1 text-sm text-gray-900">{{ $register_asset->department->name }}</p>
        </div>

        <div class="mb-5 flex content-center">
            <label class="w-40 text-sm font-medium text-gray-900 dark:text-white">Select Location <span class="text-red-900">*</span></label>
            <span> : </span>
            <p class="ml-1 text-sm text-gray-900">{{ $register_asset->location->name }}</p>
        </div>

        <!-- Asset List -->
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset List <span class="text-red-900">*</span></label>
            <div class="border-2 border-black rounded-lg p-4">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-2 py-3">No</th>
                            <th scope="col" class="px-2 py-3">PO No.</th>
                            <th scope="col" class="px-2 py-3">Invoice No.</th>
                            <th scope="col" class="px-2 py-3">Commission Date</th>
                            <th scope="col" class="px-2 py-3">Specification</th>
                            <th scope="col" class="px-2 py-3">Asset Class</th>
                            <th scope="col" class="px-2 py-3">Asset Sub Class</th>
                            <th scope="col" class="px-2 py-3">Asset Name</th>
                        </tr>
                    </thead>
                    <tbody id="asset-list-body">
                        @foreach($register_asset->detailRegisters as $detail)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 asset-row">
                                <td class="px-2 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white asset-row-number text-center">{{ $loop->iteration }}</td>
                                <td class="px-2 py-4">{{ $detail->po_no }}</td>
                                <td class="px-2 py-4">{{ $detail->invoice_no }}</td>
                                <td class="px-2 py-4">{{ $detail->commission_date }}</td>
                                <td class="px-2 py-4">{{ $detail->specification }}</td>
                                <td class="px-2 py-4">{{ $detail->assetName->assetSubClass->assetClass->name }}</td>
                                <td class="px-2 py-4">{{ $detail->assetName->assetSubClass->name }}</td>
                                <td class="px-2 py-4">{{ $detail->assetName->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-5">
            @php
                $insuredText = $register_asset->insured;
                $insuredClass = '';

                if ($insuredText == 0) {
                    $insuredClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                } elseif ($insuredText == 1) {
                    $insuredClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                } else { 
                    $insuredClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                }
            @endphp
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Insured <span class="text-red-900">*</span> :  <span class="{{ $insuredClass }} font-medium px-2 py-0.5 rounded">{{ ($register_asset->insured == 1) ? "Yes" : "No" }}</span></label>
        </div>

        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Approval List <span class="text-red-900">*</span></label>
            <div class="border-2 border-black rounded-lg p-4">
                
                <div class="flex flex-row mb-2">
                    @php
                        $sequenceText = $register_asset->sequence;
                        $sequenceClass = '';

                        if ($sequenceText == 0) {
                            $sequenceClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                        } elseif ($sequenceText == 1) {
                            $sequenceClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                        } else { 
                            $sequenceClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                        }
                    @endphp
                    <label class="w-auto mr-2 text-sm font-medium text-gray-900 dark:text-white">Sequence <span class="text-red-900">*</span> :  <span class="{{ $sequenceClass }} font-medium px-2 py-0.5 rounded">{{ ($register_asset->sequence == 1) ? "Yes" : "No" }}</span></label>
                </div>

                <hr class="mb-2">
                
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" colspan="2" class="text-center px-2 py-3">Persetujuan Approval</th>
                            <th scope="col" class="px-2 py-3">Name</th>
                            <th scope="col" class="px-2 py-3">Signature</th>
                            <th scope="col" class="px-2 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody id="approval-list-body">
                        @foreach($register_asset->approvals as $approv)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <th scope="row" class="font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $approv->approval_action }}</th>   
                                <th scope="row" class="font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $approv->role }}</th>
                                <td class="px-2 py-4">{{ $approv->user?->name ?? "-" }}</td>
                                <td class="px-2 py-4">{{ $approv->status }}</td>
                                <td class="px-2 py-4">{{ $approv->approval_date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <a href="{{ route('register-asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Back</a>
    </div>
@endsection