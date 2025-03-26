<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sms extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'message', 'user_ids', 'status',];

    protected $casts = [
        'user_ids' => 'array',
    ];

    public function logs()
    {
        return $this->hasMany(SmsLog::class);
    }
}
