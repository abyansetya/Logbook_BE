<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
        {
            return [
                'id' => $this->id,
                // Format tanggal sesuai tampilan di UI (contoh: 10 Desember 2025)
                // Anda bisa menggunakan format 'Y-m-d' atau format lokal Indonesia
                'tanggal_log' => Carbon::parse($this->tanggal_log)->translatedFormat('d F Y'),
                
                // Kolom keterangan (Inisiasi kerja sama oleh mitra, dll)
                'keterangan' => $this->keterangan,
                
                // Kolom contact person (Andi - Legal BCA)
                'contact_person' => $this->contact_person,
                'updated_at' => optional($this->updated_at)->translatedFormat('d F Y'),
                // Data user yang menginput log (Admin Undip)
                'admin' => [
                    'id' => $this->user->id ?? null,
                    'nama' => $this->user->nama ?? 'Sistem',
                ]
               
            ];
        }
}
