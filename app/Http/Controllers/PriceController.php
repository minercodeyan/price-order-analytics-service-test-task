<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceRequest;
use App\Services\PriceParserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PriceController extends Controller
{
    public function __construct(
        protected PriceParserService $priceParserService
    ) {}

    public function getPrice(PriceRequest $request){
        $factory = $request->input('factory');
        $collection = $request->input('collection');
        $article = $request->input('article');

        $price = $this->priceParserService->parsePrice($factory, $collection, $article);

        if ($price === null) {
            return response()->json([
                'message' => 'Could not retrieve price for the given article.'
            ], 404);
        }

        return response()->json([
            'price' => $price,
            'factory' => $factory,
            'collection' => $collection,
            'article' => $article,
        ]);
    }
}
