<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Summarizer</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .spinner {
            border: 3px solid rgba(0, 0, 0, 0.1);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border-left-color: #ef4444;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="flex flex-col min-h-screen">

    <div class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-red-600 rounded flex items-center justify-center text-white font-bold">YT</div>
                <h1 class="text-xl font-bold text-gray-800">Summarizer</h1>
            </div>
            {{-- <a href="/event-kaboodles" class="text-gray-600 hover:text-gray-900 font-medium text-sm">Back to Chat</a> --}}
        </div>
    </div>

    <div class="flex-1 flex flex-col items-center justify-center p-4">

        <div class="w-full max-w-2xl text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Video to Insight in Seconds</h2>
            <p class="text-gray-500">Paste a YouTube or video URL below to get a comprehensive summary powered by AI.
            </p>
        </div>

        <div class="w-full max-w-2xl bg-white rounded-xl shadow-lg p-6 md:p-8">
            <form id="summarize-form" class="space-y-4">
                <div>
                    <label for="url" class="sr-only">Video URL</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z" />
                            </svg>
                        </div>
                        <input type="url" name="url" id="url"
                            class="focus:ring-red-500 focus:border-red-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3 border"
                            placeholder="https://www.youtube.com/watch?v=..." required>
                    </div>
                </div>
                <button type="submit" id="submit-btn"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    Summarize Video
                </button>
            </form>

            <div id="loading" class="hidden mt-6 text-center">
                <div class="flex justify-center items-center space-x-2">
                    <div class="spinner"></div>
                    <span class="text-gray-500 text-sm font-medium">Analyzing content... this may take a moment.</span>
                </div>
            </div>

            <div id="result" class="hidden mt-8 border-t border-gray-100 pt-6">
                <div class="prose prose-red max-w-none text-gray-800" id="result-content">
                    <!-- Summary content goes here -->
                </div>
                <div class="mt-6 flex justify-end">
                    <button onclick="copyToClipboard()"
                        class="text-sm text-gray-500 hover:text-gray-700 flex items-center space-x-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        <span>Copy Summary</span>
                    </button>
                </div>
            </div>

            <div id="error-msg"
                class="hidden mt-4 bg-red-50 text-red-700 p-3 rounded-md text-sm border border-red-100 text-center">
            </div>

        </div>

        {{-- <p class="mt-8 text-center text-xs text-gray-400">
            Powered by NeuronAI & Gemini. Not affiliated with YouTube.
        </p> --}}
    </div>

    <script>
        const form = document.getElementById('summarize-form');
        const loading = document.getElementById('loading');
        const result = document.getElementById('result');
        const resultContent = document.getElementById('result-content');
        const errorMsg = document.getElementById('error-msg');
        const submitBtn = document.getElementById('submit-btn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const url = document.getElementById('url').value;

            // Reset UI
            result.classList.add('hidden');
            errorMsg.classList.add('hidden');
            loading.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

            try {
                const response = await fetch('/youtube/summarize', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        url
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    resultContent.innerHTML = marked.parse(data.content);
                    result.classList.remove('hidden');
                } else {
                    errorMsg.textContent = data.message || 'Failed to generate summary.';
                    errorMsg.classList.remove('hidden');
                }
            } catch (error) {
                errorMsg.textContent = 'Network error. Please try again.';
                errorMsg.classList.remove('hidden');
            } finally {
                loading.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });

        function copyToClipboard() {
            const text = resultContent.innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied to clipboard!');
            });
        }
    </script>
</body>

</html>
