<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\Collector;
use App\Client\IsbnClient;
use App\Http\Resources\BookResource;
use App\Http\Resources\CollectorResource;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
       * @param  array  $data
       * @return Book
       */
      public function createBook(array $data): Book
      {
            $data['uuid'] = (string) Str::uuid();
            return Book::create($data);
      }

      /**
       * Retrieve details of a specific book, including its ISBN.
       *
       * @param string $uuid
       * @return BookResource
       * @throws ModelNotFoundException
       */
      public function getBookDetails(string $uuid): BookResource
      {
            // Find the book by UUID or throw a 404 error
            $book = Book::where('uuid', $uuid)->firstOrFail();

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
       * @param  int  $collectorId
       * @return array
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
       * @param Book|null $book
       * @return BookResource|null
       */
      private function formatBookResource(?Book $book): ?BookResource
      {
            return $book ? new BookResource($book) : null;
      }
}