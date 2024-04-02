<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = ['invitation_code', 'user_id', 'session_name', 'collection'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}

