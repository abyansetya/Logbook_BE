<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'log';

    protected $fillable = [
        'user_id',
        'mitra_id',
        'dokumen_id',
        'unit_id',
        'keterangan',
        'tanggal_log',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
