<?php

namespace App\Http\Controllers;

use App\Http\Requests\SoapStoreRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Services\SoapService;
use App\Traits\OrderAnnotations;
use App\Traits\SoapOrderAnnotations;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use OrderAnnotations, SoapOrderAnnotations;
    public function __construct(
        protected SoapService $soapService,
        protected OrderService $orderService) {}
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrderById($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Заказ не найден'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }

    public function soapStore(SoapStoreRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $result = $this->soapService->createSoapOrder($validatedData);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $result['order_id'],
                'order_number' => $result['order_number'],
                'hash' => $result['hash']
            ],
            'message' => $result['message']
        ], 201);
    }
}
