<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateBookRequest;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class CreateBookController
{

    protected $bookService;

    // Inject the BookService via the constructor
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Handle the incoming request to create a new book.
     *
     * @param  \App\Http\Requests\CreateBookRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(CreateBookRequest $request)
    {
        // Use the validated data from the request
        $validated = $request->validated();

        // Delegate to the BookService to create the book
        $book = $this->bookService->createBook($validated);

        // Return a JSON response with the created book data
        return response()->json([
            'data' => $book,
        ], 201);
    }
}
