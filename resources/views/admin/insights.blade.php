<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>PayPatch — Platform Insights</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Space+Grotesk:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js for analytics visualization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
      x-data="{ profileOpen: false }">

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
                   class="flex items-center gap-3.5 px-4 py-3 hover:bg-[#1A103C]/5 text-[#1A103C]/70 hover:text-[#1A103C] font-semibold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                   class="flex items-center gap-3.5 px-4 py-3 bg-[#6C3AF4]/10 border border-[#6C3AF4]/15 text-[#6C3AF4] font-bold rounded-2xl text-sm transition transform active:scale-98">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
    </aside>

    <!-- ==================== MAIN CONTENT AREA ==================== -->
    <main class="flex-grow flex flex-col min-w-0 overflow-hidden bg-white/65 backdrop-blur-sm">
        
        <header class="h-20 bg-white border-b border-[#1A103C]/5 flex items-center justify-between px-8 flex-shrink-0 relative z-10">
            <div>
                <h1 class="heading-font text-2xl font-extrabold text-[#1A103C] tracking-tight">Platform Insights</h1>
                <p class="text-[#8C8BA5] text-xs font-semibold mt-0.5">Explore detailed financial tracking, user expansion, and platform statistics.</p>
            </div>
        </header>

        <div class="flex-grow overflow-y-auto p-8 space-y-6">

            <!-- 1. METRICS GRID -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Registered Users -->
                <div class="bg-white border border-[#1A103C]/5 rounded-[2rem] p-6 shadow-sm">
                    <span class="text-[10px] font-bold text-[#8C8BA5] uppercase tracking-wider block">Total Members</span>
                    <span class="heading-font text-2xl font-black text-[#1A103C] mt-2 block">{{ number_format($totalUsers) }}</span>
                    <p class="text-xs text-slate-400 font-medium mt-1">Platform user accounts registered.</p>
                </div>

                <!-- Total Groups -->
                <div class="bg-white border border-[#1A103C]/5 rounded-[2rem] p-6 shadow-sm">
                    <span class="text-[10px] font-bold text-[#8C8BA5] uppercase tracking-wider block">Total Active Groups</span>
                    <span class="heading-font text-2xl font-black text-[#1A103C] mt-2 block">{{ number_format($totalGroups) }}</span>
                    <p class="text-xs text-slate-400 font-medium mt-1">Active group splits established.</p>
                </div>

                <!-- Total Expenses -->
                <div class="bg-white border border-[#1A103C]/5 rounded-[2rem] p-6 shadow-sm">
                    <span class="text-[10px] font-bold text-[#8C8BA5] uppercase tracking-wider block">Total Expenses Recorded</span>
                    <span class="heading-font text-2xl font-black text-[#1A103C] mt-2 block">{{ number_format($totalExpenses) }}</span>
                    <p class="text-xs text-slate-400 font-medium mt-1">Shared expenses registered globally.</p>
                </div>
            </div>

            <!-- 2. ANALYTICS CHARTS GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Chart 1: Expenses Logged Per Month -->
                <div class="bg-white border border-[#1A103C]/5 rounded-[2rem] p-6 shadow-sm">
                    <h3 class="heading-font text-sm font-bold text-[#1A103C] mb-4">Expense Growth Volume (Monthly)</h3>
                    <div class="relative w-full h-64">
                        <canvas id="expensesVolumeChart"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Group Size Distribution -->
                <div class="bg-white border border-[#1A103C]/5 rounded-[2rem] p-6 shadow-sm">
                    <h3 class="heading-font text-sm font-bold text-[#1A103C] mb-4">Group Membership Density</h3>
                    <div class="relative w-full h-64">
                        <canvas id="groupSizeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 3. FULL WIDTH USER SIGN-INS OVER TIME CHART -->
            <div class="bg-white border border-[#1A103C]/5 rounded-[2rem] p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="heading-font text-sm font-bold text-[#1A103C]">User Sign-ins Over Time</h3>
                    <span class="text-xs font-bold text-slate-500 bg-[#F8F9FD] px-3.5 py-1 rounded-full border border-slate-200/60">Last 7 Days</span>
                </div>
                <div class="relative w-full h-64">
                    <canvas id="insightsSigninChart"></canvas>
                </div>
            </div>

        </div>

    </main>

    <!-- ==================== CHARTS INITIALIZATION SCRIPT ==================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart 1: Expense Volume Growth Chart
            const ctx1 = document.getElementById('expensesVolumeChart').getContext('2d');
            const volumeChart = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: @json($months),
                    datasets: [{
                        label: 'Total Expenses Volume',
                        data: @json($expenseSums),
                        backgroundColor: '#6C3AF4',
                        hoverBackgroundColor: '#B026F3',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            grid: { color: 'rgba(26, 16, 60, 0.05)', borderDash: [5, 5] }
                        }
                    }
                }
            });

            // Chart 2: Group Size Density Distribution Chart
            const ctx2 = document.getElementById('groupSizeChart').getContext('2d');
            const sizeChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['2 members', '3-5 members', '6-10 members', '10+ members'],
                    datasets: [{
                        data: [45, 30, 18, 7],
                        backgroundColor: ['#6C3AF4', '#B026F3', '#3B82F6', '#10B981'],
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
                                font: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' }
                            }
                        }
                    }
                }
            });

            // Chart 3: User Sign-ins Over Time Line Chart
            const ctx3 = document.getElementById('insightsSigninChart').getContext('2d');
            
            const fillGradient3 = ctx3.createLinearGradient(0, 0, 0, 240);
            fillGradient3.addColorStop(0, 'rgba(108, 58, 244, 0.28)');
            fillGradient3.addColorStop(1, 'rgba(108, 58, 244, 0.00)');

            const signinChart = new Chart(ctx3, {
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
                        tension: 0.38,
                        fill: true,
                        backgroundColor: fillGradient3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1A103C',
                            titleFont: { family: 'Plus Jakarta Sans', size: 10, weight: 'bold' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' },
                            padding: 10,
                            borderRadius: 12,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 9, weight: '600' },
                                color: '#8C8BA5'
                            }
                        },
                        y: {
                            grid: {
                                borderDash: [5, 5],
                                color: 'rgba(26, 16, 60, 0.05)'
                            },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 9, weight: '600' },
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
