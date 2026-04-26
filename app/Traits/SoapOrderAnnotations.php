<?php

namespace App\Traits;

use OpenApi\Attributes as OA;

trait SoapOrderAnnotations
{
    #[OA\Post(
        path: "/api/soap/order",
        description: "Create a new order via SOAP protocol (REST wrapper). Requires API key in header.",
        summary: "Create order via SOAP",
        security: [
            ["ApiKeyAuth" => []]
        ],
        requestBody: new OA\RequestBody(
            description: "Order data",
            required: true,
            content: new OA\JsonContent(
                required: ["email", "total_amount", "items", "payment"],
                properties: [
                    new OA\Property(property: "email", description: "Customer email", type: "string", format: "email", example: "customer@example.com"),
                    new OA\Property(property: "user_id", description: "User ID (optional)", type: "integer", example: 1, nullable: true),
                    new OA\Property(property: "client_name", description: "First name", type: "string", example: "John", nullable: true),
                    new OA\Property(property: "client_surname", description: "Last name", type: "string", example: "Doe", nullable: true),
                    new OA\Property(property: "company_name", description: "Company name", type: "string", example: "ACME Corp", nullable: true),
                    new OA\Property(property: "currency", description: "Currency code", type: "string", example: "EUR", default: "EUR", maxLength: 3),
                    new OA\Property(property: "total_amount", description: "Total order amount", type: "number", format: "float", example: 1500.00),
                    new OA\Property(property: "discount", description: "Discount percentage (0-100)", type: "integer", example: 10, nullable: true, maximum: 100, minimum: 0),
                    new OA\Property(property: "locale", description: "Locale", type: "string", example: "en_US", maxLength: 5),
                    new OA\Property(property: "name", description: "Order name", type: "string", example: "My Order", nullable: true),

                    // Items array
                    new OA\Property(
                        property: "items",
                        description: "Order items",
                        type: "array",
                        items: new OA\Items(
                            required: ["article_id", "amount", "price"],
                            properties: [
                                new OA\Property(property: "article_id", description: "Article ID", type: "integer", example: 1001),
                                new OA\Property(property: "amount", description: "Quantity", type: "number", format: "float", example: 10.5, minimum: 0.001),
                                new OA\Property(property: "price", description: "Unit price", type: "number", format: "float", example: 99.99, minimum: 0),
                                new OA\Property(property: "price_eur", description: "Price in EUR", type: "number", format: "float", example: 99.99, nullable: true),
                                new OA\Property(property: "currency", description: "Item currency", type: "string", example: "EUR", maxLength: 3),
                                new OA\Property(property: "measure", description: "Unit of measure", type: "string", example: "m", maxLength: 2),
                                new OA\Property(property: "weight", description: "Weight", type: "number", format: "float", example: 25.5, nullable: true),
                                new OA\Property(property: "packaging_count", description: "Packaging count", type: "number", format: "float", example: 1, default: 1),
                                new OA\Property(property: "pallet", description: "Pallet quantity", type: "number", format: "float", example: 0, nullable: true),
                            ]
                        ),
                        minItems: 1
                    ),

                    // Delivery object
                    new OA\Property(
                        property: "delivery",
                        description: "Delivery information",
                        properties: [
                            new OA\Property(property: "cost", description: "Delivery cost", type: "number", format: "float", example: 50.00),
                            new OA\Property(property: "cost_eur", description: "Delivery cost in EUR", type: "number", format: "float", example: 50.00),
                            new OA\Property(property: "type", description: "0 - client address, 1 - warehouse address", type: "integer", example: 0, enum: [0, 1]),
                            new OA\Property(property: "country_id", description: "Country ID", type: "integer", example: 1),
                            new OA\Property(property: "city", description: "City", type: "string", example: "Berlin"),
                            new OA\Property(property: "address", description: "Address line", type: "string", example: "Main Str. 123"),
                            new OA\Property(property: "phone", description: "Phone number", type: "string", example: "+49123456789")
                        ],
                        type: "object",
                        nullable: true
                    ),

                    // Payment object
                    new OA\Property(
                        property: "payment",
                        description: "Payment information",
                        required: ["type"],
                        properties: [
                            new OA\Property(property: "type", description: "1-card, 2-bank transfer, 3-cash", type: "integer", example: 1, enum: [1, 2, 3]),
                            new OA\Property(property: "vat_type", description: "0-private person, 1-VAT payer", type: "integer", example: 0, enum: [0, 1]),
                            new OA\Property(property: "vat_number", description: "VAT number", type: "string", example: "DE123456789", maxLength: 100),
                            new OA\Property(property: "tax_number", description: "Tax number", type: "string", example: "123/456/789", maxLength: 50)
                        ],
                        type: "object"
                    )
                ]
            )
        ),
        tags: ["Orders"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Order created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "order_id", type: "integer", example: 12345),
                                new OA\Property(property: "order_number", type: "string", example: "2412156789"),
                                new OA\Property(property: "hash", type: "string", example: "a1b2c3d4e5f6789012345678")
                            ],
                            type: "object"
                        ),
                        new OA\Property(property: "message", type: "string", example: "Заказ успешно создан")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ошибка валидации"),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            additionalProperties: new OA\AdditionalProperties(
                                type: "array",
                                items: new OA\Items(type: "string")
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized - Invalid or missing API key",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Invalid API key")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Business logic error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Не найден артикул с ID 1001")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Database connection error")
                    ]
                )
            )
        ]
    )]
    public function soapStore()
    {
    }
}
