<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class StatisticsService
{
    /**
     * Получить статистику заказов с группировкой и пагинацией
     *
     * @param string $groupBy
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getOrderStatistics(string $groupBy, int $page, int $perPage): array
    {
        // Формируем группировку в зависимости от параметра
        $groupByConfig = $this->getGroupByConfig($groupBy);

        // Получаем данные с группировкой
        $query = Order::query()
            ->whereNull('deleted_at') // Только не удаленные
            ->select(
                DB::raw($groupByConfig['select'] . ' as period'),
                DB::raw('COUNT(*) as orders_count')
            )
            ->groupBy(DB::raw($groupByConfig['group_by']))
            ->orderBy('period', 'desc');

        // Получаем общее количество для пагинации
        $total = $query->get()->count();

        // Применяем пагинацию
        $results = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Формируем данные для ответа
        $data = [];
        foreach ($results as $item) {
            $data[] = [
                'period' => $item->period,
                'orders_count' => (int) $item->orders_count,
            ];
        }

        // Создаем объект пагинации
        $paginator = new LengthAwarePaginator(
            $data,
            $total,
            $perPage,
            $page,
            ['path' => '/api/orders/statistics']
        );

        return [
            'data' => $data,
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'group_by' => $groupBy,
        ];
    }

    private function getGroupByConfig(string $groupBy): array
    {
        return match ($groupBy) {
            'day' => [
                'select' => "DATE_FORMAT(create_date, '%Y-%m-%d')",
                'group_by' => "DATE(create_date)",
                'format' => 'Y-m-d',
            ],
            'month' => [
                'select' => "DATE_FORMAT(create_date, '%Y-%m')",
                'group_by' => "YEAR(create_date), MONTH(create_date)",
                'format' => 'Y-m',
            ],
            'year' => [
                'select' => "DATE_FORMAT(create_date, '%Y')",
                'group_by' => "YEAR(create_date)",
                'format' => 'Y',
            ],
            default => [
                'select' => "DATE_FORMAT(create_date, '%Y-%m')",
                'group_by' => "YEAR(create_date), MONTH(create_date)",
                'format' => 'Y-m',
            ],
        };
    }
}
