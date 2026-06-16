<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — Share every moment, Settle every balance</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/logo.png') }}">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Split expenses, trips, dinners, and hangouts with friends. PayPatch keeps the money side fair.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">

    <!-- Styles / Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            font-optical-sizing: auto;
        }
        .heading-display {
            font-family: 'Fraunces', serif;
            font-optical-sizing: auto;
        }
        .heading-serif {
            font-family: 'Fraunces', serif;
            font-optical-sizing: auto;
        }
        .heading-font {
            font-family: 'Space Grotesk', sans-serif;
        }
        @keyframes letter-fade-in {
            0% {
                opacity: 0;
                transform: translateY(16px) scale(0.9);
                filter: blur(3px);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }
        .animated-letter {
            display: inline-block;
            opacity: 0;
            animation: letter-fade-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), color 0.3s;
            cursor: default;
        }
        .animated-letter:hover {
            transform: translateY(-8px) scale(1.18);
            color: #6C3AF4;
        }
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-up { animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
    </style>
</head>
@php
    $errorModal = '';
    if ($errors->any()) {
        $errorModal = old('name') || old('country') ? 'register' : 'login';
    }
    $initialModal = $defaultModal ?? $errorModal;
@endphp
<body class="h-full min-h-screen text-slate-800 antialiased overflow-x-hidden relative"
      x-data="{ 
          activeModal: '{{ $initialModal }}', 
          showPw: false, 
          showConfirmPw: false,
          closeModal() {
              this.activeModal = '';
              // If we are currently on the /login or /register page, navigate back to /
              if (window.location.pathname === '/login' || window.location.pathname === '/register') {
                  window.location.href = '/';
              }
          }
      }">

    <!-- FULL SCREEN BACKGROUND & MAIN WRAPPER -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-all duration-500 z-0 select-none"
         style="background-image: url('{{ asset('assets/auth-bg.jpg') }}?v=3');">
    </div>
    <!-- Light overlay for readability on light background -->
    <div class="absolute inset-0 bg-white/20 z-0 pointer-events-none"></div>

    <!-- MAIN INTERFACE CONTAINER WITH INLINE CSS BLUR TRANSITION -->
    <div class="relative z-10 flex flex-col min-h-screen w-full"
         :style="activeModal ? 'filter: blur(10px); transform: scale(0.985); pointer-events: none; transition: filter 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);' : 'transition: filter 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);'">
        
        <!-- HEADER / NAVIGATION -->
        <header class="w-full px-6 py-5 md:px-[1.5in] flex justify-between items-center max-w-none">
            <!-- LOGO -->
            <a href="/" class="flex items-center gap-2 outline-none">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 md:h-11 transition transform hover:scale-105 active:scale-95" alt="PayPatch Logo">
            </a>

            <!-- NAVIGATION CAPSULE -->
            <nav class="backdrop-blur-md bg-white/40 border border-white/50 rounded-full py-2.5 pl-12 pr-3 flex items-center gap-10 md:gap-14 shadow-lg shadow-purple-950/5">
                <a href="#" class="text-[#1A103C] hover:text-[#6C3AF4] transition-colors font-semibold text-sm hidden md:inline-block heading-font">Home</a>
                <a href="#" class="text-[#1A103C] hover:text-[#6C3AF4] transition-colors font-semibold text-sm hidden md:inline-block heading-font">About us</a>
                <a href="#" class="text-[#1A103C] hover:text-[#6C3AF4] transition-colors font-semibold text-sm hidden md:inline-block heading-font">Premium</a>

                <button @click="activeModal = 'login'"
                        class="px-7 py-2.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white rounded-full font-bold text-[11px] uppercase tracking-widest shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 heading-font">
                    Login
                </button>
            </nav>
        </header>

        <!-- LANDING HERO CONTENT -->
        <main class="flex-grow flex items-center w-full px-6 md:px-[1.5in] py-12 md:py-24 max-w-none">
            <div class="max-w-5xl text-left flex flex-col items-start gap-4 md:gap-6">
                <!-- HERO HEADING -->
                <h1 id="hero-title" class="heading-serif text-5xl md:text-7xl font-bold text-[#1A103C] leading-[1.15] tracking-tight">
                    Share every moment<br>
                    Settle every balance..
                </h1>

                <!-- HERO SUBTEXT -->
                <p class="text-[#1A103C]/80 text-base md:text-[1.1rem] font-medium max-w-lg leading-relaxed">
                    Plan trips, dinners, and hangouts together,<br>
                    while we keep the money side fair.
                </p>

                <!-- HERO CALL TO ACTION -->
                <div class="mt-2 animate-slide-up">
                    <button @click="activeModal = 'register'"
                            class="px-8 py-3.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white font-bold rounded-full text-[11px] uppercase tracking-widest shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 heading-font">
                        Start Splitting
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- BLUR BACKGROUND OVERLAY (DEEP VIOLET BLUR TINT) -->
    <div class="fixed inset-0 bg-[#1A103C]/40 backdrop-blur-sm z-40 transition-all duration-400"
         x-show="activeModal"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-sm"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 backdrop-blur-sm"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         @click="closeModal()"
         style="display: none;">
    </div>

    <!-- AUTH MODALS CONTAINER -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         x-show="activeModal"
         style="display: none;">
        
        <!-- ==================== LOGIN POPUP MODAL ==================== -->
        <div class="bg-white/85 backdrop-blur-2xl border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[485px] p-10 relative transition-all duration-400"
             x-show="activeModal === 'login'"
             x-transition:enter="transition ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-12 scale-[0.96]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-12 scale-[0.96]"
             @click.away="closeModal()">

            <!-- CLOSE BUTTON -->
            <button @click="closeModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-200/50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- MODAL HEADER -->
            <div class="text-center mb-6">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 mx-auto mb-4" alt="PayPatch Logo">
                <h2 class="heading-font text-2xl md:text-3xl font-extrabold text-[#1A103C] tracking-tight">Welcome Back</h2>
                <p class="text-[#5E5873] text-[0.85rem] font-medium mt-1 leading-normal">Log in to continue splitting the good times.</p>
            </div>

            <!-- SERVER VALIDATION ERRORS -->
            @if ($errors->any() && $initialModal === 'login')
                <div class="mb-4 text-xs text-red-600 bg-red-50/70 p-3 rounded-2xl border border-red-200/50">
                    <ul class="list-disc list-inside space-y-0.5 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- LOGIN FORM -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- EMAIL FIELD -->
                <div>
                    <label for="login-email" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </span>
                        <input id="login-email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="you@example.com"
                               class="block w-full pl-11 pr-4 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>
                </div>

                <!-- PASSWORD FIELD -->
                <div>
                    <label for="login-password" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <input id="login-password" :type="showPw ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               placeholder="••••••••••••"
                               class="block w-full pl-11 pr-12 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                        
                        <button type="button" @click="showPw = !showPw" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#8C8BA5] hover:text-[#1A103C] outline-none">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="!showPw">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="showPw" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="flex items-center justify-between py-0.5">
                    <label for="remember_me" class="flex items-center select-none cursor-pointer">
                        <input id="remember_me" name="remember" type="checkbox" checked
                               class="rounded border-slate-300/80 text-[#6C3AF4] focus:ring-[#6C3AF4]/20 focus:ring-offset-0 h-4 w-4 bg-[#F8F8FC]">
                        <span class="ml-2 text-xs font-semibold text-slate-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-[#6C3AF4] hover:text-[#592BD4] transition">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit"
                        class="block w-full py-3.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white font-bold rounded-full shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 text-[11px] uppercase tracking-widest outline-none heading-font">
                    Log In
                </button>
            </form>

            <!-- SEPARATOR -->
            <div class="relative my-4.5 flex items-center">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="flex-shrink mx-4 text-xs font-bold text-slate-400 uppercase tracking-widest">or</span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            <!-- GOOGLE SIGN IN BUTTON -->
            <a href="{{ route('auth.google') }}" 
                    class="w-full flex items-center justify-center gap-3 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-750 font-bold rounded-xl shadow-sm transition transform active:scale-98 text-sm outline-none">
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                    <path fill="#EA4335" d="M12 5.04c1.67 0 3.2.58 4.39 1.71l3.27-3.27C17.68 1.54 14.99 1 12 1 7.35 1 3.39 3.65 1.5 7.56l3.85 2.99C6.27 7.55 8.91 5.04 12 5.04z"></path>
                    <path fill="#4285F4" d="M23.49 12.27c0-.81-.07-1.59-.2-2.36H12v4.51h6.46c-.29 1.48-1.14 2.73-2.42 3.57l3.77 2.92c2.2-2.03 3.68-5.01 3.68-8.64z"></path>
                    <path fill="#FBBC05" d="M5.35 10.55c-.24-.72-.38-1.49-.38-2.29s.14-1.57.38-2.29L1.5 2.98C.54 4.9.01 7.07.01 9.35c0 2.28.53 4.45 1.49 6.37l3.85-3.17z"></path>
                    <path fill="#34A853" d="M12 23c3.24 0 5.97-1.07 7.96-2.91l-3.77-2.92c-1.08.72-2.45 1.16-4.19 1.16-3.09 0-5.73-2.51-6.65-5.51l-3.85 2.99C3.39 20.35 7.35 23 12 23z"></path>
                </svg>
                Sign in with Google
            </a>

            <!-- FOOTER -->
            <div class="text-center mt-5">
                <p class="text-[#5E5873] text-[0.85rem] font-semibold">
                    Don't have an account? 
                    <a href="#" @click.prevent="activeModal = 'register'" class="text-[#6C3AF4] hover:text-[#592BD4] font-extrabold transition ml-0.5">Sign up</a>
                </p>
            </div>

        </div>

        <!-- ==================== REGISTRATION POPUP MODAL ==================== -->
        <div class="bg-white/85 backdrop-blur-2xl border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[485px] p-10 relative transition-all duration-400"
             x-show="activeModal === 'register'"
             x-transition:enter="transition ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-12 scale-[0.96]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-12 scale-[0.96]"
             @click.away="closeModal()">

            <!-- CLOSE BUTTON -->
            <button @click="closeModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-200/50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- MODAL HEADER -->
            <div class="text-center mb-5">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 mx-auto mb-4" alt="PayPatch Logo">
                <h2 class="heading-font text-2xl md:text-3xl font-extrabold text-[#1A103C] tracking-tight">Create Account</h2>
                <p class="text-[#5E5873] text-[0.85rem] font-medium mt-1 leading-normal">Join PayPatch and start splitting with friends.</p>
            </div>

            <!-- SERVER VALIDATION ERRORS -->
            @if ($errors->any() && $initialModal === 'register')
                <div class="mb-4 text-xs text-red-600 bg-red-50/70 p-3 rounded-2xl border border-red-200/50">
                    <ul class="list-disc list-inside space-y-0.5 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- REGISTER FORM -->
            <form method="POST" action="{{ route('register') }}" class="space-y-3">
                @csrf

                <!-- NAME FIELD -->
                <div>
                    <label for="reg-name" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1 ml-1">Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        <input id="reg-name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               placeholder="Enter your full name"
                               class="block w-full pl-11 pr-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>
                </div>

                <!-- EMAIL FIELD -->
                <div>
                    <label for="reg-email" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1 ml-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </span>
                        <input id="reg-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                               placeholder="you@example.com"
                               class="block w-full pl-11 pr-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>
                </div>

                <!-- COUNTRY FIELD -->
                <div>
                    <label for="reg-country" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1 ml-1">Country</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2m-4-7a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                        <select id="reg-country" name="country" required
                                class="block w-full pl-11 pr-10 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm appearance-none cursor-pointer">
                            <option value="" disabled {{ old('country') ? '' : 'selected' }}>Select country</option>
                            <option value="United States" {{ old('country') === 'United States' ? 'selected' : '' }}>United States</option>
                            <option value="United Kingdom" {{ old('country') === 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="Canada" {{ old('country') === 'Canada' ? 'selected' : '' }}>Canada</option>
                            <option value="Australia" {{ old('country') === 'Australia' ? 'selected' : '' }}>Australia</option>
                            <option value="Singapore" {{ old('country') === 'Singapore' ? 'selected' : '' }}>Singapore</option>
                            <option value="Sri Lanka" {{ old('country') === 'Sri Lanka' ? 'selected' : '' }}>Sri Lanka</option>
                            <option value="India" {{ old('country') === 'India' ? 'selected' : '' }}>India</option>
                            <option value="Germany" {{ old('country') === 'Germany' ? 'selected' : '' }}>Germany</option>
                            <option value="France" {{ old('country') === 'France' ? 'selected' : '' }}>France</option>
                            <option value="Japan" {{ old('country') === 'Japan' ? 'selected' : '' }}>Japan</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- PASSWORD FIELD -->
                <div>
                    <label for="reg-password" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1 ml-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <input id="reg-password" :type="showPw ? 'text' : 'password'" name="password" required autocomplete="new-password"
                               placeholder="Create a password"
                               class="block w-full pl-11 pr-12 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                        
                        <button type="button" @click="showPw = !showPw" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#8C8BA5] hover:text-[#1A103C] outline-none">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="!showPw">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="showPw" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- CONFIRM PASSWORD FIELD -->
                <div>
                    <label for="reg-password-confirm" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1 ml-1">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <input id="reg-password-confirm" :type="showConfirmPw ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                               placeholder="Confirm your password"
                               class="block w-full pl-11 pr-12 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                        
                        <button type="button" @click="showConfirmPw = !showConfirmPw" 
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[#8C8BA5] hover:text-[#1A103C] outline-none">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="!showConfirmPw">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-show="showConfirmPw" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- REGISTER BUTTON -->
                <button type="submit"
                        class="block w-full py-3.5 mt-4 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white font-bold rounded-full shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 text-[11px] uppercase tracking-widest outline-none heading-font">
                    Register
                </button>

                <!-- SEPARATOR -->
                <div class="relative my-4 flex items-center">
                    <div class="flex-grow border-t border-slate-200"></div>
                    <span class="flex-shrink mx-4 text-xs font-bold text-slate-400 uppercase tracking-widest">or</span>
                    <div class="flex-grow border-t border-slate-200"></div>
                </div>

                <!-- GOOGLE SIGN UP BUTTON -->
                <a href="{{ route('auth.google') }}" 
                   class="w-full flex items-center justify-center gap-3 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-extrabold rounded-xl shadow-sm transition transform active:scale-98 text-sm outline-none">
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path fill="#EA4335" d="M12 5.04c1.67 0 3.2.58 4.39 1.71l3.27-3.27C17.68 1.54 14.99 1 12 1 7.35 1 3.39 3.65 1.5 7.56l3.85 2.99C6.27 7.55 8.91 5.04 12 5.04z"></path>
                        <path fill="#4285F4" d="M23.49 12.27c0-.81-.07-1.59-.2-2.36H12v4.51h6.46c-.29 1.48-1.14 2.73-2.42 3.57l3.77 2.92c2.2-2.03 3.68-5.01 3.68-8.64z"></path>
                        <path fill="#FBBC05" d="M5.35 10.55c-.24-.72-.38-1.49-.38-2.29s.14-1.57.38-2.29L1.5 2.98C.54 4.9.01 7.07.01 9.35c0 2.28.53 4.45 1.49 6.37l3.85-3.17z"></path>
                        <path fill="#34A853" d="M12 23c3.24 0 5.97-1.07 7.96-2.91l-3.77-2.92c-1.08.72-2.45 1.16-4.19 1.16-3.09 0-5.73-2.51-6.65-5.51l-3.85 2.99C3.39 20.35 7.35 23 12 23z"></path>
                    </svg>
                    Sign up with Google
                </a>
            </form>

            <!-- FOOTER -->
            <div class="text-center mt-5">
                <p class="text-[#5E5873] text-[0.85rem] font-semibold">
                    Already have an account? 
                    <a href="#" @click.prevent="activeModal = 'login'" class="text-[#6C3AF4] hover:text-[#592BD4] font-extrabold transition ml-0.5">Back to login</a>
                </p>
            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const titleEl = document.getElementById('hero-title');
            if (titleEl) {
                const lines = titleEl.innerHTML.split('<br>');
                let finalHtml = '';
                let globalDelay = 0;
                
                lines.forEach((line, lineIndex) => {
                    const trimmedLine = line.trim();
                    const chars = Array.from(trimmedLine);
                    chars.forEach((char) => {
                        if (char === ' ') {
                            finalHtml += '<span class="inline-block">&nbsp;</span>';
                        } else {
                            const delay = globalDelay * 0.03;
                            finalHtml += `<span class="animated-letter" style="animation-delay: ${delay}s">${char}</span>`;
                            globalDelay++;
                        }
                    });
                    if (lineIndex < lines.length - 1) {
                        finalHtml += '<br>';
                    }
                });
                titleEl.innerHTML = finalHtml;
            }
        });
    </script>
</body>
</html>
