import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.default.css';

document.addEventListener('DOMContentLoaded', function () {
    const selectUser = document.getElementById('select-user');
    if (!selectUser) return;

    const nameInput = document.getElementById('name');
    const userIdInput = document.getElementById('user_id');
    const searchUrl = selectUser.dataset.searchUrl;
    
    const preselectedDataAttr = selectUser.dataset.preselected;
    let initialOptions = [];
    let initialItems = [];

    if (preselectedDataAttr) {
        try {
            // Ubah string JSON dari atribut data menjadi objek JavaScript
            const preselectedData = JSON.parse(preselectedDataAttr);
            // Siapkan data untuk opsi awal
            initialOptions.push(preselectedData);
            // Set item yang harus dipilih saat pertama kali dimuat
            initialItems.push(preselectedData.id);
        } catch (e) {
            console.error("Gagal mem-parsing data pre-selected:", e);
        }
    }

    if (selectUser && searchUrl) {
        new TomSelect(selectUser, {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'email'],
            options: initialOptions,
            items: initialItems,

            render: {
                option: function(data, escape) {
                    return `<div class="p-2">
                                <div class="font-semibold">${escape(data.name)}</div>
                                <div class="text-sm text-gray-500">${escape(data.email)}</div>
                            </div>`;
                },
                item: function(data, escape) {
                    return `<div class="flex items-center">${escape(data.name)} <span class="text-xs text-gray-500 ml-2">(${escape(data.email)})</span></div>`;
                }
            },

            load: function(query, callback) {
                if (query.length < 3) return callback();
                
                fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        callback(data);
                    }).catch(() => {
                        callback();
                    });
            },
            
            onChange: function(value) {
                const selectedData = this.options[value];
                if (selectedData) {
                    nameInput.value = selectedData.name;
                    userIdInput.value = selectedData.id;
                } else {
                    nameInput.value = '';
                    userIdInput.value = '';
                }
            }
        });
    }
});