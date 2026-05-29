<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Get all group IDs the user belongs to
        $groupIds = Group::forUser($userId)->pluck('id');

        // Calculate metrics for summary cards
        $totalActivities = ActivityLog::whereIn('group_id', $groupIds)->count();
        $expensesCount = ActivityLog::whereIn('group_id', $groupIds)->where('type', 'expense')->count();
        $settlementsCount = ActivityLog::whereIn('group_id', $groupIds)->where('type', 'settle')->count();
        
        // Load active groups for right side panel
        $activeGroups = Group::forUser($userId)
            ->with(['members'])
            ->latest()
            ->take(5)
            ->get();

        return view('activity.index', compact('totalActivities', 'expensesCount', 'settlementsCount', 'activeGroups'));
    }
}

