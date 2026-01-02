<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';

    protected $fillable = [
        'mitra_id',
        'jenis_dokumen_id',
        'nomor_dokumen_mitra',
        'nomor_dokumen_undip',
        'judul_dokumen',
        'status_id',
        'tanggal_masuk',
        'tanggal_terbit',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_terbit' => 'date',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function jenisDokumen()
    {
        return $this->belongsTo(JenisDokumen::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}