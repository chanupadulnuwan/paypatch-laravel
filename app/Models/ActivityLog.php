<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['group_id', 'user_id', 'message', 'type', 'request_to_user_id', 'request_amount'];

    // The group this log entry belongs to
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // The user who triggered this activity
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // The recipient user of the settlement request
    public function requestToUser()
    {
        return $this->belongsTo(User::class, 'request_to_user_id');
    }
}
