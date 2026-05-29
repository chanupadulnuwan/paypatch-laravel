{{--
    MemberSearch Livewire component view
    Shown on the group detail page sidebar.
    wire:model.live="search" → updates $search in PHP on every keystroke
    $this->results           → computed property that runs the LIKE query
--}}
<div>
    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Add Member</label>

    {{-- Search input — results update as you type --}}
    <div class="relative">
        <input type="text"
               wire:model.live="search"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
               placeholder="Search by email...">

        {{-- Loading indicator while Livewire is fetching --}}
        <span wire:loading class="absolute right-2 top-2 text-xs text-gray-400">searching...</span>
    </div>

    {{-- Results dropdown --}}
    @if($this->results->isNotEmpty())
        <ul class="mt-2 border border-gray-200 rounded-lg divide-y divide-gray-100 bg-white shadow-sm">
            @foreach($this->results as $user)
                <li class="flex justify-between items-center px-3 py-2">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                    </div>
                    {{-- Standard form POST to GroupController@addMember --}}
                    <form method="POST" action="{{ route('groups.addMember', $groupId) }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        <button type="submit"
                                class="text-xs bg-indigo-600 text-white px-3 py-1 rounded-lg hover:bg-indigo-700 transition">
                            Add
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    @elseif(strlen($search) >= 2)
        <p class="text-xs text-gray-400 mt-2">No users found.</p>
    @endif
</div>
