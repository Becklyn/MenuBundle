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
     * @param ContainerInterface $renderer
     */
    public function __construct (ContainerInterface $locator)
    {
        $this->locator = $locator;
    }


    /**
     * @param MenuItem|null $root
     * @param array         $options
     *
     * @return string
     */
    public function renderMenu (?MenuItem $root, array $options = []) : string
    {
        return $this->locator->get(MenuRenderer::class)->render($root, $options);
    }


    /**
     * @inheritDoc
     */
    public function getFunctions ()
    {
        return [
            new TwigFunction("menu_render", [$this, "renderMenu"], ["is_safe" => ["html"]]),
        ];
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedServices ()
    {
        return [
            MenuRenderer::class,
        ];
    }
}
