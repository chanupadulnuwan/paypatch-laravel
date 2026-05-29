<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // The primary key is 'key' (a string), not the default 'id'
    protected $primaryKey = 'key';
    public $incrementing = false;     // not auto-increment
    protected $keyType = 'string';
    public $timestamps = false;       // no created_at / updated_at

    protected $fillable = ['key', 'value'];

    // Helper: get a setting value by key, with an optional default
    // Usage: Setting::getValue('max_group_members', 10)
    public static function getValue(string $key, $default = null)
    {
        $setting = static::find($key);
        return $setting ? $setting->value : $default;
    }

    // Helper: set (create or update) a setting
    // Usage: Setting::setValue('max_group_members', 15)
    public static function setValue(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
