<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Gunakan Authenticatable

class WaliMurid extends Authenticatable // Ubah extends Model menjadi Authenticatable
{
    use HasFactory;

    protected $fillable = ['siswa_id', 'nama', 'email', 'password'];

    protected $hidden = ['password'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}