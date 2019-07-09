<?php declare(strict_types=1);

namespace Becklyn\Menu\Visitor;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Target\LazyRoute;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Core visitor, that checks the security and replaces the URLs.
 */
class CoreVisitor implements ItemVisitor
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
    public function visit (MenuItem $item) : void
    {
        $target = $item->getTarget();

        // replace target with URL
        if ($target instanceof LazyRoute)
        {
            // store the previous route in an extra attribute
            $item->setExtra("_route", $target->getRoute());

            try
            {
                $item->setTarget($target->generate($this->urlGenerator));
            }
            catch (MissingMandatoryParametersException $exception)
            {
                // ignore exception if no parameters were given
                // otherwise -> rethrow
                if (!empty($target->getParameters()))
                {
                    throw $exception;
                }

                $item->setTarget(null);
            }
        }

        // check security
        if (null !== $item->getSecurity())
        {
            if (!$this->authorizationChecker->isGranted(new Expression($item->getSecurity())))
            {
                $item->setVisible(false);
            }
        }
    }
}
