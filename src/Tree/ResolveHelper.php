<?php declare(strict_types=1);

namespace Becklyn\Menu\Tree;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResolveHelper
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;


    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct (UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function generateUrl (string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) : string
    {
        return $this->urlGenerator->generate($name, $parameters, $referenceType);
    }
}
