@extends('layouts.app')

@section('title', 'Login - Data Analytics Agent')

@section('content')
    <div class="min-h-screen flex items-center justify-center p-6 bg-[#f0f2f5] w-full">
        <div class="max-w-md w-full">
            <div class="glass-card p-10 shadow-2xl animate-fade-in relative overflow-hidden">
                <!-- Decorative background element -->
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl"></div>

                <div class="text-center mb-10">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-600 text-white shadow-lg shadow-indigo-200 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Welcome Back</h2>
                    <p class="mt-3 text-gray-500 font-medium">Please enter your details to sign in</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all bg-white"
                            placeholder="example@mail.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-semibold text-gray-700">Password</label>
                        </div>
                        <input type="password" name="password" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all bg-white"
                            placeholder="••••••••">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="remember" class="ml-3 text-sm font-medium text-gray-600">Remember me</label>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-8 rounded-2xl transition-all shadow-lg shadow-indigo-100 active:scale-[0.98] transform">
                        Sign In
                    </button>
                </form>

                <div class="mt-10 text-center border-t border-gray-100 pt-8">
                    <p class="text-gray-600 font-medium">
                        Don't have an account?
                        <a href="{{ route('register') }}"
                            class="text-indigo-600 hover:text-indigo-700 font-bold ml-1 transition-colors">Create
                            Account</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hide sidebar on login page */
        #sidebar,
        #sidebar-overlay,
        #toggle-sidebar-mobile {
            display: none !important;
        }

        #main-content-area {
            margin-left: 0 !important;
            width: 100% !important;
        }
    </style>
@endsection
