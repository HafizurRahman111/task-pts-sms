<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sms extends Model
{
    use HasFactory;

    protected $table = 'sms_logs';

    protected $fillable = [
        'purpose',
        'student_ids',
        'message',
        'status',
        'gateway_response'
    ];

    protected function casts(): array
    {
        return [
            'student_ids' => 'array',
        ];
    }

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
