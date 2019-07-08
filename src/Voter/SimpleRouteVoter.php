<?php declare(strict_types=1);

namespace Becklyn\Menu\Voter;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Target\RouteTarget;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Simple voter that just checks whether the route of the item matches to the current route.
 */
class SimpleRouteVoter implements VoterInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;


    /**
     * @param RequestStack $requestStack
     */
    public function __construct (RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    /**
     * @inheritDoc
     */
    public function vote (MenuItem $item) : ?bool
    {
        $request = $this->requestStack->getMasterRequest();

        if (null === $request)
        {
            return null;
        }

        $route = $request->attributes->get("_route");

        if (null === $route)
        {
            return null;
        }

        $target = $item->getTarget();

        return $target instanceof RouteTarget
            ? $target->getRoute() === $route
            : null;
    }
}
