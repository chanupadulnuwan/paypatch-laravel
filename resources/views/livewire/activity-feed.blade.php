{{--
    ActivityFeed Livewire component view
    wire:poll.5000ms → Livewire re-renders this component every 5 seconds automatically
    Real-time Livewire search models and category filters allow instantaneous UI updates!
--}}
<div class="px-8 py-8 md:px-12 w-full flex flex-col min-h-full" wire:poll.5000ms>

    <!-- ==================== HEADER ROW ==================== -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <!-- Title & Subtext -->
        <div>
            <h1 class="heading-serif text-3xl md:text-[2.1rem] font-bold text-[#1A103C]">
                Activity Log ⚡
            </h1>
            <p class="text-slate-500 font-medium text-sm mt-1">Keep track of splits, settlements, and member updates across your groups.</p>
        </div>

        <!-- Search, Notifications & CTA -->
        <div class="flex items-center gap-4 w-full md:w-auto">
            <!-- Livewire Search Input -->
            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z"></path>
                    </svg>
                </span>
                <input type="text" 
                       placeholder="Search activities..." 
                       wire:model.live.debounce.250ms="searchQuery"
                       class="block w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200/80 rounded-full text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/10 focus:border-[#6C3AF4]/60 transition text-sm">
            </div>

            <!-- Notification Bell -->
            <button class="relative p-2.5 bg-white border border-slate-200/80 hover:bg-slate-50 text-[#1A103C]/80 rounded-full shadow-sm transition outline-none">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.7c0 2.01-.76 3.99-2.2 5.58a23.848 23.848 0 005.454 1.31m5.714 0a3 3 0 11-5.714 0m5.714 0a3 3 0 11-5.714 0"></path>
                </svg>
                <span class="absolute -top-1 -right-1 h-5 w-5 bg-[#6C3AF4] text-white text-[10px] font-bold flex items-center justify-center rounded-full border border-white">3</span>
            </button>
        </div>
    </header>

    <!-- ==================== SUMMARY CARDS ROW ==================== -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- 1. TOTAL ACTIVITIES -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 flex items-center gap-5 shadow-sm shadow-slate-100/50">
            <!-- Purple Circle Icon -->
            <div class="h-14 w-14 bg-purple-50 flex items-center justify-center rounded-full text-[#6C3AF4] flex-shrink-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 0A48.536 48.536 0 0112 3m0 0c-2.917 0-5.747.294-8.5.862m0 0a2.25 2.25 0 00-1.75 2.2V19.5a2.25 2.25 0 002.25 2.25h12.75"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="text-slate-400 text-xs font-semibold tracking-wide">Total Logs</span>
                <h3 class="heading-font text-2xl font-extrabold text-[#6C3AF4] mt-0.5">
                    {{ $totalActivitiesCount }}
                </h3>
                <span class="text-[11px] text-slate-400 font-semibold mt-0.5">Across all joined groups</span>
            </div>
        </div>

        <!-- 2. EXPENSES SPLIT -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 flex items-center gap-5 shadow-sm shadow-slate-100/50">
            <!-- Orange Circle Icon -->
            <div class="h-14 w-14 bg-amber-50 flex items-center justify-center rounded-full text-amber-500 flex-shrink-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75m-.75-3h.75m-.75 3h.75m-.75-3h.75m3 9h.75m-.75-3h.75m-.75 3h.75m-.75-3h.75m3 9h.75m-.75-3h.75m-.75 3h.75m-.75-3h.75M3.75 21.75V12h16.5v9.75m-16.5 0a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5m-16.5 0h16.5"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="text-slate-400 text-xs font-semibold tracking-wide">Expenses Logged</span>
                <h3 class="heading-font text-2xl font-extrabold text-amber-500 mt-0.5">
                    {{ $expensesCount }}
                </h3>
                <span class="text-[11px] text-slate-400 font-semibold mt-0.5">Split bills & cash logs</span>
            </div>
        </div>

        <!-- 3. SETTLEMENTS -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 flex items-center gap-5 shadow-sm shadow-slate-100/50">
            <!-- Green Circle Icon -->
            <div class="h-14 w-14 bg-emerald-50 flex items-center justify-center rounded-full text-emerald-500 flex-shrink-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="text-slate-400 text-xs font-semibold tracking-wide">Settlements Finalized</span>
                <h3 class="heading-font text-2xl font-extrabold text-emerald-500 mt-0.5">
                    {{ $settlementsCount }}
                </h3>
                <span class="text-[11px] text-slate-400 font-semibold mt-0.5">Balances squared up ✓</span>
            </div>
        </div>
    </section>

    <!-- ==================== MAIN SECTION GRID ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start flex-grow">
        
        <!-- LEFT COLUMN: TIMELINE FEED (SPAN 2) -->
        <div class="lg:col-span-2 bg-white border border-slate-100 rounded-3xl shadow-sm p-6 flex flex-col min-h-[500px]">
            <!-- Filters Tabs at Top -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6 border-b border-slate-100 pb-5">
                <h2 class="heading-font text-xl font-bold text-[#1A103C]">Recent Activity</h2>
                
                <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl">
                    <button wire:click="setFilter('all')" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition outline-none {{ $activeFilter === 'all' ? 'bg-white text-[#6C3AF4] shadow-sm' : 'text-slate-500 hover:text-[#1A103C]' }}">
                        All
                    </button>
                    <button wire:click="setFilter('expense')" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition outline-none {{ $activeFilter === 'expense' ? 'bg-white text-[#6C3AF4] shadow-sm' : 'text-slate-500 hover:text-[#1A103C]' }}">
                        Expenses
                    </button>
                    <button wire:click="setFilter('settle')" 
                            class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition outline-none {{ $activeFilter === 'settle' ? 'bg-white text-[#6C3AF4] shadow-sm' : 'text-slate-500 hover:text-[#1A103C]' }}">
                        Settlements
                    </button>
                </div>
            </div>

            <!-- Activity Logs Feed List -->
            <div class="relative pl-6 border-l border-slate-150 flex flex-col gap-6 flex-grow">
                @forelse($logs as $log)
                    <!-- Log Item Row -->
                    <div class="relative group">
                                           <!-- Glow Bullet Point on timeline -->
                        <span class="absolute -left-[31px] top-1.5 h-4.5 w-4.5 rounded-full border-3 border-white shadow-sm flex items-center justify-center transition"
                               :class="'{{ $log->type }}' === 'expense' ? 'bg-amber-400' : ('{{ $log->type }}' === 'settle' ? 'bg-emerald-400' : ('{{ $log->type }}' === 'settlement_request' ? 'bg-indigo-400' : 'bg-purple-400'))">
                        </span>

                        <div class="bg-slate-50/20 hover:bg-slate-50/70 border border-slate-100/50 hover:border-slate-100 rounded-2xl p-4.5 transition duration-200 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <!-- User Icon Circle -->
                                <div class="h-10 w-10 rounded-full flex-shrink-0 flex items-center justify-center text-lg font-bold shadow-sm"
                                     :class="'{{ $log->type }}' === 'expense' ? 'bg-amber-100 text-amber-600' : ('{{ $log->type }}' === 'settle' ? 'bg-emerald-100 text-emerald-600' : ('{{ $log->type }}' === 'settlement_request' ? 'bg-indigo-100 text-indigo-600' : 'bg-purple-100 text-purple-600'))">
                                    @if($log->type === 'expense')   💸
                                    @elseif($log->type === 'settle') 🤝
                                    @elseif($log->type === 'settlement_request') 📣
                                    @elseif($log->type === 'group')  👥
                                    @elseif($log->type === 'reminder') 🔔
                                    @else                             📌
                                    @endif
                                </div>

                                <div class="flex flex-col">
                                    <p class="text-sm font-bold text-[#1A103C] leading-snug tracking-tight">
                                        {{ $log->message }}
                                    </p>
                                    @if($log->type === 'settlement_request' && $log->request_to_user_id === Auth::id())
                                        <div class="mt-3">
                                            <a href="{{ route('groups.show', $log->group_id) }}?settle_to={{ $log->user_id }}&settle_amt={{ $log->request_amount }}"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] text-white text-[10px] font-bold rounded-lg shadow-sm hover:from-[#592BD4] hover:to-[#9E1CE0] transition transform active:scale-97 outline-none">
                                                 Settle Up Now
                                            </a>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-2.5 mt-2">
                                        <!-- Group capsule -->
                                        @if($log->group)
                                            <span class="px-2.5 py-0.5 bg-[#6C3AF4]/5 border border-[#6C3AF4]/10 text-[#6C3AF4] text-[10px] font-bold rounded-full">
                                                {{ $log->group->name }}
                                            </span>
                                        @endif
                                        <span class="text-[10px] text-slate-400 font-semibold flex items-center gap-1">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $log->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Chevron indicator -->
                            <div class="flex items-center self-center flex-shrink-0">
                                <a href="{{ route('groups.show', $log->group_id) }}" 
                                   class="p-1.5 hover:bg-white border border-transparent hover:border-slate-100 text-slate-300 hover:text-[#6C3AF4] rounded-full transition outline-none">
                                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>

                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="text-center py-20 text-slate-400 flex flex-col items-center justify-center flex-grow -ml-6">
                        <div class="text-6xl mb-4">📋</div>
                        @if(!empty($searchQuery))
                            <h3 class="font-bold text-[#1A103C] text-lg">No search results</h3>
                            <p class="text-sm mt-1 text-slate-400 max-w-sm">No activity matches "{{ $searchQuery }}". Try adjusting your keywords.</p>
                        @else
                            <h3 class="font-bold text-[#1A103C] text-lg">No activity recorded</h3>
                            <p class="text-sm mt-1 text-slate-400 max-w-sm">Expenses and settlements across your groups will show up chronologically on this timeline.</p>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Auto Refreshing Info Banner -->
            <div class="mt-8 border-t border-slate-100 pt-4 flex items-center justify-center gap-1.5 text-[10px] text-slate-400 font-semibold tracking-wider uppercase select-none">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#6C3AF4]/50 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-[#6C3AF4]"></span>
                </span>
                Auto-refreshes every 5 seconds
            </div>
        </div>

        <!-- RIGHT COLUMN: SIDEBAR WIDGETS -->
        <div class="flex flex-col gap-8">
            
            <!-- WIDGET 1: ACTIVE GROUPS -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="heading-font text-base md:text-lg font-bold text-[#1A103C]">Active Groups</h2>
                    <a href="{{ route('groups.index') }}" class="text-xs font-bold text-[#6C3AF4] hover:underline">View all</a>
                </div>

                <div class="flex flex-col gap-4">
                    @forelse($activeGroups as $group)
                        <a href="{{ route('groups.show', $group->id) }}" 
                           class="flex items-center justify-between p-2.5 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition duration-200">
                            <div class="flex items-center gap-3">
                                <!-- Gradient Cover Circle representation -->
                                <div class="h-9 w-9 rounded-xl flex-shrink-0 flex items-center justify-center text-white font-extrabold text-xs shadow-sm bg-gradient-to-tr from-purple-500 to-indigo-500">
                                    {{ strtoupper(substr($group->name, 0, 2)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-[#1A103C]">{{ $group->name }}</span>
                                    <span class="text-[10px] text-slate-400 mt-0.5">
                                        {{ $group->members->count() }} {{ Str::plural('member', $group->members->count()) }}
                                    </span>
                                </div>
                            </div>

                            <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                            </svg>
                        </a>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-400">
                            No groups created yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- WIDGET 2: ACTIVITY INSIGHTS -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
                <h2 class="heading-font text-base md:text-lg font-bold text-[#1A103C] mb-6">Activity Insights</h2>

                <div class="flex flex-col gap-5">
                    <!-- Stat 1 -->
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between text-xs font-semibold text-slate-500">
                            <span>Settled Splits</span>
                            <span class="text-[#6C3AF4] font-extrabold">85% Completed</span>
                        </div>
                        <!-- Progress Bar -->
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#6C3AF4] to-[#B026F3] rounded-full" style="width: 85%;"></div>
                        </div>
                    </div>

                    <!-- Stat 2 -->
                    <div class="flex justify-between items-center py-2.5 border-y border-slate-50">
                        <div class="flex flex-col">
                            <span class="text-[11px] text-slate-400 font-semibold">Active Frequency</span>
                            <span class="text-xs font-bold text-[#1A103C] mt-0.5">Frequent splitting</span>
                        </div>
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-full border border-emerald-100">
                            +12% wkly
                        </span>
                    </div>

                    <!-- Stat 3 -->
                    <div class="flex justify-between items-center">
                        <div class="flex flex-col">
                            <span class="text-[11px] text-slate-400 font-semibold">Avg. Resolve Window</span>
                            <span class="text-xs font-bold text-[#1A103C] mt-0.5">1.2 Days</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Fast Pace</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
