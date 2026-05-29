{{-- Create group form --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Create a New Group</h2>
    </x-slot>

    <div class="py-8 max-w-lg mx-auto px-4">
        <div class="bg-white rounded-xl shadow-sm p-8">
            <form method="POST" action="{{ route('groups.store') }}">
                @csrf

                {{-- Group name --}}
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                           placeholder="e.g. Galle Trip, Apartment 4B">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
                        Create Group
                    </button>
                    <a href="{{ route('groups.index') }}"
                       class="flex-1 text-center bg-gray-100 text-gray-600 py-2 rounded-lg font-semibold hover:bg-gray-200 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
