<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Item\MenuItem;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Renderer\MenuRenderer;
use Becklyn\Menu\Visitor\TranslationVisitor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tests\Becklyn\Menu\Renderer\RendererTestTrait;

class MenuItemTranslationsTest extends TestCase
{
    use RendererTestTrait;


    /**
     *
     */
    public function testTranslations () : void
    {
        $root = new MenuItem();
        $root->createChild("label1")->addLinkClass("item-1");
        $root->createChild("label2")->addLinkClass("item-2");

        $renderer = $this->createTranslatedRenderer();
        $html = $renderer->render($root, ["translationDomain" => "domain"]);
        $crawler = new Crawler($html);

        self::assertSame("TRANS: label1 (domain)", $crawler->filter(".item-1")->text());
        self::assertSame("TRANS: label2 (domain)", $crawler->filter(".item-2")->text());
    }


    /**
     *
     */
    public function testWithoutDomain () : void
    {
        $root = new MenuItem();
        $root->createChild("label1")->addLinkClass("item-1");
        $root->createChild("label2")->addLinkClass("item-2");

        $renderer = $this->createTranslatedRenderer();
        $html = $renderer->render($root, ["translationDomain" => null]);
        $crawler = new Crawler($html);

        self::assertSame("label1", $crawler->filter(".item-1")->text());
        self::assertSame("label2", $crawler->filter(".item-2")->text());
    }


    /**
     * @return MenuRenderer
     */
    private function createTranslatedRenderer () : MenuRenderer
    {
        $translator = new class implements TranslatorInterface
        {
            /**
             * @inheritDoc
             */
            public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
            {
                return "TRANS: {$id} ({$domain})";
            }


            /**
             * @inheritDoc
             */
            public function getLocale () : string
            {
                return "de";
            }
        };

        return $this->createRenderer([
            new TranslationVisitor($translator),
        ]);
    }
}
