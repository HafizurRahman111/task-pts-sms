<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = ['sms_id', 'user_id', 'phone_number', 'status', 'gateway_response'];

    protected $casts = [
        'gateway_response' => 'array',
    ];

    public function sms()
    {
        return $this->belongsTo(Sms::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
