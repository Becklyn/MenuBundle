<?php declare(strict_types=1);

namespace Becklyn\Menu\Twig;

use Becklyn\Menu\Renderer\MenuRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuTwigExtension extends AbstractExtension
{
    /**
     * @var MenuRenderer
     */
    private $renderer;


    /**
     * @param MenuRenderer $renderer
     */
    public function __construct (MenuRenderer $renderer)
    {
        $this->renderer = $renderer;
    }


    /**
     * @inheritDoc
     */
    public function getFunctions ()
    {
        return [
            new TwigFunction("menu_render", [$this->renderer, "render"], ["is_safe" => ["html"]]),
        ];
    }
}
