<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            'title_slider' => $this->title_slider,
            'description_slider' => $this->description_slider,
            'image_slider' => url($this->image_slider),
            'link_1' => $this->link_1,
            'link_2' => $this->link_2,
            'estado' => $this->estado,
        ];
    }
}
