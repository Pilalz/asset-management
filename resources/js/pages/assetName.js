import $ from 'jquery';
import 'datatables.net-dt';

$(document).ready(function() {

    const assetSubClassesData = window.assetSubClassesForFilterData || [];

    $('#assetNameTable thead tr:eq(0) th').each(function(i) {
        var title = $(this).text().trim();
        var cell = $('#filter-row').children().eq(i);
        if (i === 0 || i === 6) {
            return;
        }
        else if (i === 1) {
            let options = assetSubClassesData.map(assetSub =>
                `<option value="${assetSub.name}">${assetSub.name}</option>` // Value pakai ID
            ).join('');
            $(cell).html(
                `<select class="filter-select w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option selected value="">Select Asset Sub Class</option>
                    ${options}
                </select>`
            );
        }
        else if (i === 3 || i === 4 ||  i === 5) {
            $(cell).html('<input type="number" min="0" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
        }
        else {
            $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
        }
    });

    var table = $('#assetNameTable').DataTable({
        dom:  "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
                "<'overflow-x-auto'tr>" +
                "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
        processing: true,
        serverSide: true,
        ajax: "/api/asset-name",
        autoWidth: false,
        orderCellsTop: true,
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
            { data: 'asset_sub_class_name', name: 'assetSubClass.name' },
            { data: 'name', name: 'name' },
            { data: 'grouping', name: 'grouping' },
            { data: 'commercial', name: 'commercial' },
            { data: 'fiscal', name: 'fiscal' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            search: "Search : ",
            searchPlaceholder: "Cari di sini...",
        },
        initComplete: function () {
            // --- Tambahkan kelas ke search box utama di sini ---
            $('.dt-search input').addClass('w-full sm:w-auto bg-white-50 border border-white-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500');
            
            // --- Logika untuk filter per kolom ---
            this.api().columns().every(function (index) {
                var column = this;
                var cell = $('#assetNameTable thead #filter-row').children().eq(column.index());
                
                if (column.settings()[0].bSearchable === false) {
                    return;
                }
                
                var input = $('input', cell);
                input.on('keyup change clear', function(e) {
                    e.stopPropagation();
                    if (column.search() !== this.value) {
                        column.search(this.value).draw();
                    }
                });
                input.on('click', function(e) {
                    e.stopPropagation();
                });

                var select = $('select', cell);
                select.on('change', function(e) {
                    e.stopPropagation();
                    if (column.search() !== this.value) {
                        column.search(this.value).draw();
                    }
                });
                select.on('click', function(e) {
                    e.stopPropagation();
                });
            });
        },

        columnDefs: [
            {
                targets: 0,
                className: 'px-6 py-4'
            },
            {
                targets: [4, 5], 
                render: function (data, type, row) {
                    if (type === 'display') {
                        return data + ' (Years)';
                    }
                    return data;
                }
            },
        ],

        createdRow: function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
    });

    table.columns().every(function() {
        var that = this;
        
        // Event untuk filtering saat mengetik
        $('input', $('#assetNameTable thead #filter-row').children().eq(this.index())).on('keyup change clear', function(e) {
            e.stopPropagation(); // Hentikan event agar tidak memicu sorting
            if (that.search() !== this.value) {
                that.search(this.value).draw();
            }
        });
    });

    $('#importForm').on('submit', function(e) {
        e.preventDefault(); // Mencegah submit form biasa

        let formData = new FormData(this);
        let $btn = $('#btnSubmit');
        let $error = $('#importError');
        let $progressContainer = $('#progressContainer');
        let $progressBar = $('#progressBar');
        let $statusMessage = $('#statusMessage');
        let formAction = $(this).attr('action');

        // Reset UI
        $btn.prop('disabled', true).text('Uploading...');
        $error.addClass('hidden').text('');
        $progressContainer.removeClass('hidden');
        $progressBar.css('width', '0%').text('0%');
        $statusMessage.removeClass('hidden').text('Mengunggah file...');

        // 1. AJAX Upload
        $.ajax({
            url: formAction,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $btn.text('Processing...');
                $statusMessage.text(response.message);
                
                // 2. Mulai Polling Status
                if (response.job_id) {
                    trackProgress(response.job_id);
                }
            },
            error: function(xhr) {
                // Error Validasi / Upload
                $btn.prop('disabled', false).text('Import Excel');
                $progressContainer.addClass('hidden');
                $statusMessage.addClass('hidden');
                
                let msg = 'Terjadi kesalahan.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message; // Pesan error Laravel
                }
                $error.removeClass('hidden').text(msg);
            }
        });
    });

    // Fungsi Polling (Cek Status Setiap 1 Detik)
    function trackProgress(jobId) {
        let $progressBar = $('#progressBar');
        let $statusMessage = $('#statusMessage');
        let $btn = $('#btnSubmit');

        let interval = setInterval(function() {
            $.ajax({
                url: "/api/import-status/" + jobId, // Panggil route baru tadi
                type: "GET",
                success: function(data) {
                    // Update Progress Bar
                    let percent = data.progress || 0;
                    $progressBar.css('width', percent + '%').text(percent + '%');

                    // Cek Status
                    if (data.status === 'completed') {
                        clearInterval(interval);
                        $progressBar.removeClass('bg-blue-600').addClass('bg-green-600');
                        $statusMessage.text('Import Selesai! Merefresh halaman...');
                        $btn.text('Selesai');
                        
                        // Reload halaman setelah 1 detik
                        setTimeout(() => window.location.reload(), 1000);

                    } else if (data.status === 'failed') {
                        clearInterval(interval);
                        $progressBar.removeClass('bg-blue-600').addClass('bg-red-600');
                        $statusMessage.text('Gagal: ' + (data.error || 'Unknown error'));
                        $btn.prop('disabled', false).text('Import Excel');
                    } else {
                        // Masih running
                        $statusMessage.text('Memproses data... (' + (data.processed_rows || 0) + ' baris)');
                    }
                },
                error: function() {
                    // Jika gagal cek status (misal koneksi putus), hentikan
                    clearInterval(interval);
                    $statusMessage.text('Gagal mengambil status import.');
                }
            });
        }, 1000); // Cek setiap 1000ms (1 detik)
    }
});