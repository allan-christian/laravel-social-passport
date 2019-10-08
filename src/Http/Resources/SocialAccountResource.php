<?php

namespace AllanChristian\SocialPassport\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'provider' => $this->provider_name,
            'provider_id' => $this->provider_id,
            'created_at' => $this->created_at->format('Y-m-d\TH:i:sP'),
        ];
    }
}
