<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Http\HttpException;
use App\Http\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
        $this->router->get('/my-surveys/new', ['Create', 'show']);
        $this->router->get('/my-surveys/{survey}/questions/{question}/edit', ['Question', 'edit']);
        $this->router->post('/my-surveys/{survey}/checkout', ['Payment', 'checkout']);
    }

    public function testExtractsIntegerParameters(): void
    {
        $route = $this->router->match('GET', '/my-surveys/12/questions/7/edit');

        $this->assertSame(['Question', 'edit'], $route['handler']);
        $this->assertSame([12, 7], $route['params']);
    }

    public function testStaticSegmentIsNotTreatedAsParameter(): void
    {
        $this->assertSame(['Create', 'show'], $this->router->match('GET', '/my-surveys/new')['handler']);
    }

    public function testRejectsNonNumericAndZeroIds(): void
    {
        foreach (['/my-surveys/abc/checkout', '/my-surveys/0/checkout', "/my-surveys/1'--/checkout"] as $path) {
            try {
                $this->router->match('POST', $path);
                $this->fail("Expected 404 for {$path}");
            } catch (HttpException $e) {
                $this->assertSame(404, $e->status);
            }
        }
    }

    public function testWrongMethodIs405(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Method not allowed.');

        $this->router->match('GET', '/my-surveys/3/checkout');
    }
}
