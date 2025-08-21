<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama_lengkap',
        'kelas',
        'no_hp_wali',
        'alamat',
    ];

    /**
     * Mendefinisikan bahwa satu siswa bisa punya banyak tagihan.
     */
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }
    public function wali()
    {
        return $this->hasOne(WaliMurid::class);
    }
}