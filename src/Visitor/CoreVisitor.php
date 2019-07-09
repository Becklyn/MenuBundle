<?php declare(strict_types=1);

namespace Becklyn\Menu\Visitor;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Target\LazyRoute;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Exception\ExceptionInterface;
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
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @param UrlGeneratorInterface         $urlGenerator
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param LoggerInterface               $logger
     */
    public function __construct (
        UrlGeneratorInterface $urlGenerator,
        AuthorizationCheckerInterface $authorizationChecker,
        LoggerInterface $logger
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->authorizationChecker = $authorizationChecker;
        $this->logger = $logger;
    }


    /**
     * @inheritDoc
     */
    public function visit (MenuItem $item) : void
    {
        $target = $item->getTarget();

        // check security
        if (null !== $item->getSecurity())
        {
            if (!$this->authorizationChecker->isGranted(new Expression($item->getSecurity())))
            {
                $item->setVisible(false);
            }
        }

        // replace target with URL
        // do it after the security check, as it might hide the node
        if ($target instanceof LazyRoute)
        {
            // store the previous route in an extra attribute
            $item->setExtra("_route", $target->getRoute());

            if ($item->isVisible())
            {
                try
                {
                    $item->setTarget($target->generate($this->urlGenerator));
                }
                catch (ExceptionInterface $exception)
                {
                    $this->logger->error("Failed to resolve LazyRoute in item {item}: {message}", [
                        "item" => $item->getLabel(),
                        "message" => $exception->getMessage(),
                        "exception" => $exception,
                    ]);
                    $item->setTarget(null);
                }
            }
        }
    }


    /**
     * @inheritDoc
     */
    public function supports (array $options) : bool
    {
        return true;
    }
}
