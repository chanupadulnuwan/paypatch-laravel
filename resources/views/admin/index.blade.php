<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — Admin Dashboard</title>

    <!-- Google Fonts — Fraunces · Space Grotesk · DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">

    <!-- Styles / Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js for beautiful client-side dynamic graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            font-optical-sizing: auto;
        }
        .heading-display { font-family: 'Fraunces', serif; font-optical-sizing: auto; }
        .heading-serif   { font-family: 'Fraunces', serif; font-optical-sizing: auto; }
        .heading-font    { font-family: 'Space Grotesk', sans-serif; }

        /* ── Animations ── */
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0);    }
        }
        @keyframes fadeIn {
            from { opacity: 0; } to { opacity: 1; }
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0);   }
        }
        .animate-slide-up          { animation: slideUpFade 0.55s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-slide-up-delay-1  { animation: slideUpFade 0.55s 0.08s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-slide-up-delay-2  { animation: slideUpFade 0.55s 0.16s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-slide-up-delay-3  { animation: slideUpFade 0.55s 0.24s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-slide-up-delay-4  { animation: slideUpFade 0.55s 0.32s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-fade-in           { animation: fadeIn 0.4s ease both; }
        .animate-count             { animation: countUp 0.7s 0.3s cubic-bezier(0.16,1,0.3,1) both; }

        /* Card hover lift */
        .card-lift { transition: all 0.28s cubic-bezier(0.16,1,0.3,1); }
        .card-lift:hover { transform: translateY(-3px); box-shadow: 0 16px 48px rgba(108,58,244,0.10); }

        /* Table row pulse on hover */
        .table-row-hover { transition: background 0.18s ease; }
        .table-row-hover:hover { background: rgba(108,58,244,0.03); }
    </style>
</head>
<body class="h-full flex overflow-hidden text-[#1A103C]"
      style="background-image: url('/assets/park-bg.png'); background-size: cover; background-position: center; background-attachment: fixed;"
      x-data="{
          profileOpen: false,
          selectedUserForView: null,
          showUserViewModal: false,
          showBanModal: false,
          banUserAction: '',
          banUserName: '',
          banReasonCategory: 'Violated community rules and terms of service',
          banCustomReason: '',
          showDeleteModal: false,
          deleteUserAction: '',
          deleteUserName: '',
          deleteReason: '',
          deleteConfirmText: ''
      }">

    <!-- ==================== LEFT SIDEBAR (EXACT SAME PREMIUM SIDEBAR) ==================== -->
    <aside class="w-72 flex-shrink-0 relative overflow-hidden bg-cover bg-center flex flex-col justify-between p-6 border-r border-[#1A103C]/5"
           style="background-image: url('{{ asset('assets/sidebar-bg.png') }}?v=3');">
        
        <!-- Translucent Overlay -->
        <div class="absolute inset-x-0 top-0 h-2/3 bg-gradient-to-b from-white via-white/95 to-white/40 pointer-events-none z-0"></div>

        <div class="relative z-10 w-full flex flex-col gap-10">
            <!-- LOGO -->
            <a href="{{ route('admin') }}" class="flex items-center outline-none">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 transition transform hover:scale-103 active:scale-97" alt="PayPatch Logo">
            </a>

            <!-- SIDEBAR NAV ITEMS (ADMIN FOCUS) -->
            <nav class="flex flex-col gap-2.5">
                <!-- Admin Dashboard -->
                <a href="{{ route('admin') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 {{ Route::is('admin') && !request()->is('admin/packages') && !request()->is('admin/activity') && !request()->is('admin/insights') ? 'bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold' : 'hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold' }} rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Admin Packages -->
                <a href="{{ route('admin.packages') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 {{ request()->is('admin/packages') ? 'bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold' : 'hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold' }} rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Packages
                </a>

                <!-- Admin Activity -->
                <a href="{{ route('admin.activity') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 {{ request()->is('admin/activity') ? 'bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold' : 'hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold' }} rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Activity
                </a>

                <!-- Admin Insights -->
                <a href="{{ route('admin.insights') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 {{ request()->is('admin/insights') ? 'bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold' : 'hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold' }} rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9-1V4a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
                    </svg>
                    Insights
                </a>
            </nav>
        </div>

        <!-- ==================== USER BAR ==================== -->
        <div class="relative z-20">
            <div @click="profileOpen = true" 
                 class="w-full flex items-center justify-between p-3.5 bg-white border border-[#1A103C]/10 rounded-2xl shadow-md cursor-pointer hover:bg-slate-50 transition transform active:scale-99">
                <div class="flex items-center gap-3">
                    @if(Auth::user()->profile_photo_path && File::exists(public_path(Auth::user()->profile_photo_path)))
                        <img src="{{ asset(Auth::user()->profile_photo_path) }}" class="h-9 w-9 rounded-full object-cover border border-[#6C3AF4]/10 shadow">
                    @else
                        <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-[#6C3AF4] to-[#B026F3] flex items-center justify-center text-white font-bold text-sm shadow">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="font-extrabold text-sm text-[#1A103C]">Admin {{ explode(' ', Auth::user()->name)[0] }}</span>
                </div>
            </div>
        </div>

        <!-- ==================== EDIT PROFILE DRAWER (ON SIDEBAR) ==================== -->
        <div x-show="profileOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-250 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="absolute inset-0 bg-white shadow-2xl z-30 flex flex-col p-6 overflow-y-auto"
             style="display: none;">
             
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
                  <div class="flex flex-col items-center gap-3">
                      <div class="relative group">
                          <template x-if="avatarPreview">
                              <img :src="avatarPreview" class="h-20 w-20 rounded-full object-cover border-2 border-[#6C3AF4]/15 shadow-md">
                          </template>
                          <template x-if="!avatarPreview">
                              @if(Auth::user()->profile_photo_path && File::exists(public_path(Auth::user()->profile_photo_path)))
                                  <img src="{{ asset(Auth::user()->profile_photo_path) }}" class="h-20 w-20 rounded-full object-cover border-2 border-[#6C3AF4]/10 shadow-md">
                              @else
                                  <div class="h-20 w-20 rounded-full bg-gradient-to-tr from-[#6C3AF4] to-[#B026F3] flex items-center justify-center text-white font-black text-2xl shadow-md border-2 border-white">
                                      {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                  </div>
                              @endif
                          </template>
                          <label class="absolute inset-0 flex items-center justify-center bg-black/40 text-white text-[10px] font-bold rounded-full opacity-0 group-hover:opacity-100 transition cursor-pointer">
                              Upload
                              <input type="file" name="profile_photo" class="hidden" accept="image/*" @change="handleAvatarChange">
                          </label>
                      </div>
                  </div>

                 <div>
                     <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Name</label>
                     <input type="text" name="name" value="{{ Auth::user()->name }}" required
                            class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm font-semibold text-slate-800">
                 </div>

                 <div>
                     <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email</label>
                     <input type="email" name="email" value="{{ Auth::user()->email }}" required
                            class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm font-semibold text-slate-800">
                 </div>

                 <div>
                     <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Country</label>
                     <input type="text" name="country" value="{{ Auth::user()->country }}" placeholder="e.g. United States"
                            class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm font-semibold text-slate-800">
                 </div>

                 <div class="border-t border-slate-100 pt-4 mt-2">
                     <h4 class="text-xs font-extrabold text-[#1A103C] uppercase tracking-wider mb-3">Change Password</h4>
                     
                     <div class="space-y-3">
                         <div>
                             <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Current Password</label>
                             <input type="password" name="current_password" placeholder="••••••••"
                                    class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm">
                         </div>
                         <div>
                             <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">New Password</label>
                             <input type="password" name="new_password" placeholder="••••••••"
                                    class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm">
                         </div>
                         <div>
                             <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Confirm New Password</label>
                             <input type="password" name="new_password_confirmation" placeholder="••••••••"
                                    class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm">
                         </div>
                     </div>
                 </div>

                 <button type="submit" 
                         class="w-full py-3 mt-4 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow shadow-[#6C3AF4]/20 transition active:scale-98">
                     Save Changes
                 </button>
             </form>
         </div>
    </aside>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <main class="flex-grow flex flex-col min-w-0 overflow-hidden bg-white/65 backdrop-blur-sm">
        
        <!-- Header -->
        <header class="h-20 bg-white/80 backdrop-blur-sm border-b border-white/60 flex items-center justify-between px-8 flex-shrink-0 relative z-10 animate-fade-in">
            <div>
                <h1 class="heading-display text-2xl font-bold text-[#1A103C] tracking-tight">Admin Dashboard</h1>
                <p class="text-[#8C8BA5] text-xs font-medium mt-0.5">Manage users, groups, limits, and platform activity.</p>
            </div>

            <!-- Header Actions -->
            <div class="flex items-center gap-6">
                <!-- Search Bar Form -->
                <form method="GET" action="{{ route('admin') }}" class="relative w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search users, groups, actions..."
                           class="block w-full pl-10 pr-10 py-2 bg-[#F8F8FC] border border-slate-200/85 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/10 focus:border-[#6C3AF4] transition">
                    @if(!empty($search))
                        <a href="{{ route('admin', array_filter(['country' => $country === 'all' ? null : $country, 'period' => $period === 'all' ? null : $period])) }}" 
                           class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition"
                           title="Clear Search">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                    @if($country) <input type="hidden" name="country" value="{{ $country }}"> @endif
                    @if($period) <input type="hidden" name="period" value="{{ $period }}"> @endif
                </form>

                <!-- Notifications Bell -->
                <div class="relative">
                    <button class="p-2 hover:bg-slate-100 rounded-xl transition text-slate-500 hover:text-slate-800 outline-none">
                        <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 h-4 w-4 bg-[#B026F3] border-2 border-white rounded-full flex items-center justify-center text-[8px] font-black text-white">3</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Body Scroll -->
        <div class="flex-grow overflow-y-auto p-8 space-y-6">

            <!-- Success / Error Alert Messages -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm font-semibold shadow-sm">
                    <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3 text-rose-800 text-sm font-semibold shadow-sm">
                    <svg class="h-5 w-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- 1. FILTERS ROW -->
            <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-2xl p-4 flex items-center justify-between shadow-sm animate-fade-in">
                <form method="GET" action="{{ route('admin') }}" class="flex items-center gap-4 w-full">
                    @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                    
                    <!-- Country Selector Dropdown -->
                    <div class="flex flex-col gap-1 w-64">
                        <label class="text-[9px] font-bold text-[#8C8BA5] uppercase tracking-wider ml-1">Country</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                🌐
                            </span>
                            <select name="country" onchange="this.form.submit()"
                                    class="block w-full pl-9 pr-8 py-2 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-xs font-semibold text-slate-800 outline-none appearance-none cursor-pointer">
                                <option value="all" {{ $country == 'all' || empty($country) ? 'selected' : '' }}>All Countries</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c }}" {{ $country == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Sign-in Period Selector Dropdown -->
                    <div class="flex flex-col gap-1 w-64">
                        <label class="text-[9px] font-bold text-[#8C8BA5] uppercase tracking-wider ml-1">Sign-in Period</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                📅
                            </span>
                            <select name="period" onchange="this.form.submit()"
                                    class="block w-full pl-9 pr-8 py-2 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-xs font-semibold text-slate-800 outline-none appearance-none cursor-pointer">
                                <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="7days" {{ $period == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                                <option value="30days" {{ $period == '30days' ? 'selected' : '' }}>Last 30 Days</option>
                                <option value="all" {{ $period == 'all' || empty($period) ? 'selected' : '' }}>All Time</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 2. FOUR STATS + ACCOUNT TYPES ROW -->
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
                <!-- Stat Card 1: Total Users -->
                <div class="bg-white/85 backdrop-blur-sm border border-white/60 rounded-[1.5rem] p-5 shadow-lg card-lift animate-slide-up flex flex-col justify-between h-36">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-[#8C8BA5] uppercase tracking-wider">Total Users</span>
                            <span class="heading-font text-2xl font-black text-[#1A103C] mt-1">{{ number_format($totalUsers) }}</span>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="text-emerald-500 font-bold">↑ 12.6%</span>
                        <span class="text-slate-400 font-medium">vs last 7 days</span>
                    </div>
                </div>

                <!-- Stat Card 2: Active Today -->
                <div class="bg-white/85 backdrop-blur-sm border border-white/60 rounded-[1.5rem] p-5 shadow-lg card-lift animate-slide-up-delay-1 flex flex-col justify-between h-36">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-[#8C8BA5] uppercase tracking-wider">Active Today</span>
                            <span class="heading-font text-2xl font-black text-[#1A103C] mt-1">{{ number_format($activeToday) }}</span>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="text-emerald-500 font-bold">↑ 8.4%</span>
                        <span class="text-slate-400 font-medium">vs yesterday</span>
                    </div>
                </div>

                <!-- Stat Card 3: Banned Users -->
                <div class="bg-white/85 backdrop-blur-sm border border-white/60 rounded-[1.5rem] p-5 shadow-lg card-lift animate-slide-up-delay-2 flex flex-col justify-between h-36">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-[#8C8BA5] uppercase tracking-wider">Banned Users</span>
                            <span class="heading-font text-2xl font-black text-[#1A103C] mt-1">{{ number_format($bannedUsersCount) }}</span>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="text-rose-500 font-bold">↓ 4.1%</span>
                        <span class="text-slate-400 font-medium">vs last 7 days</span>
                    </div>
                </div>

                <!-- Stat Card 4: Total Groups -->
                <div class="bg-white/85 backdrop-blur-sm border border-white/60 rounded-[1.5rem] p-5 shadow-lg card-lift animate-slide-up-delay-3 flex flex-col justify-between h-36">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-[#8C8BA5] uppercase tracking-wider">Total Groups</span>
                            <span class="heading-font text-2xl font-black text-[#1A103C] mt-1">{{ number_format($totalGroups) }}</span>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="text-emerald-500 font-bold">↑ 7.3%</span>
                        <span class="text-slate-400 font-medium">vs last 7 days</span>
                    </div>
                </div>

                <!-- Account Types Ratio Card -->
                <div class="bg-white/85 backdrop-blur-sm border border-white/60 rounded-[1.5rem] p-5 shadow-lg card-lift animate-slide-up-delay-4 flex flex-col justify-between h-36">
                    <div class="space-y-3.5">
                        <!-- Free accounts progress bar -->
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-[#6C3AF4]"></span> Free Accounts</span>
                                <span>{{ number_format($freeAccountsCount) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-1.5 overflow-hidden">
                                <div class="bg-[#6C3AF4] h-full rounded-full transition-all duration-500" style="width: {{ $freePercent }}%"></div>
                            </div>
                            <div class="text-[9px] font-bold text-[#8C8BA5] text-right mt-0.5">{{ $freePercent }}%</div>
                        </div>

                        <!-- Premium accounts progress bar -->
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-[#B026F3]"></span> Premium Accounts</span>
                                <span>{{ number_format($premiumAccountsCount) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-1.5 overflow-hidden">
                                <div class="bg-[#B026F3] h-full rounded-full transition-all duration-500" style="width: {{ $premiumPercent }}%"></div>
                            </div>
                            <div class="text-[9px] font-bold text-[#8C8BA5] text-right mt-0.5">{{ $premiumPercent }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. TWO COLUMN DETAILED ROW -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <!-- LEFT COLUMN: User Management Table (Takes 2 columns wide) -->
                <div class="bg-white/85 backdrop-blur-sm border border-white/60 rounded-[2rem] p-6 shadow-lg card-lift animate-slide-up-delay-1 lg:col-span-2">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="heading-font text-lg font-bold text-[#1A103C] tracking-tight">User Management</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs font-semibold text-slate-700">
                            <thead>
                                <tr class="text-[10px] font-bold text-[#8C8BA5] uppercase tracking-wider border-b border-slate-100 pb-3">
                                    <th class="pb-3 pl-2">User</th>
                                    <th class="pb-3">Country</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Last Sign In</th>
                                    <th class="pb-3 text-center">Groups Created</th>
                                    <th class="pb-3">Account Type</th>
                                    <th class="pb-3 text-right pr-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($users as $u)
                                    @php
                                        // Custom Flag picker ISO code
                                        $flagIso = match(strtolower($u->country ?? '')) {
                                            'united states', 'usa', 'us' => 'us',
                                            'united kingdom', 'uk', 'gb', 'great britain' => 'gb',
                                            'india' => 'in',
                                            'ghana' => 'gh',
                                            'malaysia' => 'my',
                                            'brazil' => 'br',
                                            'france' => 'fr',
                                            'spain' => 'es',
                                            'sri lanka' => 'lk',
                                            'canada' => 'ca',
                                            'australia' => 'au',
                                            'germany' => 'de',
                                            'japan' => 'jp',
                                            default => null
                                        };
                                    @endphp
                                    <tr class="table-row-hover transition">
                                        <!-- User avatar + detail -->
                                        <td class="py-3.5 pl-2">
                                            <div class="flex items-center gap-3">
                                                @if($u->profile_photo_path && File::exists(public_path($u->profile_photo_path)))
                                                    <img src="{{ asset($u->profile_photo_path) }}" class="h-9 w-9 rounded-full object-cover border border-slate-100 shadow-sm">
                                                @else
                                                    <div class="h-9 w-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-[#6C3AF4] uppercase text-[11px]">
                                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-slate-800">{{ $u->name }}</span>
                                                    <span class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $u->email }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Country -->
                                        <td class="py-3.5 text-xs text-slate-600 font-bold">
                                            <div class="flex items-center gap-1.5">
                                                @if($flagIso)
                                                    <img src="https://flagcdn.com/w20/{{ $flagIso }}.png" 
                                                         srcset="https://flagcdn.com/w40/{{ $flagIso }}.png 2x" 
                                                         width="20" alt="{{ $u->country }}" class="rounded shadow-sm">
                                                @else
                                                    <span class="text-sm">🌐</span>
                                                @endif
                                                <span>{{ $u->country ?? 'Not Provided' }}</span>
                                            </div>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-3.5">
                                            @if($u->status === 'banned')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-50 text-rose-600 border border-rose-100 uppercase tracking-wider">Banned</span>
                                            @elseif($u->status === 'inactive')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-slate-100 text-slate-500 uppercase tracking-wider">Inactive</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">Active</span>
                                            @endif
                                        </td>

                                        <!-- Last Sign In -->
                                        <td class="py-3.5 text-slate-400 font-medium">
                                            {{ $u->last_login_at ? \Carbon\Carbon::parse($u->last_login_at)->format('M d, Y h:i A') : 'Never' }}
                                        </td>

                                        <!-- Groups Count -->
                                        <td class="py-3.5 text-center text-xs font-bold text-slate-800">
                                            {{ $u->groups_count }}
                                        </td>

                                        <!-- Account Type -->
                                        <td class="py-3.5">
                                            <form method="POST" action="{{ route('admin.toggleAccountType', $u) }}" class="inline-block">
                                                @csrf
                                                <button type="submit" title="Click to change account type"
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-extrabold transition uppercase tracking-wider cursor-pointer active:scale-95 outline-none {{ $u->account_type === 'premium' ? 'bg-purple-50 text-purple-600 border border-purple-100 hover:bg-purple-100' : 'bg-blue-50 text-blue-600 border border-blue-100 hover:bg-blue-100' }}">
                                                    {{ $u->account_type }}
                                                </button>
                                            </form>
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3.5 text-right pr-2">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <!-- View button -->
                                                <button @click="selectedUserForView = {
                                                            name: '{{ addslashes($u->name) }}',
                                                            email: '{{ $u->email }}',
                                                            country: '{{ $u->country ?? 'Not Provided' }}',
                                                            status: '{{ $u->status }}',
                                                            role: '{{ $u->role }}',
                                                            account_type: '{{ $u->account_type }}',
                                                            last_seen: '{{ $u->last_login_at ? \Carbon\Carbon::parse($u->last_login_at)->format('M d, Y h:i A') : 'Never' }}',
                                                            joined: '{{ $u->created_at->format('d M Y') }}',
                                                            groups_count: {{ $u->groups_count }}
                                                        }; showUserViewModal = true;"
                                                        class="px-2 py-1 hover:bg-[#6C3AF4]/5 border border-transparent hover:border-[#6C3AF4]/15 rounded-lg text-[#6C3AF4] font-extrabold text-[10px] uppercase transition active:scale-95 outline-none">
                                                    View
                                                </button>

                                                <!-- Ban button -->
                                                @if($u->id !== Auth::id())
                                                    @if($u->status === 'banned')
                                                        <form method="POST" action="{{ route('admin.banUser', $u) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="px-2 py-1 hover:bg-emerald-50 border border-transparent hover:border-emerald-100 rounded-lg text-emerald-600 font-extrabold text-[10px] uppercase transition active:scale-95 outline-none">
                                                                Unban
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button"
                                                                @click="banUserAction = '{{ route('admin.banUser', $u) }}'; banUserName = '{{ addslashes($u->name) }}'; banReasonCategory = 'Violated community rules and terms of service'; banCustomReason = ''; showBanModal = true;"
                                                                class="px-2 py-1 hover:bg-rose-50 border border-transparent hover:border-rose-100 rounded-lg text-rose-500 font-extrabold text-[10px] uppercase transition active:scale-95 outline-none">
                                                            Ban
                                                        </button>
                                                    @endif

                                                    <!-- Delete button -->
                                                    <button type="button"
                                                            @click="deleteUserAction = '{{ route('admin.deleteUser', $u) }}'; deleteUserName = '{{ addslashes($u->name) }}'; deleteReason = ''; deleteConfirmText = ''; showDeleteModal = true;"
                                                            class="px-2 py-1 hover:bg-red-500 hover:text-white border border-transparent hover:border-red-600 rounded-lg text-red-500 font-extrabold text-[10px] uppercase transition active:scale-95 outline-none">
                                                        Delete
                                                    </button>
                                                @else
                                                    <span class="text-[9px] font-bold text-slate-300 pr-4">You</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400 font-bold">No users match your criteria.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer container -->
                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-slate-400 text-xs">
                        <div>
                            Showing <span class="font-bold text-slate-700">{{ $users->firstItem() ?? 0 }}</span> to <span class="font-bold text-slate-700">{{ $users->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-700">{{ $users->total() }}</span> users
                        </div>
                        <div class="flex items-center gap-1.5">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Recent Actions + Sign-ins Chart -->
                <div class="space-y-6 lg:col-span-1">
                    
                    <!-- Recent Admin Actions Card -->
                    <div class="bg-white/85 backdrop-blur-sm border border-white/60 rounded-[2rem] p-6 shadow-lg card-lift animate-slide-up-delay-2">
                        <div class="flex items-center justify-between mb-4.5">
                            <h3 class="heading-font text-sm font-bold text-[#1A103C]">Recent Admin Actions</h3>
                            <a href="{{ route('admin.activity') }}" class="text-[10px] font-extrabold text-[#6C3AF4] uppercase tracking-wider hover:underline">View All</a>
                        </div>

                        <!-- Timeline track -->
                        <div class="space-y-4">
                            @foreach($recentActions as $act)
                                @php
                                    // Visual categorizer based on message
                                    $circleColor = 'bg-blue-50 text-blue-500';
                                    $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
                                    
                                    if(str_contains(strtolower($act->message), 'ban')) {
                                        $circleColor = 'bg-rose-50 text-rose-500';
                                        $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>';
                                    } elseif(str_contains(strtolower($act->message), 'deleted') || str_contains(strtolower($act->message), 'remove')) {
                                        $circleColor = 'bg-orange-50 text-orange-500';
                                        $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>';
                                    } elseif(str_contains(strtolower($act->message), 'package') || str_contains(strtolower($act->message), 'account')) {
                                        $circleColor = 'bg-purple-50 text-purple-500';
                                        $iconPath = '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>';
                                    }
                                @endphp
                                <div class="flex items-start gap-3 text-xs leading-relaxed">
                                    <div class="h-8.5 w-8.5 rounded-full {{ $circleColor }} flex flex-shrink-0 items-center justify-center border border-current/10 shadow-sm mt-0.5">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            {!! $iconPath !!}
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-800">{{ $act->message }}</span>
                                        <span class="text-[9px] text-[#8C8BA5] font-semibold mt-0.5">
                                            {{ $act->user->email ?? 'admin' }} • {{ $act->created_at->format('M d, Y h:i A') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- User Sign-ins Over Time Card -->
                    <div class="bg-white/85 backdrop-blur-sm border border-white/60 rounded-[2rem] p-6 shadow-lg card-lift animate-slide-up-delay-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="heading-font text-sm font-bold text-[#1A103C]">User Sign-ins Over Time</h3>
                            <select class="border border-slate-200/80 rounded-full px-3 py-1 text-[9px] font-bold text-slate-500 bg-[#F8F9FD] outline-none cursor-pointer">
                                <option>Last 7 Days</option>
                            </select>
                        </div>

                        <!-- Dynamic line chart canvas -->
                        <div class="relative w-full h-48 mt-4">
                            <canvas id="signinChart"></canvas>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-xs">
                            <span class="text-emerald-500 font-bold">↑ 15.8%</span>
                            <span class="text-[#8C8BA5] font-medium">vs previous 7 days</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </main>

    <!-- ==================== USER VIEW DETAILS MODAL ==================== -->
    <div x-show="showUserViewModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-[#1A103C]/20 backdrop-blur-sm transition-opacity" 
             @click="showUserViewModal = false"></div>

        <!-- Modal Box -->
        <div class="bg-white border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[440px] p-8 relative z-10 transition-all duration-300"
             x-show="showUserViewModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-96"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-96">

            <button @click="showUserViewModal = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center mb-6" x-show="selectedUserForView">
                <div class="h-16 w-16 mx-auto rounded-full bg-gradient-to-tr from-[#6C3AF4] to-[#B026F3] flex items-center justify-center text-white text-2xl font-black border-2 border-white shadow mb-3">
                    <span x-text="selectedUserForView ? selectedUserForView.name.substring(0, 1).toUpperCase() : ''"></span>
                </div>
                <h3 class="heading-font text-xl font-extrabold text-[#1A103C]" x-text="selectedUserForView ? selectedUserForView.name : ''"></h3>
                <p class="text-slate-400 text-xs font-semibold" x-text="selectedUserForView ? selectedUserForView.email : ''"></p>
            </div>

            <div class="space-y-3.5 text-xs text-slate-600 font-bold" x-show="selectedUserForView">
                <div class="flex items-center justify-between p-2.5 bg-[#F8F9FD] rounded-xl">
                    <span class="text-[#8C8BA5] font-semibold">Account Status:</span>
                    <span class="px-2 py-0.5 rounded-full uppercase tracking-wider text-[9px] font-extrabold"
                          :class="selectedUserForView && selectedUserForView.status === 'banned' ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'"
                          x-text="selectedUserForView ? selectedUserForView.status : ''"></span>
                </div>
                
                <div class="flex items-center justify-between p-2.5 bg-[#F8F9FD] rounded-xl">
                    <span class="text-[#8C8BA5] font-semibold">Account Type:</span>
                    <span class="px-2 py-0.5 rounded-full uppercase tracking-wider text-[9px] font-extrabold bg-purple-50 text-purple-600 border border-purple-100"
                          x-text="selectedUserForView ? selectedUserForView.account_type : ''"></span>
                </div>

                <div class="flex items-center justify-between p-2.5 bg-[#F8F9FD] rounded-xl">
                    <span class="text-[#8C8BA5] font-semibold">Country:</span>
                    <span class="text-slate-800" x-text="selectedUserForView ? selectedUserForView.country : ''"></span>
                </div>

                <div class="flex items-center justify-between p-2.5 bg-[#F8F9FD] rounded-xl">
                    <span class="text-[#8C8BA5] font-semibold">Groups Joined:</span>
                    <span class="text-slate-800 font-black text-sm" x-text="selectedUserForView ? selectedUserForView.groups_count : 0"></span>
                </div>

                <div class="flex items-center justify-between p-2.5 bg-[#F8F9FD] rounded-xl">
                    <span class="text-[#8C8BA5] font-semibold">Registered:</span>
                    <span class="text-slate-500 font-medium" x-text="selectedUserForView ? selectedUserForView.joined : ''"></span>
                </div>

                <div class="flex items-center justify-between p-2.5 bg-[#F8F9FD] rounded-xl">
                    <span class="text-[#8C8BA5] font-semibold">Last Access:</span>
                    <span class="text-slate-500 font-medium" x-text="selectedUserForView ? selectedUserForView.last_seen : ''"></span>
                </div>
            </div>

            <button @click="showUserViewModal = false"
                    class="block w-full py-3 mt-6 bg-[#6C3AF4] hover:bg-[#6C3AF4]/95 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition active:scale-98">
                Close Profile
            </button>
        </div>
    </div>

    <!-- ==================== BAN USER MODAL ==================== -->
    <div x-show="showBanModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-[#1A103C]/20 backdrop-blur-sm transition-opacity" 
             @click="showBanModal = false"></div>

        <!-- Modal Box -->
        <div class="bg-white border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[440px] p-8 relative z-10 transition-all duration-300"
             x-show="showBanModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-96"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-96">

            <button @click="showBanModal = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center mb-6">
                <div class="h-12 w-12 mx-auto rounded-full bg-rose-50 flex items-center justify-center text-rose-500 mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
                <h3 class="heading-font text-xl font-extrabold text-[#1A103C]">Ban User</h3>
                <p class="text-slate-400 text-xs font-semibold mt-1">Specify a reason for banning <span class="text-[#6C3AF4] font-black" x-text="banUserName"></span>.</p>
            </div>

            <form method="POST" :action="banUserAction" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Select Suggestion / Reason</label>
                    <div class="space-y-2">
                        <!-- Suggestion 1 -->
                        <label class="flex items-start gap-2.5 p-3 bg-[#F8F9FD] border border-slate-100 rounded-xl cursor-pointer hover:bg-slate-100/50 transition">
                            <input type="radio" name="reason_cat" value="Violated community rules and terms of service" 
                                   x-model="banReasonCategory"
                                   class="mt-0.5 rounded-full border-slate-300 text-[#6C3AF4] focus:ring-[#6C3AF4]/20 h-4 w-4 bg-white">
                            <span class="text-xs font-semibold text-slate-750">Violated community rules & terms of service</span>
                        </label>
                        <!-- Suggestion 2 -->
                        <label class="flex items-start gap-2.5 p-3 bg-[#F8F9FD] border border-slate-100 rounded-xl cursor-pointer hover:bg-slate-100/50 transition">
                            <input type="radio" name="reason_cat" value="Spamming, posting advertising links, or fraudulent activity" 
                                   x-model="banReasonCategory"
                                   class="mt-0.5 rounded-full border-slate-300 text-[#6C3AF4] focus:ring-[#6C3AF4]/20 h-4 w-4 bg-white">
                            <span class="text-xs font-semibold text-slate-750">Spamming, ads, or fraudulent activity</span>
                        </label>
                        <!-- Suggestion 3 -->
                        <label class="flex items-start gap-2.5 p-3 bg-[#F8F9FD] border border-slate-100 rounded-xl cursor-pointer hover:bg-slate-100/50 transition">
                            <input type="radio" name="reason_cat" value="Abusive behavior, harassment, or offensive language towards other members" 
                                   x-model="banReasonCategory"
                                   class="mt-0.5 rounded-full border-slate-300 text-[#6C3AF4] focus:ring-[#6C3AF4]/20 h-4 w-4 bg-white">
                            <span class="text-xs font-semibold text-slate-750">Abusive behavior or harassment</span>
                        </label>
                        <!-- Suggestion 4 -->
                        <label class="flex items-start gap-2.5 p-3 bg-[#F8F9FD] border border-slate-100 rounded-xl cursor-pointer hover:bg-slate-100/50 transition">
                            <input type="radio" name="reason_cat" value="Other" 
                                   x-model="banReasonCategory"
                                   class="mt-0.5 rounded-full border-slate-300 text-[#6C3AF4] focus:ring-[#6C3AF4]/20 h-4 w-4 bg-white">
                            <span class="text-xs font-semibold text-slate-750">Other (Type reason below)</span>
                        </label>
                    </div>
                </div>

                <!-- Custom Reason Textarea -->
                <div x-show="banReasonCategory === 'Other'"
                     x-transition:enter="transition ease-out duration-200 transform"
                     x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     class="mt-3">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 ml-1">Custom Reason</label>
                    <textarea x-model="banCustomReason" rows="3" placeholder="Explain the custom reason here..."
                              class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/10 focus:border-[#6C3AF4] transition"></textarea>
                </div>

                <!-- Hidden Input carrying computed reason to controller -->
                <input type="hidden" name="reason" :value="banReasonCategory === 'Other' ? banCustomReason : banReasonCategory">

                <div class="flex items-center gap-3 mt-6">
                    <button type="button" @click="showBanModal = false"
                            class="w-1/2 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs uppercase tracking-wider transition active:scale-98">
                        Cancel
                    </button>
                    <button type="submit" :disabled="banReasonCategory === 'Other' && !banCustomReason.trim()"
                            class="w-1/2 py-3 bg-rose-500 hover:bg-rose-600 disabled:opacity-50 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition active:scale-98">
                        Ban User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== DELETE USER MODAL ==================== -->
    <div x-show="showDeleteModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-[#1A103C]/20 backdrop-blur-sm transition-opacity" 
             @click="showDeleteModal = false"></div>

        <!-- Modal Box -->
        <div class="bg-white border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[440px] p-8 relative z-10 transition-all duration-300"
             x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-96"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-96">

            <button @click="showDeleteModal = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center mb-6">
                <div class="h-12 w-12 mx-auto rounded-full bg-red-50 flex items-center justify-center text-red-500 mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <h3 class="heading-font text-xl font-extrabold text-[#1A103C]">Permanently Delete User</h3>
                <p class="text-rose-500 text-[10px] font-bold mt-1.5 px-3 py-1.5 bg-rose-50 border border-rose-100 rounded-xl leading-normal">
                    ⚠️ Warning: This will delete the user account <span class="text-[#6C3AF4] font-black" x-text="deleteUserName"></span> and completely clear all their personal group splits, expenses, and transaction history from the database!
                </p>
            </div>

            <form method="POST" :action="deleteUserAction" class="space-y-4">
                @csrf
                @method('DELETE')
                
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 ml-1">Reason for Deletion</label>
                    <textarea name="reason" x-model="deleteReason" rows="3" required placeholder="Explain the reason for permanent deletion here..."
                              class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/10 focus:border-[#6C3AF4] transition"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-rose-500 uppercase tracking-wider mb-1.5 ml-1">Double-Check Confirmation</label>
                    <input type="text" name="confirm_text" x-model="deleteConfirmText" required placeholder='Type "DELETE" to confirm'
                           class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-rose-200 rounded-xl text-center text-xs font-black tracking-widest text-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-500/10 focus:border-rose-500 transition">
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="button" @click="showDeleteModal = false"
                            class="w-1/2 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs uppercase tracking-wider transition active:scale-98">
                        Cancel
                    </button>
                    <button type="submit" :disabled="!deleteReason.trim() || deleteConfirmText.trim().toLowerCase() !== 'delete'"
                            class="w-1/2 py-3 bg-red-500 hover:bg-red-600 disabled:opacity-50 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition active:scale-98">
                        Permanently Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== CHART INITIALIZATION SCRIPT ==================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('signinChart').getContext('2d');
            
            // Create soft glowing gradient background under the line
            const fillGradient = ctx.createLinearGradient(0, 0, 0, 180);
            fillGradient.addColorStop(0, 'rgba(108, 58, 244, 0.28)'); // Purple glow
            fillGradient.addColorStop(1, 'rgba(108, 58, 244, 0.00)');

            // Chart configuration
            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Sign-ins',
                        data: @json($chartData),
                        borderColor: '#6C3AF4',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6C3AF4',
                        pointBorderWidth: 2,
                        pointRadius: 4.5,
                        pointHoverRadius: 6.5,
                        tension: 0.38, // Smooth rounded line
                        fill: true,
                        backgroundColor: fillGradient
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide default legend
                        },
                        tooltip: {
                            backgroundColor: '#1A103C',
                            titleFont: {
                                family: 'DM Sans',
                                size: 10,
                                weight: 'bold'
                            },
                            bodyFont: {
                                family: 'DM Sans',
                                size: 11,
                                weight: 'bold'
                            },
                            padding: 10,
                            borderRadius: 12,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false // No x grid lines
                            },
                            ticks: {
                                font: {
                                    family: 'DM Sans',
                                    size: 9,
                                    weight: '600'
                                },
                                color: '#8C8BA5'
                            }
                        },
                        y: {
                            grid: {
                                borderDash: [5, 5], // Dotted lines
                                color: 'rgba(26, 16, 60, 0.05)'
                            },
                            ticks: {
                                font: {
                                    family: 'DM Sans',
                                    size: 9,
                                    weight: '600'
                                },
                                color: '#8C8BA5'
                            }
                        }
                    }
                }
            });
        });
    </script>

</body>
</html>
