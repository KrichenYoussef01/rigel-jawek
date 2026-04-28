<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',  // ✅ ajouter
    'is_paid'    => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
