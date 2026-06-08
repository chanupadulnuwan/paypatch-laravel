<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    protected $fillable = ['user_id', 'group_id', 'image_path', 'caption', 'audience'];

    public function user()     { return $this->belongsTo(User::class); }
    public function group()    { return $this->belongsTo(Group::class); }
    public function likes()    { return $this->hasMany(PostLike::class, 'post_id'); }
    public function comments() { return $this->hasMany(PostComment::class, 'post_id'); }
}
