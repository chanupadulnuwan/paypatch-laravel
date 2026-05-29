<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — Email Verification</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center bg-gradient-to-tr from-[#F8F9FD] via-[#6C3AF4]/5 to-[#B026F3]/5 p-4 text-[#1A103C]">

    <!-- Floating Translucent verification container -->
    <div class="bg-white/80 backdrop-blur-2xl border border-white/50 shadow-2xl rounded-[2.5rem] w-full max-w-[420px] p-8 md:p-10 relative transition-all duration-300">
        
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('assets/logo.png') }}?v=3" class="h-10 mx-auto mb-5" alt="PayPatch Logo">
            <h2 class="heading-font text-2xl font-black tracking-tight text-[#1A103C]">2FA Verification Required</h2>
            <p class="text-[#8C8BA5] text-xs font-semibold mt-2.5 leading-relaxed">
                We have sent a 6-digit login verification PIN to your registered email address.
            </p>
        </div>

        <!-- Check email notice -->
        <div class="mb-6 p-3 bg-[#6C3AF4]/5 border border-[#6C3AF4]/10 rounded-2xl text-[10px] font-bold text-[#6C3AF4] flex items-start gap-2.5 leading-relaxed">
            <span class="text-xs">✉️</span>
            <span>A 6-digit verification code has been sent to your registered email inbox. Please enter the code below to verify your account and continue.</span>
        </div>

        <!-- Forms validation error -->
        @if ($errors->any())
            <div class="mb-5 p-3.5 bg-rose-50 border border-rose-100 rounded-2xl flex flex-col gap-1">
                @foreach ($errors->all() as $error)
                    <span class="text-[10px] text-rose-600 font-bold">{{ $error }}</span>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.two-factor.verify') }}" class="space-y-5">
            @csrf

            <!-- 6-digit code input -->
            <div>
                <label for="code" class="block text-[10px] font-black text-[#1A103C]/80 uppercase tracking-wider mb-2 ml-1">Enter 6-Digit PIN Code</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                        🔑
                    </span>
                    <input type="text" id="code" name="code" required maxlength="6" placeholder="e.g. 123456" autofocus autocomplete="one-time-code"
                           class="block w-full pl-11 pr-4 py-3.5 bg-[#F8F8FC] border border-slate-200/80 rounded-2xl text-center text-lg font-black tracking-[0.25em] text-[#1A103C] placeholder:tracking-normal placeholder:font-semibold placeholder:text-sm focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="block w-full py-4 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#6C3AF4]/95 hover:to-[#B026F3]/95 text-white font-extrabold rounded-2xl text-xs uppercase tracking-wider shadow shadow-[#6C3AF4]/25 transition transform active:scale-97 outline-none">
                Verify & Log In
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="/" class="text-[10px] font-black text-[#6C3AF4] uppercase tracking-wider hover:underline">
                ← Return to Landing Page
            </a>
        </div>

    </div>

</body>
</html>
