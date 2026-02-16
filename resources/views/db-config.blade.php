@extends('layouts.app')

@section('title', 'Database Configuration')

@section('content')
    <div class="max-w-5xl mx-auto w-full p-4 md:p-10 overflow-y-auto">
        <div class="glass-card overflow-hidden">
            <div class="p-6 md:p-8 border-b flex flex-col sm:flex-row justify-between items-center bg-gray-50/50 gap-4">
                <h3 id="page-title" class="text-xl md:text-2xl font-bold text-gray-900">Database Connections</h3>
                <button id="add-new-btn" onclick="showAddForm()"
                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-2xl transition-all shadow-lg active:scale-95">
                    Add New Connection
                </button>
            </div>

            <div id="page-tabs" class="flex border-b bg-gray-50/30 hidden overflow-x-auto whitespace-nowrap">
                <button onclick="switchTab('db-config')" id="tab-db-config"
                    class="px-6 md:px-8 py-4 text-xs md:text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 transition-all uppercase tracking-wider">Database
                    Config</button>
                {{-- <button onclick="switchTab('sql-import')" id="tab-sql-import"
                    class="px-6 md:px-8 py-4 text-xs md:text-sm font-bold border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-all uppercase tracking-wider">SQL
                    Import</button> --}}
            </div>

            <div class="p-4 md:p-8">
                <!-- List View -->
                <div id="db-list-content" class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <!-- Items will be loaded here -->
                </div>

                <!-- Form View -->
                <div id="db-form-container" class="hidden max-w-2xl mx-auto space-y-6 md:space-y-8 mb-3">
                    <!-- DB Form -->
                    <div id="db-config-content" class="space-y-4 md:space-y-6">
                        <form id="db-config-form" class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <input type="hidden" name="config_id" id="config-id-input">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Connection
                                    Name</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all"
                                    placeholder="e.g. Production MySQL">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">DB Connection</label>
                                <input type="text" name="connection" value="mysql" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">DB Host</label>
                                <input type="text" name="host" value="127.0.0.1" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">DB Port</label>
                                <input type="text" name="port" value="3306" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">DB Database</label>
                                <input type="text" name="database" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">DB Username</label>
                                <input type="text" name="username" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">DB Password</label>
                                <input type="password" name="password"
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            </div>
                            <div class="md:col-span-2 flex flex-row gap-3 mb-8 mt-2 md:mt-6 w-full">
                                <button type="button" onclick="showListView()"
                                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 md:py-4 px-4 md:px-6 rounded-2xl transition-all">
                                    Cancel
                                </button>

                                <button type="button" onclick="testDbConnection()"
                                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-indigo-600 font-bold py-3 md:py-4 px-4 md:px-6 rounded-2xl transition-all">
                                    Test
                                </button>

                                <button type="button" onclick="saveDbConfig()" id="save-db-btn" disabled
                                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 md:py-4 px-4 md:px-6 rounded-2xl transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- SQL Form -->
                    <div id="sql-import-content" class="hidden space-y-4 md:space-y-6">
                        <form id="sql-import-form" class="space-y-4 md:space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Import Name</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all"
                                    placeholder="e.g. Legacy Sales Data">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">SQL File
                                    (.sql)</label>
                                <input type="file" name="sql_file" id="sql-file-input" accept=".sql" required
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all bg-white text-sm">
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-6">
                                <button type="button" onclick="showListView()"
                                    class="order-2 sm:order-1 flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 md:py-4 px-6 rounded-2xl transition-all">Cancel</button>
                                <button type="button" onclick="uploadSqlFile()"
                                    class="order-1 sm:order-2 flex-[2] bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 md:py-4 px-6 rounded-2xl transition-all shadow-lg">Upload
                                    & Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        let lastTestPassed = false;

        function setSaveEnabled(isEnabled) {
            $('#save-db-btn').prop('disabled', !isEnabled);
        }

        function showListView() {
            $('#page-title').text("Database Connections");
            $('#add-new-btn').removeClass('hidden');
            $('#page-tabs').addClass('hidden');
            $('#db-list-content').removeClass('hidden');
            $('#db-form-container').addClass('hidden');
            loadDbConfigs();
        }

        function showAddForm() {
            $('#page-title').text("Add New Connection");
            $('#add-new-btn').addClass('hidden');
            $('#page-tabs').removeClass('hidden');
            $('#db-list-content').addClass('hidden');
            $('#db-form-container').removeClass('hidden');
            $('#db-config-form')[0].reset();
            $('#config-id-input').val("");
            lastTestPassed = false;
            setSaveEnabled(false);
            switchTab('db-config');
        }

        async function editDbConfig(id) {
            try {
                const res = await fetch(`/api/db-config/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                $('#page-title').text("Edit Connection");
                $('#add-new-btn').addClass('hidden');
                $('#page-tabs').addClass('hidden');
                $('#db-list-content').addClass('hidden');
                $('#db-form-container').removeClass('hidden');
                $('#db-config-content').removeClass('hidden');
                $('#sql-import-content').addClass('hidden');

                $('#config-id-input').val(data.id);
                const form = $('#db-config-form')[0];
                form.elements['name'].value = data.name;
                form.elements['connection'].value = data.connection;
                form.elements['host'].value = data.host;
                form.elements['port'].value = data.port;
                form.elements['database'].value = data.database;
                form.elements['username'].value = data.username;
                form.elements['password'].value = "";
                lastTestPassed = false;
                setSaveEnabled(false);
            } catch (e) {
                showToast("Error loading config: " + e.message);
            }
        }

        async function deleteDbConfig(type, id) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete this ${type === 'db' ? 'database connection' : 'SQL file'}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const res = await fetch(`/api/db-config/${type}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        const result = await res.json();
                        if (result.success) {
                            showToast(result.message, 'success');
                            loadDbConfigs();
                        } else {
                            showToast(result.message, 'error');
                        }
                    } catch (e) {
                        showToast("Delete failed: " + e.message);
                    }
                }
            });
        }

        function switchTab(tab) {
            if (tab === 'db-config') {
                $('#db-config-content').removeClass('hidden');
                $('#sql-import-content').addClass('hidden');
                $('#tab-db-config').addClass('border-indigo-600 text-indigo-600').removeClass(
                    'border-transparent text-gray-500');
                $('#tab-sql-import').removeClass('border-indigo-600 text-indigo-600').addClass(
                    'border-transparent text-gray-500');
            } else {
                $('#db-config-content').addClass('hidden');
                $('#sql-import-content').removeClass('hidden');
                $('#tab-sql-import').addClass('border-indigo-600 text-indigo-600').removeClass(
                    'border-transparent text-gray-500');
                $('#tab-db-config').removeClass('border-indigo-600 text-indigo-600').addClass(
                    'border-transparent text-gray-500');
            }
        }

        async function testDbConnection() {
            const formData = new FormData($('#db-config-form')[0]);
            const data = Object.fromEntries(formData.entries());

            try {
                const res = await fetch('/api/db-config/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                showToast(result.message, result.success ? 'success' : 'error');
                lastTestPassed = !!result.success;
                setSaveEnabled(lastTestPassed);
            } catch (e) {
                showToast("Connection test failed: " + e.message);
                lastTestPassed = false;
                setSaveEnabled(false);
            }
        }

        async function saveDbConfig() {
            if (!lastTestPassed) {
                showToast('Please test the connection successfully before saving.');
                setSaveEnabled(false);
                return;
            }
            const formData = new FormData($('#db-config-form')[0]);
            const data = Object.fromEntries(formData.entries());
            const configId = $('#config-id-input').val();

            try {
                const url = configId ? `/api/db-config/${configId}` : '/api/db-config/save';
                const method = configId ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                if (result.success) {
                    showToast(result.message, 'success');
                    showListView();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (e) {
                showToast("Save failed: " + e.message);
            }
        }

        async function uploadSqlFile() {
            const formData = new FormData($('#sql-import-form')[0]);
            const fileInput = $('#sql-file-input')[0];

            if (!fileInput.files[0]) {
                showToast("Please select a file first");
                return;
            }

            try {
                const res = await fetch('/api/db-config/upload-sql', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const result = await res.json();
                if (result.success) {
                    showToast(result.message, 'success');
                    showListView();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (e) {
                showToast("Upload failed: " + e.message);
            }
        }

        async function loadDbConfigs() {
            try {
                const res = await fetch('/api/db-config/list', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                const $listContainer = $('#db-list-content');

                $listContainer.empty();

                if (data.databases.length === 0 && data.sqlFiles.length === 0) {
                    $listContainer.html(
                        '<div class="col-span-full text-center text-gray-400 py-16 md:py-20 bg-white rounded-3xl border border-dashed border-gray-300 px-6">No connections configured. Click "Add New" to get started.</div>'
                    );
                }

                data.databases.forEach(db => {
                    const $item = $('<div>').addClass(
                        'flex flex-col p-5 md:p-6 bg-white rounded-3xl border border-gray-200 hover:border-indigo-500 transition-all group shadow-sm'
                    );
                    $item.html(`
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 md:p-4 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all">
                            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        </div>
                        <div class="flex gap-1 md:gap-2">
                            <button onclick="editDbConfig(${db.id})" class="p-2 text-gray-400 hover:text-indigo-600 rounded-lg transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                            <button onclick="deleteDbConfig('db', ${db.id})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>
                    </div>
                    <div class="text-lg md:text-xl font-bold text-gray-900 mb-1 truncate">${db.name}</div>
                    <div class="text-xs md:text-sm text-gray-500 font-medium truncate">${db.database} • ${db.host}</div>
                `);
                    $listContainer.append($item);
                });

                data.sqlFiles.forEach(sql => {
                    const $item = $('<div>').addClass(
                        'flex flex-col p-5 md:p-6 bg-white rounded-3xl border border-gray-200 hover:border-purple-500 transition-all group shadow-sm'
                    );
                    $item.html(`
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 md:p-4 bg-purple-50 text-purple-600 rounded-2xl group-hover:bg-purple-600 group-hover:text-white transition-all">
                            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex gap-1 md:gap-2">
                            <button onclick="deleteDbConfig('sql', ${sql.id})" class="p-2 text-gray-400 hover:text-red-600 rounded-lg transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>
                    </div>
                    <div class="text-lg md:text-xl font-bold text-gray-900 mb-1 truncate">${sql.name}</div>
                    <div class="text-xs md:text-sm text-gray-500 font-medium truncate">SQL File Import</div>
                `);
                    $listContainer.append($item);
                });
            } catch (e) {
                console.error("Failed to load configs", e);
            }
        }

        $(document).ready(function() {
            loadDbConfigs();
            setSaveEnabled(false);
            $('#db-config-form').on('input', 'input, select', function() {
                lastTestPassed = false;
                setSaveEnabled(false);
            });
        });
    </script>
@endsection
