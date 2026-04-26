<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStatisticsRequest;

use App\Services\StatisticsService;
use App\Traits\OrderStatisticsAnnotations;
use Illuminate\Http\JsonResponse;

class OrderStatisticsController extends Controller
{
    use OrderStatisticsAnnotations;
    protected StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index(OrderStatisticsRequest $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);
        $groupBy = $request->input('group_by', 'month');

        $statistics = $this->statisticsService->getOrderStatistics($groupBy, $page, $perPage);

        return response()->json($statistics);
    }
}
