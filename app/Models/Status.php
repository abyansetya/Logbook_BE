<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'status';

    protected $fillable = [
        'nama',
    ];

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }
}
