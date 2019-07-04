<?php declare(strict_types=1);

namespace Becklyn\Menu\Target;

use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * A route target, that must later be resolved to point to an URL using a router.
 */
class RouteTarget
{
    /**
     * @var string
     */
    private $route;


    /**
     * @var array
     */
    private $parameters;


    /**
     * @var int
     */
    private $referenceType;


    /**
     *
     * @param string $route
     * @param array  $parameters
     * @param int    $referenceType
     */
    public function __construct (string $route, array $parameters = [], int $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $this->route = $route;
        $this->parameters = $parameters;
        $this->referenceType = $referenceType;
    }


    /**
     * @return string
     */
    public function getRoute () : string
    {
        return $this->route;
    }


    /**
     * @return array
     */
    public function getParameters () : array
    {
        return $this->parameters;
    }


    /**
     * @return int
     */
    public function getReferenceType () : int
    {
        return $this->referenceType;
    }
}
