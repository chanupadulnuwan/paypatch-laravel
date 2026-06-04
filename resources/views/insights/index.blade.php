<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — Personal Insights</title>

    <!-- Google Fonts for Premium Pairings -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400..700;1,400..700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles / Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js for premium analytics visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .heading-serif {
            font-family: 'Lora', serif;
        }
        .heading-font {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-[#F8F9FD] text-[#1A103C]" 
      x-data="{ 
          profileOpen: {{ session('modal') === 'profile' ? 'true' : 'false' }} 
      }">

    <!-- ==================== LEFT SIDEBAR ==================== -->
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
                   class="flex items-center gap-3.5 px-4 py-3 hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Groups -->
                <a href="{{ route('groups.index') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                    Groups
                </a>

                <!-- Friends -->
                <a href="{{ route('friends') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                    </svg>
                    Friends
                </a>

                <!-- Activity -->
                <a href="{{ route('activity') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                    </svg>
                    Activity
                </a>

                <!-- Insights -->
                <a href="{{ route('insights') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
                    @if(Auth::user()->profile_photo_path && File::exists(public_path(Auth::user()->profile_photo_path)))
                        <img src="{{ asset(Auth::user()->profile_photo_path) }}" class="h-9 w-9 rounded-full object-cover border border-[#6C3AF4]/10 shadow">
                    @else
                        <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shadow">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="font-extrabold text-sm text-[#1A103C]">{{ explode(' ', Auth::user()->name)[0] }}</span>
                </div>
            </div>
        </div>

        <!-- ==================== EDIT PROFILE DRAWER ==================== -->
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
                     <h3 class="heading-font text-lg font-extrabold text-[#1A103C]">My Account</h3>
                 </div>
                 <form method="POST" action="{{ route('logout') }}">
                     @csrf
                     <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 transition outline-none">
                         Sign Out
                     </button>
                 </form>
             </div>

             <!-- Form -->
             <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-5 flex-grow">
                 @csrf

                 <div class="flex flex-col items-center gap-3">
                     <div class="relative group">
                         @if(Auth::user()->profile_photo_path && File::exists(public_path(Auth::user()->profile_photo_path)))
                             <img src="{{ asset(Auth::user()->profile_photo_path) }}" class="h-20 w-20 rounded-full object-cover border-2 border-[#6C3AF4]/10 shadow-md">
                         @else
                             <div class="h-20 w-20 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-2xl shadow-md uppercase">
                                 {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                             </div>
                         @endif
                         <label class="absolute inset-0 bg-[#1A103C]/60 text-white rounded-full flex items-center justify-center cursor-pointer opacity-0 group-hover:opacity-100 transition duration-150">
                             <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"></path>
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"></path>
                             </svg>
                             <input type="file" name="profile_photo" class="hidden" accept="image/*">
                         </label>
                     </div>
                     <span class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">Change photo</span>
                 </div>

                 <!-- Inputs -->
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

                 <!-- Password -->
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

                 <button type="submit" 
                         class="mt-auto w-full py-2.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#592BD4] hover:to-[#9E1CE0] text-white rounded-xl font-bold text-xs shadow-md shadow-purple-500/10 transition transform active:scale-97">
                     Save Settings
                 </button>
             </form>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT WORKSPACE ==================== -->
    <main class="flex-grow flex flex-col overflow-y-auto px-8 py-8 md:px-12 w-full max-w-full">
        
        <!-- ==================== HEADER ROW ==================== -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="heading-serif text-3xl md:text-[2.1rem] font-bold text-[#1A103C]">
                    Personal Insights
                </h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Deep analysis of your spending splits, balances, and top networks.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-slate-500 bg-white border border-slate-200/80 px-4 py-2 rounded-full shadow-sm">
                    ✨ Personal Mode
                </span>
            </div>
        </header>

        <!-- ==================== SUMMARY CARDS ROW ==================== -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- 1. Total Paid Card -->
            <div class="bg-gradient-to-tr from-purple-500/5 to-purple-500/10 border border-purple-200/30 rounded-3xl p-6 flex justify-between items-start shadow-sm">
                <div class="flex flex-col gap-1">
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Cash Paid</span>
                    <h3 class="heading-font text-3xl font-extrabold text-[#6C3AF4] mt-1.5">
                        Rs. {{ number_format($totalPaid, 2) }}
                    </h3>
                    <span class="text-xs text-slate-400 font-semibold mt-2.5">Expenses you fully paid for</span>
                </div>
                <div class="h-10 w-10 bg-purple-100 flex items-center justify-center rounded-full text-[#6C3AF4]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.22.03a6.009 6.009 0 012.35 1.042c.83.626 1.43 1.523 1.73 2.546l.22.813c.094.347-.13.687-.488.758a.562.562 0 01-.634-.413l-.22-.813a4.877 4.877 0 00-1.405-2.07 4.887 4.887 0 00-1.91-1.008V11.23M12 6c-2.316.035-4.148 1.488-4.148 3.51 0 1.54 1.07 2.653 2.585 3.097v.032c1.782.44 2.87 1.516 2.87 3.361 0 2.05-1.921 3.52-4.307 3.52m4.307-10.428V3"></path>
                    </svg>
                </div>
            </div>

            <!-- 2. Total Cost / Share Card -->
            <div class="bg-gradient-to-tr from-indigo-500/5 to-indigo-500/10 border border-indigo-200/30 rounded-3xl p-6 flex justify-between items-start shadow-sm">
                <div class="flex flex-col gap-1">
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Share / Cost</span>
                    <h3 class="heading-font text-3xl font-extrabold text-[#4F46E5] mt-1.5">
                        Rs. {{ number_format($totalShare, 2) }}
                    </h3>
                    <span class="text-xs text-slate-400 font-semibold mt-2.5">Your absolute share of splits</span>
                </div>
                <div class="h-10 w-10 bg-indigo-100 flex items-center justify-center rounded-full text-[#4F46E5]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"></path>
                    </svg>
                </div>
            </div>

            <!-- 3. Net Balance Card -->
            @php
                $isOwed = $netBalance >= 0;
            @endphp
            <div class="rounded-3xl p-6 flex justify-between items-start shadow-sm border
                 {{ $isOwed ? 'bg-[#F0FFF4] border-emerald-200/30 text-emerald-900' : 'bg-[#FFF0F0] border-red-200/30 text-red-900' }}">
                <div class="flex flex-col gap-1">
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-wider">Net Balance</span>
                    <h3 class="heading-font text-3xl font-extrabold mt-1.5 {{ $isOwed ? 'text-[#10B981]' : 'text-[#E63946]' }}">
                        Rs. {{ number_format(abs($netBalance), 2) }}
                    </h3>
                    <span class="text-xs text-slate-400 font-semibold mt-2.5">
                        {{ $isOwed ? 'You are owed overall' : 'You owe overall across groups' }}
                    </span>
                </div>
                <div class="h-10 w-10 flex items-center justify-center rounded-full 
                     {{ $isOwed ? 'bg-emerald-100 text-[#10B981]' : 'bg-red-100 text-[#E63946]' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        @if($isOwed)
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25"></path>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
                        @endif
                    </svg>
                </div>
            </div>
        </section>

        <!-- ==================== ANALYTICS CHARTS GRID ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-8">
            <!-- Spending Trend line chart -->
            <section class="lg:col-span-7 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="heading-font text-base font-extrabold text-[#1A103C]">Monthly Spending Trend</h2>
                        <p class="text-[11px] text-slate-400 font-medium">Your monthly split cost shares over the last 6 months.</p>
                    </div>
                </div>
                <div class="relative w-full h-64">
                    <canvas id="spendingTrendChart"></canvas>
                </div>
            </section>

            <!-- Group breakdown doughnut chart -->
            <section class="lg:col-span-5 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="heading-font text-base font-extrabold text-[#1A103C]">Spending by Group</h2>
                        <p class="text-[11px] text-slate-400 font-medium">Cost shares divided by group splits.</p>
                    </div>
                </div>
                <div class="relative w-full h-64">
                    @if(empty($groupSpendingNames))
                        <div class="absolute inset-0 flex items-center justify-center text-slate-400 font-bold text-xs">
                            No group splits recorded yet.
                        </div>
                    @else
                        <canvas id="groupBreakdownChart"></canvas>
                    @endif
                </div>
            </section>
        </div>

        <!-- ==================== PEER BALANCES GRID ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Debtors (Who owes you) -->
            <section class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
                <h3 class="heading-font text-base font-bold text-[#1A103C] mb-4 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Who Owes You (Top Debtors)
                </h3>
                @if(empty($topDebtors))
                    <div class="text-center py-8 text-slate-400 text-xs font-semibold">
                        No one owes you money right now.
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach($topDebtors as $debtor)
                            <div class="flex items-center justify-between p-3.5 hover:bg-[#F8F9FD] rounded-2xl border border-slate-100 transition">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-emerald-400 to-teal-500 flex items-center justify-center text-white font-extrabold text-xs shadow-sm">
                                        {{ strtoupper(substr($debtor['user']->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-xs block text-[#1A103C]">{{ $debtor['user']->name }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">{{ $debtor['user']->email }}</span>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-[#10B981]">
                                    Rs. {{ number_format($debtor['balance'], 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- Creditors (Who you owe) -->
            <section class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
                <h3 class="heading-font text-base font-bold text-[#1A103C] mb-4 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                    Who You Owe (Top Creditors)
                </h3>
                @if(empty($topCreditors))
                    <div class="text-center py-8 text-slate-400 text-xs font-semibold">
                        You don't owe anyone money right now. 🎉
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach($topCreditors as $creditor)
                            <div class="flex items-center justify-between p-3.5 hover:bg-[#F8F9FD] rounded-2xl border border-slate-100 transition">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-rose-400 to-orange-500 flex items-center justify-center text-white font-extrabold text-xs shadow-sm">
                                        {{ strtoupper(substr($creditor['user']->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-xs block text-[#1A103C]">{{ $creditor['user']->name }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">{{ $creditor['user']->email }}</span>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-[#E63946]">
                                    Rs. {{ number_format($creditor['balance'], 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <!-- ==================== KEY RECORDS ROW ==================== -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Average split share -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center text-indigo-500 flex-shrink-0">
                    📊
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Average Split share</span>
                    <span class="heading-font text-lg font-extrabold text-[#1A103C] mt-1 block">Rs. {{ number_format($avgExpenseShare, 2) }}</span>
                    <span class="text-[9px] text-slate-400 font-semibold mt-0.5 block">Per split expense share</span>
                </div>
            </div>

            <!-- Most active group -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center text-purple-500 flex-shrink-0">
                    ⚡
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Most Active Group</span>
                    <span class="heading-font text-lg font-extrabold text-[#1A103C] mt-1 block truncate max-w-[180px]" title="{{ $mostActiveGroup->name ?? 'N/A' }}">
                        {{ $mostActiveGroup->name ?? 'None Yet' }}
                    </span>
                    <span class="text-[9px] text-slate-400 font-semibold mt-0.5 block">
                        {{ $mostActiveGroup ? $mostActiveGroup->expenses->count() . ' expenses registered' : 'No expenses recorded' }}
                    </span>
                </div>
            </div>

            <!-- Most expensive group -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center text-pink-500 flex-shrink-0">
                    💰
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Most Expensive Group</span>
                    <span class="heading-font text-lg font-extrabold text-[#1A103C] mt-1 block truncate max-w-[180px]" title="{{ $mostExpensiveGroup->name ?? 'N/A' }}">
                        {{ $mostExpensiveGroup->name ?? 'None Yet' }}
                    </span>
                    <span class="text-[9px] text-slate-400 font-semibold mt-0.5 block">
                        {{ $mostExpensiveGroup ? 'Rs. ' . number_format($mostExpensiveGroup->expenses->sum('amount'), 2) . ' total' : 'No expenses recorded' }}
                    </span>
                </div>
            </div>
        </section>

    </main>

    <!-- ==================== CHARTS SCRIPT ==================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart 1: Spending Trend over time (Line Chart)
            const ctx1 = document.getElementById('spendingTrendChart').getContext('2d');
            const fillGrad = ctx1.createLinearGradient(0, 0, 0, 240);
            fillGrad.addColorStop(0, 'rgba(108, 58, 244, 0.22)');
            fillGrad.addColorStop(1, 'rgba(108, 58, 244, 0.00)');

            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: @json($months),
                    datasets: [{
                        label: 'My Splits Cost',
                        data: @json($monthlySpending),
                        borderColor: '#6C3AF4',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6C3AF4',
                        pointBorderWidth: 2,
                        pointRadius: 4.5,
                        pointHoverRadius: 6.5,
                        tension: 0.38,
                        fill: true,
                        backgroundColor: fillGrad
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Plus Jakarta Sans', size: 9, weight: 'bold' }, color: '#8C8BA5' }
                        },
                        y: {
                            grid: { color: 'rgba(26, 16, 60, 0.05)', borderDash: [5, 5] },
                            ticks: { font: { family: 'Plus Jakarta Sans', size: 9, weight: 'bold' }, color: '#8C8BA5' }
                        }
                    }
                }
            });

            // Chart 2: Group breakdown (Doughnut Chart)
            const canvas2 = document.getElementById('groupBreakdownChart');
            if (canvas2) {
                const ctx2 = canvas2.getContext('2d');
                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: @json($groupSpendingNames),
                        datasets: [{
                            data: @json($groupSpendingTotals),
                            backgroundColor: ['#6C3AF4', '#B026F3', '#3B82F6', '#10B981', '#F59E0B'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: { family: 'Plus Jakarta Sans', size: 9, weight: 'bold' },
                                    boxWidth: 10,
                                    padding: 10
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
