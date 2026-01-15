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
        'keterangan',
        'contact_person',
        'tanggal_log'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class);
    }
}
