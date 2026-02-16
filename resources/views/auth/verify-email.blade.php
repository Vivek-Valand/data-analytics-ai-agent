@extends('layouts.app')

@section('title', 'Verify Email - Data Analytics Agent')

@section('content')
    <div class="min-h-screen flex items-center justify-center p-6 bg-[#f0f2f5] w-full">
        <div class="max-w-md w-full">
            <div class="glass-card p-10 shadow-2xl animate-fade-in relative overflow-hidden text-center">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-indigo-50 text-indigo-600 mb-8 border-4 border-white shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>

                <h2 class="text-3xl font-bold text-gray-900 tracking-tight mb-4">Check Your Email</h2>
                <p class="text-gray-600 font-medium mb-8 leading-relaxed">
                    We've sent a verification link to your email address. Please click the link to verify your account and
                    continue.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div
                        class="mb-8 p-4 bg-green-50 rounded-2xl border border-green-100 text-green-700 text-sm font-semibold">
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" class="mb-6">
                    @csrf
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-8 rounded-2xl transition-all shadow-lg shadow-indigo-100 active:scale-[0.98] transform">
                        Resend Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-indigo-600 font-bold transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
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
