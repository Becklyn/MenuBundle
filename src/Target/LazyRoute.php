<?php declare(strict_types=1);

namespace Becklyn\Menu\Target;

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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
     * @param string $route         the route name  #Route
     * @param array  $parameters    the parameters required for generating the route
     * @param int    $referenceType the reference type to generate for this route
     */
    public function __construct (string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $this->route = $route;
        $this->parameters = $this->normalizeParameters($parameters);
        $this->referenceType = $referenceType;
    }


    /**
     */
    public function getRoute () : string
    {
        return $this->route;
    }


    /**
     */
    public function getParameters () : array
    {
        return $this->parameters;
    }


    /**
     */
    public function getReferenceType () : int
    {
        return $this->referenceType;
    }


    /**
     * @throws RouteNotFoundException
     * @throws MissingMandatoryParametersException
     * @throws InvalidParameterException
     */
    public function generate (UrlGeneratorInterface $urlGenerator) : string
    {
        return $urlGenerator->generate($this->route, $this->parameters, $this->referenceType);
    }


    /**
     * @return static
     */
    public function withParameters (array $parameters) : self
    {
        $modified = clone $this;
        $modified->parameters = \array_replace($modified->parameters, $this->normalizeParameters($parameters));
        return $modified;
    }


    /**
     * Normalizes the parameters
     */
    private function normalizeParameters (array $parameters) : array
    {
        $normalized = [];

        foreach ($parameters as $key => $value)
        {
            // automatically integrate with entities, so that you can just pass the entity and it will automatically use its id
            $normalized[$key] = \is_object($value) && \method_exists($value, "getId")
                ? $value->getId()
                : $value;
        }

        return $normalized;
    }
}
