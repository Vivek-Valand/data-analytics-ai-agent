@extends('layouts.app')

@section('title', 'Register - Data Analytics Agent')

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
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Create Account</h2>
                    <p class="mt-3 text-gray-500 font-medium">Join our mission-driven analytics platform</p>
                </div>

                <form action="{{ route('register') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all bg-white"
                            placeholder="John Doe">
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all bg-white"
                            placeholder="john@example.com">
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all bg-white"
                            placeholder="Test@12345678">
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all bg-white"
                            placeholder="••••••••">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-8 rounded-2xl transition-all shadow-lg shadow-indigo-100 active:scale-[0.98] transform">
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center border-t border-gray-100 pt-6">
                    <p class="text-gray-600 font-medium">
                        Already have an account?
                        <a href="{{ route('login') }}"
                            class="text-indigo-600 hover:text-indigo-700 font-bold ml-1 transition-colors">Sign In</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hide sidebar on register page */
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
