<?php
// app/Services/StatisticsService.php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getOrderStatistics(string $groupBy, int $page, int $perPage): array
    {
        DB::statement("SET SESSION sql_mode = ''");

        $groupByConfig = $this->getGroupByConfig($groupBy);

        $results = Order::query()
            ->whereNull('deleted_at')
            ->select(
                DB::raw($groupByConfig['select'] . ' as period'),
                DB::raw('COUNT(*) as orders_count')
            )
            ->groupBy(DB::raw($groupByConfig['group_by']))
            ->orderBy(DB::raw('MIN(create_date)'), 'desc')
            ->get();


        $allData = [];
        foreach ($results as $item) {
            $allData[] = [
                'period' => $item->period,
                'orders_count' => (int) $item->orders_count,
            ];
        }

        $total = count($allData);
        $offset = ($page - 1) * $perPage;
        $data = array_slice($allData, $offset, $perPage);

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
            'group_by' => $groupBy,
        ];
    }

    private function getGroupByConfig(string $groupBy): array
    {
        return match ($groupBy) {
            'day' => [
                'select' => "DATE_FORMAT(create_date, '%Y-%m-%d')",
                'group_by' => "DATE(create_date)",
            ],
            'year' => [
                'select' => "DATE_FORMAT(create_date, '%Y')",
                'group_by' => "YEAR(create_date)",
            ],
            default => [
                'select' => "DATE_FORMAT(create_date, '%Y-%m')",
                'group_by' => "YEAR(create_date), MONTH(create_date)",
            ],
        };
    }
}
