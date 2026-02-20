
// Make confirmDelete global so it can be called from onclick attributes
window.confirmDelete = function (url) {
    const modal = new Modal(document.getElementById('delete-modal'));
    const form = document.getElementById('delete-form');
    form.action = url;
    modal.show();

    // Handle close button manually to ensure modal instance is used
    const closeBtns = document.querySelectorAll('[data-modal-hide="delete-modal"]');
    closeBtns.forEach(btn => {
        btn.onclick = () => modal.hide();
    });
};

document.addEventListener('DOMContentLoaded', function () {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            if (themeToggleDarkIcon) themeToggleDarkIcon.classList.remove('hidden');
            if (themeToggleLightIcon) themeToggleLightIcon.classList.add('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            if (themeToggleDarkIcon) themeToggleDarkIcon.classList.add('hidden');
            if (themeToggleLightIcon) themeToggleLightIcon.classList.remove('hidden');
        }
    }

    const savedTheme = localStorage.getItem('color-theme');
    if (savedTheme) {
        applyTheme(savedTheme);
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        applyTheme('dark');
    } else {
        applyTheme('light');
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            const isDark = document.documentElement.classList.toggle('dark');
            const newTheme = isDark ? 'dark' : 'light';
            localStorage.setItem('color-theme', newTheme);
            applyTheme(newTheme);
        });
    }

    const sidebar = document.getElementById('logo-sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggleBtn = document.getElementById('sidebar-toggle');
    const sidebarToggleIcon = document.getElementById('sidebar-toggle-icon');
    const sidebarText = document.getElementsByClassName('textSidebar');

    if (sidebar && mainContent && sidebarToggleBtn && sidebarToggleIcon) {
        function applySidebarState(isCollapsed) {
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                sidebar.style.width = '4.5rem'; // 72px

                if (window.innerWidth >= 640) {
                    mainContent.style.marginLeft = '4.5rem';
                }
                sidebarToggleIcon.style.transform = 'rotate(180deg)';
                for (let text of sidebarText) {
                    text.classList.add('hidden');
                }
            } else {
                sidebar.classList.remove('collapsed');
                sidebar.style.width = '16rem'; // 256px
                if (window.innerWidth >= 640) {
                    mainContent.style.marginLeft = '16rem';
                }
                sidebarToggleIcon.style.transform = 'rotate(0deg)';
                for (let text of sidebarText) {
                    text.classList.remove('hidden');
                }
            }
        }

        const dropdownToggles = document.querySelectorAll('aside button[data-collapse-toggle]');

        dropdownToggles.forEach(toggle => {
            const arrowIcon = toggle.querySelector('.arrow-icon');
            if (arrowIcon && toggle.getAttribute('aria-expanded') === 'true') {
                arrowIcon.classList.add('rotate-180');
            }
        });

        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function (event) {
                // Cek apakah sidebar sedang dalam keadaan kecil (collapsed)
                if (sidebar.classList.contains('collapsed')) {
                    // Mencegah dropdown terbuka di saat yang bersamaan
                    event.preventDefault();
                    event.stopPropagation();

                    // Buka sidebar
                    applySidebarState(false);
                    localStorage.setItem('sidebar-collapsed', 'false');
                } else {
                    // PENAMBAHAN BARU: Logika untuk memutar arrow icon
                    const arrowIcon = this.querySelector('.arrow-icon');
                    if (arrowIcon) {
                        arrowIcon.classList.toggle('rotate-180');
                    }
                }
            });
        });

        const isSidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        applySidebarState(isSidebarCollapsed);

        sidebarToggleBtn.addEventListener('click', () => {
            const currentlyCollapsed = sidebar.classList.contains('collapsed');
            const newState = !currentlyCollapsed;
            localStorage.setItem('sidebar-collapsed', newState);
            applySidebarState(newState);
        });

        window.addEventListener('resize', () => {
            const currentState = localStorage.getItem('sidebar-collapsed') === 'true';
            if (window.innerWidth < 640) {
                mainContent.style.marginLeft = '0';
            } else {
                applySidebarState(currentState);
            }
        });

        sidebar.addEventListener('mouseenter', function () {
            const isStoredAsCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isStoredAsCollapsed) {
                applySidebarState(false); // Buka sidebar
            }
        });

        sidebar.addEventListener('mouseleave', function () {
            const isStoredAsCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isStoredAsCollapsed) {
                applySidebarState(true);
            }
        });
    }

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
            }
        };

        // Buat instance Dismiss dari Flowbite
        const dismiss = new Dismiss(targetEl, triggerEl, options);

        // (Opsional) Sembunyikan notifikasi secara otomatis setelah 5 detik
        setTimeout(() => {
            dismiss.hide();
        }, 5000);
    });

    // ─── Import Modal: File Input Preview ────────────────────────────────────
    // Update tampilan label saat user pilih file di semua import modal
    document.querySelectorAll('input[type="file"][id="excel_file"]').forEach(input => {
        const label = document.querySelector(`label[for="${input.id}"]`);
        if (!label) return;

        // Simpan inner HTML asli untuk di-reset nanti
        const originalInner = label.innerHTML;

        input.addEventListener('change', function () {
            if (!this.files || !this.files.length) return;

            const file = this.files[0];
            const sizeMB = (file.size / 1024 / 1024).toFixed(2);

            label.innerHTML = `
                <div class="flex flex-col items-center justify-center pt-5 pb-6 gap-2">
                    <svg class="w-10 h-10 text-green-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4.7 4.5 9.3-9"/>
                    </svg>
                    <p class="text-sm font-semibold text-green-600 dark:text-green-400 text-center break-all px-2">${file.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${sizeMB} MB</p>
                    <p class="text-xs text-indigo-500 dark:text-indigo-400 underline cursor-pointer">Change file</p>
                </div>
            `;
            label.classList.add('border-green-400', 'dark:border-green-500');
            label.classList.remove('border-gray-300', 'dark:border-gray-600');
        });

        // Reset label saat modal ditutup
        document.querySelectorAll('[data-modal-hide="import-modal"]').forEach(closeBtn => {
            closeBtn.addEventListener('click', () => {
                input.value = '';
                label.innerHTML = originalInner;
                label.classList.remove('border-green-400', 'dark:border-green-500');
                label.classList.add('border-gray-300', 'dark:border-gray-600');
            });
        });
    });
    // ─────────────────────────────────────────────────────────────────────────
});
