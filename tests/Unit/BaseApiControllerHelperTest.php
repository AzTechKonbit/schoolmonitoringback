<?php

namespace Tests\Unit;

use App\Core\Http\Controllers\BaseApiController;
use ReflectionMethod;
use Tests\TestCase;

class BaseApiControllerHelperTest extends TestCase
{
    public function test_error_includes_errors_when_provided(): void
    {
        $controller = new BaseApiController;

        $method = new ReflectionMethod(BaseApiController::class, 'error');
        $method->setAccessible(true);

        $response = $method->invoke($controller, 'Bad', 400, ['field' => 'required']);

        $json = $response->getData(true);

        $this->assertFalse($json['success']);
        $this->assertEquals('Bad', $json['message']);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['field' => 'required'], $json['errors']);
    }

    public function test_error_without_errors_omits_key(): void
    {
        $controller = new BaseApiController;

        $method = new ReflectionMethod(BaseApiController::class, 'error');
        $method->setAccessible(true);

        $response = $method->invoke($controller, 'Fail', 500);

        $json = $response->getData(true);

        $this->assertFalse($json['success']);
        $this->assertArrayNotHasKey('errors', $json);
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_success_returns_payload(): void
    {
        $controller = new BaseApiController;

        $method = new ReflectionMethod(BaseApiController::class, 'success');
        $method->setAccessible(true);

        $response = $method->invoke($controller, ['a' => 1], 'Ok', 201);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(['success' => true, 'message' => 'Ok', 'data' => ['a' => 1]], $response->getData(true));
    }
}