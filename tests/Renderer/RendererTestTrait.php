<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Renderer;

use Becklyn\Menu\Renderer\MenuRenderer;
use Tests\Becklyn\Menu\Twig\TestTwigMocksExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

trait RendererTestTrait
{
    /**
     * Creates and wires a menu renderer
     *
     * @param array $visitors
     */
    private function createRenderer (array $visitors = []) : MenuRenderer
    {
        $loader = new FilesystemLoader();
        $loader->addPath(__DIR__ . "/../../src/Resources/views", "BecklynMenu");

        $twig = new Environment($loader, [
            "auto_reload" => true,
            "cache" => false,
            "debug" => true,
            "strict_variables" => true,
        ]);

        $twig->addExtension(new TestTwigMocksExtension());

        return new MenuRenderer($twig, $visitors);
    }
}
