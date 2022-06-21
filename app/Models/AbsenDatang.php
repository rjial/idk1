<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AbsenDatang extends Model
{
    use HasFactory;

    protected $table = 'absen_datang';
    protected $primaryKey = "id";

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}