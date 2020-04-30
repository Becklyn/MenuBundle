<?php declare(strict_types=1);

namespace Tests\Becklyn\RadBundle\Route;

use Becklyn\Menu\Target\LazyRoute;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

class LazyRouteTest extends TestCase
{
    /**
     * @return iterable
     */
    public function provideCloneWithParameters () : iterable
    {
        yield "simple merge" => [
            ["p1" => "a", "p2" => "b"],
            ["p3" => "c"],
            ["p1" => "a", "p2" => "b", "p3" => "c"]
        ];

        yield "overwrite" => [
            ["p1" => "a", "p2" => "b"],
            ["p3" => "c", "p1" => "d"],
            ["p1" => "d", "p2" => "b", "p3" => "c"]
        ];

        yield "overwrite with null" => [
            ["p1" => "a"],
            ["p1" => null],
            ["p1" => null]
        ];
    }


    /**
     * Tests automatic entity interface support
     */
    public function testEntityInterfaceSupport () : void
    {
        $entity = new class
        {
            public function getId () : ?int { return 123; }
        };

        $route = new LazyRoute("route_name", ["page" => $entity]);

        $router = $this->getMockBuilder(RouterInterface::class)
            ->getMock();

        $router
            ->expects(self::once())
            ->method("generate")
            ->with("route_name", ["page" => 123])
            ->willReturn("result");

        $route->generate($router);
    }


    /**
     * Tests that entity interfaces are also supported when using `withParameters()`
     */
    public function testEntityInterfaceSupportWhenCloning () : void
    {
        $entity1 = new class
        {
            public function getId () : ?int { return 123; }
        };

        $entity2 = new class
        {
            public function getId () : ?int { return 234; }
        };

        $route1 = new LazyRoute("route_name", ["page" => $entity1]);

        $router = $this->getMockBuilder(RouterInterface::class)
            ->getMock();

        $router
            ->expects(self::exactly(2))
            ->method("generate")
            ->withConsecutive(
                ["route_name", ["page" => 123]],
                ["route_name", ["page" => 234]]
            )
            ->willReturn("result");

        $route1->generate($router);

        $route2 = $route1->withParameters(["page" => $entity2]);
        $route2->generate($router);
    }
}
