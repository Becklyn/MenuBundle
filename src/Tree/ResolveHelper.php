<?php declare(strict_types=1);

namespace Becklyn\Menu\Tree;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ResolveHelper
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;


    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;


    /**
     * @param UrlGeneratorInterface         $urlGenerator
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct (UrlGeneratorInterface $urlGenerator, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->urlGenerator = $urlGenerator;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @inheritDoc
     */
    public function generateUrl (string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) : string
    {
        return $this->urlGenerator->generate($name, $parameters, $referenceType);
    }


    /**
     * @param string $expression
     *
     * @return bool
     */
    public function isGranted (string $expression) : bool
    {
        return $this->authorizationChecker->isGranted(new Expression($expression));
    }
}
