<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    // Tambahkan kolom yang boleh diisi secara massal di sinis
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'address',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}