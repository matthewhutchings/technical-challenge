<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GetCollectorSummaryController
{
    private BookService $bookService;

    /**
     * Inject the BookService into the controller.
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Handle the incoming request to retrieve the most recently added book summary for a collector.
     */
    public function __invoke(Request $request, int $collectorId): JsonResponse
    {
        // Use the BookService to get the recent book summary for the collector
        $summary = $this->bookService->getRecentBookSummary($collectorId);

        // Return the summary as a JSON response
        return response()->json(['data' => $summary], 200);
    }
}
