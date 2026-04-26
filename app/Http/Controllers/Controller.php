<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Price Order Analytics Service API",
    title: "Order API"
)]
#[OA\Server(
    url: "http://localhost:8080",
    description: "Local server"
)]
#[OA\Server(
    url: "https://api.yourdomain.com",
    description: "Production server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer"
)]
abstract class Controller
{
    //
}
