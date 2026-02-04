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
                'tanggal_log' => Carbon::parse($this->tanggal_log)->translatedFormat('d F Y'),
                'keterangan' => $this->keterangan,
                'unit_id' => $this->unit_id,
                'unit_name' => $this->unit->nama ?? null,
                'updated_at' => optional($this->updated_at)->translatedFormat('d F Y'),
                'admin' => [
                    'id' => $this->user->id ?? null,
                    'nama' => $this->user->nama ?? 'Sistem',
                ]
               
            ];
        }
}
