<div class="px-4 pb-24 pt-4 md:p-10 bg-transparent flex justify-center w-full">
    <div class="max-w-3xl w-full shadow-2xl rounded-3xl overflow-hidden glass-input pointer-events-auto">
        <form id="analytics-form" class="flex items-center gap-2 p-3">

            <!-- DB Selector -->
            <select id="db-selector"
                class="bg-transparent text-xs text-gray-500 focus:outline-none focus:ring-0 px-3 py-2 rounded-xl border border-gray-200">
                <option value="default">Default DB</option>
            </select>

            <!-- Message Input -->
            <textarea id="message-input" rows="1" placeholder="Enter an analysis prompt..."
                class="flex-1 bg-transparent border-none text-gray-800 focus:outline-none focus:ring-0 px-3 py-2 resize-none max-h-48"></textarea>

            <!-- Send Button -->
            <button type="submit" id="send-btn"
                class="p-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl transition-all shadow-md active:scale-95 disabled:opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                </svg>
            </button>

        </form>
    </div>
</div>
