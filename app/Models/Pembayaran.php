<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Mendefinisikan bahwa pembayaran ini milik satu tagihan.
     */
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    /**
     * Mendefinisikan bahwa pembayaran ini diproses oleh satu user (admin/petugas).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}