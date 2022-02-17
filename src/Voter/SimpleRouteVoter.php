<?php declare(strict_types=1);

namespace Becklyn\Menu\Voter;

use Becklyn\Menu\Item\MenuItem;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Simple voter that just checks whether the route of the item matches to the current route.
 */
class SimpleRouteVoter implements VoterInterface
{
    private RequestStack $requestStack;
    private bool $alsoCheckParameters;


    public function __construct (RequestStack $requestStack, bool $alsoCheckParameters = false)
    {
        $this->requestStack = $requestStack;
        $this->alsoCheckParameters = $alsoCheckParameters;
    }


    /**
     * @inheritDoc
     */
    public function vote (MenuItem $item) : ?bool
    {
        $request = $this->requestStack->getMainRequest();

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

        if (null === $targetRoute)
        {
            return null;
        }

        if ($targetRoute !== $route)
        {
            return false;
        }

        return $this->alsoCheckParameters
            ? $this->checkParameters(
                $request->attributes->get("_route_params"),
                $item->getExtra("_route_params", [])
            )
            : true;
    }


    /**
     * Checks that the parameters are equal.
     */
    private function checkParameters (array $left, array $right) : bool
    {
        if (\count($left) === \count($right))
        {
            foreach ($left as $key => $value)
            {
                if (!\array_key_exists($key, $right) || !$this->compare($right[$key], $value))
                {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Compares the two values.
     */
    protected function compare ($left, $right) : bool
    {
        return $left === $right;
    }

}
