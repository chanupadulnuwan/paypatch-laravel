<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ActivityFeed extends Component
{
    public $searchQuery = '';
    public $activeFilter = 'all'; // all, expense, settle, group

    protected $queryString = [
        'searchQuery' => ['except' => ''],
        'activeFilter' => ['except' => 'all'],
    ];

    public function render()
    {
        $userId = Auth::id();
        $groupIds = Group::forUser($userId)->pluck('id');

        // Calculate counts dynamically for metrics row
        $totalActivitiesCount = ActivityLog::whereIn('group_id', $groupIds)->count();
        $expensesCount = ActivityLog::whereIn('group_id', $groupIds)->where('type', 'expense')->count();
        $settlementsCount = ActivityLog::whereIn('group_id', $groupIds)->where('type', 'settle')->count();

        // Build search & filter query
        $query = ActivityLog::whereIn('group_id', $groupIds)
            ->with(['user', 'group']);

        if (!empty($this->searchQuery)) {
            $query->where('message', 'like', '%' . $this->searchQuery . '%');
        }

        if ($this->activeFilter !== 'all') {
            $query->where('type', $this->activeFilter);
        }

        $logs = $query->latest()
            ->limit(20)
            ->get();

        // Fetch active groups for the sidebar widget
        $activeGroups = Group::forUser($userId)
            ->with(['members'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.activity-feed', compact(
            'logs', 
            'totalActivitiesCount', 
            'expensesCount', 
            'settlementsCount', 
            'activeGroups'
        ));
    }

    public function setFilter($filter)
    {
        $this->activeFilter = $filter;
    }
}
