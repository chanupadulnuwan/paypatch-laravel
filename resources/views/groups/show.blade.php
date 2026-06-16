<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — {{ $group->name }}</title>

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
        @keyframes shimmer { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-6px); } }
        @keyframes countUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .animate-slide-up-delay-1 { animation: slideUpFade 0.6s 0.1s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .animate-slide-up-delay-2 { animation: slideUpFade 0.6s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .animate-slide-up-delay-3 { animation: slideUpFade 0.6s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .animate-fade-in { animation: fadeIn 0.5s ease both; }
        .animate-scale-in { animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-count { animation: countUp 0.8s 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
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
          activeModal: '{{ session('modal') ?? '' }}', 
          groupTab: '{{ session('modal') === 'edit-group' ? 'members' : 'settings' }}',
          showEditGroup: false,
          editingExpense: {
              id: null,
              title: '',
              amount: 0,
              paid_by: '',
              split_type: 'equal',
              selected_members: []
          }
      }"
      x-init="
          @if(session('modal') && str_starts_with(session('modal'), 'edit-expense-'))
              @php
                  $errExpId = (int) str_replace('edit-expense-', '', session('modal'));
                  $errExpense = \App\Models\Expense::with('shares')->find($errExpId);
              @endphp
              @if($errExpense)
                  editingExpense = {
                      id: {{ $errExpense->id }},
                      title: '{{ addslashes($errExpense->title) }}',
                      amount: {{ $errExpense->amount }},
                      paid_by: '{{ $errExpense->paid_by }}',
                      split_type: '{{ $errExpense->split_type }}',
                      selected_members: [
                          @foreach($errExpense->shares as $share)
                              @if($share->share_amount > 0)
                                  '{{ $share->user_id }}',
                              @endif
                          @endforeach
                      ]
                  };
                  activeModal = 'edit-expense';
              @endif
          @endif
          
          @if(request()->has('settle_to'))
              activeModal = 'settle';
              setTimeout(() => {
                  document.getElementById('settle-to').value = '{{ request()->query('settle_to') }}';
                  document.getElementById('settle-amt').value = '{{ request()->query('settle_amt') }}';
              }, 150);
          @endif
      ">

    <!-- ==================== LEFT SIDEBAR (EXACT SAME NAV BAR) ==================== -->
    <aside class="w-72 flex-shrink-0 relative overflow-hidden bg-cover bg-center flex flex-col justify-between p-6 border-r border-[#1A103C]/5"
           style="background-image: url('{{ asset('assets/sidebar-bg.png') }}?v=3');">
        
        <!-- White/Translucent Gradient Top Overlay -->
        <div class="absolute inset-x-0 top-0 h-2/3 bg-gradient-to-b from-white via-white/95 to-white/40 pointer-events-none z-0"></div>

        <div class="relative z-10 w-full flex flex-col gap-10">
            <!-- LOGO -->
            <a href="/" class="flex items-center outline-none">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 transition transform hover:scale-103 active:scale-97" alt="PayPatch Logo">
            </a>            <!-- SIDEBAR NAV ITEMS -->
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
                             @if(Auth::user()->profile_photo_path && File::exists(public_path(Auth::user()->profile_photo_path)))
                                 <img src="{{ asset(Auth::user()->profile_photo_path) }}" class="h-20 w-20 rounded-full object-cover border-2 border-[#6C3AF4]/10 shadow-md">
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
                         class="mt-auto w-full py-2.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#592BD4] hover:to-[#9E1CE0] text-white rounded-full font-bold text-[11px] uppercase tracking-widest shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300">
                     Save Settings
                 </button>
             </form>
        </div>

    </aside>

    <!-- ==================== MAIN WORKSPACE ==================== -->
    <main class="flex-grow flex flex-col overflow-y-auto px-8 py-8 md:px-12 w-full max-w-full bg-white/65 backdrop-blur-sm"
          :style="activeModal ? 'filter: blur(10px); transform: scale(0.985); pointer-events: none; transition: filter 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);' : 'transition: filter 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);'">

        <!-- Flash alerts -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
                 class="mb-5 p-4 bg-green-100/80 border border-green-200 text-green-700 rounded-2xl text-sm font-semibold flex justify-between items-center transition duration-300">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-green-700 hover:text-green-950 ml-2 outline-none font-bold text-xs">✕</button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
                 class="mb-5 p-4 bg-red-100/80 border border-red-200 text-red-700 rounded-2xl text-sm font-semibold flex justify-between items-center transition duration-300">
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="text-red-700 hover:text-red-950 ml-2 outline-none font-bold text-xs">✕</button>
            </div>
        @endif

        <!-- Pending Settlement Requests Banner Alert -->
        @if(isset($pendingSettlementRequests) && $pendingSettlementRequests->isNotEmpty())
            <div class="mb-6 flex flex-col gap-3">
                @foreach($pendingSettlementRequests as $requestLog)
                    <div class="p-4 bg-gradient-to-r from-purple-500/10 via-[#6C3AF4]/10 to-[#B026F3]/5 border border-[#6C3AF4]/30 rounded-3xl flex justify-between items-center shadow-sm backdrop-blur-md">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 bg-purple-100/80 rounded-2xl flex items-center justify-center text-purple-700 font-bold">
                                📣
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-[#1A103C]">{{ $requestLog->message }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $requestLog->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="activeModal = 'settle'; setTimeout(() => { document.getElementById('settle-to').value = '{{ $requestLog->user_id }}'; document.getElementById('settle-amt').value = '{{ $requestLog->request_amount }}'; }, 100)"
                                    class="px-4 py-2 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white text-[11px] font-bold rounded-full shadow-md shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 outline-none">
                                Settle Up
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- ==================== TOP BANNER CARD ==================== -->
        @php
            $coverStyle = '';
            $coverClass = 'bg-gradient-to-tr from-[#6C3AF4] to-[#B026F3]'; // Default premium gradient
            if ($group->cover_image_path) {
                if (str_starts_with($group->cover_image_path, 'preset:')) {
                    $preset = str_replace('preset:', '', $group->cover_image_path);
                    if ($preset === 'sunrise') {
                        $coverClass = 'bg-gradient-to-tr from-[#FF512F] to-[#DD2476]';
                    } elseif ($preset === 'ocean') {
                        $coverClass = 'bg-gradient-to-tr from-[#2193b0] to-[#6dd5ed]';
                    } elseif ($preset === 'deepspace') {
                        $coverClass = 'bg-gradient-to-tr from-[#0F2027] to-[#2C5364]';
                    } elseif ($preset === 'dusk') {
                        $coverClass = 'bg-gradient-to-tr from-[#2C3E50] to-[#FD746C]';
                    } elseif ($preset === 'cyberpunk') {
                        $coverClass = 'bg-gradient-to-tr from-[#F107A3] to-[#7B2CBF]';
                    }
                } else {
                    $coverStyle = "background-image: url('" . asset($group->cover_image_path) . "?v=" . time() . "');";
                    $coverClass = 'bg-cover bg-center';
                }
            }
        @endphp
        <section class="w-full h-48 md:h-52 {{ $coverClass }} rounded-[2rem] p-8 md:p-10 flex flex-col justify-between items-start relative overflow-hidden shadow-md shadow-[#1A103C]/5 mb-8 flex-shrink-0"
                 style="{{ $coverStyle }}">
            <!-- Semi-darkened horizontal layout overlay -->
            <div class="absolute inset-0 bg-slate-950/20 z-0"></div>

            <div class="relative z-10 w-full flex justify-between items-center h-full">
                <!-- Group Identity Info -->
                <div class="text-white flex flex-col gap-1.5">
                    <h1 class="heading-serif text-3xl md:text-4xl font-extrabold flex items-center gap-2 drop-shadow-md">
                        {{ $group->name }} 🏖️
                    </h1>
                    <p class="text-white/80 font-bold text-xs md:text-sm tracking-wide mt-1.5 drop-shadow-sm uppercase">
                        Created {{ $group->created_at->format('M d, Y') }} &bull; {{ $members->count() }} Members
                    </p>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-3">
                    <!-- Edit Group Trigger Button -->
                    @if($group->created_by === Auth::id())
                        <button @click="activeModal = 'edit-group'; groupTab = 'settings'" 
                                class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/25 rounded-2xl flex items-center gap-1.5 transition text-xs font-bold shadow-md outline-none">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                            </svg>
                            Edit Group
                        </button>
                    @endif
                </div>
            </div>
        </section>

        <!-- ==================== METRICS ROW ==================== -->
        @php
            $myBalance = $memberBalances[Auth::id()] ?? 0;
            $youOweAmount = $myBalance < 0 ? abs($myBalance) : 0;
            $youAreOwedAmount = $myBalance > 0 ? $myBalance : 0;
        @endphp
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 flex-shrink-0">
            <!-- 1. TOTAL EXPENSES -->
            <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-5 flex justify-between items-start shadow-lg card-lift animate-slide-up">
                <div class="flex flex-col gap-0.5">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Total Expenses</span>
                    <h3 class="heading-font text-2xl font-extrabold text-[#1A103C] mt-1">
                        {{ $group->currency }} {{ number_format($expenses->sum('amount'), 2) }}
                    </h3>
                    <span class="text-[10px] text-slate-400 font-semibold mt-2.5">All expenses combined</span>
                </div>
                <!-- Wallet Icon Box -->
                <div class="h-9 w-9 bg-purple-50 flex items-center justify-center rounded-2xl text-[#6C3AF4]">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                </div>
            </div>

            <!-- 2. YOU OWE -->
            <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-5 flex justify-between items-start shadow-lg card-lift animate-slide-up">
                <div class="flex flex-col gap-0.5">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">You Owe</span>
                    <h3 class="heading-font text-2xl font-extrabold text-[#E63946] mt-1">
                        {{ $group->currency }} {{ number_format($youOweAmount, 2) }}
                    </h3>
                    <span class="text-[10px] text-slate-400 font-semibold mt-2.5">
                        {{ $youOweAmount > 0 ? 'You need to pay' : 'No debt active' }}
                    </span>
                </div>
                <!-- Red Diagonal Arrow -->
                <div class="h-9 w-9 bg-red-50 flex items-center justify-center rounded-2xl text-[#E63946]">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"></path>
                    </svg>
                </div>
            </div>

            <!-- 3. YOU ARE OWED -->
            <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-5 flex justify-between items-start shadow-lg card-lift animate-slide-up">
                <div class="flex flex-col gap-0.5">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">You Are Owed</span>
                    <h3 class="heading-font text-2xl font-extrabold text-[#10B981] mt-1">
                        {{ $group->currency }} {{ number_format($youAreOwedAmount, 2) }}
                    </h3>
                    <span class="text-[10px] text-slate-400 font-semibold mt-2.5">
                        {{ $youAreOwedAmount > 0 ? 'You will receive' : 'Nothing owed to you' }}
                    </span>
                </div>
                <!-- Green Down Arrow -->
                <div class="h-9 w-9 bg-emerald-50 flex items-center justify-center rounded-2xl text-[#10B981]">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25"></path>
                    </svg>
                </div>
            </div>

            <!-- 4. GROUP BALANCE -->
            <div class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-5 flex justify-between items-start shadow-lg card-lift animate-slide-up">
                <div class="flex flex-col gap-0.5">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Group Balance</span>
                    <h3 class="heading-font text-2xl font-extrabold text-[#1A103C] mt-1">
                        {{ $group->currency }} {{ number_format(abs($myBalance), 2) }}
                    </h3>
                    <span class="text-[10px] font-bold mt-2.5 flex items-center gap-1">
                        @if($myBalance > 0)
                            <span class="text-[#10B981]">You are ahead ↗</span>
                        @elseif($myBalance < 0)
                            <span class="text-[#E63946]">You are behind ↘</span>
                        @else
                            <span class="text-slate-400">All settled up 🎉</span>
                        @endif
                    </span>
                </div>
                <!-- Person circle -->
                <div class="h-9 w-9 bg-purple-50 flex items-center justify-center rounded-2xl text-[#6C3AF4]">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </section>

        <!-- ==================== TWO COLUMN SECTION ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: RECENT EXPENSES & SUGGESTIONS -->
            <div class="lg:col-span-8 flex flex-col gap-8 w-full">
                
                <!-- A. RECENT EXPENSES -->
                <section class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-6 shadow-lg card-lift">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="heading-font text-lg font-extrabold text-[#1A103C] tracking-tight">Recent Expenses</h2>
                        <div class="flex items-center gap-2">
                            <!-- select menu mockup -->
                            <select class="border border-slate-200 rounded-full px-4 py-1.5 text-[10px] font-bold text-slate-500 bg-[#F8F9FD] outline-none cursor-pointer">
                                <option>All Expenses</option>
                            </select>
                        </div>
                    </div>

                    @if($expenses->isEmpty())
                        <div class="text-center py-16 text-slate-400">
                            <div class="text-4xl mb-3">💸</div>
                            <p class="text-sm font-semibold">No expenses yet.</p>
                            <button @click="activeModal = 'add-expense'" class="mt-2 text-xs font-bold text-[#6C3AF4] hover:underline">Add first expense</button>
                        </div>
                    @else
                        <div class="flex flex-col gap-4 max-h-[420px] overflow-y-auto pr-1">
                            @foreach($expenses as $index => $expense)
                                @php
                                    // Categories Mapping based on Expense Title keywords
                                    $titleLower = strtolower($expense->title);
                                    if (str_contains($titleLower, 'dinner') || str_contains($titleLower, 'cafe') || str_contains($titleLower, 'food') || str_contains($titleLower, 'pizza')) {
                                        $cat = 'Food';
                                        $catBg = 'bg-[#6C3AF4]/10 text-[#6C3AF4]';
                                    } elseif (str_contains($titleLower, 'rental') || str_contains($titleLower, 'scooty') || str_contains($titleLower, 'ride') || str_contains($titleLower, 'taxi')) {
                                        $cat = 'Transport';
                                        $catBg = 'bg-blue-100/50 text-blue-600';
                                    } elseif (str_contains($titleLower, 'ticket') || str_contains($titleLower, 'beach') || str_contains($titleLower, 'entry')) {
                                        $cat = 'Tickets';
                                        $catBg = 'bg-rose-100/50 text-rose-600';
                                    } elseif (str_contains($titleLower, 'club') || str_contains($titleLower, 'night') || str_contains($titleLower, 'party')) {
                                        $cat = 'Entertainment';
                                        $catBg = 'bg-indigo-100/50 text-indigo-600';
                                    } else {
                                        $cat = 'Activities';
                                        $catBg = 'bg-orange-100/50 text-orange-600';
                                    }
                                @endphp
                                <div class="flex items-center justify-between p-3.5 hover:bg-[#F8F9FD] border border-transparent hover:border-slate-100 rounded-2xl transition">
                                    <div class="flex items-center gap-4">
                                        <!-- User Initials Circle -->
                                        <div class="h-11 w-11 rounded-full bg-slate-100 border border-slate-200/50 flex items-center justify-center text-[#1A103C] font-extrabold text-sm shadow-sm">
                                            {{ strtoupper(substr($expense->paidBy->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="font-extrabold text-sm text-[#1A103C]">{{ $expense->title }}</h3>
                                            <p class="text-[10px] font-semibold text-slate-400 mt-1">
                                                {{ $expense->created_at->format('M d') }} &bull; Paid by {{ $expense->paid_by === Auth::id() ? 'You' : explode(' ', $expense->paidBy->name)[0] }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Category Pill -->
                                    <span class="px-3.5 py-1 rounded-full text-[10px] font-bold {{ $catBg }} tracking-wide">
                                        {{ $cat }}
                                    </span>

                                    <div class="flex items-center gap-4">
                                        <div class="text-right flex items-center gap-3">
                                            <span class="text-sm font-bold text-[#1A103C]">{{ $group->currency }} {{ number_format($expense->amount, 2) }}</span>
                                            <!-- Inline actions if creator or owner -->
                                            @if($expense->created_by === Auth::id() || $group->created_by === Auth::id())
                                                <div class="flex items-center gap-2">
                                                    <!-- Pencil/Edit Icon Button -->
                                                    <button @click="
                                                        editingExpense = {
                                                            id: {{ $expense->id }},
                                                            title: '{{ addslashes($expense->title) }}',
                                                            amount: {{ $expense->amount }},
                                                            paid_by: '{{ $expense->paid_by }}',
                                                            split_type: '{{ $expense->split_type }}',
                                                            selected_members: [
                                                                @foreach($expense->shares as $share)
                                                                    @if($share->share_amount > 0)
                                                                        '{{ $share->user_id }}',
                                                                    @endif
                                                                @endforeach
                                                            ]
                                                        };
                                                        activeModal = 'edit-expense';
                                                    " class="p-1 hover:bg-slate-100 text-slate-400 hover:text-[#6C3AF4] rounded-lg transition outline-none" title="Edit">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                                                        </svg>
                                                    </button>

                                                    <!-- Trash/Delete Icon Button -->
                                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Delete this expense?')"
                                                                class="p-1 hover:bg-slate-100 text-slate-400 hover:text-red-600 rounded-lg transition outline-none" title="Delete">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer Link -->
                        <div class="text-center mt-6 pt-3 border-t border-slate-100/60">
                            <a href="#" class="text-[#6C3AF4] hover:text-[#592BD4] text-xs font-bold transition">View all expenses</a>
                        </div>
                    @endif
                </section>

                <!-- B. SETTLEMENT SUGGESTIONS -->
                <section class="bg-white border border-[#1A103C]/8 rounded-3xl p-6 shadow-sm">
                    <div class="flex flex-col mb-4">
                        <h2 class="heading-font text-lg font-extrabold text-[#1A103C] tracking-tight">Settlement Suggestions</h2>
                        <span class="text-[10px] font-semibold text-slate-400">Simplify settlements with fewer payments.</span>
                    </div>

                    @if(empty($simplifiedDebts))
                        <div class="text-center py-8 text-slate-400">
                            <div class="text-2xl mb-1">🎉</div>
                            <p class="text-xs font-semibold">All settled up with fewer payments!</p>
                        </div>
                    @else
                        <div class="flex flex-col gap-4 mt-3">
                            @foreach($simplifiedDebts as $debt)
                                @php
                                    $fromUser = $members->firstWhere('id', $debt['from']);
                                    $toUser   = $members->firstWhere('id', $debt['to']);
                                    $isDebtor = ($debt['from'] == Auth::id());
                                @endphp
                                @if($fromUser && $toUser)
                                    <div class="flex items-center justify-between p-3.5 hover:bg-[#F8F9FD] border border-transparent hover:border-slate-100 rounded-2xl transition">
                                        <div class="flex items-center gap-3">
                                            <!-- From user initials -->
                                            <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-extrabold text-xs border border-slate-200/30">
                                                {{ strtoupper(substr($fromUser->name, 0, 1)) }}
                                            </div>
                                            <span class="text-xs text-slate-400">→</span>
                                            <!-- To user initials -->
                                            <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-extrabold text-xs border border-slate-200/30">
                                                {{ strtoupper(substr($toUser->name, 0, 1)) }}
                                            </div>

                                            <p class="text-xs font-semibold text-slate-700 ml-1.5">
                                                <span class="font-bold text-[#1A103C]">{{ $fromUser->id === Auth::id() ? 'You' : explode(' ', $fromUser->name)[0] }}</span> pays
                                                <span class="font-bold text-[#1A103C]">{{ $toUser->id === Auth::id() ? 'You' : explode(' ', $toUser->name)[0] }}</span>
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <span class="text-sm font-extrabold {{ $isDebtor ? 'text-[#E63946]' : 'text-[#10B981]' }}">
                                                Settle {{ $group->currency }} {{ number_format($debt['amount'], 2) }}
                                            </span>
                                            
                                            <!-- Dynamic Settle Button (opens settlement modal with default values!) -->
                                            <button @click="activeModal = 'settle'; setTimeout(() => { document.getElementById('settle-to').value = '{{ $toUser->id }}'; document.getElementById('settle-amt').value = '{{ $debt['amount'] }}'; }, 100)"
                                                    class="px-5 py-2 rounded-full text-[11px] font-bold bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white shadow-md shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 outline-none">
                                                Settle Now
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </section>

            </div>

            <!-- RIGHT COLUMN: MEMBERS & QUICK ACTIONS -->
            <div class="lg:col-span-4 flex flex-col gap-8 w-full">
                
                <!-- A. MEMBERS LIST -->
                <section class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-6 shadow-lg card-lift">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="heading-font text-lg font-extrabold text-[#1A103C] tracking-tight">Members</h2>
                        <span class="bg-[#6C3AF4]/10 text-[#6C3AF4] text-[10px] font-bold px-2.5 py-1 rounded-full">
                            {{ $members->count() }} members
                        </span>
                    </div>

                    <div class="flex flex-col gap-4 max-h-[220px] overflow-y-auto pr-1">
                        @foreach($members as $member)
                            @php
                                $balance = $memberBalances[$member->id] ?? 0;
                            @endphp
                            <div class="flex items-center justify-between py-0.5">
                                <div class="flex items-center gap-3">
                                    <!-- Member Avatar -->
                                    <img src="{{ $member->profile_photo_url }}" 
                                         class="h-9 w-9 rounded-full object-cover border border-slate-200/40 shadow-sm" 
                                         alt="{{ $member->name }}">
                                    <div>
                                        <h4 class="font-extrabold text-xs text-[#1A103C] flex items-center gap-1">
                                            {{ $member->name }}
                                            @if($member->id === Auth::id())
                                                <span class="text-[10px] text-slate-400 font-semibold">(You)</span>
                                            @endif
                                            @if($member->id === $group->created_by)
                                                <span class="text-[9px] text-[#6C3AF4] font-bold bg-[#6C3AF4]/10 px-1.5 py-0.25 rounded-md">Owner</span>
                                            @endif
                                        </h4>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold {{ $balance >= 0 ? 'text-[#10B981]' : 'text-[#E63946]' }}">
                                        {{ $balance >= 0 ? '' : '-' }}{{ $group->currency }} {{ number_format(abs($balance), 2) }}
                                    </span>

                                    <!-- Remove member button (owner only) -->
                                    @if($group->created_by === Auth::id() && $member->id !== Auth::id())
                                        <form method="POST" action="{{ route('groups.removeMember', $group) }}">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $member->id }}">
                                            <button type="submit" onclick="return confirm('Remove member from group?')"
                                                    class="text-[9px] font-bold text-red-400 hover:text-red-600 transition outline-none">Remove</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Footer Link -->
                    <div class="text-center mt-6 pt-3 border-t border-slate-100/60">
                        <a href="#" class="text-[#6C3AF4] hover:text-[#592BD4] text-xs font-bold transition">View all members</a>
                    </div>
                </section>

                <!-- B. QUICK ACTIONS -->
                <section class="bg-white/80 backdrop-blur-sm border border-white/60 rounded-3xl p-6 shadow-lg card-lift">
                    <h2 class="heading-font text-lg font-extrabold text-[#1A103C] tracking-tight mb-5">Quick Actions</h2>

                    <div class="flex gap-3">
                        <!-- Add Expense Trigger -->
                        <button @click="activeModal = 'add-expense'" 
                                class="flex-grow py-2.5 bg-[#6C3AF4]/5 border border-[#6C3AF4]/15 hover:bg-gradient-to-r hover:from-[#6C3AF4] hover:to-[#B026F3] hover:border-transparent hover:text-white text-[#6C3AF4] rounded-xl font-bold text-[10px] tracking-wide transition transform active:scale-97 flex items-center justify-center gap-1 outline-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                            </svg>
                            Add Expense
                        </button>

                        <!-- Settle Up Trigger -->
                        <button @click="activeModal = 'settle'" 
                                class="flex-grow py-2.5 bg-[#B026F3]/5 border border-[#B026F3]/15 hover:bg-gradient-to-r hover:from-[#B026F3] hover:to-[#6C3AF4] hover:border-transparent hover:text-white text-[#B026F3] rounded-xl font-bold text-[10px] tracking-wide transition transform active:scale-97 flex items-center justify-center gap-1 outline-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25"></path>
                            </svg>
                            Settle Up
                        </button>

                        <!-- Invite Friends Trigger -->
                        <button @click="activeModal = 'edit-group'; groupTab = 'members'" 
                                class="flex-grow py-2.5 bg-slate-50 border border-slate-200/80 hover:bg-slate-800 hover:border-transparent hover:text-white text-slate-700 rounded-xl font-bold text-[10px] tracking-wide transition transform active:scale-97 flex items-center justify-center gap-1 outline-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m2.25-9h1.5a2.25 2.25 0 012.25 2.25v13.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V5.25A2.25 2.25 0 013.75 3h1.5m10.5 0v3.75c0 .621.504 1.125 1.125 1.125h3.75M9 16.5h1.5M9 13.5h3.75m-3.75-3h3.75"></path>
                            </svg>
                            Invite Friends
                        </button>
                    </div>
                </section>

            </div>

        </div>

    </main>

    <!-- ==================== PORTAL POPUPS (INTERACTIVE MODALS) ==================== -->
    
    <!-- Backdrop Blur Tint -->
    <div class="fixed inset-0 bg-[#1A103C]/40 backdrop-blur-sm z-40 transition-all duration-400"
         x-show="activeModal"
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-sm"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 backdrop-blur-sm"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         @click="activeModal = ''"
         style="display: none;">
    </div>

    <!-- Modals Layout Frame -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
         x-show="activeModal"
         style="display: none;">

        <!-- 1. ADD EXPENSE POPUP MODAL -->
        <div class="bg-white/85 backdrop-blur-2xl border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[460px] p-8 relative transition-all duration-400"
             x-show="activeModal === 'add-expense'"
             x-data="{ splitType: 'equal', selectedMembers: [ @foreach($members as $m) '{{ $m->id }}', @endforeach ], expenseAmount: 0 }"
             x-transition:enter="transition ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-12 scale-[0.96]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-12 scale-[0.96]"
             @click.away="activeModal = ''">

            <button @click="activeModal = ''" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-200/50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center mb-6">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 mx-auto mb-4" alt="PayPatch Logo">
                <h2 class="heading-font text-2xl font-extrabold text-[#1A103C] tracking-tight">Add Expense</h2>
                <p class="text-[#5E5873] text-[0.85rem] font-medium mt-1">Record a new shared expense in this group.</p>
            </div>

            @if ($errors->any() && session('modal') === 'add-expense')
                <div class="mb-4 p-3.5 bg-red-50 border border-red-100 rounded-xl flex flex-col gap-1">
                    @foreach ($errors->all() as $error)
                        <span class="text-[11px] text-red-600 font-semibold">{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('expenses.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="group_id" value="{{ $group->id }}">

                <!-- TITLE -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Title</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.3m18 0V5.25A2.25 2.25 0 0018.75 3h-4.318M12 4.5v15m0-15h.008v.008H12V4.5zm0 15h.008v.008H12v-.008z"></path>
                            </svg>
                        </span>
                        <input type="text" name="title" required placeholder="e.g. Scooter rental, Dinner" value="{{ old('title') }}"
                               class="block w-full pl-11 pr-4 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>
                </div>

                <!-- AMOUNT -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Amount ({{ $group->currency }})</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <span class="text-sm font-extrabold">{{ $group->currency }}</span>
                        </span>
                        <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00" x-model.number="expenseAmount"
                               class="block w-full pl-11 pr-4 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>
                </div>

                <!-- PAID BY -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Paid By</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                            </svg>
                        </span>
                        <select name="paid_by" required
                                class="block w-full pl-11 pr-10 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm appearance-none cursor-pointer">
                            @foreach($members as $m)
                                <option value="{{ $m->id }}" {{ $m->id == Auth::id() ? 'selected' : '' }}>
                                    {{ $m->id == Auth::id() ? 'You' : $m->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- SPLIT TYPE -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Split Type</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0112 21c-1.07 0-2.097-.148-3.07-.423v-.109m0 0C8.43 19.36 8 18.232 8 17c0-1.127.43-2.155 1.13-2.91M8 17v-.003c0-1.113.285-2.16.786-3.07M8 17h-.008v-.008H8V17zm0 0l-.008-.008v.008zm0 0v.109a11.386 11.386 0 01-3 1.011m0 0a4.125 4.125 0 01-2.513-4.062 4.125 4.125 0 015.026-3.953m0 0A9.337 9.337 0 0012 9.5a9.38 9.38 0 002.625.372M12 9.5c.38 0 .753-.016 1.121-.048m-1.121.048a9.38 9.38 0 01-2.625-.372"></path>
                            </svg>
                        </span>
                        <select name="split_type" x-model="splitType" required
                                class="block w-full pl-11 pr-10 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm appearance-none cursor-pointer">
                            <option value="equal">Split Equally</option>
                            <option value="custom">Split by Selected Members</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- CUSTOM SPLITS (SELECT MEMBERS) SECTION -->
                <div x-show="splitType === 'custom'" x-transition class="mt-4 p-4.5 bg-slate-50 border border-slate-100 rounded-2xl space-y-3">
                    <label class="block text-[10px] font-bold text-[#1A103C]/80 uppercase tracking-wider mb-2">Select Members to Split With</label>
                    <div class="space-y-3 max-h-[180px] overflow-y-auto pr-1">
                        @foreach($members as $m)
                            <label class="flex items-center justify-between p-2 hover:bg-white rounded-xl transition cursor-pointer border border-transparent hover:border-slate-100 select-none">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-7 w-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-extrabold text-[10px] uppercase border border-slate-200/30">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-bold text-[#1A103C]">{{ $m->name }}</span>
                                </div>
                                <input type="checkbox" name="selected_members[]" value="{{ $m->id }}"
                                       x-model="selectedMembers"
                                       class="rounded text-[#6C3AF4] focus:ring-[#6C3AF4]/15 h-4.5 w-4.5 border-slate-200 cursor-pointer">
                            </label>
                        @endforeach
                    </div>

                    <!-- Running Total Helper -->
                    <div class="mt-3 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-400">Split Result:</span>
                        <span class="text-emerald-600">
                            Each pays: {{ $group->currency }} <span x-text="selectedMembers.length > 0 ? Number(expenseAmount / selectedMembers.length).toFixed(2) : '0.00'"></span> (<span x-text="selectedMembers.length"></span> selected)
                        </span>
                    </div>
                </div>

                <button type="submit" 
                        class="block w-full py-3.5 mt-4 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white font-bold rounded-full shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 text-[11px] uppercase tracking-widest outline-none">
                    Save Expense
                </button>
            </form>
        </div>

        <!-- 1.1 EDIT EXPENSE POPUP MODAL -->
        <div class="bg-white/85 backdrop-blur-2xl border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[460px] p-8 relative transition-all duration-400"
             x-show="activeModal === 'edit-expense'"
             x-transition:enter="transition ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-12 scale-[0.96]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-12 scale-[0.96]"
             @click.away="activeModal = ''">

            <button @click="activeModal = ''" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-200/50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center mb-6">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 mx-auto mb-4" alt="PayPatch Logo">
                <h2 class="heading-font text-2xl font-extrabold text-[#1A103C] tracking-tight">Edit Expense</h2>
                <p class="text-[#5E5873] text-[0.85rem] font-medium mt-1">Configure details for this recorded expense.</p>
            </div>

            @if ($errors->any() && session('modal') && str_starts_with(session('modal'), 'edit-expense-'))
                <div class="mb-4 p-3.5 bg-red-50 border border-red-100 rounded-xl flex flex-col gap-1">
                    @foreach ($errors->all() as $error)
                        <span class="text-[11px] text-red-600 font-semibold">{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form method="POST" :action="'/expenses/' + editingExpense.id" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- TITLE -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Title</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.3m18 0V5.25A2.25 2.25 0 0018.75 3h-4.318M12 4.5v15m0-15h.008v.008H12V4.5zm0 15h.008v.008H12v-.008z"></path>
                            </svg>
                        </span>
                        <input type="text" name="title" required placeholder="e.g. Scooter rental, Dinner" x-model="editingExpense.title"
                               class="block w-full pl-11 pr-4 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>
                </div>

                <!-- AMOUNT -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Amount ({{ $group->currency }})</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <span class="text-sm font-extrabold">{{ $group->currency }}</span>
                        </span>
                        <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00" x-model.number="editingExpense.amount"
                               class="block w-full pl-11 pr-4 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>
                </div>

                <!-- PAID BY -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Paid By</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                            </svg>
                        </span>
                        <select name="paid_by" x-model="editingExpense.paid_by" required
                                class="block w-full pl-11 pr-10 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm appearance-none cursor-pointer">
                            @foreach($members as $m)
                                <option value="{{ $m->id }}">
                                    {{ $m->id == Auth::id() ? 'You' : $m->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- SPLIT TYPE -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Split Type</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0112 21c-1.07 0-2.097-.148-3.07-.423v-.109m0 0C8.43 19.36 8 18.232 8 17c0-1.127.43-2.155 1.13-2.91M8 17v-.003c0-1.113.285-2.16.786-3.07M8 17h-.008v-.008H8V17zm0 0l-.008-.008v.008zm0 0v.109a11.386 11.386 0 01-3 1.011m0 0a4.125 4.125 0 01-2.513-4.062 4.125 4.125 0 015.026-3.953m0 0A9.337 9.337 0 0012 9.5a9.38 9.38 0 002.625.372M12 9.5c.38 0 .753-.016 1.121-.048m-1.121.048a9.38 9.38 0 01-2.625-.372"></path>
                            </svg>
                        </span>
                        <select name="split_type" x-model="editingExpense.split_type" required
                                class="block w-full pl-11 pr-10 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm appearance-none cursor-pointer">
                            <option value="equal">Split Equally</option>
                            <option value="custom">Split by Selected Members</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- CUSTOM SPLITS (SELECT MEMBERS) SECTION -->
                <div x-show="editingExpense.split_type === 'custom'" x-transition class="mt-4 p-4.5 bg-slate-50 border border-slate-100 rounded-2xl space-y-3">
                    <label class="block text-[10px] font-bold text-[#1A103C]/80 uppercase tracking-wider mb-2">Select Members to Split With</label>
                    <div class="space-y-3 max-h-[180px] overflow-y-auto pr-1">
                        @foreach($members as $m)
                            <label class="flex items-center justify-between p-2 hover:bg-white rounded-xl transition cursor-pointer border border-transparent hover:border-slate-100 select-none">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-7 w-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-extrabold text-[10px] uppercase border border-slate-200/30">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-bold text-[#1A103C]">{{ $m->name }}</span>
                                </div>
                                <input type="checkbox" name="selected_members[]" value="{{ $m->id }}"
                                       x-model="editingExpense.selected_members"
                                       class="rounded text-[#6C3AF4] focus:ring-[#6C3AF4]/15 h-4.5 w-4.5 border-slate-200 cursor-pointer">
                            </label>
                        @endforeach
                    </div>

                    <!-- Running Total Helper -->
                    <div class="mt-3 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-400">Split Result:</span>
                        <span class="text-emerald-600">
                            Each pays: {{ $group->currency }} <span x-text="editingExpense.selected_members && editingExpense.selected_members.length > 0 ? Number(editingExpense.amount / editingExpense.selected_members.length).toFixed(2) : '0.00'"></span> (<span x-text="editingExpense.selected_members ? editingExpense.selected_members.length : 0"></span> selected)
                        </span>
                    </div>
                </div>

                <button type="submit" 
                        class="block w-full py-3.5 mt-4 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white font-bold rounded-full shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 text-[11px] uppercase tracking-widest outline-none">
                    Save Changes
                </button>
            </form>
        </div>

        <!-- 2. SETTLE UP POPUP MODAL -->
        <div class="bg-white/85 backdrop-blur-2xl border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[440px] p-8 relative transition-all duration-400"
             x-show="activeModal === 'settle'"
             x-transition:enter="transition ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-12 scale-[0.96]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-12 scale-[0.96]"
             @click.away="activeModal = ''">

            <button @click="activeModal = ''" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-200/50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center mb-6">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 mx-auto mb-4" alt="PayPatch Logo">
                <h2 class="heading-font text-2xl font-extrabold text-[#1A103C] tracking-tight">Record Payment</h2>
                <p class="text-[#5E5873] text-[0.85rem] font-medium mt-1">Record a balance settlement directly to a friend.</p>
            </div>

            <form method="POST" action="{{ route('groups.settle', $group) }}" class="space-y-4">
                @csrf

                <!-- RECIPIENT -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Pay To</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                            </svg>
                        </span>
                        <select id="settle-to" name="to_user_id" required
                                class="block w-full pl-11 pr-10 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm appearance-none cursor-pointer">
                            <option value="" disabled selected>Select member...</option>
                            @foreach($members as $m)
                                @if($m->id !== Auth::id())
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- AMOUNT -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Amount ({{ $group->currency }})</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <span class="text-sm font-extrabold">{{ $group->currency }}</span>
                        </span>
                        <input id="settle-amt" type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00"
                               class="block w-full pl-11 pr-4 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>
                </div>

                <!-- NOTE -->
                <div>
                    <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Note</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                            </svg>
                        </span>
                        <input type="text" name="note" placeholder="Note (optional)"
                               class="block w-full pl-11 pr-4 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>
                </div>

                <div class="flex flex-col gap-3 mt-4">
                    <!-- Record Payment Button (formaction points to direct settle) -->
                    <button type="submit" formaction="{{ route('groups.settle', $group) }}"
                            class="block w-full py-3.5 bg-[#10B981] hover:bg-[#059669] text-white font-bold rounded-xl shadow-lg shadow-emerald-600/10 transition transform active:scale-98 text-sm outline-none text-center">
                        Record Payment Directly
                    </button>

                    <!-- Request Settle Button (formaction points to settle request) -->
                    <button type="submit" formaction="{{ route('groups.settleRequest', $group) }}"
                            class="block w-full py-3.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#592BD4] hover:to-[#9E1CE0] text-white font-bold rounded-full shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 text-[11px] uppercase tracking-widest outline-none text-center">
                        Ask to Settle Up
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. EDIT GROUP POPUP MODAL -->
        <div class="bg-white/95 backdrop-blur-2xl border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[720px] p-10 md:p-12 relative transition-all duration-400 max-h-[90vh] overflow-y-auto min-h-[550px] flex flex-col justify-between"
             x-show="activeModal === 'edit-group'"
             x-transition:enter="transition ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-12 scale-[0.96]"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-12 scale-[0.96]"
             @click.away="activeModal = ''">

            <button @click="activeModal = ''" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-200/50 z-10">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center mb-8">
                <h2 class="heading-font text-3xl font-extrabold text-[#1A103C] tracking-tight">Edit Group Details</h2>
                <p class="text-[#5E5873] text-[0.95rem] font-medium mt-1.5">Configure group settings, cover image, currency, and members.</p>
            </div>

            <!-- Tabs / Sections: 1. Settings 2. Members 3. Danger Zone -->
            <div class="space-y-8 flex-grow flex flex-col justify-between">
                <!-- Tab Headers -->
                <div class="flex border-b border-slate-100 pb-3 gap-6">
                    <button @click="groupTab = 'settings'" 
                            :class="groupTab === 'settings' ? 'text-[#6C3AF4] border-b-2 border-[#6C3AF4] font-bold text-base' : 'text-slate-400 font-semibold text-base'"
                            class="pb-1.5 outline-none transition">
                        Settings
                    </button>
                    <button @click="groupTab = 'members'" 
                            :class="groupTab === 'members' ? 'text-[#6C3AF4] border-b-2 border-[#6C3AF4] font-bold text-base' : 'text-slate-400 font-semibold text-base'"
                            class="pb-1.5 outline-none transition flex items-center gap-2">
                        Members
                        <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full font-bold">{{ $members->count() }}</span>
                    </button>
                    <button @click="groupTab = 'danger'" 
                            :class="groupTab === 'danger' ? 'text-red-600 border-b-2 border-red-600 font-bold text-base' : 'text-slate-400 font-semibold text-base'"
                            class="pb-1.5 outline-none transition">
                        Danger Zone
                    </button>
                </div>

                <!-- Tab 1: Settings Form -->
                <div x-show="groupTab === 'settings'" class="space-y-6 flex-grow">
                    <form method="POST" action="{{ route('groups.update', $group) }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column: Info -->
                            <div class="space-y-6">
                                <!-- GROUP NAME -->
                                <div>
                                    <label class="block text-xs font-extrabold text-[#1A103C] uppercase tracking-wider mb-2 ml-1">Group Name</label>
                                    <input type="text" name="name" value="{{ $group->name }}" required
                                           class="block w-full px-4 py-3.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm font-semibold">
                                </div>

                                <!-- CURRENCY SELECT -->
                                <div>
                                    <label class="block text-xs font-extrabold text-[#1A103C] uppercase tracking-wider mb-2 ml-1">Currency</label>
                                    <div class="relative">
                                        <select name="currency" required
                                                class="block w-full px-4 py-3.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm font-semibold appearance-none cursor-pointer">
                                            <option value="LKR" {{ $group->currency === 'LKR' ? 'selected' : '' }}>LKR (Rs.)</option>
                                            <option value="USD" {{ $group->currency === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                            <option value="EUR" {{ $group->currency === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                            <option value="GBP" {{ $group->currency === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                            <option value="AUD" {{ $group->currency === 'AUD' ? 'selected' : '' }}>AUD (A$)</option>
                                            <option value="JPY" {{ $group->currency === 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                                        </select>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Covers -->
                            <div class="space-y-6">
                                <!-- PRESET COVER PICKER -->
                                <div x-data="{ selectedPreset: '{{ str_starts_with($group->cover_image_path, 'preset:') ? str_replace('preset:', '', $group->cover_image_path) : '' }}' }">
                                    <label class="block text-xs font-extrabold text-[#1A103C] uppercase tracking-wider mb-2.5 ml-1">Preset Cover Gradient</label>
                                    <input type="hidden" name="preset_cover" :value="selectedPreset">
                                    
                                    <div class="grid grid-cols-5 gap-2">
                                        <!-- Sunrise -->
                                        <button type="button" @click="selectedPreset = 'sunrise'" 
                                                :class="selectedPreset === 'sunrise' ? 'ring-2 ring-purple-600 scale-105' : ''"
                                                class="h-10 rounded-xl bg-gradient-to-tr from-[#FF512F] to-[#DD2476] transition hover:scale-103 active:scale-97" title="Sunrise"></button>
                                        
                                        <!-- Ocean -->
                                        <button type="button" @click="selectedPreset = 'ocean'" 
                                                :class="selectedPreset === 'ocean' ? 'ring-2 ring-purple-600 scale-105' : ''"
                                                class="h-10 rounded-xl bg-gradient-to-tr from-[#2193b0] to-[#6dd5ed] transition hover:scale-103 active:scale-97" title="Ocean"></button>
                                        
                                        <!-- Deep Space -->
                                        <button type="button" @click="selectedPreset = 'deepspace'" 
                                                :class="selectedPreset === 'deepspace' ? 'ring-2 ring-purple-600 scale-105' : ''"
                                                class="h-10 rounded-xl bg-gradient-to-tr from-[#0F2027] to-[#2C5364] transition hover:scale-103 active:scale-97" title="Deep Space"></button>
                                        
                                        <!-- Dusk -->
                                        <button type="button" @click="selectedPreset = 'dusk'" 
                                                :class="selectedPreset === 'dusk' ? 'ring-2 ring-purple-600 scale-105' : ''"
                                                class="h-10 rounded-xl bg-gradient-to-tr from-[#2C3E50] to-[#FD746C] transition hover:scale-103 active:scale-97" title="Dusk"></button>
                                        
                                        <!-- Cyberpunk -->
                                        <button type="button" @click="selectedPreset = 'cyberpunk'" 
                                                :class="selectedPreset === 'cyberpunk' ? 'ring-2 ring-purple-600 scale-105' : ''"
                                                class="h-10 rounded-xl bg-gradient-to-tr from-[#F107A3] to-[#7B2CBF] transition hover:scale-103 active:scale-97" title="Cyberpunk"></button>
                                    </div>
                                </div>

                                <!-- CUSTOM COVER UPLOAD -->
                                <div>
                                    <label class="block text-xs font-extrabold text-[#1A103C] uppercase tracking-wider mb-2 ml-1">Or Upload Custom Cover Image</label>
                                    <input type="file" name="cover_image" accept="image/*"
                                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-[#6C3AF4]/10 file:text-[#6C3AF4] hover:file:bg-[#6C3AF4]/20 transition cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <button type="submit" 
                                class="block w-full py-4 mt-6 bg-[#6C3AF4] hover:bg-[#592BD4] text-white font-extrabold rounded-xl shadow-lg shadow-[#6C3AF4]/20 transition transform active:scale-98 text-sm outline-none">
                            Save Group Settings
                        </button>
                    </form>
                </div>


                <!-- Tab 2: Member Management -->
                <div x-show="groupTab === 'members'" class="space-y-4" style="display: none;">
                    <!-- Invite new member form -->
                    <form method="POST" action="{{ route('groups.addMember', $group) }}" class="space-y-3 pb-4 border-b border-slate-100">
                        @csrf
                        <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider ml-1">Invite New Member</label>
                        <div class="flex gap-2">
                            <input type="email" name="email" required placeholder="friend@example.com"
                                   class="block flex-grow px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                            <button type="submit" class="px-5 py-2.5 bg-[#6C3AF4] hover:bg-[#592BD4] text-white font-bold rounded-xl text-xs transition">
                                Add
                            </button>
                        </div>
                    </form>

                    <!-- Existing Members List -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider ml-1">Current Members</label>
                        <div class="max-h-[220px] overflow-y-auto space-y-2 pr-1">
                            @foreach($members as $member)
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-100 rounded-xl">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $member->profile_photo_url }}" 
                                             class="h-7 w-7 rounded-full object-cover border border-slate-200/60 shadow-sm" 
                                             alt="{{ $member->name }}">
                                        <div>
                                            <span class="text-xs font-extrabold text-[#1A103C]">{{ $member->name }}</span>
                                            @if($member->id === $group->created_by)
                                                <span class="text-[8px] bg-purple-100 text-purple-700 font-extrabold px-1 py-0.25 rounded-md ml-1">Owner</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($group->created_by === Auth::id() && $member->id !== Auth::id())
                                        <form method="POST" action="{{ route('groups.removeMember', $group) }}">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $member->id }}">
                                            <button type="submit" onclick="return confirm('Remove {{ $member->name }} from group?')"
                                                    class="text-[10px] font-extrabold text-red-500 hover:text-red-700 transition">
                                                Remove
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Danger Zone -->
                <div x-show="groupTab === 'danger'" class="space-y-4" style="display: none;">
                    <div class="p-4 bg-red-50 border border-red-200 rounded-2xl">
                        <h4 class="text-red-800 font-bold text-sm">Delete Group</h4>
                        <p class="text-red-700 text-xs mt-1">This action is irreversible. All expenses, settlements, shares, and activity logs will be permanently deleted.</p>
                    </div>

                    @if($group->created_by === Auth::id())
                        <form method="POST" action="{{ route('groups.destroy', $group) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you absolutely sure you want to delete this entire group? This cannot be undone!')"
                                    class="w-full py-3.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg shadow-red-600/10 transition transform active:scale-98 text-sm outline-none">
                                Delete Group Permanently
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-slate-400 italic text-center py-4">Only the group creator/owner can delete this group.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

</body>
</html>
