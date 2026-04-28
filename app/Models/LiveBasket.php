<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class LiveBasket extends Model
{
     protected $fillable = ['live_session_id', 'client_name', 'articles', 'phones', 'time'];
    
    protected $casts = [
        'articles' => 'array',
        'phones'   => 'array',
    ];

    public function session()
    {
        return DB::table('live_sessions')->where('id', $this->live_session_id)->first();
    }
}
