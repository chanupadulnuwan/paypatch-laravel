{{--
    ExpenseForm Livewire component view
    Key Livewire concepts used here:
      - wire:model.live="amount"  → updates $amount in PHP instantly as user types
      - wire:model="title"        → updates on input blur (cheaper, no preview needed)
      - wire:model="groupId"      → triggers updatedGroupId() to refresh member count
      - wire:submit="save"        → calls save() method on the component
      - $this->splitAmount        → computed property, recalculates when amount changes
--}}
<div class="bg-white rounded-xl shadow-sm p-8">

    {{-- Success / error messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    <form wire:submit="save">
        @csrf

        {{-- Group selector --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Group</label>
            <select wire:model="groupId"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Select a group...</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </select>
            @error('groupId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Expense title --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <input type="text"
                   wire:model="title"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                   placeholder="e.g. Dinner, Fuel, Groceries">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Amount — wire:model.live means the split preview updates as you type --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount (LKR)</label>
            <input type="number"
                   wire:model.live="amount"
                   step="0.01" min="0.01"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                   placeholder="0.00">
            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Live split preview — updates instantly as amount changes --}}
        <div class="mb-5 p-4 bg-indigo-50 rounded-lg">
            <p class="text-sm text-indigo-700">
                ⚖️ Equal split among <strong>{{ $memberCount }}</strong> members:
                <span class="font-bold text-indigo-900">{{ $this->splitAmount }}</span> per person
            </p>
        </div>

        {{-- Paid by selector --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Paid by</label>
            <select wire:model="paidBy"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @forelse($members as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @empty
                    <option value="{{ auth()->id() }}">{{ auth()->user()->name }}</option>
                @endforelse
            </select>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
            {{-- wire:loading shows a spinner while save() is running --}}
            <span wire:loading.remove>Add Expense</span>
            <span wire:loading>Saving...</span>
        </button>
    </form>
</div>
