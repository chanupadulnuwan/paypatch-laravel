<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — Manage Packages</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'DM Sans', sans-serif; font-optical-sizing: auto; }
        .heading-display { font-family: 'Fraunces', serif; font-optical-sizing: auto; }
        .heading-serif   { font-family: 'Fraunces', serif; font-optical-sizing: auto; }
        .heading-font    { font-family: 'Space Grotesk', sans-serif; }
        @keyframes slideUpFade { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        .animate-slide-up         { animation: slideUpFade 0.55s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-slide-up-delay-1 { animation: slideUpFade 0.55s 0.1s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-fade-in          { animation: fadeIn 0.4s ease both; }
        .card-lift { transition: all 0.28s cubic-bezier(0.16,1,0.3,1); }
        .card-lift:hover { transform: translateY(-3px); box-shadow: 0 16px 48px rgba(108,58,244,0.10); }
    </style>
</head>
<body class="h-full flex overflow-hidden text-[#1A103C]"
      style="background-image: url('/assets/park-bg.png'); background-size: cover; background-position: center; background-attachment: fixed;"
      x-data="{
          profileOpen: false,
          activeModal: '{{ session('modal') ?? '' }}',
          showCreateModal: false,
          showEditModal: false,
          editingPackage: {
              id: null,
              name: '',
              price: 0,
              discount_percent: 0,
              max_group_members: 10,
              max_groups: 5,
              features: []
          }
      }">

    <!-- ==================== LEFT SIDEBAR ==================== -->
    <aside class="w-72 flex-shrink-0 relative overflow-hidden bg-cover bg-center flex flex-col justify-between p-6 border-r border-[#1A103C]/5"
           style="background-image: url('{{ asset('assets/sidebar-bg.png') }}?v=3');">
        
        <div class="absolute inset-x-0 top-0 h-2/3 bg-gradient-to-b from-white via-white/95 to-white/40 pointer-events-none z-0"></div>

        <div class="relative z-10 w-full flex flex-col gap-10">
            <!-- LOGO -->
            <a href="{{ route('admin') }}" class="flex items-center outline-none">
                <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 transition transform hover:scale-103 active:scale-97" alt="PayPatch Logo">
            </a>

            <!-- SIDEBAR NAV ITEMS (ADMIN FOCUS) -->
            <nav class="flex flex-col gap-2.5">
                <a href="{{ route('admin') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.packages') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Packages
                </a>

                <a href="{{ route('admin.activity') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    Activity
                </a>

                <a href="{{ route('admin.insights') }}" 
                   class="flex items-center gap-3.5 px-4 py-3 hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9-1V4a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
                    </svg>
                    Insights
                </a>
            </nav>
        </div>

        <div class="relative z-20">
            <div @click="profileOpen = true" 
                 class="w-full flex items-center justify-between p-3.5 bg-white border border-[#1A103C]/10 rounded-2xl shadow-md cursor-pointer hover:bg-slate-50 transition transform active:scale-99">
                <div class="flex items-center gap-3">
                    @if(Auth::user()->profile_photo_path)
                        <img src="{{ Auth::user()->profile_photo_url }}" class="h-9 w-9 rounded-full object-cover border border-[#6C3AF4]/10 shadow">
                    @else
                        <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-[#6C3AF4] to-[#B026F3] flex items-center justify-center text-white font-bold text-sm shadow">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="font-extrabold text-sm text-[#1A103C]">Admin {{ explode(' ', Auth::user()->name)[0] }}</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <main class="flex-grow flex flex-col min-w-0 overflow-hidden bg-white/65 backdrop-blur-sm">
        
        <header class="h-20 bg-white border-b border-[#1A103C]/5 flex items-center justify-between px-8 flex-shrink-0 relative z-10">
            <div>
                <h1 class="heading-font text-2xl font-extrabold text-[#1A103C] tracking-tight">Manage Packages</h1>
                <p class="text-[#8C8BA5] text-xs font-semibold mt-0.5">Control global limits, subscription packages, prices, and plan tiers.</p>
            </div>

            <!-- Create New Package Button -->
            <button @click="showCreateModal = true"
                    class="px-5 py-2.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#6C3AF4]/95 hover:to-[#B026F3]/95 text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow shadow-[#6C3AF4]/20 transition transform active:scale-97 outline-none">
                + Create Package
            </button>
        </header>

        <div class="flex-grow overflow-y-auto p-8 space-y-8">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-800 text-sm font-semibold shadow-sm animate-fade-in">
                    <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- 1. DYNAMIC PLANS CARDS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($packages as $package)
                    @php
                        // Custom gradients based on package name
                        $borderTheme = 'border-[#1A103C]/5';
                        $accentColor = 'text-slate-800';
                        $badgeTheme = 'bg-slate-100 text-slate-500';
                        if (str_contains(strtolower($package->name), 'premium plus') || str_contains(strtolower($package->name), 'plus')) {
                            $borderTheme = 'border-t-4 border-t-[#B026F3] border-[#1A103C]/5';
                            $accentColor = 'text-[#B026F3]';
                            $badgeTheme = 'bg-[#B026F3]/10 text-[#B026F3]';
                        } elseif (str_contains(strtolower($package->name), 'premium')) {
                            $borderTheme = 'border-t-4 border-t-[#6C3AF4] border-[#1A103C]/5';
                            $accentColor = 'text-[#6C3AF4]';
                            $badgeTheme = 'bg-[#6C3AF4]/10 text-[#6C3AF4]';
                        }
                    @endphp
                    <div @click="
                            editingPackage = {
                                id: {{ $package->id }},
                                name: '{{ addslashes($package->name) }}',
                                price: {{ $package->price }},
                                discount_percent: {{ $package->discount_percent }},
                                max_group_members: {{ $package->max_group_members }},
                                max_groups: {{ $package->max_groups }},
                                features: [
                                    @foreach($package->features_list as $f)
                                        '{{ addslashes($f) }}',
                                    @endforeach
                                ]
                            };
                            showEditModal = true;
                         "
                         class="bg-white border {{ $borderTheme }} rounded-[2.2rem] p-7 shadow-sm relative overflow-hidden cursor-pointer hover:shadow-md hover:scale-101 transition-all duration-300 group select-none">
                        
                        <span class="absolute top-5 right-5 text-[9px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full {{ $badgeTheme }}">
                            {{ $package->price > 0 ? 'PAID' : 'FREE' }}
                        </span>

                        <h3 class="heading-font text-lg font-black {{ $accentColor }} group-hover:underline transition">{{ $package->name }}</h3>
                        
                        <div class="mt-4 flex items-baseline gap-1">
                            @if($package->price > 0)
                                @if($package->discount_percent > 0)
                                    @php
                                        $discounted = round($package->price * (1 - $package->discount_percent / 100), 2);
                                    @endphp
                                    <span class="heading-font text-3xl font-black text-[#1A103C]">${{ number_format($discounted, 2) }}</span>
                                    <span class="text-xs text-slate-400 line-through font-semibold">${{ number_format($package->price, 2) }}</span>
                                    <span class="text-[10px] font-bold bg-rose-50 text-rose-500 px-2 py-0.5 rounded-lg ml-1">-{{ $package->discount_percent }}%</span>
                                @else
                                    <span class="heading-font text-3xl font-black text-[#1A103C]">${{ number_format($package->price, 2) }}</span>
                                @endif
                                <span class="text-[10px] font-bold text-slate-400 lowercase ml-0.5">/mo</span>
                            @else
                                <span class="heading-font text-3xl font-black text-[#1A103C]">$0.00</span>
                                <span class="text-[10px] font-bold text-slate-400 lowercase ml-0.5">/forever</span>
                            @endif
                        </div>

                        <!-- Limits row -->
                        <div class="mt-4.5 bg-slate-50 border border-slate-100 rounded-2xl p-3.5 space-y-2 text-xs font-bold text-[#1A103C]">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Members Limit:</span>
                                <span class="text-slate-800">{{ $package->max_group_members >= 9999 ? 'Unlimited' : $package->max_group_members }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Groups Limit:</span>
                                <span class="text-slate-800">{{ $package->max_groups >= 9999 ? 'Unlimited' : $package->max_groups }}</span>
                            </div>
                        </div>

                        <!-- Bullet points -->
                        <div class="mt-6 space-y-3 pt-5 border-t border-slate-50 text-xs text-slate-600 font-bold">
                            @foreach($package->features_list as $f)
                                @if(!empty(trim($f)))
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-emerald-500">✓</span> {{ $f }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 2. GLOBAL SETTINGS ALIGNMENT -->
            <div class="bg-white border border-[#1A103C]/5 rounded-[2rem] p-6 shadow-sm">
                <h2 class="heading-font text-lg font-bold text-[#1A103C] mb-2">Global Setting Limits</h2>
                <p class="text-[#8C8BA5] text-xs font-medium mb-6">Assign general group member cap values applied to standard accounts globally.</p>

                <form method="POST" action="{{ route('admin.settings') }}" class="max-w-md space-y-4">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label for="max_group_members" class="text-xs font-bold text-[#1A103C] uppercase tracking-wider ml-1">Maximum Members Per Group (Free tier fallback)</label>
                        <input type="number" id="max_group_members" name="max_group_members"
                               value="{{ $maxGroupMembers }}" min="2" max="100" required
                               class="block w-full px-4 py-3 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm">
                    </div>

                    <button type="submit" 
                            class="px-5 py-3 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow shadow-[#6C3AF4]/20 transition active:scale-98 outline-none">
                        Save Global Setting
                    </button>
                </form>
            </div>

        </div>

    </main>

    <!-- ==================== CREATE PACKAGE MODAL ==================== -->
    <div x-show="showCreateModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="absolute inset-0 bg-[#1A103C]/20 backdrop-blur-sm" @click="showCreateModal = false"></div>

        <div class="bg-white border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[460px] p-8 relative z-10 transition-all duration-300"
             x-show="showCreateModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-96"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-96">

            <button @click="showCreateModal = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="text-center mb-6">
                <h3 class="heading-font text-2xl font-black text-[#1A103C] tracking-tight">Create Subscription Package</h3>
                <p class="text-[#8C8BA5] text-xs font-semibold mt-1">Design a brand new custom plan for your platform users.</p>
            </div>

            <form method="POST" action="{{ route('admin.storePackage') }}" class="space-y-4">
                @csrf

                <!-- Package Name -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Package Name</label>
                    <input type="text" name="name" required placeholder="e.g. Starter Pack, Enterprise"
                           class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                </div>

                <!-- Price and Discount -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Price (USD)</label>
                        <input type="number" name="price" step="0.01" min="0" required placeholder="0.00"
                               class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Discount (%)</label>
                        <input type="number" name="discount_percent" min="0" max="100" required placeholder="0"
                               class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                    </div>
                </div>

                <!-- Group and Member limits -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Max Group Size</label>
                        <input type="number" name="max_group_members" min="2" max="10000" required placeholder="10"
                               class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Max Active Groups</label>
                        <input type="number" name="max_groups" min="1" max="10000" required placeholder="5"
                               class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                    </div>
                </div>

                <!-- Features Bullets (Textarea helper where each line is a bullet) -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Plan Features (One per line)</label>
                    <textarea name="features[]" rows="3" placeholder="Feature bullet 1&#10;Feature bullet 2"
                              class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition"></textarea>
                </div>

                <button type="submit"
                        class="block w-full py-3.5 mt-6 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow shadow-[#6C3AF4]/20 transition transform active:scale-97 outline-none">
                    Save New Package
                </button>
            </form>
        </div>
    </div>

    <!-- ==================== EDIT PACKAGE MODAL ==================== -->
    <div x-show="showEditModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="absolute inset-0 bg-[#1A103C]/20 backdrop-blur-sm" @click="showEditModal = false"></div>

        <div class="bg-white border border-white/50 shadow-2xl rounded-[2.2rem] w-full max-w-[460px] p-8 relative z-10 transition-all duration-300"
             x-show="showEditModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-8 scale-96"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-96">

            <button @click="showEditModal = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 transition outline-none p-1.5 rounded-full hover:bg-slate-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Delete Package (if not Free Tier) -->
            <form x-show="editingPackage.name !== 'Free Tier'"
                  method="POST" :action="'/admin/packages/' + editingPackage.id" class="absolute top-6 left-6 inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Delete this subscription package plan?')"
                        class="text-xs font-bold text-rose-500 hover:text-rose-700 transition outline-none">
                    Delete Plan
                </button>
            </form>

            <div class="text-center mb-6">
                <h3 class="heading-font text-2xl font-black text-[#1A103C] tracking-tight">Edit Package Plan</h3>
                <p class="text-[#8C8BA5] text-xs font-semibold mt-1">Configure pricing, limits, and options for this package tier.</p>
            </div>

            <form method="POST" :action="'/admin/packages/' + editingPackage.id" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Package Name -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Package Name</label>
                    <input type="text" name="name" required x-model="editingPackage.name"
                           class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                </div>

                <!-- Price and Discount -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Price (USD)</label>
                        <input type="number" name="price" step="0.01" min="0" required x-model="editingPackage.price"
                               class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Discount (%)</label>
                        <input type="number" name="discount_percent" min="0" max="100" required x-model="editingPackage.discount_percent"
                               class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                    </div>
                </div>

                <!-- Group and Member limits -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Max Group Size</label>
                        <input type="number" name="max_group_members" min="2" max="10000" required x-model="editingPackage.max_group_members"
                               class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Max Active Groups</label>
                        <input type="number" name="max_groups" min="1" max="10000" required x-model="editingPackage.max_groups"
                               class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition">
                    </div>
                </div>

                <!-- Features Bullets (Textarea helper where each line is a bullet) -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Plan Features (One per line)</label>
                    <textarea name="features[]" rows="3" :value="editingPackage.features.join('\n')"
                              class="block w-full px-4 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition"></textarea>
                </div>

                <button type="submit"
                        class="block w-full py-3.5 mt-6 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow shadow-[#6C3AF4]/20 transition transform active:scale-97 outline-none">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

</body>
</html>
