<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class FullCalendarEventResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'date' => $this->date,
            'status' => $this->status,
            'created_at' => $this->created_at->format('m-d-Y H:i:s'),
            'user' => new UserResource($this->user),
        ];
    }

    public function with($request)
    {
        return [
            'success' => true
        ];
    }
}
