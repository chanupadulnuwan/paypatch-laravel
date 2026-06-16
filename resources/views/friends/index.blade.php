<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — Friends & Balances</title>

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
        .heading-display { font-family: 'Fraunces', serif; font-optical-sizing: auto; }
        .heading-serif { font-family: 'Fraunces', serif; font-optical-sizing: auto; }
        .heading-font { font-family: 'Space Grotesk', sans-serif; }

        @keyframes slideUpFade { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-6px); } }
        .animate-slide-up { animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .animate-slide-up-delay-1 { animation: slideUpFade 0.6s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .animate-slide-up-delay-2 { animation: slideUpFade 0.6s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .animate-fade-in { animation: fadeIn 0.5s ease both; }
        .animate-scale-in { animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; }
        /* Card hover lift & glow */
        .card-lift { 
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.35s cubic-bezier(0.16, 1, 0.3, 1); 
        }
        .card-lift:hover { 
            transform: translateY(-5px) scale(1.01); 
            box-shadow: 0 20px 40px rgba(108, 58, 244, 0.12), 0 0 20px rgba(176, 38, 243, 0.08); 
            border-color: rgba(108, 58, 244, 0.25);
        }

        /* Button hover pop & glow */
        button, .btn-glow {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.25s ease, color 0.25s ease;
        }
        button:hover:not(:disabled), .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 58, 244, 0.15), 0 0 12px rgba(176, 38, 243, 0.1);
        }
        button:active:not(:disabled) {
            transform: translateY(0) scale(0.97);
        }

        .nav-underline { position: relative; }
        .nav-underline::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: #6C3AF4; transition: width 0.3s ease; border-radius: 2px; }
        .nav-underline:hover::after { width: 100%; }
    </style>
</head>
<body class="h-full flex overflow-hidden text-[#1A103C]"
      style="background-image: url('/assets/park-bg.png'); background-size: cover; background-position: center; background-attachment: fixed;" 
      x-data="{ 
          profileOpen: {{ session('modal') === 'profile' ? 'true' : 'false' }},
          searchQuery: '', 
          showInviteModal: false, 
          toastMessage: '', 
          toastVisible: false, 
          toastType: 'success',
          inviteName: '',
          inviteEmail: '',
          inviteMessage: 'Hey! Join me on PayPatch so we can easily split our bills and keep track of expenses together.',
          isSubmittingInvite: false
      }">

    <!-- Toast Notification Banner -->
    <div x-show="toastVisible"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-[-100%] opacity-0 md:translate-y-0 md:translate-x-[100%]"
         x-transition:enter-end="translate-y-0 opacity-100 md:translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100 md:translate-x-0"
         x-transition:leave-end="translate-y-[-100%] opacity-0 md:translate-y-0 md:translate-x-[100%]"
         class="fixed top-5 right-5 z-50 flex items-center gap-3 px-5 py-4 rounded-2xl shadow-xl border text-sm font-semibold max-w-sm"
         :class="toastType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800'"
         style="display: none;">
        <div class="h-6 w-6 rounded-full flex items-center justify-center text-xs font-bold"
             :class="toastType === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'">
            <span x-show="toastType === 'success'">✓</span>
            <span x-show="toastType === 'error'">⚠</span>
        </div>
        <span x-text="toastMessage"></span>
    </div>

    <!-- ==================== LEFT SIDEBAR (EXACT SAME NAV BAR) ==================== -->
    <aside class="w-72 flex-shrink-0 relative overflow-hidden bg-cover bg-center flex flex-col justify-between p-6 border-r border-[#1A103C]/5"
           style="background-image: url('{{ asset('assets/sidebar-bg.png') }}?v=3');">
        
        <!-- White/Translucent Gradient Top Overlay -->
        <div class="absolute inset-x-0 top-0 h-2/3 bg-gradient-to-b from-white via-white/95 to-white/40 pointer-events-none z-0"></div>

        <div class="relative z-10 w-full flex flex-col gap-10">
            <!-- LOGO -->
            <a href="/" class="flex items-center outline-none">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 transition transform hover:scale-103 active:scale-97" alt="PayPatch Logo">
            </a>

            <!-- SIDEBAR NAV ITEMS -->
            <nav class="flex flex-col gap-2.5">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 {{ Route::is('dashboard') ? 'bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold' : 'hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold' }} rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="{{ Route::is('dashboard') ? '2.5' : '2' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Groups -->
                <a href="{{ route('groups.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 {{ Route::is('groups.*') ? 'bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold' : 'hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold' }} rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="{{ Route::is('groups.*') ? '2.5' : '2' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                    Groups
                </a>

                <!-- Friends -->
                <a href="{{ route('friends') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 {{ Route::is('friends') ? 'bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold' : 'hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold' }} rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="{{ Route::is('friends') ? '2.5' : '2' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                    </svg>
                    Friends
                </a>

                <!-- Activity -->
                <a href="{{ route('activity') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 {{ Route::is('activity') ? 'bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold' : 'hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold' }} rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="{{ Route::is('activity') ? '2.5' : '2' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                    </svg>
                    Activity
                </a>

                <!-- Insights -->
                <a href="{{ route('insights') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 {{ Route::is('insights') ? 'bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold' : 'hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold' }} rounded-2xl text-sm transition relative">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="{{ Route::is('insights') ? '2.5' : '2' }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"></path>
                    </svg>
                    Insights
                </a>
            </nav>
        </div>

        <!-- ==================== USER BAR ==================== -->
        <div class="relative z-20">
            <div @click="profileOpen = true" 
                 class="w-full flex items-center justify-between p-3.5 bg-white border border-[#1A103C]/10 rounded-2xl shadow-md cursor-pointer hover:bg-slate-50 transition transform active:scale-99 animate-fade-in">
                <div class="flex items-center gap-3">
                    <!-- User Avatar -->
                    @if(Auth::user()->profile_photo_path)
                        <img src="{{ Auth::user()->profile_photo_url }}" class="h-9 w-9 rounded-full object-cover border border-[#6C3AF4]/10 shadow">
                    @else
                        <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <!-- User Name -->
                    <span class="font-extrabold text-sm text-[#1A103C]">{{ explode(' ', Auth::user()->name)[0] }}</span>
                </div>
            </div>
        </div>

        <!-- ==================== EDIT PROFILE DRAWER (ON NAV BAR) ==================== -->
        <div x-show="profileOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-250 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="absolute inset-0 bg-white shadow-2xl z-30 flex flex-col p-6 overflow-y-auto"
             style="display: none;">
             
             <!-- Drawer Header -->
             <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                 <div class="flex items-center gap-3">
                     <button @click="profileOpen = false" 
                             class="p-1.5 hover:bg-slate-100 rounded-full text-[#1A103C]/70 hover:text-[#1A103C] transition outline-none">
                         <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                         </svg>
                     </button>
                     <h3 class="heading-font text-lg font-extrabold text-[#1A103C] tracking-tight">My Account</h3>
                 </div>
                 <form method="POST" action="{{ route('logout') }}">
                     @csrf
                     <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 transition outline-none">
                         Sign Out
                     </button>
                 </form>
             </div>

             <!-- Form for profile, avatar and password update -->
             <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-5 flex-grow"
                   x-data="{
                       avatarPreview: null,
                       handleAvatarChange(event) {
                           const file = event.target.files[0];
                           if (file) {
                               this.avatarPreview = URL.createObjectURL(file);
                           }
                       }
                   }">
                 @csrf

                 <!-- Avatar Upload Section -->
                 <div class="flex flex-col items-center gap-3">
                     <div class="relative group">
                         <template x-if="avatarPreview">
                             <img :src="avatarPreview" class="h-20 w-20 rounded-full object-cover border-2 border-[#6C3AF4]/15 shadow-md">
                         </template>
                         <template x-if="!avatarPreview">
                              @if(Auth::user()->profile_photo_path)
                                  <img src="{{ Auth::user()->profile_photo_url }}" class="h-20 w-20 rounded-full object-cover border-2 border-[#6C3AF4]/10 shadow-md">
                              @else
                                 <div class="h-20 w-20 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-2xl shadow-md uppercase">
                                     {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                 </div>
                             @endif
                         </template>
                         <!-- Camera Upload Indicator overlay -->
                         <label class="absolute inset-0 bg-[#1A103C]/60 text-white rounded-full flex items-center justify-center cursor-pointer opacity-0 group-hover:opacity-100 transition duration-150">
                             <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"></path>
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"></path>
                             </svg>
                             <input type="file" name="profile_photo" class="hidden" accept="image/*" @change="handleAvatarChange">
                         </label>
                     </div>
                     <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Change photo</span>
                 </div>

                 <!-- Display errors from validation -->
                 @if ($errors->any() && session('modal') === 'profile')
                     <div class="p-3 bg-red-50 border border-red-100 rounded-xl flex flex-col gap-1">
                         @foreach ($errors->all() as $error)
                             <span class="text-[11px] text-red-600 font-semibold">{{ $error }}</span>
                         @endforeach
                     </div>
                 @endif

                 <!-- Profile inputs -->
                 <div class="flex flex-col gap-1.5">
                     <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Full Name</label>
                     <input type="text" name="name" value="{{ Auth::user()->name }}" required
                            class="block w-full px-3.5 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-sm text-[#1A103C] focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/15 focus:border-[#6C3AF4]/60 transition">
                 </div>

                 <div class="flex flex-col gap-1.5">
                     <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email Address</label>
                     <input type="email" name="email" value="{{ Auth::user()->email }}" required
                            class="block w-full px-3.5 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-sm text-[#1A103C] focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/15 focus:border-[#6C3AF4]/60 transition">
                 </div>

                 <!-- Password Section -->
                 <div class="mt-4 border-t border-slate-100 pt-4">
                     <h4 class="text-[11px] font-bold text-[#1A103C]/80 uppercase tracking-wider mb-3">Update Password</h4>
                     
                     <div class="flex flex-col gap-3">
                         <div class="flex flex-col gap-1.5">
                             <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Current Password</label>
                             <input type="password" name="current_password" placeholder="••••••••"
                                    class="block w-full px-3.5 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-sm text-[#1A103C] focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/15 focus:border-[#6C3AF4]/60 transition">
                         </div>

                         <div class="flex flex-col gap-1.5">
                             <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">New Password</label>
                             <input type="password" name="new_password" placeholder="••••••••"
                                    class="block w-full px-3.5 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-sm text-[#1A103C] focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/15 focus:border-[#6C3AF4]/60 transition">
                         </div>

                         <div class="flex flex-col gap-1.5">
                             <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Confirm New Password</label>
                             <input type="password" name="new_password_confirmation" placeholder="••••••••"
                                    class="block w-full px-3.5 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-sm text-[#1A103C] focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/15 focus:border-[#6C3AF4]/60 transition">
                         </div>
                     </div>
                 </div>

                 <!-- Plan & Billing Section -->
                 <div class="mt-4 border-t border-slate-100 pt-4" x-data="{ upgradeModalOpen: false }">
                     <h4 class="text-[11px] font-bold text-[#1A103C]/80 uppercase tracking-wider mb-2">Plan & Billing</h4>
                     
                     <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-100 rounded-xl shadow-sm">
                         <div>
                             <span class="text-[10px] font-bold text-slate-400 uppercase block">Current Tier</span>
                             <span class="text-xs font-extrabold text-[#1A103C] mt-0.5 block">
                                 {{ Auth::user()->account_type === 'premium' ? 'Premium Plan' : 'Free Tier' }}
                             </span>
                         </div>
                         <button type="button" @click="upgradeModalOpen = true" 
                                 class="px-3.5 py-1.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#592BD4] hover:to-[#9E1CE0] text-white rounded-xl font-bold text-[10px] shadow-md shadow-purple-500/10 transition transform active:scale-97 outline-none">
                             🚀 Upgrade
                         </button>
                     </div>

                     <!-- Upgrade Plan Popup Modal Overlay -->
                     <div x-show="upgradeModalOpen" 
                          class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                          x-transition
                          style="display: none;">
                         
                         <div @click.away="upgradeModalOpen = false"
                              class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-[#1A103C]/5 text-left p-6">
                             
                             <!-- Header -->
                             <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
                                 <div>
                                     <h3 class="heading-font text-base font-bold text-[#1A103C]">Select Plan</h3>
                                     <p class="text-[10px] text-slate-400 mt-0.5">Upgrade or downgrade your membership dynamically.</p>
                                 </div>
                                 <button type="button" @click="upgradeModalOpen = false" 
                                         class="p-1 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition outline-none">
                                     <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                     </svg>
                                 </button>
                             </div>

                             <!-- List of packages -->
                             <div class="flex flex-col gap-3.5 max-h-[320px] overflow-y-auto pr-1">
                                 @php
                                     $pkgs = \App\Models\Package::all();
                                 @endphp
                                 @forelse($pkgs as $p)
                                     <form method="POST" action="{{ route('profile.upgradePlan') }}">
                                         @csrf
                                         <input type="hidden" name="plan_name" value="{{ $p->name }}">
                                         <div class="border border-slate-200/80 rounded-2xl p-4 flex justify-between items-center hover:border-[#6C3AF4]/40 hover:bg-[#6C3AF4]/2 transition bg-white">
                                             <div>
                                                 <div class="flex items-center gap-1.5">
                                                     <span class="font-bold text-[#1A103C] text-xs">{{ $p->name }}</span>
                                                     @if(Auth::user()->account_type === 'premium' && ($p->name === 'Premium' || $p->name === 'Premium Plus'))
                                                         <span class="bg-[#10B981]/15 text-[#10B981] text-[7px] font-black uppercase px-2 py-0.5 rounded-full">Active</span>
                                                     @elseif(Auth::user()->account_type === 'free' && $p->name === 'Free Tier')
                                                         <span class="bg-[#10B981]/15 text-[#10B981] text-[7px] font-black uppercase px-2 py-0.5 rounded-full">Active</span>
                                                     @endif
                                                 </div>
                                                 <p class="text-[9px] text-slate-400 font-semibold mt-1">Limits: {{ $p->max_group_members }} members &bull; {{ $p->max_groups }} groups</p>
                                             </div>
                                             <button type="submit" 
                                                     class="px-4 py-1.5 bg-[#6C3AF4] hover:bg-[#592BD4] text-white rounded-lg font-bold text-[10px] shadow-sm transition outline-none">
                                                 Select
                                             </button>
                                         </div>
                                     </form>
                                 @empty
                                     @foreach(['Free Tier', 'Premium', 'Premium Plus'] as $pn)
                                         <form method="POST" action="{{ route('profile.upgradePlan') }}">
                                             @csrf
                                             <input type="hidden" name="plan_name" value="{{ $pn }}">
                                             <div class="border border-slate-200 rounded-2xl p-4 flex justify-between items-center hover:border-[#6C3AF4]/40 hover:bg-[#6C3AF4]/2 transition">
                                                 <div>
                                                     <span class="font-bold text-[#1A103C] text-xs">{{ $pn }}</span>
                                                 </div>
                                                 <button type="submit" class="px-4 py-1.5 bg-[#6C3AF4] text-white rounded-lg font-bold text-[10px]">Select</button>
                                             </div>
                                         </form>
                                     @endforeach
                                 @endforelse
                             </div>
                         </div>
                     </div>
                 </div>

                 <!-- Action Button -->
                 <button type="submit" 
                         class="mt-auto w-full py-2.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#592BD4] hover:to-[#9E1CE0] text-white rounded-xl font-bold text-xs shadow-md shadow-purple-500/10 transition transform active:scale-97">
                     Save Settings
                 </button>
             </form>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT WORKSPACE ==================== -->
    <main class="flex-grow flex flex-col overflow-y-auto px-8 py-8 md:px-12 w-full max-w-full bg-white/65 backdrop-blur-sm">
        
        <!-- ==================== HEADER ROW ==================== -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <!-- Greeting & Subtext -->
            <div>
                <h1 class="heading-serif text-3xl md:text-[2.1rem] font-bold text-[#1A103C] animate-slide-up">
                    Hello, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                </h1>
                <p class="text-slate-500 font-medium text-sm mt-1">See how your friends are doing and stay updated.</p>
            </div>

            <!-- Search, Notifications & CTA -->
            <div class="flex items-center gap-4 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative w-full md:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z"></path>
                        </svg>
                    </span>
                    <input type="text" 
                           placeholder="Search friends..." 
                           x-model="searchQuery"
                           class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200/80 rounded-full text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/10 focus:border-[#6C3AF4]/60 transition text-sm">
                </div>

                <!-- Notification Bell -->
                <button class="relative p-2.5 bg-white border border-slate-200/80 hover:bg-slate-50 text-[#1A103C]/80 rounded-full shadow-sm transition outline-none">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.7c0 2.01-.76 3.99-2.2 5.58a23.848 23.848 0 005.454 1.31m5.714 0a3 3 0 11-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
                    </svg>
                    <!-- Bell Badge -->
                    <span class="absolute -top-1 -right-1 h-5 w-5 bg-[#6C3AF4] text-white text-[10px] font-bold flex items-center justify-center rounded-full border border-white">3</span>
                </button>

                <!-- Invite Friends CTA -->
                <button @click="showInviteModal = true" 
                        class="flex-shrink-0 px-7 py-2.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white rounded-full font-bold text-[11px] uppercase tracking-widest shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 flex items-center gap-1.5 outline-none whitespace-nowrap heading-font">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Invite Friends
                </button>
            </div>
        </header>

        <!-- ==================== SUMMARY CARDS ROW ==================== -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- 1. YOU OWE -->
            <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-6 flex items-center gap-5 shadow-lg card-lift animate-slide-up">
                <!-- Red Arrow Circle -->
                <div class="h-14 w-14 bg-red-50 flex items-center justify-center rounded-full text-[#E63946] flex-shrink-0">
                    <svg class="h-6 w-6 transform rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-slate-400 text-xs font-semibold tracking-wide">You owe friends</span>
                    <h3 class="heading-font text-2xl font-extrabold text-[#E63946] mt-0.5">
                        Rs. {{ number_format($youOwe, 0) }}
                    </h3>
                    <span class="text-[11px] text-slate-400 font-semibold mt-0.5">Across {{ $friends->where('balance', '<', 0)->count() }} {{ Str::plural('friend', $friends->where('balance', '<', 0)->count()) }}</span>
                </div>
            </div>

            <!-- 2. YOU ARE OWED -->
            <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-6 flex items-center gap-5 shadow-lg card-lift animate-slide-up">
                <!-- Green Arrow Circle -->
                <div class="h-14 w-14 bg-emerald-50 flex items-center justify-center rounded-full text-[#10B981] flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-slate-400 text-xs font-semibold tracking-wide">You are owed</span>
                    <h3 class="heading-font text-2xl font-extrabold text-[#10B981] mt-0.5">
                        Rs. {{ number_format($youAreOwed, 0) }}
                    </h3>
                    <span class="text-[11px] text-slate-400 font-semibold mt-0.5">Across {{ $friends->where('balance', '>', 0)->count() }} {{ Str::plural('friend', $friends->where('balance', '>', 0)->count()) }}</span>
                </div>
            </div>

            <!-- 3. TOTAL FRIENDS -->
            <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-6 flex items-center gap-5 shadow-lg card-lift animate-slide-up">
                <!-- Purple User Icon -->
                <div class="h-14 w-14 bg-purple-50 flex items-center justify-center rounded-full text-[#6C3AF4] flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.947 11.947 0 0112 21c-2.67 0-5.12-.873-7.18-2.339l-.16-.11v-.1c0-1.113.285-2.16.786-3.07M11.25 15.75a3 3 0 100-6 3 3 0 000 6zm4.5 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm0-7.5a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-slate-400 text-xs font-semibold tracking-wide">Total friends</span>
                    <h3 class="heading-font text-2xl font-extrabold text-[#6C3AF4] mt-0.5">
                        {{ $totalFriends }}
                    </h3>
                    <span class="text-[11px] text-slate-400 font-semibold mt-0.5">Connected with you</span>
                </div>
            </div>
        </section>

        <!-- ==================== MAIN SECTION GRID ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- LEFT COLUMN: YOUR FRIENDS (SPAN 2) -->
            <div class="lg:col-span-2 bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl shadow-lg p-6 card-lift">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="heading-font text-xl font-bold text-[#1A103C]">Your Friends</h2>
                    
                    <!-- Sort Drodown Selector Mock -->
                    <div class="relative" x-data="{ sortOpen: false }">
                        <button @click="sortOpen = !sortOpen" 
                                class="text-xs font-bold text-slate-500 hover:text-[#6C3AF4] transition flex items-center gap-1 px-3 py-1.5 hover:bg-slate-50 rounded-xl border border-slate-100 outline-none">
                            Sort by: Name
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path>
                            </svg>
                        </button>
                        <div x-show="sortOpen" 
                             @click.away="sortOpen = false" 
                             x-transition
                             class="absolute right-0 mt-1.5 w-36 bg-white border border-slate-100 rounded-xl shadow-lg p-1.5 flex flex-col gap-1 z-30"
                             style="display: none;">
                            <button @click="sortOpen = false" class="text-left px-3 py-1.5 hover:bg-slate-50 rounded-lg text-xs font-semibold text-[#6C3AF4]">Name (A-Z)</button>
                            <button @click="sortOpen = false" class="text-left px-3 py-1.5 hover:bg-slate-50 rounded-lg text-xs font-semibold text-slate-600">Balance (High)</button>
                        </div>
                    </div>
                </div>

                <!-- Friends Rows List -->
                <div class="divide-y divide-slate-100">
                    @forelse($friends as $item)
                        <!-- Row -->
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between group hover:bg-slate-50/50 -mx-6 px-6 transition duration-200"
                             x-show="searchQuery === '' || '{{ strtolower($item['user']->name) }}'.includes(searchQuery.toLowerCase())">
                            
                            <div class="flex items-center gap-4">
                                <!-- Dynamic Gravatar / Pravatar -->
                                <img src="{{ $item['user']->profile_photo_url }}" 
                                     alt="{{ $item['user']->name }}" 
                                     class="h-11 w-11 rounded-full object-cover border border-slate-100 shadow-sm">

                                <div class="flex flex-col">
                                    <span class="font-bold text-[#1A103C] group-hover:text-[#6C3AF4] transition text-sm md:text-base">{{ $item['user']->name }}</span>
                                    <span class="text-xs text-slate-400 mt-0.5">
                                        @if($item['balance'] > 0)
                                            You are owed <span class="text-emerald-500 font-bold">Rs. {{ number_format($item['balance'], 2) }}</span>
                                        @elseif($item['balance'] < 0)
                                            You owe <span class="text-rose-500 font-bold">Rs. {{ number_format(abs($item['balance']), 2) }}</span>
                                        @else
                                            <span class="text-slate-400 font-medium">Settled up ✓</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Right End Arrow Indicator -->
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-slate-300 group-hover:text-[#6C3AF4] transition transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                                </svg>
                            </div>

                        </div>
                    @empty
                        <!-- Empty State -->
                        <div class="text-center py-16 text-slate-400">
                            <div class="text-6xl mb-4">🤝</div>
                            <h3 class="font-bold text-[#1A103C] text-lg">No shared balances yet</h3>
                            <p class="text-sm mt-1 text-slate-400 max-w-sm mx-auto">Create a group, add expenses, and your friends will appear here with calculated net balances.</p>
                            <button @click="showInviteModal = true" 
                                    class="mt-5 px-5 py-2 bg-[#6C3AF4] hover:bg-[#592BD4] text-white text-xs font-bold rounded-xl transition">
                                Invite a Friend
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- RIGHT COLUMN: ACTIVITY & SUGGESTIONS -->
            <div class="flex flex-col gap-8">
                
                <!-- CARD 1: FRIENDS ACTIVITY -->
                <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl shadow-lg p-6 card-lift">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="heading-font text-base md:text-lg font-bold text-[#1A103C]">Friends Activity</h2>
                        <a href="{{ route('activity') }}" class="text-xs font-bold text-[#6C3AF4] hover:underline">View all</a>
                    </div>

                    <div class="flex flex-col gap-5">
                        @forelse($recentExpenses as $expense)
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex gap-3">
                                    <!-- User Avatar with Tiny Status Badge overlay -->
                                    <div class="relative flex-shrink-0">
                                        <img src="{{ $expense->paidBy->profile_photo_url }}" 
                                             class="h-9 w-9 rounded-full object-cover border border-slate-100 shadow-sm"
                                             alt="{{ $expense->paidBy->name }}">
                                        
                                        <!-- Mini arrow badge overlay -->
                                        @if($expense->paid_by === Auth::id())
                                            <span class="absolute -bottom-1 -right-1 h-4 w-4 bg-emerald-500 rounded-full flex items-center justify-center text-white border border-white">
                                                <svg class="h-2 w-2" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
                                                </svg>
                                            </span>
                                        @else
                                            <span class="absolute -bottom-1 -right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-white border border-white">
                                                <svg class="h-2 w-2 transform rotate-180" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
                                                </svg>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Middle Details -->
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-[#1A103C]">
                                            {{ $expense->paid_by === Auth::id() ? 'You' : $expense->paidBy->name }} added an expense
                                        </span>
                                        <span class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">
                                            {{ $expense->title }} · {{ $expense->group->name }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Right Amount & Time -->
                                <div class="flex flex-col items-end flex-shrink-0 text-right">
                                    <span class="text-xs font-bold text-[#1A103C]">Rs. {{ number_format($expense->amount, 0) }}</span>
                                    <span class="text-[9px] text-slate-400 mt-0.5">{{ $expense->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <!-- Empty Activity -->
                            <div class="text-center py-8 text-slate-400 text-xs">
                                <p>No recent activity across your groups yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- CARD 2: SUGGESTIONS -->
                <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl shadow-lg p-6 card-lift">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="heading-font text-base md:text-lg font-bold text-[#1A103C]">Suggestions</h2>
                        <button @click="toastMessage = 'All recommendations are loaded below!'; toastType = 'success'; toastVisible = true; setTimeout(() => toastVisible = false, 3000);" 
                                class="text-xs font-bold text-[#6C3AF4] hover:underline outline-none">View all</button>
                    </div>

                    <div class="flex flex-col gap-4">
                        @php
                            $displaySuggestions = $suggestions;
                            if ($displaySuggestions->isEmpty()) {
                                // Fallback mock users to ensure high-fidelity display
                                $displaySuggestions = collect([
                                    (object) ['id' => 991, 'name' => 'Pasindu Madushan', 'email' => 'pasindu@paypatch.com'],
                                    (object) ['id' => 992, 'name' => 'Imeshi Fernando', 'email' => 'imeshi@paypatch.com'],
                                    (object) ['id' => 993, 'name' => 'Tharindu Silva', 'email' => 'tharindu@paypatch.com']
                                ]);
                            }
                        @endphp

                        @foreach($displaySuggestions as $user)
                            <div class="flex items-center justify-between gap-2" x-data="{ added: false }">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user instanceof \App\Models\User ? $user->profile_photo_url : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF' }}" 
                                         alt="{{ $user->name }}" 
                                         class="h-9 w-9 rounded-full object-cover border border-slate-100 shadow-sm">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-[#1A103C]">{{ $user->name }}</span>
                                        <span class="text-[10px] text-slate-400 mt-0.5">
                                            {{ ($user->id ?? 0) % 2 === 0 ? '5' : '3' }} mutual groups
                                        </span>
                                    </div>
                                </div>

                                <!-- Add Friend Interactive Button -->
                                <button @click="
                                            let btn = $el;
                                            btn.disabled = true;
                                            added = true;
                                            setTimeout(() => {
                                                toastMessage = 'Friend invitation sent to {{ $user->name }}!';
                                                toastType = 'success';
                                                toastVisible = true;
                                                setTimeout(() => toastVisible = false, 4000);
                                            }, 600);
                                        "
                                        :class="added ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'text-[#6C3AF4] border border-[#6C3AF4]/30 hover:bg-[#6C3AF4]/5'"
                                        class="flex-shrink-0 text-[10px] font-bold px-3 py-1.5 rounded-xl transition duration-150 outline-none select-none">
                                    <span x-show="!added">Add Friend</span>
                                    <span x-show="added" style="display: none;">Invited ✓</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- ==================== INVITE FRIENDS MODAL ==================== -->
    <div x-show="showInviteModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition
         style="display: none;">
        
        <div @click.away="showInviteModal = false"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all border border-[#1A103C]/5"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-slate-50 to-white">
                <div>
                    <h3 class="heading-font text-lg font-bold text-[#1A103C]">Invite Friends to PayPatch</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Grow your circle and split expenses seamlessly.</p>
                </div>
                <button @click="showInviteModal = false" 
                        class="p-1.5 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition outline-none">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body Form -->
            <div class="p-6 flex flex-col gap-4">
                <!-- Friend Name -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Friend's Name</label>
                    <input type="text" 
                           placeholder="Enter their full name" 
                           x-model="inviteName"
                           class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-[#1A103C] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/15 focus:border-[#6C3AF4]/60 transition text-sm">
                </div>

                <!-- Friend Email -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                    <input type="email" 
                           placeholder="friend@example.com" 
                           x-model="inviteEmail"
                           class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-[#1A103C] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/15 focus:border-[#6C3AF4]/60 transition text-sm">
                </div>

                <!-- Custom Message -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Invitation Message</label>
                    <textarea rows="3" 
                              x-model="inviteMessage"
                              class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-[#1A103C] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/15 focus:border-[#6C3AF4]/60 transition text-sm resize-none"></textarea>
                </div>
            </div>

            <!-- Modal Footer Actions -->
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-end gap-3">
                <button @click="showInviteModal = false" 
                        class="px-5 py-2.5 rounded-2xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 font-bold text-xs transition outline-none">
                    Cancel
                </button>
                <button @click="
                            if (!inviteEmail || !inviteName) {
                                toastMessage = 'Please enter both a name and valid email!';
                                toastType = 'error';
                                toastVisible = true;
                                setTimeout(() => toastVisible = false, 3000);
                                return;
                            }
                            isSubmittingInvite = true;
                            setTimeout(() => {
                                isSubmittingInvite = false;
                                showInviteModal = false;
                                toastMessage = 'Invitation sent successfully to ' + inviteEmail + '!';
                                toastType = 'success';
                                toastVisible = true;
                                inviteName = '';
                                inviteEmail = '';
                                setTimeout(() => toastVisible = false, 4000);
                            }, 1200);
                        "
                        class="px-6 py-2.5 bg-[#6C3AF4] hover:bg-[#592BD4] text-white rounded-2xl font-bold text-xs shadow-md shadow-purple-500/10 transition outline-none flex items-center gap-1.5 select-none">
                    <span x-show="!isSubmittingInvite">Send Invitation</span>
                    <!-- Spinning Loader -->
                    <span x-show="isSubmittingInvite" style="display: none;" class="flex items-center gap-1">
                        <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </div>

        </div>

    </div>

</body>
</html>
