import $ from 'jquery';
import 'datatables.net-dt';

$(document).ready(function() {
    if ($('#depreciationTable').length) {
        $('#depreciationTable').DataTable({
            "dom": "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-800'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm text-gray-700 dark:text-gray-200'f>>" +
                "<'overflow-x-auto'tr>" +
                "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-800'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
            
            // Menonaktifkan pengurutan pada kolom bulan
            "columnDefs": [
                { "orderable": false, "targets": '_all' }, // Matikan sort untuk semua kolom
                { "orderable": true, "targets": [1, 2] }  // Aktifkan kembali HANYA untuk Asset Name & Number
            ],

            // Mengatur bahasa (opsional)
            "language": {
                "search": "Cari Aset:",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ aset",
                "paginate": {
                    "next": ">",
                    "previous": "<"
                }
            }
        });
    }
});