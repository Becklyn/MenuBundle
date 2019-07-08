<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Item;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Target\RouteTarget;
use Becklyn\Menu\Tree\ResolveHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MenuItemTest extends TestCase
{
    /**
     *
     */
    public function testSetNullParent () : void
    {
        $parent = new MenuItem("parent");
        $child = $parent->addChild("child");

        self::assertCount(1, $parent->getChildren());
        self::assertSame($child, $parent->getChildren()[0]);
        self::assertSame($parent, $child->getParent());

        $child->setParent(null);

        self::assertCount(0, $parent->getChildren());
        self::assertNull($child->getParent());
    }


    /**
     *
     */
    public function testSetOtherParent () : void
    {
        $parent1 = new MenuItem("parent 1");
        $parent2 = new MenuItem("parent 2");
        $child = $parent1->addChild("child");

        self::assertCount(1, $parent1->getChildren());
        self::assertCount(0, $parent2->getChildren());
        self::assertSame($child, $parent1->getChildren()[0]);
        self::assertSame($parent1, $child->getParent());

        $child->setParent($parent2);

        self::assertCount(0, $parent1->getChildren());
        self::assertCount(1, $parent2->getChildren());
        self::assertSame($child, $parent2->getChildren()[0]);
        self::assertSame($parent2, $child->getParent());
    }


    /**
     *
     */
    public function testConstructorOptions () : void
    {
        $listItemAttributes = ["list" => "listItemAttributes"];
        $linkAttributes = ["list" => "linkAttributes"];
        $childListAttributes = ["list" => "childListAttributes"];
        $extras = ["list" => "extras"];

        $item = new MenuItem("item", [
            "priority" => 5,
            "listItemAttributes" => $listItemAttributes,
            "linkAttributes" => $linkAttributes,
            "childListAttributes" => $childListAttributes,
            "target" => "abc",
            "visible" => false,
            "current" => true,
            "extras" => $extras,
            "key" => "key",
            "security" => "security",
        ]);

        self::assertSame("item", $item->getLabel());
        self::assertSame(5, $item->getPriority());
        self::assertSame($listItemAttributes, $item->getListItemAttributes());
        self::assertSame($linkAttributes, $item->getLinkAttributes());
        self::assertSame($childListAttributes, $item->getChildListAttributes());
        self::assertSame("abc", $item->getTarget());
        self::assertSame(false, $item->isVisible());
        self::assertSame(true, $item->isCurrent());
        self::assertSame($extras, $item->getExtras());
        self::assertSame("key", $item->getKey());
        self::assertSame("security", $item->getSecurity());
    }


    /**
     *
     */
    public function testAncestors () : void
    {
        $parent = new MenuItem("parent", ["current" => true]);
        $child = $parent->addChild("child", ["current" => true]);
        $grandchild = $child->addChild("grandchild");

        $urlGenerator = $this->getMockBuilder(ResolveHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parent->resolveTree($urlGenerator, [
            "currentClass" => "current",
            "ancestorClass" => "ancestor",
        ]);

        self::assertContains("ancestor", $parent->getListItemAttributes()["class"]);
        self::assertContains("current", $parent->getListItemAttributes()["class"]);

        self::assertNotContains("ancestor", $child->getListItemAttributes()["class"]);
        self::assertContains("current", $child->getListItemAttributes()["class"]);

        self::assertNotContains("ancestor", $grandchild->getListItemAttributes()["class"]);
        self::assertNotContains("current", $grandchild->getListItemAttributes()["class"]);
    }


    /**
     *
     */
    public function testLevel () : void
    {
        $parent = new MenuItem("parent");
        $child = $parent->addChild("child");
        $grandchild = $child->addChild("grandchild");

        self::assertSame(0, $parent->getLevel());
        self::assertSame(1, $child->getLevel());
        self::assertSame(2, $grandchild->getLevel());
    }


    /**
     * @param array $config
     * @param       $expected
     */
    public function testTarget () : void
    {
        $url = new MenuItem(null, ["target" => "abc"]);
        self::assertSame("abc", $url->getTarget());


        $routeWithout = new MenuItem(null, ["route" => "route"]);
        self::assertInstanceOf(RouteTarget::class, $routeWithout->getTarget());
        self::assertSame("route", $routeWithout->getTarget()->getRoute());
        self::assertSame([], $routeWithout->getTarget()->getParameters());

        $routeWith = new MenuItem(null, ["route" => "route2", "routeParameters" => ["test" => 123]]);
        self::assertInstanceOf(RouteTarget::class, $routeWith->getTarget());
        self::assertSame("route2", $routeWith->getTarget()->getRoute());
        self::assertSame(["test" => 123], $routeWith->getTarget()->getParameters());
    }


    /**
     *
     */
    public function testPriority () : void
    {
        $parent = new MenuItem();
        $parent->addChild("10", ["priority" => 10]);
        $parent->addChild("100", ["priority" => 100]);
        $parent->addChild("-10", ["priority" => -10]);
        $parent->addChild("50", ["priority" => 50]);
        $parent->addChild("none");

        $urlGenerator = $this->getMockBuilder(ResolveHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $parent->resolveTree($urlGenerator, []);

        $children = $parent->getChildren();
        self::assertSame("100", $children[0]->getLabel());
        self::assertSame("50", $children[1]->getLabel());
        self::assertSame("10", $children[2]->getLabel());
        self::assertSame("none", $children[3]->getLabel());
        self::assertSame("-10", $children[4]->getLabel());
    }
}
