<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class socialAccount extends Model
{
    protected $fillable = [
        'user_id', 'provider_id', 'provived_name'
    
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
