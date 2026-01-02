<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlasifikasiMitra extends Model
{
    use HasFactory;

    protected $table = 'klasifikasi_mitra';

    protected $fillable = [
        'nama',
    ];

    public function mitra()
    {
        return $this->hasMany(Mitra::class);
    }
}