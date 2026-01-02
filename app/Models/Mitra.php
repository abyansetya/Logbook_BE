<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra';

    protected $fillable = [
        'nama',
        'klasifikasi_mitra_id',
        'alamat',
        'contact_person',
        'logo_mitra',
    ];

    public function klasifikasiMitra()
    {
        return $this->belongsTo(KlasifikasiMitra::class);
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
