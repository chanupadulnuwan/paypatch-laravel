<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — Create Account & Select Plan</title>

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
        }
        .heading-font {
            font-family: 'Space Grotesk', sans-serif;
        }
        .heading-serif {
            font-family: 'Fraunces', serif;
        }
    </style>
</head>
@php
    // Fetch packages dynamically from database for maximum fidelity and details mapping
    $packages = \App\Models\Package::all();
    if($packages->isEmpty()) {
        // Fallback array if table is empty
        $packages = collect([
            (object)[
                'name' => 'Free Tier',
                'price' => 0.00,
                'discount_percent' => 0,
                'features_list' => ['Limit of 10 members per group', 'Standard splits (Equal & Checklist)', '2-Way Settle Up requests']
            ],
            (object)[
                'name' => 'Premium',
                'price' => 9.99,
                'discount_percent' => 15,
                'features_list' => ['Limit of 50 members per group', 'Custom percent & weight split tools', 'Priority support response']
            ],
            (object)[
                'name' => 'Premium Plus',
                'price' => 19.99,
                'discount_percent' => 20,
                'features_list' => ['All Premium plan features', 'Smart AI settlement optimizer', 'Unlimited transaction history']
            ]
        ]);
    }
@endphp
<body class="h-full min-h-screen bg-slate-900 text-slate-800 antialiased overflow-y-auto relative py-12 px-4"
      x-data="{ 
          selectedPlan: 'Free Tier', 
          showPw: false, 
          showConfirmPw: false 
      }">

    <!-- FULL SCREEN BACKGROUND BLUR -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-all duration-500 z-0 select-none pointer-events-none"
         style="background-image: url('{{ asset('assets/auth-bg.jpg') }}?v=3'); filter: blur(5px); transform: scale(1.02);">
    </div>

    <!-- MAIN BOX WRAPPER -->
    <div class="relative z-10 w-full max-w-6xl mx-auto bg-white/90 backdrop-blur-2xl border border-white/60 shadow-2xl rounded-[2.5rem] overflow-hidden grid grid-cols-1 lg:grid-cols-12">
        
        <!-- ==================== LEFT COLUMN: SELECT PRICING PLAN ==================== -->
        <div class="lg:col-span-7 p-8 md:p-10 border-b lg:border-b-0 lg:border-r border-slate-200/60 bg-gradient-to-br from-slate-50/50 to-white/30 flex flex-col justify-between">
            <div>
                <!-- Brand logo and back link -->
                <div class="flex items-center justify-between mb-8">
                    <a href="/" class="flex items-center outline-none">
                        <img src="{{ asset('assets/logo.png') }}?v=3" class="h-9 transition transform hover:scale-105 active:scale-95" alt="PayPatch Logo">
                    </a>
                    <a href="/" class="text-xs font-bold text-slate-400 hover:text-[#6C3AF4] transition flex items-center gap-1">
                        ← Back to landing
                    </a>
                </div>

                <div class="mb-6">
                    <h2 class="heading-font text-2xl md:text-3xl font-extrabold text-[#1A103C] tracking-tight">Select Your Plan</h2>
                    <p class="text-slate-500 text-xs font-semibold mt-1">Select a membership package that suits your bill splitting dynamics.</p>
                </div>

                <!-- Pricing Cards list -->
                <div class="grid grid-cols-1 gap-4">
                    @foreach($packages as $pkg)
                        @php
                            $features = is_array($pkg->features_list) ? $pkg->features_list : explode(',', $pkg->features);
                            // Set custom badge based on plan
                            $badge = '';
                            if($pkg->name === 'Premium') {
                                $badge = 'Best Seller';
                            } elseif($pkg->name === 'Premium Plus') {
                                $badge = 'AI Enabled';
                            }
                        @endphp
                        <!-- Card Card selection container -->
                        <div @click="selectedPlan = '{{ $pkg->name }}'"
                             :class="selectedPlan === '{{ $pkg->name }}' ? 'border-[#6C3AF4] bg-[#6C3AF4]/5 ring-2 ring-[#6C3AF4]/15 scale-[1.01] shadow-lg shadow-purple-500/5' : 'border-slate-200/80 hover:bg-slate-50/50 cursor-pointer bg-white'"
                             class="border rounded-3xl p-5 transition duration-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden group select-none">
                            
                            <!-- Left Info -->
                            <div class="flex-grow">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-extrabold text-[#1A103C] text-sm md:text-base">{{ $pkg->name }}</h3>
                                    @if(!empty($badge))
                                        <span class="bg-[#6C3AF4]/10 text-[#6C3AF4] text-[8px] font-black uppercase px-2 py-0.5 rounded-full">
                                            {{ $badge }}
                                        </span>
                                    @endif
                                    @if($pkg->discount_percent > 0)
                                        <span class="bg-rose-500/10 text-rose-600 text-[8px] font-black uppercase px-2 py-0.5 rounded-full">
                                            {{ $pkg->discount_percent }}% OFF
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Features checklist list -->
                                <ul class="mt-2.5 space-y-1">
                                    @foreach($features as $f)
                                        <li class="flex items-center gap-1.5 text-[11px] text-slate-500 font-semibold">
                                            <span class="text-emerald-500">✓</span>
                                            {{ trim($f) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Right Pricing Info -->
                            <div class="text-right md:min-w-[120px] flex-shrink-0 flex flex-col justify-center items-end">
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">Pricing</span>
                                <h4 class="heading-font text-lg md:text-xl font-black text-[#1A103C] mt-0.5">
                                    {{ $pkg->price == 0.00 ? 'Free' : '$' . number_format($pkg->price, 2) }}
                                </h4>
                                <span class="text-[9px] text-slate-400 font-medium block">per month</span>
                            </div>

                            <!-- Selected Pill overlay indicator inside card -->
                            <div class="absolute top-4 right-4 h-5 w-5 rounded-full bg-[#6C3AF4] flex items-center justify-center text-white"
                                 x-show="selectedPlan === '{{ $pkg->name }}'"
                                 x-transition style="display: none;">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Left Footer note -->
            <div class="mt-8 border-t border-slate-200/60 pt-4 flex items-center gap-2 text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                🔒 SSL Encrypted &bull; 💳 Secure checkout &bull; 🔄 Cancel anytime
            </div>
        </div>

        <!-- ==================== RIGHT COLUMN: REGISTRATION SIGNUP FORM ==================== -->
        <div class="lg:col-span-5 p-8 md:p-10 bg-white flex flex-col justify-center">
            
            <div class="mb-6 text-center lg:text-left">
                <h2 class="heading-font text-2xl md:text-3xl font-extrabold text-[#1A103C] tracking-tight">Create Account</h2>
                <p class="text-slate-500 text-xs font-semibold mt-1">Fill in the fields below to complete signup.</p>
            </div>

            <!-- SERVER VALIDATION ERRORS -->
            @if ($errors->any())
                <div class="mb-4 text-xs text-red-600 bg-red-50/70 p-3.5 rounded-2xl border border-red-200/50">
                    <ul class="list-disc list-inside space-y-0.5 font-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- REGISTER FORM -->
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- BIND SELECTED PLAN DYNAMICALLY -->
                <input type="hidden" name="plan" :value="selectedPlan">

                <!-- NAME FIELD -->
                <div>
                    <label for="reg-name" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Full Name</label>
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
                    <label for="reg-email" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Email Address</label>
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

                <!-- COUNTRY DROPDOWN -->
                <div>
                    <label for="reg-country" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Country</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2m-4-7a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                        <select id="reg-country" name="country" required
                                class="block w-full pl-11 pr-10 py-2.5 bg-[#F8F8FC] border border-slate-200/80 rounded-xl text-slate-850 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:border-[#6C3AF4] transition text-sm appearance-none cursor-pointer">
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

                <!-- PASSWORD -->
                <div>
                    <label for="reg-password" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <input id="reg-password" :type="showPw ? 'text' : 'password'" name="password" required autocomplete="new-password"
                               placeholder="••••••••••••"
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

                <!-- CONFIRM PASSWORD -->
                <div>
                    <label for="reg-password-confirm" class="block text-xs font-bold text-[#1A103C] uppercase tracking-wider mb-1.5 ml-1">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#8C8BA5]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <input id="reg-password-confirm" :type="showConfirmPw ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                               placeholder="••••••••••••"
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

                <!-- SUBMIT BUTTON -->
                <button type="submit" 
                        class="block w-full py-3.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] hover:from-[#592BD4] hover:to-[#9E1CE0] text-white font-extrabold rounded-xl shadow-lg shadow-[#6C3AF4]/15 transition transform active:scale-98 text-sm outline-none">
                    Create Account & Select Plan
                </button>

                <!-- SEPARATOR -->
                <div class="relative my-4 flex items-center">
                    <div class="flex-grow border-t border-slate-200"></div>
                    <span class="flex-shrink mx-4 text-xs font-bold text-slate-400 uppercase tracking-widest">or</span>
                    <div class="flex-grow border-t border-slate-200"></div>
                </div>

                <!-- GOOGLE SIGN UP BUTTON -->
                <a :href="'{{ route('auth.google') }}?plan=' + selectedPlan" 
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

            <!-- FOOTER LINKS -->
            <div class="text-center mt-5">
                <p class="text-slate-500 text-xs font-semibold">
                    Already have an account? 
                    <a href="/?modal=login" class="text-[#6C3AF4] hover:text-[#592BD4] font-extrabold transition ml-0.5">Log in here</a>
                </p>
            </div>

        </div>

    </div>

</body>
</html>
