<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — Dashboard</title>

    <!-- Google Fonts for Premium Pairings -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:ital,wght@0,400..700;1,400..700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles / Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
          profileOpen: {{ session('modal') === 'profile' ? 'true' : 'false' }}, 
          showNewGroupModal: false 
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
        </div>        <!-- ==================== USER BAR ==================== -->
        <div class="relative z-20">
            <div @click="profileOpen = true" 
                 class="w-full flex items-center justify-between p-3.5 bg-white border border-[#1A103C]/10 rounded-2xl shadow-md cursor-pointer hover:bg-slate-50 transition transform active:scale-99 animate-fade-in">
                <div class="flex items-center gap-3">
                    <!-- User Avatar -->
                    @if(Auth::user()->profile_photo_path && File::exists(public_path(Auth::user()->profile_photo_path)))
                        <img src="{{ asset(Auth::user()->profile_photo_path) }}" class="h-9 w-9 rounded-full object-cover border border-[#6C3AF4]/10 shadow">
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
                     <h3 class="heading-font text-lg font-extrabold text-[#1A103C]">My Account</h3>
                 </div>
                 <form method="POST" action="{{ route('logout') }}">
                     @csrf
                     <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 transition outline-none">
                         Sign Out
                     </button>
                 </form>
             </div>

             <!-- Form for profile, avatar and password update -->
             <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-5 flex-grow">
                 @csrf

                 <!-- Avatar Upload Section -->
                 <div class="flex flex-col items-center gap-3">
                     <div class="relative group">
                         @if(Auth::user()->profile_photo_path && File::exists(public_path(Auth::user()->profile_photo_path)))
                             <img src="{{ asset(Auth::user()->profile_photo_path) }}" class="h-20 w-20 rounded-full object-cover border-2 border-[#6C3AF4]/10 shadow-md">
                         @else
                             <div class="h-20 w-20 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-2xl shadow-md uppercase">
                                 {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                             </div>
                         @endif
                         <!-- Camera Upload Indicator overlay -->
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
    <main class="flex-grow flex flex-col overflow-y-auto px-8 py-8 md:px-12 w-full max-w-full">
        
        <!-- ==================== HEADER ROW ==================== -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <!-- Greeting & Subtext -->
            <div>
                <h1 class="heading-serif text-3xl md:text-[2.1rem] font-bold text-[#1A103C]">
                    Good morning, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                </h1>
                <p class="text-slate-500 font-medium text-sm mt-1">Here's what's happening with your balances today.</p>
            </div>

            <!-- Search, Notifications & CTA -->
            <div class="flex items-center gap-4 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative w-full md:w-56">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z"></path>
                        </svg>
                    </span>
                    <input type="text" placeholder="Search anything..." 
                           class="block w-full pl-10 pr-4 py-2 bg-white border border-slate-200/80 rounded-full text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/10 focus:border-[#6C3AF4]/60 transition text-sm">
                </div>

                <!-- Notification Bell -->
                <button class="p-2.5 bg-white border border-slate-200/80 hover:bg-slate-50 text-[#1A103C]/80 rounded-full shadow-sm transition outline-none">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.7c0 2.01-.76 3.99-2.2 5.58a23.848 23.848 0 005.454 1.31m5.714 0a3 3 0 11-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
                    </svg>
                </button>

                <!-- New Group CTA Button -->
                <button @click="showNewGroupModal = true" 
                        class="flex-shrink-0 px-6 py-2.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#592BD4] hover:to-[#9E1CE0] text-white rounded-2xl font-bold text-sm shadow-lg shadow-purple-500/10 transition transform active:scale-97 flex items-center gap-1.5 outline-none whitespace-nowrap">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    New Group
                </button>
            </div>
        </header>

        <!-- ==================== SUMMARY CARDS ROW ==================== -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- 1. YOU OWE -->
            <div class="bg-[#FFF0F0] border border-red-200/30 rounded-3xl p-6 flex justify-between items-start shadow-sm shadow-red-950/2">
                <div class="flex flex-col gap-1">
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-wider">You owe</span>
                    <h3 class="heading-font text-3xl font-extrabold text-[#E63946] mt-1.5">
                        Rs. {{ number_format($youOwe, 2) }}
                    </h3>
                    <span class="text-xs text-slate-500 font-semibold mt-2.5">Across {{ $groupsYouOweCount }} {{ Str::plural('group', $groupsYouOweCount) }}</span>
                </div>
                <!-- Red Diagonal Arrow -->
                <div class="h-10 w-10 bg-red-100 flex items-center justify-center rounded-full text-[#E63946]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
                    </svg>
                </div>
            </div>

            <!-- 2. YOU ARE OWED -->
            <div class="bg-[#F0FFF4] border border-emerald-200/30 rounded-3xl p-6 flex justify-between items-start shadow-sm shadow-emerald-950/2">
                <div class="flex flex-col gap-1">
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-wider">You are owed</span>
                    <h3 class="heading-font text-3xl font-extrabold text-[#10B981] mt-1.5">
                        Rs. {{ number_format($youAreOwed, 2) }}
                    </h3>
                    <span class="text-xs text-slate-500 font-semibold mt-2.5">Across {{ $groupsYouAreOwedCount }} {{ Str::plural('group', $groupsYouAreOwedCount) }}</span>
                </div>
                <!-- Green Down-Diagonal Arrow -->
                <div class="h-10 w-10 bg-emerald-100 flex items-center justify-center rounded-full text-[#10B981]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25"></path>
                    </svg>
                </div>
            </div>

            <!-- 3. EXCHANGE RATE & CHART -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 flex justify-between items-start shadow-sm">
                <div class="flex flex-col gap-1 w-2/3">
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-wider">Today's Dollar Rate</span>
                    <h3 class="heading-font text-xl md:text-[1.35rem] font-extrabold text-[#1A103C] mt-2.5">
                        1 USD = Rs. {{ number_format($exchangeRate, 2) }}
                    </h3>
                    <span class="text-xs text-slate-400 font-semibold mt-2.5">Updated just now</span>
                </div>
                
                <!-- Tiny Sparkline SVG Chart (Purple smooth graph wave) -->
                <div class="w-1/3 h-14 flex flex-col justify-end">
                    <svg class="w-full h-10" viewBox="0 0 100 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="40">
                                <stop offset="0%" stop-color="#6C3AF4" stop-opacity="0.3"></stop>
                                <stop offset="100%" stop-color="#6C3AF4" stop-opacity="0"></stop>
                            </linearGradient>
                        </defs>
                        <!-- Shaded area -->
                        <path d="M0 35 C15 32, 25 15, 45 22 C65 29, 75 10, 100 5 L100 40 L0 40 Z" fill="url(#chartGrad)"></path>
                        <!-- Line curve -->
                        <path d="M0 35 C15 32, 25 15, 45 22 C65 29, 75 10, 100 5" stroke="#6C3AF4" stroke-width="2.5" stroke-linecap="round"></path>
                    </svg>
                </div>
            </div>
        </section>

        <!-- ==================== DOUBLE-COLUMN SECTION ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: YOUR GROUPS -->
            <section class="lg:col-span-7 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="heading-font text-lg font-extrabold text-[#1A103C]">Your Groups</h2>
                    <a href="{{ route('groups.index') }}" class="text-[#6C3AF4] hover:text-[#592BD4] text-xs font-bold tracking-wide">View all</a>
                </div>

                @if($groups->isEmpty())
                    <div class="text-center py-12 text-slate-400">
                        <div class="text-4xl mb-3 text-slate-300">👥</div>
                        <p class="text-sm font-semibold">No groups yet.</p>
                        <a @click.prevent="showNewGroupModal = true" href="#" class="mt-2 inline-block text-xs font-bold text-[#6C3AF4] hover:underline">Create first group</a>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach($groups->take(5) as $group)
                            @php
                                $balance = $group->your_balance ?? 0;
                                // Choose avatar background gradient based on name string length
                                $gradients = [
                                    'from-orange-400 to-rose-500',   // sunrise
                                    'from-emerald-400 to-teal-500',  // emerald
                                    'from-purple-500 to-indigo-500', // purple
                                    'from-sky-400 to-blue-500',      // cyan
                                    'from-violet-500 to-fuchsia-500' // pink
                                ];
                                $grad = $gradients[strlen($group->name) % count($gradients)];
                            @endphp
                            <a href="{{ route('groups.show', $group) }}" 
                               class="flex items-center justify-between p-3.5 hover:bg-[#F8F9FD] border border-transparent hover:border-slate-100 rounded-2xl transition">
                                <div class="flex items-center gap-4">
                                    <!-- Beautiful Mockup Circular Avatar Gradient -->
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-tr {{ $grad }} flex items-center justify-center text-white font-extrabold text-lg shadow-sm">
                                        {{ strtoupper(substr($group->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-extrabold text-sm text-[#1A103C]">{{ $group->name }}</h3>
                                        <p class="text-xs font-bold mt-1">
                                            @if($balance > 0)
                                                <span class="text-[#10B981]">You are owed Rs. {{ number_format($balance, 2) }}</span>
                                            @elseif($balance < 0)
                                                <span class="text-[#E63946]">You owe Rs. {{ number_format(abs($balance), 2) }}</span>
                                            @else
                                                <span class="text-slate-400">All settled</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="text-right">
                                        <span class="text-xs font-bold text-[#1A103C]">Rs. {{ number_format($group->total_expenses, 2) }}</span>
                                        <p class="text-[10px] font-semibold text-slate-400">Total expenses</p>
                                    </div>
                                    <svg class="h-4.5 w-4.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- RIGHT COLUMN: EXPENSES & SUGGESTIONS -->
            <div class="lg:col-span-5 flex flex-col gap-6 w-full">
                
                <!-- 1. RECENT EXPENSES -->
                <section class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
                    <div class="flex justify-between items-center mb-5">
                        <h2 class="heading-font text-lg font-extrabold text-[#1A103C]">Recent Expenses</h2>
                        <a href="{{ route('activity') }}" class="text-[#6C3AF4] hover:text-[#592BD4] text-xs font-bold tracking-wide">View all</a>
                    </div>

                    @if($recentExpenses->isEmpty())
                        <div class="text-center py-6 text-slate-400">
                            <p class="text-xs font-semibold">No recent expenses.</p>
                        </div>
                    @else
                        <div class="flex flex-col gap-3.5">
                            @foreach($recentExpenses as $expense)
                                <div class="flex items-center justify-between py-1">
                                    <div class="flex items-center gap-3">
                                        <!-- Initials Avatar -->
                                        <div class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center text-[#1A103C] font-extrabold text-xs border border-slate-200/40">
                                            {{ strtoupper(substr($expense->paidBy->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-extrabold text-xs text-[#1A103C]">{{ $expense->title }}</h4>
                                            <p class="text-[10px] font-semibold text-slate-400 mt-0.5">
                                                Paid by {{ $expense->paid_by == Auth::id() ? 'You' : explode(' ', $expense->paidBy->name)[0] }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-xs font-bold text-[#1A103C]">Rs. {{ number_format($expense->amount, 2) }}</span>
                                        <p class="text-[10px] font-semibold text-slate-400 mt-0.5">{{ $expense->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <!-- 2. SMART SUGGESTIONS -->
                <section class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm">
                    <div class="flex flex-col mb-4">
                        <h2 class="heading-font text-lg font-extrabold text-[#1A103C]">Suggestions</h2>
                        <span class="text-[10px] font-semibold text-slate-400">Smart ways to settle up</span>
                    </div>

                    @if(empty($suggestions))
                        <div class="text-center py-6 text-slate-400">
                            <div class="text-2xl mb-1">🎉</div>
                            <p class="text-xs font-semibold">All settled up!</p>
                        </div>
                    @else
                        <div class="flex flex-col gap-4">
                            @foreach($suggestions as $sug)
                                @php 
                                    $isDebtor = ($sug['from']->id == Auth::id());
                                @endphp
                                <div class="flex items-center justify-between py-1 border-b border-slate-100/60 last:border-0 pb-3 last:pb-0">
                                    <!-- Settle Transfer Details -->
                                    <div class="flex items-center gap-2">
                                        <!-- From User Initials -->
                                        <div class="h-7 w-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-extrabold text-[10px] border border-slate-200/30">
                                            {{ strtoupper(substr($sug['from']->name, 0, 1)) }}
                                        </div>
                                        <span class="text-[10px] text-slate-400">→</span>
                                        <!-- To User Initials -->
                                        <div class="h-7 w-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-extrabold text-[10px] border border-slate-200/30">
                                            {{ strtoupper(substr($sug['to']->name, 0, 1)) }}
                                        </div>
                                    </div>

                                    <!-- Amount & Direction -->
                                    <div class="text-center flex-grow px-2">
                                        <span class="text-xs font-extrabold {{ $isDebtor ? 'text-[#E63946]' : 'text-[#10B981]' }}">
                                            Rs. {{ number_format($sug['amount'], 2) }}
                                        </span>
                                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-wide truncate max-w-[100px] mx-auto mt-0.5" title="{{ $sug['group_name'] }}">
                                            {{ $sug['group_name'] }}
                                        </p>
                                    </div>

                                    <!-- Settle Action Button (Links to Group Show details) -->
                                    <div>
                                        <a href="{{ route('groups.show', $sug['group_id']) }}" 
                                           class="px-4 py-1.5 rounded-full text-[10px] font-bold shadow-sm transition transform hover:scale-103 active:scale-97 outline-none
                                                  {{ $isDebtor ? 'bg-[#FFF0F0] text-[#E63946] border border-red-200/30 hover:bg-[#FFE3E3]' : 'bg-[#F0FFF4] text-[#10B981] border border-emerald-200/30 hover:bg-[#E3FFE9]' }}">
                                            Settle
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

            </div>

        </div>

    </main>

    <!-- ==================== NEW GROUP MODAL ==================== -->
    <div x-show="showNewGroupModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition
         style="display: none;">
        
        <div @click.away="showNewGroupModal = false"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-[#1A103C]/5"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="scale-95 opacity-0 translate-y-4"
             x-transition:enter-end="scale-100 opacity-100 translate-y-0">
            
            <form method="POST" action="{{ route('groups.store') }}">
                @csrf
                
                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-slate-50 to-white">
                    <div>
                        <h3 class="heading-font text-lg font-bold text-[#1A103C]">Create a New Group</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Start splitting expenses with friends.</p>
                    </div>
                    <button type="button" @click="showNewGroupModal = false" 
                            class="p-1.5 hover:bg-slate-100 rounded-full text-slate-400 hover:text-slate-600 transition outline-none">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <div class="p-6 flex flex-col gap-4">
                    <!-- Group Name -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Group Name</label>
                        <input type="text" 
                               name="name"
                               placeholder="e.g. Goa Trip, Apartment 4B" 
                               required
                               class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-[#1A103C] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/15 focus:border-[#6C3AF4]/60 transition text-sm">
                    </div>
                </div>

                <!-- Modal Footer Actions -->
                <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="showNewGroupModal = false" 
                            class="px-5 py-2.5 rounded-2xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 font-bold text-xs transition outline-none">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#592BD4] hover:to-[#9E1CE0] text-white rounded-2xl font-bold text-xs shadow-md shadow-purple-500/10 transition outline-none">
                        Create Group
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
