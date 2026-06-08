<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['group_id', 'user_id', 'message', 'type', 'is_read', 'request_to_user_id', 'request_amount'];
    protected $casts = ['is_read' => 'boolean'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function requestToUser()
    {
        return $this->belongsTo(User::class, 'request_to_user_id');
    }
}
