<?php

use App\Models\Collector;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
      $this->collector = Collector::factory()->create();
});

test('it can create a new book for a collector', function () {
      $payload = [
            'title' => 'The Great Gatsby',
            'type' => 'Fiction',
            'collector_id' => $this->collector->id,
      ];

      $response = $this->postJson('/api/books', $payload);

      $response->assertStatus(201)
            ->assertJsonStructure([
                  'data' => [
                        'uuid',
                        'title',
                        'type',
                        'collector_id',
                  ]
            ]);

      $this->assertDatabaseHas('books', ['title' => 'The Great Gatsby']);
});

test('it can retrieve details of a specific book', function () {
      $book = Book::factory()->create([
            'collector_id' => $this->collector->id,
            'title' => 'The Great Gatsby',
            'type' => 'Fiction',
      ]);

      // Mock the IsbnClient to return a fake ISBN
      Http::fake([
            'isbn.api/*' => Http::response(['isbn' => '978-3-16-148410-0'], 200),
      ]);

      $response = $this->getJson("/api/books/{$book->uuid}");

      $response->assertStatus(200)
            ->assertJsonStructure([
                  'data' => [
                        'uuid',
                        'title',
                        'type',
                        'isbn',
                        'collector' => [
                              'id',
                              'name',
                        ],
                  ]
            ]);
});

test('it can retrieve the most recently added book summary for a collector', function () {
      // Create a few books for the collector with different types
      Book::factory()->create([
            'collector_id' => $this->collector->id,
            'type' => 'Fiction',
      ]);

      Book::factory()->create([
            'collector_id' => $this->collector->id,
            'type' => 'Technical',
      ]);

      Book::factory()->create([
            'collector_id' => $this->collector->id,
            'type' => 'Self-Help',
      ]);

      $response = $this->getJson("/api/collectors/{$this->collector->id}/recently-added");

      $response->assertStatus(200)
            ->assertJsonStructure([
                  'data' => [
                        'fiction' => ['uuid', 'title', 'created_at'],
                        'technical' => ['uuid', 'title', 'created_at'],
                        'self_help' => ['uuid', 'title', 'created_at'],
                        'non_fiction' => null,  // No books of this type, should be null
                  ]
            ]);
});