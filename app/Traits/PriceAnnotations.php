<?php
// app/Traits/PriceAnnotations.php

namespace App\Traits;

use OpenApi\Attributes as OA;

trait PriceAnnotations
{
    #[OA\Get(
        path: "/api/price",
        description: "Parse product page and return price in EUR",
        summary: "Get product price from tile.expert",
        tags: ["Prices"],
        parameters: [
            new OA\Parameter(
                name: "factory",
                description: "Factory name",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "marca-corona")
            ),
            new OA\Parameter(
                name: "collection",
                description: "Collection name",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "arteseta")
            ),
            new OA\Parameter(
                name: "article",
                description: "Article code",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string", example: "k263-arteseta-camoscio-s000628660")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Price retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "price", type: "number", format: "float", example: 59.99),
                        new OA\Property(property: "factory", type: "string", example: "marca-corona"),
                        new OA\Property(property: "collection", type: "string", example: "arteseta"),
                        new OA\Property(property: "article", type: "string", example: "k263-arteseta-camoscio-s000628660")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Price not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Could not retrieve price for the given article.")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
    public function getPrice() {}
}
