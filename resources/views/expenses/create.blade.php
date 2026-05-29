{{--
    Add expense page
    Uses the ExpenseForm Livewire component for the live split preview
    Controller passes: $groups (user's groups), $selectedGroup (if group_id in query string)
--}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Add an Expense</h2>
    </x-slot>

    <div class="py-8 max-w-lg mx-auto px-4">
        {{-- ExpenseForm is a Livewire component that handles the form + live split preview --}}
        @livewire('expense-form', ['preselectedGroupId' => request('group_id')])
    </div>
</x-app-layout>
