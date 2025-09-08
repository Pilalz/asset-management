$(document).ready(function() {
    const alertElements = document.querySelectorAll('.auto-dismiss-alert');

    alertElements.forEach(targetEl => {
        // Ambil tombol 'close' di dalam notifikasi (jika ada)
        const triggerEl = targetEl.querySelector('[data-dismiss-target]');

        // Opsi yang Anda inginkan
        const options = {
            transition: 'transition-opacity',
            duration: 1000,
            timing: 'ease-out',
            onHide: (context, targetEl) => {
                console.log(`Element dengan ID ${targetEl.id} telah disembunyikan.`);
            }
        };

        // Buat instance Dismiss dari Flowbite
        const dismiss = new Dismiss(targetEl, triggerEl, options);

        // (Opsional) Sembunyikan notifikasi secara otomatis setelah 5 detik
        setTimeout(() => {
            dismiss.hide();
        }, 3000);
    });
})