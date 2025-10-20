<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMusicEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'artist' => 'sometimes|string|max:255',
            'album' => 'nullable|string|max:255',
            'genre' => 'sometimes|string|max:100',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'duration' => 'nullable|integer|min:0',
            'spotify_id' => 'nullable|string|max:255',
            'spotify_url' => 'nullable|url|max:500',
            'album_art_url' => 'nullable|url|max:500',
            'personal_rating' => 'sometimes|integer|min:1|max:5',
            'date_discovered' => 'nullable|date',
            'is_favorite' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
            'note_text' => 'nullable|string|max:1000',
            'mood' => 'nullable|string|max:50',
            'memory_context' => 'nullable|string|max:500',
            'listening_context' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'personal_rating' => 'rating',
            'spotify_id' => 'Spotify ID',
            'spotify_url' => 'Spotify URL',
            'album_art_url' => 'album art URL',
            'date_discovered' => 'discovery date',
            'note_text' => 'note',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'personal_rating.min' => 'Rating must be at least 1 star.',
            'personal_rating.max' => 'Rating cannot exceed 5 stars.',
            'tags.*.exists' => 'One or more selected tags do not exist.',
        ];
    }
}
