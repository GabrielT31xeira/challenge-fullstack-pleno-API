<?php

namespace Tests\Unit\Support;

use App\Support\ApiResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_success_response()
    {
        $response = ApiResponse::success(['foo' => 'bar'])->getData(true);

        $this->assertTrue($response['success']);
        $this->assertEquals(['foo' => 'bar'], $response['data']);
    }

    public function test_error_response()
    {
        $response = ApiResponse::error('Erro ocorrido', ['campo' => ['Inválido']])->getData(true);

        $this->assertFalse($response['success']);
        $this->assertEquals('Erro ocorrido', $response['message']);
        $this->assertEquals(['campo' => ['Inválido']], $response['errors']);
    }

    public function test_server_error_response()
    {
        $response = ApiResponse::serverError('Erro interno', 'Stacktrace')->getData(true);

        $this->assertFalse($response['success']);
        $this->assertEquals('Erro interno', $response['message']);
        $this->assertEquals(['Stacktrace'], $response['errors']);
    }

    public function test_paginated_response()
    {
        $items = collect([
            ['id' => 1, 'name' => 'Produto 1'],
            ['id' => 2, 'name' => 'Produto 2'],
        ]);

        $paginator = new LengthAwarePaginator(
            $items,
            total: 50,
            perPage: 2,
            currentPage: 1,
            options: ['path' => 'http://localhost']
        );

        $resource = new class($paginator) extends ResourceCollection {
            public function toArray($request)
            {
                return $this->collection->toArray();
            }
        };

        $response = ApiResponse::paginated($resource)->getData(true);

        $this->assertTrue($response['success']);
        $this->assertCount(2, $response['data']);

        $this->assertEquals(1, $response['meta']['current_page']);
        $this->assertEquals(2, $response['meta']['per_page']);
        $this->assertEquals(50, $response['meta']['total']);
        $this->assertEquals(25, $response['meta']['last_page']); // 50 / 2

        $this->assertArrayHasKey('first', $response['links']);
        $this->assertArrayHasKey('last', $response['links']);
        $this->assertArrayHasKey('prev', $response['links']);
        $this->assertArrayHasKey('next', $response['links']);
    }
}
