<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

// MemberSearch Livewire component
// Renders a search input on the group detail page.
// As the user types an email, results appear instantly (wire:model.live).
// Clicking "Add" submits the addMember form for that user.

class MemberSearch extends Component
{
    public string $search  = '';
    public int    $groupId;

    public function mount(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    // Computed property — runs a LIKE query against the users table
    // Accessed in Blade as $this->results
    public function getResultsProperty()
    {
        if (strlen($this->search) < 2) {
            return collect(); // don't search until at least 2 chars typed
        }

        $group = Group::with('members')->find($this->groupId);
        $existingMemberIds = $group ? $group->members->pluck('id')->toArray() : [];

        return User::where('email', 'LIKE', '%' . $this->search . '%')
            ->whereNotIn('id', $existingMemberIds) // don't show existing members
            ->limit(5)
            ->get(['id', 'name', 'email']);
    }

    public function render()
    {
        return view('livewire.member-search');
    }
}
