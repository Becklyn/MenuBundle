<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Target\LazyRoute;

use Becklyn\Menu\Target\LazyRoute;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class LazyRouteGenerateTest extends TestCase
{
    /**
     * Tests basic generation
     */
    public function testGenerate () : void
    {
        $router = $this->getMockBuilder(RouterInterface::class)
            ->getMock();

        $router
            ->expects(self::once())
            ->method("generate")
            ->with("route_name", ["param1" => "a", "param2" => "b"], UrlGeneratorInterface::NETWORK_PATH)
            ->willReturn("result");

        $route = new LazyRoute("route_name", ["param1" => "a", "param2" => "b"], UrlGeneratorInterface::NETWORK_PATH);
        $route->generate($router);
    }
}
