<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;


class PriceParserService
{
    /**
     * Парсит цену в евро со страницы tile.expert.
     *
     * @param string $factory
     * @param string $collection
     * @param string $article
     * @return float|null
     */
    public function parsePrice(string $factory, string $collection, string $article): ?float
    {
        // Формируем URL для парсинга
        $url = $this->buildUrl($factory, $collection, $article);

        try {
            // 1. Делаем GET-запрос к странице товара
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                Log::warning('Price parser: Failed to fetch page', ['url' => $url, 'status' => $response->status()]);
                return null;
            }

            // 2. Парсим HTML и ищем цену
            $price = $this->extractPriceFromHtml($response->body());

            if ($price === null) {
                Log::warning('Price parser: Could not extract price from page', ['url' => $url]);
                return null;
            }

            // 3. Возвращаем цену как float (в евро)
            return (float) $price;

        } catch (\Exception $e) {
            Log::error('Price parser error: ' . $e->getMessage(), ['url' => $url]);
            return null;
        }
    }

    private function buildUrl(string $factory, string $collection, string $article): string
    {
        return "https://tile.expert/it/tile/{$factory}/{$collection}/a/{$article}";
    }

    private function extractPriceFromHtml(string $html): ?string
    {
        $crawler = new Crawler($html);

        $jsPriceNodes = $crawler->filter('.js-price-tag');

        if ($jsPriceNodes->count() > 0) {
            $priceRaw = $jsPriceNodes->first()->attr('data-price-raw');
            if ($priceRaw && is_numeric($priceRaw)) {
                return (float) $priceRaw;
            }
        }

        return null;
    }
}
