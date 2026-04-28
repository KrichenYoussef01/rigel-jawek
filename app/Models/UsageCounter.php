<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageCounter extends Model
{
    protected $fillable = [
        'user_id',
        'mois',
        'nb_lives_utilises',
        'nb_commandes_utilises',
        'nb_commentaires_utilises',
        'nb_exports_utilises',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
?>