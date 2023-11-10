<?php

namespace App\Modules\Protocol\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProtocolResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $item = [];
        $item['id'] = $this->id;
        $item['title'] = $this->title;
        $item['link'] = $this->limit;
        $item['type'] = $this->type;
        $item['descriptions'] = $this->descriptions;
        $item['PercentComplete'] = $this->getPercentComplete();
        return $item;
    }
}