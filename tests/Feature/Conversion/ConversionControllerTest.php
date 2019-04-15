<?php

namespace Tests\Feature\Conversion;

use App\Domains\Currency\CurrencyRepository;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class ConversionControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldReturnErrorIfRequiredAttributesNotSend()
    {
        $this->withExceptionHandling();
        $response = $this->json('POST', '/api/conversion');

        $response->assertJsonValidationErrors([
            'from',
            'to',
            'amount',
        ]);
    }

    public function testShouldConvertFromBRLtoUSD()
    {
        $from = 'BRL';
        $to = 'USD';
        $amount = 3.8892;
        $total = 1;
        $this->executeTest($from, $to, $amount, $total);
    }

    /**
     * @param string $from
     * @param string $to
     * @param float  $amount
     * @param float  $total
     */
    public function executeTest(string $from, string $to, float $amount, float $total): void
    {
        $response = $this->json('POST', '/api/conversion', [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => $total,
            ]);
    }

    public function testShouldConvertFromBTCtoBRL()
    {
        $from = 'BTC';
        $to = 'BRL';
        $amount = 1;
        $total = 21242.81;
        $this->executeTest($from, $to, $amount, $total);
    }

    public function testShouldConvertFromBRLtoBTC()
    {
        $from = 'BRL';
        $to = 'BTC';
        $amount = 1000;
        $total = 0.0471;
        $this->executeTest($from, $to, $amount, $total);
    }

    public function testShouldConvertFromUSDtoBRL()
    {
        $from = 'USD';
        $to = 'BRL';
        $amount = 1;
        $total = 3.8892;
        $this->executeTest($from, $to, $amount, $total);
    }

    public function testShouldConvertFromUSDtoEUR()
    {
        $from = 'USD';
        $to = 'EUR';
        $amount = 1;
        $total = 0.8856;
        $this->executeTest($from, $to, $amount, $total);
    }

    public function testShouldConvertFromSameCurrency()
    {
        $from = 'USD';
        $to = 'USD';
        $amount = 1;
        $total = 1;
        $this->executeTest($from, $to, $amount, $total);
    }

    public function testShouldConvertFromSameBaseCurrency()
    {
        $from = 'BRL';
        $to = 'BRL';
        $amount = 1;
        $total = 1;
        $this->executeTest($from, $to, $amount, $total);
    }

    public function testShouldThrownErrorIfCurrencyNotSupported()
    {
        $this->withExceptionHandling();
        $response = $this->json(
            'POST',
            '/api/conversion',
            [
                'from' => 'QWE',
                'to' => 'QWE',
                'amount' => 'QWE',
            ]
        );

        $response->assertJsonValidationErrors([
            'from',
            'to',
        ]);

        $response->assertJsonMissingValidationErrors([
            'amount',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(CurrencyRepository::class, function () {
            $mock = Mockery::mock(CurrencyRepository::class)->makePartial();

            $currenciesValues = [
                'USD' => 3.8892,
                'EUR' => 4.3915,
                'GBP' => 5.0827,
                'ARS' => 0.0921,
                'BTC' => 21242.81,
            ];

            foreach ($currenciesValues as $currency => $value) {
                $mock->shouldReceive('getCurrencyPrice')->with($currency)->andReturn($value);
            }

            return $mock;
        });
    }
}
