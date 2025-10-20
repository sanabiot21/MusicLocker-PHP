<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'cover_image_url' => $this->cover_image_url,
            'track_count' => $this->when(
                $this->relationLoaded('musicEntries'),
                fn() => $this->musicEntries->count()
            ),
            'total_duration' => $this->when(
                $this->relationLoaded('musicEntries'),
                fn() => $this->total_duration
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Include music entries if loaded
            'entries' => MusicEntryResource::collection($this->whenLoaded('musicEntries')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
