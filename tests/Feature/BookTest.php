<?php

use App\Models\Book;
use App\Models\Collector;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->collector = Collector::factory()->create();
});

test('it can create a new book for a collector', function () {
    $payload = [
        'title' => 'My Book Name',
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
            ],
        ]);

    $this->assertDatabaseHas('books', ['title' => 'My Book Name']);
});

test('it can retrieve details of a specific book', function () {
    $book = Book::factory()->create([
        'collector_id' => $this->collector->id,
        'title' => 'My Book Name',
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
            ],
        ]);
});
test('it can retrieve the most recently added book summary for a collector', function () {
    Book::factory()->create([
        'collector_id' => $this->collector->id,
        'title' => 'My Book Name',
        'type' => 'Fiction',
    ]);

    Book::factory()->create([
        'collector_id' => $this->collector->id,
        'title' => 'Some Book',
        'type' => 'Technical',
    ]);

    $response = $this->getJson("/api/collectors/{$this->collector->id}/recently-added");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'fiction' => ['uuid', 'title', 'created_at'],
                'technical' => ['uuid', 'title', 'created_at'],
                'self_help',
                'non_fiction',
            ],
        ]);

    $response->assertJson([
        'data' => [
            'fiction' => [
                'title' => 'My Book Name',
            ],
            'technical' => [
                'title' => 'Some Book',
            ],
        ],
    ]);
});
