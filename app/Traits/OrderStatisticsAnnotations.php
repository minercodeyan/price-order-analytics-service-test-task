<?php
namespace App\Traits;

use OpenApi\Attributes as OA;

trait OrderStatisticsAnnotations
{
    #[OA\Get(
        path: "/api/orders/statistics",
        summary: "Get orders statistics with grouping and pagination",
        tags: ["Orders"],
        parameters: [
            new OA\Parameter(
                name: "page",
                description: "Page number",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 1, minimum: 1)
            ),
            new OA\Parameter(
                name: "per_page",
                description: "Items per page",
                in: "query",
                schema: new OA\Schema(type: "integer", default: 15, minimum: 1, maximum: 100)
            ),
            new OA\Parameter(
                name: "group_by",
                description: "Grouping period",
                in: "query",
                schema: new OA\Schema(type: "string", enum: ["day", "month", "year"], default: "month")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "period", type: "string", example: "2025-01"),
                                    new OA\Property(property: "orders_count", type: "integer", example: 15)
                                ]
                            )
                        ),
                        new OA\Property(property: "current_page", type: "integer", example: 1),
                        new OA\Property(property: "per_page", type: "integer", example: 15),
                        new OA\Property(property: "total", type: "integer", example: 50),
                        new OA\Property(property: "last_page", type: "integer", example: 4),
                        new OA\Property(property: "group_by", type: "string", example: "month")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function index() {}
}
