<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Analytics</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f0f2f5;
        }

        .gemini-gradient {
            background: linear-gradient(74deg, #4285f4 0, #9b72cb 9%, #d96570 20%, #d96570 24%, #9b72cb 35%, #4285f4 44%, #9b72cb 50%, #d96570 56%, #131314 75%, #131314 100%);
            background-size: 400% 400%;
            animation: gradient-animation 10s ease infinite;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes gradient-animation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .chat-bubble-ai {
            background-color: #ffffff;
            border-radius: 1.5rem;
            border-bottom-left-radius: 0.25rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .chat-bubble-user {
            background-color: #e3e3e3;
            border-radius: 1.5rem;
            border-bottom-right-radius: 0.25rem;
        }

        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .prose table {
            width: 100%;
            text-align: left;
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            border-collapse: collapse;
            border-radius: 1rem;
            overflow: hidden;
            margin: 1.5rem 0;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .prose th {
            background: #f8fafc;
            padding: 1rem;
            font-weight: 600;
            border-bottom: 2px solid #f1f5f9;
        }

        .prose td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .sidebar-transition {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dot-flashing {
            position: relative;
            width: 6px;
            height: 6px;
            border-radius: 5px;
            background-color: #4f46e5;
            color: #4f46e5;
            animation: dot-flashing 1s infinite linear alternate;
            animation-delay: .5s;
        }

        .dot-flashing::before,
        .dot-flashing::after {
            content: '';
            display: inline-block;
            position: absolute;
            top: 0;
        }

        .dot-flashing::before {
            left: -12px;
            width: 6px;
            height: 6px;
            border-radius: 5px;
            background-color: #4f46e5;
            animation: dot-flashing 1s infinite alternate;
            animation-delay: 0s;
        }

        .dot-flashing::after {
            left: 12px;
            width: 6px;
            height: 6px;
            border-radius: 5px;
            background-color: #4f46e5;
            animation: dot-flashing 1s infinite alternate;
            animation-delay: 1s;
        }

        @keyframes dot-flashing {
            0% {
                background-color: #4f46e5;
            }

            50%,
            100% {
                background-color: #e0e7ff;
            }
        }
    </style>
</head>

<body class="h-screen flex text-gray-900 overflow-hidden">

    <!-- Sidebar -->
    <div id="sidebar"
        class="bg-[#1e1f20] text-gray-300 w-72 flex-shrink-0 flex flex-col h-full absolute md:relative z-30 sidebar-transition -translate-x-full md:translate-x-0">

        <div class="p-6">
            <h1 class="text-xl font-bold text-white flex items-center gap-2 mb-8">
                <span class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-sm">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </span>
                Data Analytics Agent
            </h1>

            <button onclick="startNewChat()"
                class="w-full flex items-center gap-3 bg-[#2a2b2d] text-white rounded-full px-5 py-4 text-sm font-medium hover:bg-[#37393b] transition-all mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                New Analysis
            </button>

            <a href="/chat"
                class="w-full flex items-center gap-3 bg-white/5 text-gray-300 rounded-full px-5 py-4 text-sm font-medium hover:bg-white/10 transition-all border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Order Assistant
            </a>
        </div>

        <div class="flex-1 overflow-y-auto px-4 pb-4" id="history-list">
            <!-- Histories -->
        </div>

        <div class="p-4 bg-[#131314]">
            <div class="flex items-center gap-3 px-2">
                <div
                    class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white shadow-lg">
                    {{ substr(Auth::user()->name ?? 'G', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Guest User' }}
                    </div>
                    <div class="text-xs text-gray-500 truncate">Data Analyst</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-full bg-[#f0f2f5] relative">

        <!-- Header (Mobile) -->
        <div class="md:hidden flex items-center justify-between p-4 bg-white border-b">
            <button onclick="toggleSidebar()" class="p-2 hover:bg-gray-100 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <h1 class="text-lg font-bold gemini-gradient">Data Analytics Agent</h1>
            <div class="w-10"></div>
        </div>

        <!-- Chat Area -->
        <div id="chat-container" class="flex-1 overflow-y-auto pt-10 px-4 md:px-0">
            <div id="messages-wrapper" class="max-w-5xl mx-auto space-y-8 pb-10">
                <!-- Welcome Section -->
                <div id="welcome-section" class="py-20 text-center">
                    <h2 class="text-4xl md:text-5xl font-bold gemini-gradient mb-4">Market Insights</h2>
                    <p class="text-xl text-gray-600 mb-12">Ask for revenue trends, forecasts, or custom reports.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left max-w-3xl mx-auto">
                        <button onclick="sendPromptAndHide('Show me revenue trends for the last 30 days')"
                            class="p-6 bg-white rounded-3xl hover:bg-indigo-50 transition-colors border border-gray-100 shadow-sm group">
                            <div class="flex items-center gap-4">
                                <span class="text-3xl">📈</span>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Revenue Trends</h3>
                                    <p class="text-xs text-gray-500 mt-1">Analyze patterns over the last month</p>
                                </div>
                            </div>
                        </button>
                        <button onclick="sendPromptAndHide('Predict order volume for next week')"
                            class="p-6 bg-white rounded-3xl hover:bg-indigo-50 transition-colors border border-gray-100 shadow-sm group">
                            <div class="flex items-center gap-4">
                                <span class="text-3xl">🔮</span>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Future Forecast</h3>
                                    <p class="text-xs text-gray-500 mt-1">Predict upcoming sales volume</p>
                                </div>
                            </div>
                        </button>
                        <button onclick="sendPromptAndHide('Generate a sales report for this month')"
                            class="p-6 bg-white rounded-3xl hover:bg-indigo-50 transition-colors border border-gray-100 shadow-sm group">
                            <div class="flex items-center gap-4">
                                <span class="text-3xl">📄</span>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Export Report</h3>
                                    <p class="text-xs text-gray-500 mt-1">Create a downloadable CSV</p>
                                </div>
                            </div>
                        </button>
                        <button onclick="sendPromptAndHide('List top 10 products by revenue')"
                            class="p-6 bg-white rounded-3xl hover:bg-indigo-50 transition-colors border border-gray-100 shadow-sm group">
                            <div class="flex items-center gap-4">
                                <span class="text-3xl">🏆</span>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Product Ranking</h3>
                                    <p class="text-xs text-gray-500 mt-1">Identify your best-selling items</p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Bar -->
        <div class="px-4 pb-24 pt-4 md:p-10 bg-transparent">
            <div class="max-w-3xl mx-auto shadow-2xl rounded-3xl overflow-hidden glass-input pointer-events-auto">
                <form id="analytics-form" onsubmit="handleSubmit(event)" class="flex items-end gap-2 p-3">
                    <textarea id="message-input"
                        class="flex-1 bg-transparent border-none text-gray-800 focus:outline-none focus:ring-0 p-3 resize-none max-h-48"
                        placeholder="Enter an analysis prompt..." rows="1"
                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                        onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); handleSubmit(event); }"></textarea>

                    <div class="flex gap-2 pb-1 pr-1">
                        <button type="submit" id="send-btn"
                            class="p-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl transition-all shadow-md active:scale-95 disabled:opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" onclick="toggleSidebar()"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-20 hidden md:hidden"></div>

    <!-- Toasts -->
    <div id="toast-container" class="fixed top-6 right-6 z-50 flex flex-col gap-3"></div>

    <script>
        const chatContainer = document.getElementById('chat-container');
        const messagesWrapper = document.getElementById('messages-wrapper');
        const messageInput = document.getElementById('message-input');
        const analyticsForm = document.getElementById('analytics-form');
        const historyList = document.getElementById('history-list');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentChatId = null;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function sendPromptAndHide(text) {
            const welcome = document.getElementById('welcome-section');
            if (welcome) welcome.remove();
            messageInput.value = text;
            handleSubmit({
                preventDefault: () => {}
            });
        }

        async function loadHistory() {
            try {
                const res = await fetch('/analytics/history');
                const histories = await res.json();
                historyList.innerHTML = '';

                if (histories.length === 0) {
                    historyList.innerHTML =
                        '<div class="text-center text-gray-500 text-xs py-10">No recent analyses</div>';
                    return;
                }

                histories.forEach(h => {
                    const isActive = currentChatId == h.id;
                    const item = document.createElement('div');
                    item.className =
                        `sidebar-item group flex items-center gap-3 px-4 py-3 rounded-2xl cursor-pointer ${isActive ? 'active shadow-lg' : 'text-gray-400'} relative`;

                    const titleBtn = document.createElement('button');
                    titleBtn.className = 'flex items-center gap-3 flex-1 min-w-0 text-left';
                    titleBtn.onclick = () => loadChat(h.id);
                    titleBtn.innerHTML = `
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        <span class="text-sm font-medium truncate chat-title-text" data-id="${h.id}">${h.title || 'Untitled analysis'}</span>
                    `;

                    const menuBtn = document.createElement('button');
                    menuBtn.className =
                        'opacity-0 group-hover:opacity-100 p-1 hover:bg-white/10 rounded transition-all';
                    menuBtn.innerHTML =
                        '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>';
                    menuBtn.onclick = (e) => {
                        e.stopPropagation();
                        showMenu(h.id, e.currentTarget);
                    };

                    item.appendChild(titleBtn);
                    item.appendChild(menuBtn);
                    historyList.appendChild(item);
                });
            } catch (e) {
                console.error(e);
            }
        }

        function showMenu(chatId, button) {
            document.querySelectorAll('.context-menu').forEach(m => m.remove());
            const menu = document.createElement('div');
            menu.className =
                'context-menu absolute right-2 top-full mt-1 bg-[#2a2b2d] border border-white/10 rounded-xl shadow-2xl py-2 z-50 w-32';
            menu.innerHTML = `
                <button onclick="renameChat(${chatId})" class="w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-white/5">Rename</button>
                <button onclick="deleteChat(${chatId})" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/5">Delete</button>
            `;
            button.parentElement.appendChild(menu);
            setTimeout(() => {
                document.addEventListener('click', function closeMenu(e) {
                    if (!menu.contains(e.target)) {
                        menu.remove();
                        document.removeEventListener('click', closeMenu);
                    }
                });
            }, 0);
        }

        async function renameChat(id) {
            const textEl = document.querySelector(`.chat-title-text[data-id="${id}"]`);
            const oldTitle = textEl.innerText;
            const input = document.createElement('input');
            input.type = 'text';
            input.value = oldTitle;
            input.className =
                'bg-white/10 text-white text-sm rounded px-2 py-1 w-full outline-none ring-1 ring-indigo-500';

            const parent = textEl.parentElement;
            textEl.classList.add('hidden');
            parent.appendChild(input);
            input.onclick = (e) => e.stopPropagation();
            input.focus();

            const finish = async (save) => {
                const newTitle = input.value.trim();
                input.remove();
                textEl.classList.remove('hidden');
                if (save && newTitle && newTitle !== oldTitle) {
                    try {
                        const res = await fetch(`/analytics/rename/${id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                title: newTitle
                            })
                        });
                        if (res.ok) {
                            textEl.innerText = newTitle;
                            loadHistory();
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            };

            input.onkeydown = (e) => {
                e.stopPropagation();
                if (e.key === 'Enter') finish(true);
                if (e.key === 'Escape') finish(false);
            };
            input.onblur = () => finish(true);
        }

        async function deleteChat(id) {
            if (!confirm("Delete this analysis?")) return;
            try {
                const res = await fetch(`/analytics/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (res.ok) {
                    if (currentChatId == id) startNewChat();
                    else loadHistory();
                }
            } catch (e) {
                console.error(e);
            }
        }

        function startNewChat() {
            currentChatId = null;
            window.history.pushState({}, '', '/analytics');
            location.reload();
        }

        async function loadChat(id) {
            currentChatId = id;
            const url = new URL(window.location);
            url.searchParams.set('chat_id', id);
            window.history.pushState({}, '', url);

            loadHistory();
            if (window.innerWidth < 768) toggleSidebar();

            const welcome = document.getElementById('welcome-section');
            if (welcome) welcome.remove();

            messagesWrapper.innerHTML =
                '<div class="py-20 text-center text-gray-400 animate-pulse">Loading mission data...</div>';

            try {
                const res = await fetch(`/analytics/history/${id}`);
                const messages = await res.json();
                messagesWrapper.innerHTML = '';
                messages.filter(msg => msg.content && msg.content.trim() !== '').forEach(msg => appendMessage(msg
                    .role === 'user' ? 'user' : 'ai', msg.content, false));
                scrollToBottom();
            } catch (e) {
                showToast("Failed to load mission data");
            }
        }

        async function handleSubmit(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            const welcome = document.getElementById('welcome-section');
            if (welcome) welcome.remove();

            appendMessage('user', message);
            messageInput.value = '';
            messageInput.style.height = 'auto';

            const loadingId = showLoading();

            try {
                const res = await fetch('/analytics/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message,
                        chat_id: currentChatId
                    })
                });
                const data = await res.json();
                removeLoading(loadingId);

                if (res.ok) {
                    appendMessage('ai', data.content);
                    if (data.chat_id && !currentChatId) {
                        currentChatId = data.chat_id;
                        const url = new URL(window.location);
                        url.searchParams.set('chat_id', data.chat_id);
                        window.history.pushState({}, '', url);
                        loadHistory();
                    }
                } else {
                    appendMessage('ai', `**Error:** ${data.message || 'Mission failure.'}`);
                }
            } catch (err) {
                removeLoading(loadingId);
                showToast("Neutralizing link failed - connection error");
            }
        }

        function appendMessage(role, content, animate = true) {
            const isAi = role === 'ai';
            const div = document.createElement('div');
            div.className =
                `flex group ${!isAi ? 'justify-end' : 'justify-start'} ${animate ? 'opacity-0 translate-y-4 transition-all duration-300' : ''}`;

            const bubbleClass = !isAi ? 'chat-bubble-user px-6 py-4 max-w-[85%]' : 'chat-bubble-ai px-6 py-5 max-w-[95%]';
            const proseClass = !isAi ? 'text-gray-800' : 'prose prose-indigo prose-sm md:prose-base text-gray-800';

            div.innerHTML = `
                <div class="${bubbleClass}">
                    <div class="${proseClass}">
                        ${isAi ? marked.parse(content) : content.replace(/\n/g, '<br>')}
                    </div>
                </div>
            `;

            messagesWrapper.appendChild(div);
            if (animate) {
                void div.offsetWidth;
                div.classList.remove('opacity-0', 'translate-y-4');
            }
            scrollToBottom();
        }

        function showLoading() {
            const id = 'loading-' + Date.now();
            const div = document.createElement('div');
            div.id = id;
            div.className = 'flex justify-start';
            div.innerHTML =
                `<div class="chat-bubble-ai px-8 py-5 flex items-center h-16"><div class="dot-flashing"></div></div>`;
            messagesWrapper.appendChild(div);
            scrollToBottom();
            return id;
        }

        function removeLoading(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function scrollToBottom() {
            chatContainer.scrollTo({
                top: chatContainer.scrollHeight,
                behavior: 'smooth'
            });
        }

        function showToast(message, type = 'error') {
            const toast = document.createElement('div');
            const bg = type === 'error' ? 'bg-red-600' : 'bg-green-600';
            toast.className = `${bg} text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 animate-slide-in`;
            toast.innerHTML = `<span>${message}</span>`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        window.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const chatId = params.get('chat_id');
            if (chatId) loadChat(chatId);
            loadHistory();
        });
    </script>
</body>

</html>
