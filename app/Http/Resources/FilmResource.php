<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'publication_date' => date('d-m-Y', strtotime($this->publication_date)),
            'cover' => $this->cover,
            'status' => $this->status,
            'turnos' => count($this->shifts),
            'shifts' => $this->shifts()
        ];

    }
}
