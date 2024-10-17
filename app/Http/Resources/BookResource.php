<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        /** @var Book $book */
        $book = $this->resource;

        return [
            'uuid' => $book->uuid,
            'title' => $book->title,
            'type' => $book->type,
            'isbn' => $book->isbn,
            'collector' => $this->whenLoaded('collector', function () use ($book) {
                return new CollectorResource($book->collector);
            }),
            'created_at' => $book->created_at->toDateTimeString(),
            'updated_at' => $book->updated_at->toDateTimeString(),
        ];
    }
}
