<?php declare(strict_types=1);

namespace Becklyn\Menu\Voter;

use Becklyn\Menu\Item\MenuItem;
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

        // at this point in time the core visitor has already transformed all targets to a URL, but the previous route will
        // be stored in the extra `_route`
        $targetRoute = $item->getExtra("_route");

        return null !== $targetRoute
            ? $targetRoute === $route
            : null;
    }
}
