<?php

namespace App\Traits;
use OpenApi\Attributes as OA;
trait OrderAnnotations
{
    #[OA\Get(
        path: "/api/orders/{id}",
        description: "Retrieve detailed information about a specific order by its ID",
        summary: "Get order by ID",
        tags: ["Orders"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Order ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Order retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "hash", type: "string", example: "abc123def456"),
                                new OA\Property(property: "number", type: "string", example: "ORD-00001"),
                                new OA\Property(property: "status", type: "integer", example: 5),
                                new OA\Property(property: "status_text", type: "string", example: "Доставлен"),
                                new OA\Property(property: "client_name", type: "string", example: "Иван"),
                                new OA\Property(property: "client_surname", type: "string", example: "Петров"),
                                new OA\Property(property: "email", type: "string", example: "ivan@example.com"),
                                new OA\Property(property: "company_name", type: "string", nullable: true),
                                new OA\Property(property: "total_amount", type: "number", format: "float", example: 1250.50),
                                new OA\Property(property: "currency", type: "string", example: "EUR"),
                                new OA\Property(property: "discount", type: "integer", nullable: true),
                                new OA\Property(property: "create_date", type: "string", format: "date-time"),
                                new OA\Property(
                                    property: "delivery",
                                    properties: [
                                        new OA\Property(property: "cost", type: "number", format: "float", example: 150.00),
                                        new OA\Property(property: "city", type: "string", example: "Москва"),
                                        new OA\Property(property: "address", type: "string", example: "ул. Тверская, д. 1"),
                                        new OA\Property(property: "tracking_number", type: "string", example: "TRK123456")
                                    ],
                                    type: "object"
                                ),
                                new OA\Property(
                                    property: "payment",
                                    properties: [
                                        new OA\Property(property: "type", type: "integer", example: 1),
                                        new OA\Property(property: "full_payment_date", type: "string", format: "date", nullable: true)
                                    ],
                                    type: "object"
                                ),
                                new OA\Property(
                                    property: "items",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 1),
                                            new OA\Property(property: "article_sku", type: "string", example: "PLATE-001"),
                                            new OA\Property(property: "amount", type: "number", format: "float", example: 10),
                                            new OA\Property(property: "price", type: "number", format: "float", example: 50.50)
                                        ]
                                    )
                                )
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Заказ не найден",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Заказ не найден")
                    ]
                )
            ),
        ]
    )]
    public function show() {}
}
