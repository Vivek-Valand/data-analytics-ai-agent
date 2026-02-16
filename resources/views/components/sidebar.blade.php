<div id="sidebar"
    class="bg-[#1e1f20] text-gray-300 w-72 flex-shrink-0 flex flex-col h-screen sticky top-0 z-30 sidebar-transition -translate-x-full md:translate-x-0 overflow-hidden">

    <div class="p-6 shrink-0">
        <a href="/analytics" class="text-xl font-bold text-white flex items-center gap-2 mb-8 transition-colors">
            <span
                class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-sm group-hover:bg-indigo-400">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
            </span>
            <p class='hover:text-indigo-400'>
                Data Analytics Agent
            </p>
        </a>

        <button onclick="startNewChat()"
            class="w-full flex items-center gap-3 bg-[#2a2b2d] text-white rounded-full px-5 py-4 text-sm font-medium hover:bg-[#37393b] transition-all mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            New Analysis
        </button>

        <a href="/db-config"
            class="w-full flex items-center gap-3 bg-white/5 text-gray-300 rounded-full px-5 py-4 text-sm font-medium hover:bg-white/10 transition-all border border-white/10 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
            </svg>
            Database Configuration
        </a>
    </div>

    <div class="flex-1 overflow-y-auto px-4 pb-4 scrollbar-hide" id="history-list">
        <!-- Histories loaded via jQuery -->
    </div>

    <div class="p-4 bg-[#131314] border-t border-white/5 shrink-0">
        <div class="flex items-center justify-between gap-3 px-2">
            <div class="flex items-center gap-3 min-w-0">
                <div
                    class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white shadow-lg shrink-0">
                    {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Guest User' }}
                    </div>
                    <div class="text-[10px] text-gray-500 truncate uppercase mt-0.5 tracking-wider font-bold">Data
                        Analyst</div>
                </div>
            </div>

            <button onclick="confirmLogout()"
                class="p-2 text-gray-500 hover:text-red-400 hover:bg-red-400/10 rounded-xl transition-all"
                title="Logout">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Sign Out?',
                text: "Are you sure you want to logout?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Sign Out',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#logout-form').submit();
                }
            });
        }
    </script>
</div>
