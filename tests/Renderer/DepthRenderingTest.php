<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Renderer;

use Becklyn\Menu\Item\MenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class DepthRenderingTest extends TestCase
{
    use RendererTestTrait;


    /**
     *
     */
    public function testRendering () : void
    {
        $renderer = $this->createRenderer();

        $root = new MenuItem();
        $root
            ->createChild("1")
            ->createChild("1.1")
            ->createChild("1.1.1")
            ->createChild("1.1.1.1")
            ->createChild("1.1.1.1.1");

        $html = $renderer->render($root);
        $crawler = new Crawler($html);

        self::assertCount(1, $crawler->filter("ul.menu-level-0"));
        self::assertCount(1, $crawler->filter("ul.menu-level-1"));
        self::assertCount(1, $crawler->filter("ul.menu-level-2"));
        self::assertCount(1, $crawler->filter("ul.menu-level-3"));
        self::assertCount(1, $crawler->filter("ul.menu-level-4"));
    }


    public function testRenderingWithDepth () : void
    {

        $renderer = $this->createRenderer();

        $root = new MenuItem();
        $root
            ->createChild("1")
            ->createChild("1.1")
            ->createChild("1.1.1")
            ->createChild("1.1.1.1")
            ->createChild("1.1.1.1.1");

        $html = $renderer->render($root, ["depth" => 2]);
        $crawler = new Crawler($html);

        self::assertCount(1, $crawler->filter("ul.menu-level-0"));
        self::assertCount(1, $crawler->filter("ul.menu-level-1"));
        self::assertCount(0, $crawler->filter("ul.menu-level-2"));
        self::assertCount(0, $crawler->filter("ul.menu-level-3"));
        self::assertCount(0, $crawler->filter("ul.menu-level-4"));
    }


    public function testRenderingWithDepthNotAtRoot () : void
    {

        $renderer = $this->createRenderer();

        $root = new MenuItem();
        $root
            ->createChild("1")
            ->createChild("1.1", ["key" => "start"])
            ->createChild("1.1.1")
            ->createChild("1.1.1.1")
            ->createChild("1.1.1.1.1");

        $html = $renderer->render($root->find("start"), ["depth" => 2]);
        echo($html);
        $crawler = new Crawler($html);

        self::assertCount(1, $crawler->filter("ul.menu-level-0"), "1 li under root");
        self::assertCount(1, $crawler->filter("ul.menu-level-1"));
        self::assertCount(0, $crawler->filter("ul.menu-level-2"));
        self::assertCount(0, $crawler->filter("ul.menu-level-3"));
        self::assertCount(0, $crawler->filter("ul.menu-level-4"));
        self::assertCount(1, $crawler->filter(".menu-list > li > .menu-list"));
    }
}
