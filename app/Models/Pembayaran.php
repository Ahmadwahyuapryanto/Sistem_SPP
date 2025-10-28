<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    
    // Mengizinkan semua kolom untuk diisi (alternatif dari $fillable)
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // TAMBAHKAN BAGIAN INI UNTUK MEMPERBAIKI ERROR
    protected $casts = [
        'tanggal_bayar' => 'datetime',
    ];

    /**
     * Mendapatkan data tagihan yang terkait dengan pembayaran.
     */
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    /**
     * Mendapatkan data user (petugas) yang terkait dengan pembayaran.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}