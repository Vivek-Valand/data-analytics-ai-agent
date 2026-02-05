<div id="welcome-section" class="py-12 text-center">
    <h2 class="text-4xl md:text-5xl font-bold gemini-gradient mb-4">Play With Your Data</h2>
    <p class="text-xl text-gray-600 mb-12">Ask for revenue trends, forecasts, or custom reports.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-left max-w-5xl mx-auto px-4">
        <!-- Recent Orders -->
        <button onclick="sendPromptAndHide('Show me the last 5 orders')"
            class="p-6 bg-white rounded-3xl hover:bg-indigo-50 transition-colors border border-gray-100 shadow-sm group">
            <div class="flex items-center gap-4">
                <span class="text-3xl">📦</span>
                <div>
                    <h3 class="font-semibold text-gray-900">Recent Orders</h3>
                    <p class="text-xs text-gray-500 mt-1">Get details of the latest bookings</p>
                </div>
            </div>
        </button>

        <!-- Availability -->
        <button onclick="sendPromptAndHide('Is the \'4 Feet Rectangular Table\' available?')"
            class="p-6 bg-white rounded-3xl hover:bg-indigo-50 transition-colors border border-gray-100 shadow-sm group">
            <div class="flex items-center gap-4">
                <span class="text-3xl">✅</span>
                <div>
                    <h3 class="font-semibold text-gray-900">Availability</h3>
                    <p class="text-xs text-gray-500 mt-1">Check stock for specific items</p>
                </div>
            </div>
        </button>

        <!-- Revenue Trends -->
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

        <!-- Future Prediction -->
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

        <!-- Excel Report -->
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

        <!-- Top Ranking -->
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
