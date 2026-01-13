<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DokumenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'judul_dokumen' => $this->judul_dokumen,
            'nomor_dokumen_mitra' => $this->nomor_dokumen_mitra ?? '-',
            'nomor_dokumen_undip' => $this->nomor_dokumen_undip ?? '-',
            'tanggal_masuk' => $this->tanggal_masuk,
            'tanggal_terbit' => $this->tanggal_terbit,
            
            // Mengambil nama dari relasi, bukan cuma ID-nya
            'jenis_dokumen' => $this->jenisDokumen ? $this->jenisDokumen->nama : null,
            'status' => $this->status ? $this->status->nama : null,
            
            // Jika butuh ID asli untuk kebutuhan form edit di React
            'jenis_dokumen_id' => $this->jenis_dokumen_id,
            'status_id' => $this->status_id,
            'mitra_id' => $this->mitra_id,

            // Relasi Objek Lengkap (Hanya dikirim jika di-load di Controller)
            'mitra' => new MitraResource($this->whenLoaded('mitra')),
            
            // Log Aktivitas (Hanya muncul di halaman detail)
            'logs' => LogResource::collection($this->whenLoaded('logs')),
            
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
