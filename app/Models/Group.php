<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'created_by', 'currency', 'cover_image_path'];

    // ─── SCOPE ───────────────────────────────────────────────────────────────
    // scopeForUser: only return groups where the given user is a member
    // Usage: Group::forUser($userId)->get()
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('members', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    // ─── RELATIONSHIPS ───────────────────────────────────────────────────────
    // The user who created the group
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // All members of this group (many-to-many through group_members)
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot('joined_at');
    }

    // All expenses logged in this group
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // All settlements recorded in this group
    public function settlements()
    {
        return $this->hasMany(Settlement::class);
    }

    // Activity log entries for this group
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Clear dashboard cache for all members of this group.
     */
    public function forgetMembersCache(): void
    {
        $members = $this->relationLoaded('members') ? $this->members : $this->members()->get(['users.id']);
        foreach ($members as $member) {
            \Illuminate\Support\Facades\Cache::forget("dashboard_groups_{$member->id}");
        }
    }
}
