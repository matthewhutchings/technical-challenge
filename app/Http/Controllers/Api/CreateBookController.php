<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateBookRequest;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;

final class CreateBookController
{
    private BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Handle the incoming request to create a new book.
     */
    public function __invoke(CreateBookRequest $request): JsonResponse
    {
        // Ensure the validated data adheres to the expected structure
        /** @var array{title: string, type: string, collector_id: int} $validated */
        $validated = $request->validated();

        // Delegate to the BookService to create the book
        $book = $this->bookService->createBook($validated);

        // Return a JSON response with the created book data
        return response()->json([
            'data' => $book,
        ], 201);
    }
}
