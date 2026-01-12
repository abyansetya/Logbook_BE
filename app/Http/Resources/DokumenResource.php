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
            'jenis_dokumen' => $this->jenisDokumen->nama ?? null,
            'status' => $this->status->nama ?? null,
            'tanggal_masuk' => optional($this->tanggal_masuk)->format('Y-m-d'),
            // Logs hanya muncul jika di-load (untuk menghemat bandwidth di halaman index)
            'logs' => LogResource::collection($this->whenLoaded('logs')),
        ];
    }
}
