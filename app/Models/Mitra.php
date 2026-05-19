<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mitra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mitra';

    protected $fillable = [
        'nama',
        'klasifikasi_mitra_id',
        'alamat',
        'contact_person',
        'status',
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
