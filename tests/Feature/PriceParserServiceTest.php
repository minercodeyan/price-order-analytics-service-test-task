<?php

namespace Tests\Unit\Services;

use App\Services\PriceParserService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class PriceParserServiceTest extends TestCase
{
    private PriceParserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PriceParserService();
    }

    /** @test */
    public function it_returns_price_when_page_contains_valid_price()
    {
        // Arrange
        $factory = 'kerama-marazzi';
        $collection = 'concepto';
        $article = 'CM40CONC001';

        $expectedPrice = 129.50;
        $html = $this->generateHtmlWithPrice($expectedPrice);

        Http::fake([
            'tile.expert/*' => Http::response($html, 200)
        ]);

        // Act
        $price = $this->service->parsePrice($factory, $collection, $article);

        // Assert
        $this->assertEquals($expectedPrice, $price);

        Http::assertSent(function ($request) use ($factory, $collection, $article) {
            return $request->url() === "https://tile.expert/it/tile/{$factory}/{$collection}/a/{$article}";
        });
    }

    private function generateHtmlWithPrice(float $price): string
    {
        return sprintf(
            '<html>
                <body>
                    <div class="product-info">
                        <div class="js-price-tag" data-price-raw="%s">€%s</div>
                    </div>
                </body>
            </html>',
            $price,
            number_format($price, 2)
        );
    }
}
