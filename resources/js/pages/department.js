import $ from 'jquery';
import 'datatables.net-dt';

$(document).ready(function () {
    // Custom Search Input HTML
    const customSearchHTML = `
        <div class="relative flex items-center w-full sm:w-auto">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
            <input type="search" class="dt-custom-search pl-9 pr-4 py-2 w-full sm:w-64 bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors shadow-sm" placeholder="Search all columns..." aria-controls="departmentTable">
        </div>
    `;

    var table = $('#departmentTable').DataTable({
        dom: "<'flex flex-col md:flex-row justify-between items-center p-5 border-b border-slate-200 dark:border-gray-700 gap-4 bg-transparent'<'text-sm text-gray-600 dark:text-gray-300 font-medium'l><'text-sm relative custom-search-container'>>" +
            "<'overflow-x-auto'tr>" +
            "<'flex flex-col md:flex-row justify-between items-center p-5 border-t border-slate-200 dark:border-gray-700 gap-4 bg-transparent'<'text-sm text-gray-600 dark:text-gray-300'i><'text-sm'p>>",
        processing: true,
        serverSide: true,
        ajax: "/api/department",
        autoWidth: false,
        orderCellsTop: true,
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            search: "",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No data available",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                previous: "Prev",
                next: "Next"
            },
            zeroRecords: `
                <div class="flex flex-col items-center justify-center p-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-lg font-medium">No data available</p>
                    <p class="text-sm mt-1">Try using another search keyword.</p>
                </div>`,
            emptyTable: `
                <div class="flex flex-col items-center justify-center p-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mb-4 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-lg font-medium">No department data available</p>
                    <p class="text-sm mt-1">Please add a new department data.</p>
                </div>`,
            processing: `
                <style>
                    div.dt-processing > div:not(.dt-spinner-custom) { display: none !important; }
                    div.dt-processing { background: rgba(255, 255, 255, 0.8) !important; box-shadow: none !important; backdrop-filter: blur(2px); border-radius: 0.5rem; }
                    html.dark div.dt-processing { background: rgba(31, 41, 55, 0.8) !important; }
                </style>
                <div class="dt-spinner-custom flex justify-center items-center py-6">
                    <svg aria-hidden="true" class="w-10 h-10 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                    <span class="sr-only">Please wait...</span>
                </div>`
        },
        initComplete: function () {
            // Replace default search with custom search
            $('.custom-search-container').html(customSearchHTML);

            // Bind custom search input to DataTables search
            $('.dt-custom-search').on('keyup', function () {
                table.search(this.value).draw();
            });

            // Styling length menu
            var $lengthMenu = $('.dt-length select');
            $lengthMenu.addClass('bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ml-2 py-1.5 px-3 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:ring-blue-400');
        },

        columnDefs: [
            {
                targets: 0,
                className: 'px-6 py-4 text-center text-gray-500 dark:text-gray-400 w-16'
            },
            {
                targets: 1,
                className: 'px-6 py-4 font-semibold text-gray-900 dark:text-white align-middle'
            },
            {
                targets: 2,
                className: 'px-6 py-4 text-gray-600 dark:text-gray-300 max-w-sm truncate align-middle'
            },
            {
                targets: 3,
                className: 'px-6 py-4 text-center whitespace-nowrap align-middle',
                width: '120px'
            }
        ],

        createdRow: function (row, data, dataIndex) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors duration-200');
        },
    });
});