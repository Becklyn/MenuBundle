<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Target\LazyRoute;

use Becklyn\Menu\Target\LazyRoute;
use PHPUnit\Framework\TestCase;

class LazyRouteWithParametersTest extends TestCase
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
     * @dataProvider provideCloneWithParameters
     */
    public function testWithParameters (array $paramsBefore, array $paramsWith, array $expected) : void
    {
        $route1 = new LazyRoute("route_name", $paramsBefore);
        $route2 = $route1->withParameters($paramsWith);

        // parameters stay unchanged
        self::assertEquals($paramsBefore, $route1->getParameters());
        // check expected result parameters
        self::assertEquals($expected, $route2->getParameters());
    }
}
