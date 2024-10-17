<?php

declare(strict_types=1);

namespace App\Services;

use App\Client\IsbnClient;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\Collector;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

final class BookService
{
    private IsbnClient $isbnClient;

    public function __construct(IsbnClient $isbnClient)
    {
        $this->isbnClient = $isbnClient;
    }

    /**
     * Create a new book for a given collector.
     *
     * @param  array{title: string, type: string, collector_id: int}  $data
     */
    public function createBook(array $data): Book
    {
        $data['uuid'] = (string) Str::uuid();

        return Book::create($data);
    }

    /**
     * Retrieve details of a specific book, including its ISBN.
     *
     * @throws ModelNotFoundException
     */
    public function getBookDetails(string $uuid): BookResource
    {
        // Find the book by UUID or throw a 404 error
        $book = Book::with('collector')->where('uuid', $uuid)->firstOrFail();

        // Convert the string UUID to an instance of UuidInterface
        $uuidObject = Uuid::fromString($book->uuid);

        // Fetch the ISBN using the provided ISBN client
        $book->isbn = $this->isbnClient->get($uuidObject);

        // Return a BookResource instance
        return new BookResource($book);
    }

    /**
     * Get a summary of the most recently added books for a collector.
     *
     * @return array{fiction: array<string, mixed>|null, non_fiction: array<string, mixed>|null, technical: array<string, mixed>|null, self_help: array<string, mixed>|null}
     *
     * @throws ModelNotFoundException
     */
    public function getRecentBookSummary(int $collectorId): array
    {
        $collector = Collector::findOrFail($collectorId);

        return [
            'fiction' => $this->formatBookResource($collector->books()->where('type', 'Fiction')->latest()->first()),
            'non_fiction' => $this->formatBookResource($collector->books()->where('type', 'Non-Fiction')->latest()->first()),
            'technical' => $this->formatBookResource($collector->books()->where('type', 'Technical')->latest()->first()),
            'self_help' => $this->formatBookResource($collector->books()->where('type', 'Self-Help')->latest()->first()),
        ];
    }

    /**
     * Format the book into a resource or return null.
     *
     * @return array<string, mixed>|null
     */
    private function formatBookResource(?Book $book): ?array
    {
        return $book ? (new BookResource($book))->toArray(request()) : null;
    }
}
