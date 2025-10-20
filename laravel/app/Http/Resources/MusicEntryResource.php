<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MusicEntryResource extends JsonResource
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
            'title' => $this->title,
            'artist' => $this->artist,
            'album' => $this->album,
            'genre' => $this->genre,
            'release_year' => $this->release_year,
            'duration' => $this->duration,
            'duration_formatted' => $this->formatted_duration,
            'spotify_id' => $this->spotify_id,
            'spotify_url' => $this->spotify_url,
            'album_art_url' => $this->album_art_url,
            'personal_rating' => $this->personal_rating,
            'date_discovered' => $this->date_discovered?->format('Y-m-d'),
            'is_favorite' => $this->is_favorite,
            'date_added' => $this->created_at?->format('Y-m-d H:i:s'),
            'last_updated' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships (conditionally loaded)
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'notes' => MusicNoteResource::collection($this->whenLoaded('notes')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
