<?php declare(strict_types=1);

namespace Becklyn\Menu\Target;

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * All config required for generating a route. This is a VO to delay the actual generation of the route to a later
 * point in time.
 */
class LazyRoute
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


    /**
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return string
     *
     * @throws RouteNotFoundException
     * @throws MissingMandatoryParametersException
     * @throws InvalidParameterException
     */
    public function generate (UrlGeneratorInterface $urlGenerator) : string
    {
        return $urlGenerator->generate($this->route, $this->parameters, $this->referenceType);
    }
}
