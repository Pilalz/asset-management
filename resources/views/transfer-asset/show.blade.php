@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('transfer-asset.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
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
                        <a href="{{ route('transfer-asset.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Transfer Asset</a>
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

        <div class="flex gap-2 content-center">
            <a href="{{ route('transfer-asset.exportPdf', $transfer_asset->id) }}" target="_blank" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-sm text-sm px-5 py-2.5">
                Export PDF
            </a>
        </div>
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

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg bg-white dark:bg-gray-900">
            <div class="mb-5 flex content-center">
                <label class="flex w-40 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pengajuan </label>
                <span> : </span>
                <p class="flex ml-1 text-sm text-gray-900">{{ $transfer_asset->submit_date }}</p>
            </div>

            <div class="mb-5 flex content-center">
                <label class="flex w-40 text-sm font-medium text-gray-900 dark:text-white">Nomor Formulir </label>
                <span> : </span>
                <p class="flex ml-1 text-sm text-gray-900">{{ $transfer_asset->form_no }}</p>
            </div>

            <div class="mb-5 flex content-center">
                <label class="flex w-40 text-sm font-medium text-gray-900 dark:text-white">Department </label>
                <span> : </span>
                <p class="flex ml-1 text-sm text-gray-900">{{ $transfer_asset->department->name }}</p>
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Data </label>
                <div class="border-2 border-black rounded-lg p-4">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-2 py-3">Asset Number</th>
                                <th scope="col" class="px-2 py-3">Asset Name</th>
                                <th scope="col" class="px-2 py-3">Description</th>
                                <th scope="col" class="px-2 py-3">ID Pareto</th>
                                <th scope="col" class="px-2 py-3">No. Unit</th>
                                <th scope="col" class="px-2 py-3">No. Mesin</th>
                                <th scope="col" class="px-2 py-3">No. Engine</th>
                                <th scope="col" class="px-2 py-3">Tahun Akuisisi</th>
                                <th scope="col" class="px-2 py-3">Location</th>
                            </tr>
                        </thead>
                        <tbody id="asset-data-body">
                            @foreach($transfer_asset->detailTransfers as $detail)
                                <tr>
                                    <td class="p-4">{{ $detail->asset?->asset_number ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->assetName->name ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->description ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->pareto ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->unit_no ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->sn_chassis ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->sn_engine ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->capitalized_date->format('Y') }}</td>
                                    <td class="p-4">{{ $detail->originLocation->name ?? "-" }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-5 flex content-center">
                <label class="flex w-40 text-sm font-medium text-gray-900 dark:text-white">Destination Location </label>
                <span> : </span>
                <p class="flex ml-1 text-sm text-gray-900">{{ $transfer_asset->destinationLocation->name }}</p>
            </div>

            <div class="mb-5">
                <label class="block mb-2 w-40 text-sm font-medium text-gray-900 dark:text-white">Alasan :</label>
                <p class="w-auto px-1 border-b border-gray-700 text-sm text-gray-900">{{ $transfer_asset->reason }}</p>
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lampiran</label>
                @if($transfer_asset->attachments->isNotEmpty())
                    <ul class="list-disc list-inside pl-4">
                        @foreach($transfer_asset->attachments as $attachment)
                            <li>
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-blue-800 hover:underline">
                                    {{ $attachment->original_filename }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">Tidak ada lampiran.</p>
                @endif
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Approval List </label>
                <div class="border-2 border-black rounded-lg p-4">
                    <div class="flex flex-row mb-2">
                        @php
                            $sequenceText = $transfer_asset->sequence;
                            $sequenceClass = '';

                            if ($sequenceText == 0) {
                                $sequenceClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                            } elseif ($sequenceText == 1) {
                                $sequenceClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                            } else { 
                                $sequenceClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                            }
                        @endphp
                        <label class="w-auto mr-2 text-sm font-medium text-gray-900 dark:text-white">Sequence  :  <span class="{{ $sequenceClass }} font-medium px-2 py-0.5 rounded">{{ ($transfer_asset->sequence == 1) ? "Yes" : "No" }}</span></label>
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
                            @foreach($transfer_asset->approvals->sortBy('approval_order') as $approv)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $approv->approval_action }}</th>   
                                    <th scope="row" class="font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $approv->role }}</th>
                                    <td class="px-2 py-4">{{ $approv->pic?->name ?? "-" }}</td>
                                    @if ($approv->status == 'approved' && $approv->user->signature)
                                        <td class="px-2 py-4 status-pending">
                                            <div class="signature-container">
                                                <img src="{{ $approv->user->signature }}" alt="Signature" class="h-12">
                                            </div>
                                        </td>
                                    @else
                                        <td class="px-2 py-4 status-pending">{{ $approv->status}}</td>
                                    @endif
                                    <td class="px-2 py-4">{{ $approv->approval_date ?? "-" }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex gap-2 content-center">
                <a href="{{ route('transfer-asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Back</a>
                @if ($canApprove)
                    <button
                        type="button" 
                        data-modal-target="confirmation-modal" 
                        data-modal-toggle="confirmation-modal"
                        class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-5 py-2.5">
                        Approve & Sign
                    </button>

                    <div id="confirmation-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <div class="p-4 md:p-5 text-center">
                                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                    </svg>
                                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                                        Apakah Anda yakin ingin menyetujui formulir ini?
                                    </h3>
                                    
                                    {{-- Tombol ini yang akan men-submit form --}}
                                    <button id="confirm-approve-btn" data-modal-hide="confirmation-modal" type="button" class="text-white bg-green-600 hover:bg-green-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                        Ya, saya yakin
                                    </button>
                                    <button data-modal-hide="confirmation-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 ...">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="approve-form" action="{{ route('transfer-asset.approve', $transfer_asset) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <p class="flex items-center">{{ $userApprovalStatus }}</p>
                @endif
            </div>            
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmBtn = document.getElementById('confirm-approve-btn');
            const approveForm = document.getElementById('approve-form');

            if(confirmBtn && approveForm) {
                confirmBtn.addEventListener('click', function() {
                    approveForm.submit();
                });
            }
        });
    </script>

    @vite('resources/js/pages/alert.js')
@endpush