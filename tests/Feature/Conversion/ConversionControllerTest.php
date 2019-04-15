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

    public function testShouldConvertFromUSDtoBRL()
    {
        $from = 'USD';
        $to = 'BRL';
        $amount = 1;
        $total = 0.2571;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(CurrencyRepository::class, function () {
            $mock = Mockery::mock(CurrencyRepository::class)->makePartial();

            $currenciesValues = [
                'USD' => 3.8892,
                'EUR' => 4.3915,
            ];

            foreach ($currenciesValues as $currency => $value) {
                $mock->shouldReceive('getCurrencyPrice')->with($currency)->andReturn($value);
            }

            return $mock;
        });
    }
}
