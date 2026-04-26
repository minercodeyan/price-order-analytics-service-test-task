<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStatisticsRequest;
use App\Services\StatisticsService;
use Illuminate\Http\JsonResponse;

class OrderStatisticsController extends Controller
{
    protected StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Получить статистику заказов
     *
     * @param OrderStatisticsRequest $request
     * @return JsonResponse
     */
    public function index(OrderStatisticsRequest $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 15);
        $groupBy = $request->input('group_by', 'month');

        $statistics = $this->statisticsService->getOrderStatistics($groupBy, $page, $perPage);

        return response()->json($statistics);
    }
}
