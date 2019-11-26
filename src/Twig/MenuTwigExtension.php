<?php declare(strict_types=1);

namespace Becklyn\Menu\Twig;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Renderer\MenuRenderer;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuTwigExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $locator;


    /**
     */
    public function __construct (ContainerInterface $locator)
    {
        $this->locator = $locator;
    }


    /**
     *
     */
    public function renderMenu (?MenuItem $root, array $options = []) : string
    {
        return $this->locator->get(MenuRenderer::class)->render($root, $options);
    }


    /**
     * @inheritDoc
     */
    public function getFunctions () : array
    {
        return [
            new TwigFunction("menu_render", [$this, "renderMenu"], ["is_safe" => ["html"]]),
        ];
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedServices () : array
    {
        return [
            MenuRenderer::class,
        ];
    }
}
