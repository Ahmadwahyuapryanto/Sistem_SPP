<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Mendefinisikan bahwa satu tagihan hanya punya satu pembayaran.
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}