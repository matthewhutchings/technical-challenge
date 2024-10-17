<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\BookService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

final class GetBookController
{
    private BookService $bookService;

    /**
     * Inject the BookService via the constructor.
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        try {
            // Use the BookService to retrieve book details
            $bookDetails = $this->bookService->getBookDetails($uuid);

            return new JsonResponse([
                'data' => $bookDetails,
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return new JsonResponse([
                'error' => 'Book not found.',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'An error occurred while processing your request.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
