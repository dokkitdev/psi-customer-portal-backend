<?php

namespace App\Tests\Support;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\HttpRequestService;

trait MockHttpRequestServiceTrait
{
    protected function mockHttpRequestService(string $requestsChainFixture): void
    {
        $requestsChain = $this->getJsonFixture($requestsChainFixture);

        $methods = array_map(fn($type) => "send{$type}", array_unique(Arr::pluck($requestsChain, 'request.type')));

        $mock = $this
            ->getMockBuilder(HttpRequestService::class)
            ->onlyMethods($methods)
            ->getMock();

        foreach ($requestsChain as $index => $call) {
            $response = $this->makeResponse($call['response']);

            $requestData = $call['request']['data'];

            $mock
                ->expects($this->at($index))
                ->method("send{$call['request']['type']}")
                ->with($call['request']['url'], $requestData, $call['request']['headers'])
                ->willReturn($response);
        }

        $this->app->instance(HttpRequestService::class, $mock);
    }

    protected function makeResponse(array $response): GuzzleResponse
    {
        if (is_array($response['data'])) {
            $response['data'] = json_encode($response['data']);
        } elseif (!empty($response['data']) && file_exists($this->getFixturePath($response['data']))) {
            $response['data'] = $this->getFixture($response['data']);
        }

        return new GuzzleResponse(
            $response['status_code'],
            $response['headers'],
            $response['data']
        );
    }
}
